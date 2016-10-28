<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Sitelinks configuration module - gsitemap
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/_blank/e_sitelink.php,v $
 * $Revision$
 * $Date$
 * $Author$
 *
*/

if (!defined('e107_INIT')) { exit; }
/*if(!e107::isInstalled('_blank'))
{ 
	return;
}*/



class youtube_sitelink // include plugin-folder in the name.
{
	function config()
	{
		global $pref;
		
		$links = array();
			
		$links[] = array(
			'name'			=> "Drop-Down Categories",
			'function'		=> "myCategories"
		);


		return $links;
	}


	function myCategories()
	{
		$sql = e107::getDb();
		$tp = e107::getParser();
		$sublinks = array();
		
		$sql->select("youtube","*","youtube_id != '' ");
		
		while($row = $sql->fetch())
		{
			$sublinks[] = array(
				'link_name'			=> $tp->toHtml($row['youtube_title'],'','TITLE'),
				'link_url'			=> e107::url('youtube', 'cat', $row),
				'link_description'	=> '',
				'link_button'		=> $row['blank_icon'],
				'link_category'		=> '',
				'link_order'		=> '',
				'link_parent'		=> '',
				'link_open'			=> '',
				'link_class'		=> e_UC_PUBLIC
			);
		}
		
		return $sublinks;
	    
	}
	
}
