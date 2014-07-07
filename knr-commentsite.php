<?php
/*
Plugin Name: KNR Comment Site Name
Plugin URI: http://www.n4express.com/blog/?page_id=169
Description: Lists the site name after the comment author name
Author: k_nitin_r
Author URI: http://www.nitinkatkam.com
Version: 0.2

The KNR Comment Site plugin changes the display of comment authors to say "CommenterName says:" to "CommenterName of SiteName says:". The site name is obtained from the title tag of the page at the URL entered by the comment author. If the comment author does not enter a site URL, the plugin makes no change to the display of the comment author.

The KNR Comment Site plugin is a no-configuration plugin that automatically works against the comments that users have already posted and will post to your WordPress site to display the comment author's site name against the comment author's name. The users do not have to enter any additional information because the plugin obtains the site name from the title tag of the page located at the URL entered by the comment author.
*/

/*
-=[ Licensing ]=-

This plugin is licensed under GPL v2


-=[ Credits ]=-

Thanks go out to...

Narsing Reddy Katkam
	For providing the computers and networking equipment used for building and testing this plugin
	
Lorelle VanFossen
	For posting the plugin request to which I responded by building this plugin
	
	
-=[ Support ]=-

No explicit or implicit warranties are provided with the use of this plugin. However, if you do need any help, feel free to reach me at k_nitin_r [at] yahoo.co.in

*/

add_filter('get_comment_author', 'knr_comauth');

function knr_comauth_dircheck() {
	if (!file_exists('wp-content/comauthsite_cache'))
		mkdir('wp-content/comauthsite_cache');
}

function knr_comauth($txt) {
	$url = get_comment_author_url();
	
	if (
		(!(isset($url) && $url != ''))
		||
		is_admin()
	) return $txt; //short-circuited response if no URL
	
	//$cachefilename = ('wp-content/comauthsite_cache/comauthsite_'.get_comment_ID().'.txt');
	$cachefilename = ('wp-content/comauthsite_cache/'.sha1(get_comment_author_url()).'.txt');
	if (!file_exists($cachefilename)) {
		$markup = file_get_contents($url);	
		$matches = array();	
		preg_match('#<title>([^<]+)</title>#', $markup, $matches);
		$suffix = (count($matches) == 2 ? ' of '.$matches[1] : '');
		
		knr_comauth_dircheck();
		file_put_contents($cachefilename, $suffix);
	} else {
		$suffix = file_get_contents($cachefilename);
	}
	
	return $txt.$suffix;
}
?>