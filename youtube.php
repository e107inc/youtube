<?php
	/**
	 * e107 website system
	 *
	 * Copyright (C) 2008-2016 e107 Inc (e107.org)
	 * Released under the terms and conditions of the
	 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
	 *
	 */

if (!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

e107::css('youtube', 'youtube.css');

e107::lan('youtube',false, true);

class youtube_front
{

	private $type = array('user', 'channel_id', 'playlist_id');
	private $curVideo = null;
	private $videoData = array();
	private $category = null;
	private $social = false;
	private $caption = null;
	private $text = null;
	private $active = true;
	private $apiKey = false;
	private $maxResults = 50;
	private $subscribeID = false;

	function __construct()
	{
		/*  https://www.youtube.com/feeds/videos.xml?channel_id=UCCTFPxfqocLnf5YVrgqcRhw
			https://www.youtube.com/feeds/videos.xml?channel_id=CHANNELID
			https://www.youtube.com/feeds/videos.xml?user=USERNAME
			https://www.youtube.com/feeds/videos.xml?playlist_id=YOUR_YOUTUBE_PLAYLIST_NUMBER
		*/

		$visibilityClass = e107::pref('youtube', 'visibility',e_UC_PUBLIC);

		if(!check_class($visibilityClass))
		{
			$this->active = false;
			return false;
		}

		$this->apiKey = e107::pref('core','youtube_apikey', false);

		if(empty($_GET['cat']))
		{
			$this->text = $this->renderCategories();
			$this->caption = LAN_YOUTUBE_004;
			return false;
		}

		$this->category = e107::getParser()->filter($_GET['cat']);

		if(e107::isInstalled('social')==true)
		{
			$this->social = true;
		}



		$data = $this->lookup();

		$tp = e107::getParser();

		$caption = $tp->toHtml($data['youtube_title']);

		$list = $this->getFeed($data['youtube_ref'], $data['youtube_type']);

		if(!empty($data['youtube_subscribe']))
		{
			$this->subscribeID = $data['youtube_subscribe'];
		}


		$text = '';

		if(!empty($_GET['id']))
		{
			$this->curVideo = $_GET['id'];
			$text .= $this->renderVideo();
			$text .= "<hr />";
		}



		$text .= $this->renderList( $list);


		$this->caption = $caption;
		$this->text = $text;

	}

	function render()
	{
		if($this->active === false)
		{
			return false;
		}

		e107::getRender()->tablerender($this->caption, $this->text);
	}


	function lookup()
	{
		return e107::getDb()->retrieve('youtube', '*', "youtube_sef = '".$this->category."' LIMIT 1");
	}


	function renderCategories()
	{
		$data = e107::getDb()->retrieve('youtube', '*', "", true);
		$text = "<ul>";

		foreach($data as $val)
		{
			$link = e107::url('youtube', 'cat', $val);

			$text .= "<li><a href='".$link."'>".$val['youtube_title']."</a></li>";
		}

		$text .= "</ul>";

		return $text;
	}

	function getFeed($code, $type)
	{


		$type = intval($type);

		$this->feedType = $type;
		$this->feedId = $code;

		$feed = "https://www.youtube.com/feeds/videos.xml?".$this->type[$type]."=".$code;

	//	e107::getDebug()->log($type);
	//	e107::getDebug()->log($this->apiKey);

		if(!empty($this->apiKey))
		{

			if($type === 2) // playlist
			{
			//	$feed = "https://www.googleapis.com/youtube/v3/search?part=snippet&id=".urlencode($code)."&type=playlist&maxResults=30&key=".$apiKey;

				$feed = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=".urlencode($code)."&maxResults=".$this->maxResults."&key=".$this->apiKey;
			}

			if($type === 1) //channel
			{
				$feed = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=".urlencode($code)."&type=video&maxResults=".$this->maxResults."&key=".$this->apiKey;
			}
		}

		e107::getDebug()->log($feed);

		$cache = e107::getCache();

		$cache->setMD5($this->category."-".$this->maxResults, false);

		if(!$filed = $cache->retrieve('youtube', 15, true))
		{
			$parseFeed = ($this->apiKey) ? false : true;
			$data = e107::getXML()->loadXMLfile($feed, $parseFeed);
			$filed = ($this->apiKey) ? $data : e107::serialize($data);
			$cache->set('youtube', $filed,true);
			e107::getDebug()->log("Refreshing YouTube Feed Cache");
		}
		else
		{
			e107::getDebug()->log("Using Cached YouTube Feed Data.");
		}

		if($this->apiKey)
		{
			$data = json_decode($filed,true);
			$this->setVideoAPI($data['items'],$type);
		}
		else
		{
			$data = e107::unserialize($filed);
			$this->setVideoRSS($data['entry']);
		}

	//	e107::getDebug()->log($data);

		return $data;
	}

	/**
	 * Standard RSS Feed - 15 item limit
	 * @param $list array
	 */
	private function setVideoRSS($list)
	{

		foreach($list as $item)
		{
			$id = str_replace('yt:video:','', $item['id']);

			$this->videoData[$id]['id']             = $id;
			$this->videoData[$id]['title']          = $item['title'];
			$this->videoData[$id]['date']           = strtotime($item['published']);
			$this->videoData[$id]['description']    = $item['media_group']['media_description'];
			$this->videoData[$id]['thumbnail']      = $item['media_group']['media_thumbnail']['@attributes']['url'];
			$this->videoData[$id]['thumbnailHD']    = str_replace('hqdefault', 'sddefault', $this->videoData[$id]['thumbnail']);
		}

	}

	/**
	 * YouTube API v3.
	 * @param $list array
	 */
	private function setVideoAPI($list, $type)
	{

		if($type === 1)
		{
			$list = array_reverse($list);
		}


		foreach($list as $item)
		{
			if($type === 1)
			{
				$id = $item['id']['videoId'];
			}
			else
			{

				$id = $item['snippet']['resourceId']['videoId'];
				if(empty($item['snippet']['thumbnails']['standard']['url']))
				{
				//	continue;
				}
			}



			$this->videoData[$id]['id']             = $id;
			$this->videoData[$id]['title']          = $item['snippet']['title'];
			$this->videoData[$id]['date']           = strtotime($item['snippet']['publishedAt']);
			$this->videoData[$id]['description']    = $item['snippet']['description'];
			$this->videoData[$id]['thumbnail']      = $item['snippet']['thumbnails']['high']['url'];
			$this->videoData[$id]['thumbnailHD']    = $this->videoData[$id]['thumbnail']; // str_replace('hqdefault', 'sddefault', $this->videoData[$id]['thumbnail']);
		}

	}


	function renderVideo()
	{

		$data = $this->videoData[$this->curVideo];

		if(empty($data))
		{
			return LAN_YOUTUBE_003;
		}

		$id = $data['id'];



		e107::getDebug()->log($data);

		// http://img.youtube.com/vi/<insert-youtube-video-id-here>/maxresdefault.jpg

		e107::getMedia()->saveThumb($data['thumbnailHD'],$id);
		$img = e107::getMedia()->getThumb($id);
		$newthumb = e107::getParser()->thumbUrl($img, 'w=480&h=260&crop=c&x=1', false, true);
		//	e107::meta('og:image', e107::getParser()->replaceConstants($img,'full')); // str_replace('sddefault','maxresdefault',
		e107::getDebug()->log($newthumb);

		e107::meta('og:title', $data['title']);
		e107::meta('og:description',$data['description']);

		e107::meta('og:image',$newthumb);
		e107::meta('og:url', e_REQUEST_URL);
		e107::meta('og:type', 'article');

		$hash = $id.".youtube";

		$text = e107::getParser()->toVideo($hash);
		$text .= "<h2>".$data['title']."</h2>";
		$text .= "<div class='youtube-description'>".$data['description']."</div>";

		if($this->social === true)
		{
			$social = e107::getScBatch('social');
			$parms = array('url' => $this->getUrl($data,'full'), 'type'=>'basic', 'title'=>$data['title']);
			$text .= "<div class='text-right'><small>". $social->sc_socialshare($parms)."</small></div>";
		}

		return $text;
	}

	function getType()
	{
		return $this->data['youtube_type'];
	}

	function renderList($list=null)
	{

		if(empty($list))
		{
			return LAN_YOUTUBE_002;
		}

		$tp = e107::getParser();



		$text = "<div class='youtube row'>";

		foreach($this->videoData as $id=>$item)
		{
			$link = $this->getUrl($item);

			$active = ($id == $this->curVideo) ? true : false;


			$linkStart = "<a href='".$link."'>";
			$linkEnd    = "</a>";
			$class = 'thumbnail' ;

			if($active === true)
			{
				$linkStart = '';
				$linkEnd = '';
				$class = "thumbnail active";
			}



			$text .= "<div class='col-xs-12 col-sm-4'>
			<div class='".$class."'>".$linkStart.$this->renderThumbnail($item).$linkEnd."
			<h4>".$linkStart.$item['title'].$linkEnd."</h4>
			<div class='text-right text-muted youtube-date'><small>".$tp->toDate($item['date'], 'relative')."</small></div>
			</div>
			</div>"; // $item['title']."<br/>";

		}

		$text .= "</div>";

	//	$text .= print_a($list,true);

		if(!empty($this->subscribeID)) // channel/
		{
			$text .= '
				<script src="https://apis.google.com/js/platform.js"></script>
				<div class="youtube-subscribe"><small>'.LAN_YOUTUBE_001.'</small>
				<div class="g-ytsubscribe" data-channelid="'.$this->subscribeID.'" data-layout="default" data-theme="dark" data-count="default"></div>
				</div>
			';

		}

		return $text;


	}


	private function getUrl($item, $mode='abs')
	{

		$data = array('youtube_sef' => $this->category,
		'hash'=>$item['id'],
		'title' => eHelper::title2sef($item['title'], 'dashl')
		);

		return e107::url('youtube', 'item', $data, array('mode'=>$mode));



	}

	function renderThumbnail($item)
	{
		$src = $item['thumbnail'];
		$src2 = $item['thumbnailHD'];

		return "<div class='embed-responsive embed-responsive-16by9'><img class='img-responsive' src='".$src."' srcset='".$src2." 640w' alt='' /></div>";

	}

}


$ytb = new youtube_front;


require_once(HEADERF);

$ytb->render();

require_once(FOOTERF);
exit;

