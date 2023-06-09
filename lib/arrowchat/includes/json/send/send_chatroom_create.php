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
	
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	// ########################## INCLUDE BACK-END ###########################
	require_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');

	// ########################### GET POST DATA #############################
	$chatroom_name		= htmlspecialchars(get_var('name'));
	$chatroom_password	= get_var('password');
	
	$type = "1";
	$password = "";

	// ###################### START CREATE CHATROOM ##########################
	if (!empty($chatroom_name) AND $chatroom_name != $language[98]) 
	{
		if (!empty($user_chatrooms)) 
		{
			$flood_time = $user_chatrooms_flood *60;

			$result = $db->execute("
				SELECT session_time
				FROM arrowchat_chatroom_rooms
				WHERE author_id = '" . $db->escape_string($userid) . "'
					AND session_time > " . time() . " - " . $flood_time . "
			");
			
			if ($result AND $db->count_select() < 1) 
			{
				if (!empty($chatroom_password) && $chatroom_password != $language[99]) {
					$type = "2";
					$password = $chatroom_password;
				}
				
				$db->execute("
					INSERT INTO arrowchat_chatroom_rooms (
						author_id, 
						name, 
						type, 
						password,
						length, 
						session_time
					) 
					VALUES (
						'" . $db->escape_string($userid) . "',
						'" . $db->escape_string($chatroom_name) . "', 
						'" . $type . "', 
						'" . $db->escape_string($password) . "',
						'" . $db->escape_string($user_chatrooms_length) . "', 
						'" . time() . "'
					)
				");
			
				echo "1";
			} 
			else 
			{
				echo "-1"; // Display flood time limit error
			}
		}
		else 
		{
			echo "-2"; // Display user created chatrooms off error
		}
	}
	else
	{
		// Space for error that no chat room name was input
	}
	
	close_session();
	exit(0);

?>