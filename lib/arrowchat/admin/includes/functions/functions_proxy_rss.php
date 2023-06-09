<?php

	/*
	|| #################################################################### ||
	|| #                             ArrowChat                            # ||
	|| # ---------------------------------------------------------------- # ||
	|| #    Copyright ©2010-2012 ArrowSuites LLC. All Rights Reserved.    # ||
	|| # This file may not be redistributed in whole or significant part. # ||
	|| # ---------------- ARROWCHAT IS NOT FREE SOFTWARE ---------------- # ||
	|| #   http://www.arrowchat.com | http://www.arrowchat.com/license/   # ||
	|| #################################################################### ||
	*/

	/**
	 * Gets information about a web page from a URL
	 *
	 * @param	string	$url	The web page to get
	 * @return	array	The web page information
	*/
	function get_web_page($url)
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_USERAGENT      => "spider", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);

		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );

		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;
		
		return $header;
	}
	
	/**
	 * Returns XML information if cURL is not installed
	 *
	 * @return	string	The XML
	*/
	function no_curl_installed()
	{
		return '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	>

<channel>
	<title>ArrowChat</title>
	<atom:link href="http://www.arrowchat.com/blog/?feed=rss2" rel="self" type="application/rss+xml" />
	<link>http://www.arrowchat.com/blog</link>
	<description>Just another WordPress weblog</description>
	<lastBuildDate>Thu, 20 May 2010 12:14:53 +0000</lastBuildDate>
	<generator>http://wordpress.org/?v=2.9.2</generator>
	<language>en</language>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
			<item>
		<title>Unable to Load ArrowChat Blog</title>
		<link>http://www.arrowchat.com/blog/</link>
		<comments>http://www.arrowchat.com/blog/</comments>
		<pubDate>Thu, 20 May 2010 12:14:53 +0000</pubDate>
		<dc:creator>admin</dc:creator>
				<category><![CDATA[Versions]]></category>

		<guid isPermaLink="false">http://www.arrowchat.com/blog/</guid>
		<description><![CDATA[The cURL PHP library is not installed on your server, so we cannot deliver the ArrowChat blog to your admin panel.  However, you can click the above link to visit our blog.]]></description>
		<wfw:commentRss>http://www.arrowchat.com/blog/?feed=rss2</wfw:commentRss>
		<slash:comments>0</slash:comments>
		</item>
	</channel>
</rss>';
	}

	// Figure out if cURL is installed
	if (function_exists('curl_init')) 
	{
		$result = get_web_page($_REQUEST['url']);
		$buffer = $result['content'];
	} 
	else 
	{
		$buffer = no_curl_installed();
	}

	header('Content-type: application/xml');
	echo $buffer;

?>