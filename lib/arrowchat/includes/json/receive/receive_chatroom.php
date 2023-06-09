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
	$response 			= array();
	$chat_users			= array();
	$time 				= time();
	$user_title			= array();
	$global_is_mod		= 0;
	$global_is_admin	= 0;
	$chatroom_id		= get_var('chatroomid');
	$popout_cr			= get_var('popoutroom');

	// ###################### START CHATROOM BANLIST CHECK ###################
	if (logged_in($userid))
	{
		$result = $db->execute("
			SELECT ban_length, ban_time 
			FROM arrowchat_chatroom_banlist 
			WHERE (user_id = '" . $db->escape_string($userid) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "')
				OR (ip_address = '" . $db->escape_string($user_ip) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "')
			ORDER BY ban_time DESC
			LIMIT 1
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			if ((empty($row['ban_length']) OR ((($row['ban_length'] * 60) + $row['ban_time']) > time())) AND $is_admin != 1) 
			{
				if (empty($row['ban_length']))
				{
					$error[] = array('t' => '3', 'm' => $language[55]);
				}
				else
				{
					$error[] = array('t' => '3', 'm' => $language[56] . $row['ban_length']);
				}
					
				$response['error'] = $error;
				
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
				exit;
			}
		}
	}

	// ##################### START CHATROOM USERS RECEIVE ####################
	if (logged_in($userid)) 
	{
		if ($popout_cr == "1") {
			$popout = 0;
			updateUserSession();
		}
	
		$db->execute("
			INSERT INTO arrowchat_chatroom_users (
				user_id,
				chatroom_id,
				session_time
			) 
			VALUES (
				'" . $db->escape_string($userid) . "', 
				'" . $db->escape_string($chatroom_id) . "', 
				'" . $time . "'
			) 
			ON DUPLICATE KEY 
			UPDATE chatroom_id = '" . $db->escape_string($chatroom_id) . "', 
				session_time = '" . $time . "'
		");
		
		$result = $db->execute("
			SELECT user_id, is_admin, is_mod, block_chats 
			FROM arrowchat_chatroom_users
			WHERE (chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND session_time > (" . $time . " - 91))
			ORDER BY is_admin DESC, is_mod DESC, session_time DESC");
		
		while ($chatroom_users = $db->fetch_array($result)) 
		{
			$title = 4;
			
			if ($chatroom_users['is_mod'] == "1")
			{
				$title = 2;
			}
			
			if ($chatroom_users['is_admin'] == "1")
			{
				$title = 3;
			}

			$fetchid = $chatroom_users['user_id'];
			
			if ($fetchid == $userid) 
			{
				if ($title==2) 
				{
					$global_is_mod = 1;
				}
				
				if ($title==3) 
				{
					$global_is_admin = 1;
				}
			}
			
			if (check_if_guest($fetchid))
			{
				$title = 1;
				$sql = get_guest_details($fetchid);
				$result2 = $db->execute($sql);
				$user = $db->fetch_array($result2);
				
				$user['username'] = create_guest_username($user['userid'], $user['guest_name']);
				$link = "#";
				$avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
			}
			else
			{
				$sql = get_user_details($fetchid);
				$result3 = $db->execute($sql);
				$user = $db->fetch_array($result3);
				
				$avatar	= get_avatar($user['avatar'], $fetchid);
				$link	= get_link($user['link'], $fetchid);
			}
			
			if (((time()-$user['lastactivity']) < $online_timeout) AND $user['status'] != 'invisible' AND $user['status'] != 'offline') 
			{
				if ($user['status'] != 'busy' AND $user['status'] != 'away') 
				{
					$user['status'] = 'available';
				}
			} 
			else 
			{
				if ($user['status'] == 'invisible') 
				{
					if ($is_admin == 1) 
					{
						$user['status'] = 'available';
					}
				} 
				else
				{
					$user['status'] = 'available';
				}
			}
			
			$chat_users[] = array('id' => $user['userid'], 'n' => $db->escape_string(strip_tags($user['username'])), 'a' => $avatar, 'l' => $link, 't' => $title, 'b' => $chatroom_users['block_chats'], 'status' => $user['status']);
		}
		
		$user_title[] = array('admin' => $global_is_admin, 'mod' => $global_is_mod);
		
		$response['user_title'] = $user_title;
		$response['chat_users'] = $chat_users;
	}

	header('Content-type: application/json; charset=UTF-8');
	echo json_encode($response);
	close_session();
	exit;

?>