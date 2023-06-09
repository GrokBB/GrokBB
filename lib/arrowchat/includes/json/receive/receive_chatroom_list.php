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

	// ########################### INITILIZATION #############################
	$response 	= array();
	$chatrooms 	= array();
	$time 		= time();
	$chatroom_window	= get_var('chatroom_window');
	$chatroom_stay		= get_var('chatroom_stay');

	// ###################### START CHATROOM WINDOW CHECK ####################
	if (logged_in($userid)) 
	{
		if ($chatroom_window != "-1") 
		{
			$db->execute("
				UPDATE arrowchat_status
				SET chatroom_window = '0'
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}
		
		if ($chatroom_stay != "-1") 
		{
			$db->execute("
				UPDATE arrowchat_status
				SET chatroom_stay = '0'
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}
	}
	
	// ###################### START LOGOUT USER ####################
	if (logged_in($userid)) 
	{
		$db->execute("
			UPDATE arrowchat_chatroom_users
			SET session_time = '0'
			WHERE user_id = '" . $db->escape_string($userid) . "'
		");
	}

	// ##################### START CHATROOM LIST RECEIVE #####################
	if (logged_in($userid)) 
	{	
		$result = $db->execute("
			SELECT id, name, description, image, type, length, session_time 
			FROM arrowchat_chatroom_rooms 
			ORDER BY id ASC
		");

		while ($chatroom = $db->fetch_array($result)) 
		{
			$result2 = $db->execute("
				SELECT COUNT(user_id)
				FROM arrowchat_chatroom_users
				WHERE (chatroom_id='" . $db->escape_string($chatroom['id']) . "'
					AND session_time > (" . $time . " - 70))
			");

			$count = $db->fetch_array($result2);
		
			$not_expired = true;
			
			$result3 = $db->execute("
				SELECT sent
				FROM arrowchat_chatroom_messages
				WHERE chatroom_id = '" . $db->escape_string($chatroom['id']) . "'
				ORDER BY sent DESC
				LIMIT 1
			");
			
			$last_sent = $db->fetch_array($result3);
			
			if (!$result OR $db->count_select() < 1) 
			{
				$last_sent = NULL;
			}
		
			if (!empty($chatroom['length'])) 
			{
				$length = $chatroom['length'] * 60;
				
				if ((($last_sent['sent'] + $length) < $time AND !empty($last_sent)) OR (($chatroom['session_time'] + $length) < $time AND empty($last_sent))) 
				{
					$not_expired = false;
				}
			}
			
			if (empty($chatroom['image']))
				$chatroom['image'] = "chatroom_default.png";
				
			if (empty($chatroom['description']))
				$chatroom['description'] = $language[150];
		
			if ($not_expired AND ($chatroom['type'] != 3 OR ($chatroom['type'] == 3 AND ($is_admin == 1 OR $is_mod == 1))))
			{
				$chatroom['name'] = str_replace("\\'", "'", $chatroom['name']);
				$chatrooms[] = array('id' => $chatroom['id'], 'n' => $chatroom['name'], 'd' => $chatroom['description'], 'img' => $chatroom['image'], 't' => $chatroom['type'], 'c' => $count['COUNT(user_id)']);
			}
		}

		$response['chatrooms'] = $chatrooms;
	}

	header('Content-type: application/json; charset=UTF-8');
	echo json_encode($response);
	close_session();
	exit;

?>