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
	require_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'functions_receive.php');
	
	$time 			= time();
	$response 		= array();
	$messages 		= array();
	$notifications 	= array();
	$typing 		= array();
	$chatroom		= array();
	$announcement 	= array();
	$warnings		= array();
	$markup 		= "";
	$chatroom_check	= false;
	$initialize 	= get_var('init');
	$hash_id 		= get_var('hash');
	$chatroomid		= get_var('room');
	
	// Start a session if one does not exist
	$a = session_id();
	if (empty($a)) 
	{
		session_start();
	}
	
	// ########################### START SCRIPT #############################
	if (!empty($chatroomid) && $chatroomid != "-1")
	{
		$chatroom_check = true;
	}
	
	$long_polling = false;

	if (var_check('popout'))
	{
		$popout = true;
	}
	else
	{
		$popout = false;
	}
	
	$result = $db->execute("
		SELECT userid, last_message 
		FROM arrowchat_status 
		WHERE hash_id = '" . $db->escape_string($hash_id) . "'
	");

	if ($result AND $db->count_select() > 0) 
	{
		$row = $db->fetch_array($result);
		
		$last_message 	= $row['last_message'];
		$userid 		= $row['userid'];
	} 
	else 
	{
		$userid = NULL;
	}
	
	if ($initialize == 1) 
	{
		if (isset($_SESSION['notifications']))
		{
			unset($_SESSION['notifications']);
		}
		
		if (isset($_SESSION['tab_alert']))
		{
			unset($_SESSION['tab_alert']);
		}
		
		if (isset($_SESSION['typing']))
		{
			unset($_SESSION['typing']);
		}
		
		if (isset($_SESSION['not_typing']))
		{
			unset($_SESSION['not_typing']);
		}
		
		if (isset($_SESSION['notifytime']))
		{
			unset($_SESSION['notifytime']);
		}
		
		if (isset($_SESSION['announcetime']))
		{
			unset($_SESSION['announcetime']);
		}
		
		if (isset($_SESSION['warntime']))
		{
			unset($_SESSION['warntime']);
		}
	}
	
	if (isset($userid)) 
	{
		if (!isset($_SESSION['notifications']))
		{
			$_SESSION['notifications'] = array();
		}
		
		if (!isset($_SESSION['tab_alert']))
		{
			$_SESSION['tab_alert'] = array();
		}
		
		if (!isset($_SESSION['chatroom_mess_ids']))
		{
			$_SESSION['chatroom_mess_ids'] = array();
		}
		
		if (!isset($_SESSION['typing']))
		{
			$_SESSION['typing'] = array();
		}
		
		if (!isset($_SESSION['not_typing']))
		{
			$_SESSION['not_typing'] = array();
		}
		
		if (!isset($_SESSION['notifytime']))
		{
			$_SESSION['notifytime'] = 0;
		}
		
		if (!isset($_SESSION['announcetime']))
		{
			$_SESSION['announcetime'] = 0;
		}
		
		if (!isset($_SESSION['warntime']))
		{
			$_SESSION['warntime'] = 0;
		}
		
		getTyping();
		
		if ($notifications_on == 1 AND !$popout) 
		{ 
			getNotifications(); 
		}
		
		if ($chatroom_check AND !$popout) 
		{ 
			getChatroom(); 
		}
		
		if ($popout) 
		{ 
			checkPopout(); 
		}
		if (!$popout) 
		{ 
			getAnnouncements(); 
		}
		if ($enable_moderation == 1 AND !$popout) 
		{ 
			getWarnings(); 
		}
		
		fetchMessages();
	}

	header("Content-Type: application/x-javascript");
	echo $_GET['callback'] . '(' . json_encode($response) . ');';
	flush();
	close_session();
	exit;
	
?>