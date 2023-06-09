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

	// ########################## INCLUDE BACK-END ###########################
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');
	
	// Fix people that are trying to use old debug URL
	if (basename($_SERVER['REQUEST_URI']) == "debug.php" OR substr($_SERVER['REQUEST_URI'], -1) != "/")
	{
		header("Location: ./debug/");
	}

	// ############################### DEBUG #################################
	// Check if a user is logged in
	if (logged_in($userid))
	{
		$test_userid = "";
		$test_userid_img = "checked";
	}
	else
	{
		$test_userid = 'The User ID is not set. ArrowChat is acting as if no one is logged in which may or may not show the bar depending on your settings. <a href="javascript:;" class="vtip" title="&lt;b&gt;What this means&lt;/b&gt;<br />Depending on your ArrowChat admin settings, this may mean several things.  If guests are allowed to chat, the full bar will be displayed. If the option to show a message was chosen, then guests will see a message to login.  If neither of those is true, ArrowChat will not be displayed.<br /><br />&lt;b&gt;How to fix this&lt;/b&gt;<br />You can fix the User ID if there is a problem in the /arrowchat/includes/integration.php file. The get_user_id() function must return the logged in user\'s ID.">More Information &rarr;</a>';
		$test_userid_img = "unchecked";
	}
	
	// Check if the buddy list is functioning
	if ($disable_buddy_list == 1) 
	{
		if (!logged_in($userid))
			$userid = 0;
			
		$sql = get_online_list($userid, time());
	} 
	else 
	{
		if (!logged_in($userid))
			$userid = 0;
			
		$sql = get_friend_list($userid, time());
	}
	
	$result = $db->execute($sql);
	
	if ($result) 
	{
		$test_buddylist = "";
		$test_buddylist_img = "checked";
	} 
	else 
	{
		$test_buddylist = 'The buddy list has an error in the MySQL. Here is the error that MySQL is returning:' . $db->display_errors() . '<a href="javascript:;" class="vtip" title="&lt;b&gt;What this means&lt;/b&gt;<br />The buddy list cannot be loaded properly. You are most likely seeing an error when clicking on the chat tab.<br /><br />&lt;b&gt;How to fix this&lt;/b&gt;<br />You can fix the buddy list if there is a problem in the /arrowchat/includes/integration.php file. The get_friend_list() function is used for sites with a friends list. The get_online_list() function is used for sites that want to display all online users.">More Information &rarr;</a>';
		$test_buddylist_img = "unchecked";
	}


	// Check if the user's ID or IP address is banned
	if (!in_array($_SERVER['REMOTE_ADDR'], $banlist) OR !in_array($userid, $banlist)) 
	{
		$test_banned = "";
		$test_banned_img = "checked";
	} 
	else 
	{
		$test_banned = 'Your user ID or IP address is currently banned. You can change this in the ArrowChat admin panel.';
		$test_banned_img = "unchecked";
	}

	// Check if browser is IE6
	if (isset($_SERVER['HTTP_USER_AGENT']) AND preg_match('/(?i)msie [1-8]\./', $_SERVER['HTTP_USER_AGENT']))
	{
		$test_browser = 'You are currently using IE version 5 or 6. ArrowChat does not work with this browser.';
		$test_browser_img = "unchecked";
	} 
	else 
	{
		$test_browser = "";
		$test_browser_img = "checked";
	}

	// Check if functions_receive.php exists
	if (file_exists(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'integration.php')) {
		$integration_test = "";
		$integration_img = "checked";
	} else {
		$integration_test = "The integration file was no found.  Please go to your includes/functions/integrations/ folder and rename/move the appropriate functions file to includes/integration.php";
		$integration_img = "unchecked";
	}

	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'layout/header.php');
	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'layout/debug.php');
	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'layout/footer.php');

?>