<?php
/*
 * e107 Bootstrap CMS
 *
 * Copyright (C) 2008-2015 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 * 
 * IMPORTANT: Make sure the redirect script uses the following code to load class2.php: 
 * 
 * 	if (!defined('e107_INIT'))
 * 	{
 * 		require_once("../../class2.php");
 * 	}
 * 
 */
 
if (!defined('e107_INIT')) { exit; }

// v2.x Standard  - Simple mod-rewrite module. 

class youtube_url // plugin-folder + '_url'
{
	function config() 
	{
		$config = array();

		$config['item'] = array(
			'alias'         => 'videos',                            // default alias '_blank'. {alias} is substituted with this value below. Allows for customization within the admin area.
			'regex'			=> '^{alias}\/(.*)\/(.*)\/(.*)\/$', 						// matched against url, and if true, redirected to 'redirect' below.
			'sef'			=> '{alias}/{youtube_sef}/{hash}/{title}/', 							// used by e107::url(); to create a url from the db table.
			'redirect'		=> '{e_PLUGIN}youtube/youtube.php?cat=$1&id=$2', 		// file-path of what to load when the regex returns true.

		);

		$config['cat'] = array(
			'alias'         => 'videos',                            // default alias '_blank'. {alias} is substituted with this value below. Allows for customization within the admin area.
			'regex'			=> '^{alias}\/(.*)\/$', 						// matched against url, and if true, redirected to 'redirect' below.
			'sef'			=> '{alias}/{youtube_sef}/', 							// used by e107::url(); to create a url from the db table.
			'redirect'		=> '{e_PLUGIN}youtube/youtube.php?cat=$1', 		// file-path of what to load when the regex returns true.

		);


		$config['index'] = array(
			'alias'         => 'videos',
			'regex'			=> '^{alias}\/?(.*)$', 						// matched against url, and if true, redirected to 'redirect' below.
			'sef'			=> '{alias}', 							// used by e107::url(); to create a url from the db table.
			'redirect'		=> '{e_PLUGIN}youtube/youtube.php$1', 		// file-path of what to load when the regex returns true.

		);

		return $config;
	}
	

	
}