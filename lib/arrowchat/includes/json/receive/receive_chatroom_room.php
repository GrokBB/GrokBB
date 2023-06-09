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
	$chat_history 		= array();
	$chat_users			= array();
	$chat_name			= array();
	$error				= array();
	$user_title			= array();
	$user_cache			= array();
	$temp_array			= array();
	$roominfo			= array();
	$time 				= time();
	$global_is_mod		= 0;
	$global_is_admin	= 0;
	$chatroom_id		= get_var('chatroomid');
	$chatroom_window	= get_var('chatroom_window');
	$chatroom_stay		= get_var('chatroom_stay');
	$chatroom_pw		= get_var('chatroom_pw');
	
	// Start a session if one does not exist
	$a = session_id();
	if (empty($a)) 
	{
		session_start();
	}
	
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
				close_session();
				exit;
			}
		}
	}

	// ##################### START CHATROOM PASSWORD CHECK ###################
	if (logged_in($userid)) 
	{
		$result = $db->execute("
			SELECT type, name, welcome_message, description, password, author_id, max_users, limit_message_num, limit_seconds_num, disallowed_groups
			FROM arrowchat_chatroom_rooms 
			WHERE id = '" . $db->escape_string($chatroom_id) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			// Save on queries and get the chatroom name here
			$chat_name[] = array('n' =>  $row['name']);
			$response['chat_name'] = $chat_name;
			
			// Save other data
			$disallowed_groups = unserialize($row['disallowed_groups']);
			$chatroom_max_users = $row['max_users'];
			
			if ($row['type'] == 2) 
			{
				if ($_SESSION['chatroom_password'] == $chatroom_id AND (empty($_POST['chatroom_pw']) OR get_var('chatroom_pw') == "undefined")) 
				{
				} 
				else 
				{
					if ($chatroom_pw != $row['password'] AND $is_admin != 1 AND $is_mod != 1) 
					{
						$error[] = array('t' => '2', 'm' => $language[49]);
						$response['error'] = $error;
						
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($response);
						exit;
					} 
					else 
					{
						$_SESSION['chatroom_password'] = $chatroom_id;
					}
				}
			}
			
			$roominfo[] = array('welcome_msg' => $row['welcome_message'], 'desc' => $row['description'], 'limit_msg' => $row['limit_message_num'], 'limit_sec' => $row['limit_seconds_num']);
			$response['room_info'] = $roominfo;
		} 
		else 
		{
			$error[] = array('t' => '1', 'm' => $language[48]);
			$response['error'] = $error;
			
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response);
			close_session();
			exit;
		}
	}
	
	// #################### START CHATROOM USER GROUP CHECK ##################
	if (logged_in($userid)) 
	{
		if (!empty($disallowed_groups))
		{
			if (check_array_for_match($group_id, $disallowed_groups))
			{
				$error[] = array('t' => '1', 'm' => $language[210]);
				$response['error'] = $error;
				
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
				close_session();
				exit;
			}
		}
	}

	// ###################### START CHATROOM WINDOW CHECK ####################
	if (logged_in($userid)) 
	{
		if ($chatroom_window != "-1") 
		{
			$db->execute("
				UPDATE arrowchat_status
				SET chatroom_window = '" . $db->escape_string($chatroom_id) . "'
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}
		
		if ($chatroom_stay != "-1") {
			$db->execute("
				UPDATE arrowchat_status
				SET chatroom_stay = '" . $db->escape_string($chatroom_id) . "'
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}
	}

	// ##################### START CHATROOM USERS RECEIVE ####################
	if (logged_in($userid)) 
	{
		$already_exists = false;
		
		$result2 = $db->execute("
			SELECT is_mod, chatroom_id
			FROM arrowchat_chatroom_users
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
		");
		
		if ($row2 = $db->fetch_array($result2))
		{
			$current_is_mod = $row2['is_mod'];
			$current_chatroom_id = $row2['chatroom_id'];
			$already_exists = true;
		}
		
		if (($row['author_id'] == $userid AND !$already_exists) OR ($current_is_mod == "1" && $current_chatroom_id == $chatroom_id))
		{
			$is_mod = 1;
		}
		
		$db->execute("
			INSERT INTO arrowchat_chatroom_users (
				user_id,
				chatroom_id,
				is_admin,
				is_mod,
				block_chats,
				session_time
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($chatroom_id) . "',
				'" . $db->escape_string($is_admin) . "', 
				'" . $db->escape_string($is_mod) . "', 
				'" . $db->escape_string($chatroom_block_chats) . "',
				'" . $time . "') 
			ON DUPLICATE KEY 
			UPDATE chatroom_id = '" . $db->escape_string($chatroom_id)."', 
				is_admin = '" . $is_admin . "', 
				is_mod = '" . $db->escape_string($is_mod) . "', 
				session_time = '" . $time . "'
		");
		
		$result = $db->execute("
			SELECT user_id, is_admin, is_mod, block_chats 
			FROM arrowchat_chatroom_users
			WHERE (chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND session_time > (" . $time . " - 91))
			ORDER BY is_admin DESC, is_mod DESC, session_time DESC
		");
		
		$i = 0;
		
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
			
			$user_cache[$fetchid] = $avatar;
			
			$chat_users[] = array('id' => $user['userid'], 'n' => $db->escape_string(strip_tags($user['username'])), 'a' => $avatar, 'l' => $link, 't' => $title, 'b' => $chatroom_users['block_chats'], 'status' => $user['status']);
			
			$i++;
		}
		
		// Exit if too many users are in the chat room
		if ($i > $chatroom_max_users AND $is_admin != 1 AND $is_mod != 1 AND $chatroom_max_users != 0)
		{
			$error[] = array('t' => '3', 'm' => $language[127]);
			$response['error'] = $error;
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response);
			close_session();
			exit;
		}
		
		$user_title[] = array('admin' => $global_is_admin, 'mod' => $global_is_mod);
		
		$response['user_title'] = $user_title;
		$response['chat_users'] = $chat_users;
	}

	// ################### START CHATROOM HISTORY RECEIVE ####################
	if (logged_in($userid)) 
	{	
		$history_length = $chatroom_history_length * 60;
		
		$result = $db->execute("
			SELECT id, username, message, sent, user_id, global_message, is_mod, is_admin
			FROM arrowchat_chatroom_messages
			WHERE (chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND sent > (" . $time . " - " . $history_length . ")
				AND action = 0)
			ORDER BY sent DESC
			LIMIT 30
		");
		
		while ($chatroom_history = $db->fetch_array($result))
		{
			$temp_array[] = $chatroom_history;
		}
		
		$temp_array = array_reverse($temp_array);
		
		foreach ($temp_array as $chatroom_history)
		{
			$fetchid = $chatroom_history['user_id'];
			$chat_message = $chatroom_history['message'];
			$chat_message = str_replace("\\'", "'", $chat_message);
			$chat_message = str_replace('\\"', '"', $chat_message);
			$chat_message = clickable_links($chat_message);
			
			if (array_key_exists($fetchid, $user_cache))
			{
				$avatar = $user_cache[$fetchid];
			}
			else
			{
				$sql = get_user_details($fetchid);
				$result2 = $db->execute($sql);
				$user = $db->fetch_array($result2);
				$avatar	= get_avatar($user['avatar'], $fetchid);
				$user_cache[$fetchid] = $avatar;
			}
			
			$chat_history[] = array('id' => $chatroom_history['id'], 'n' => $db->escape_string(strip_tags($chatroom_history['username'])), 'm' => $chat_message, 't' => $chatroom_history['sent'], 'a' => $avatar, 'userid' => $fetchid, 'global' => $chatroom_history['global_message'], 'mod' => $chatroom_history['is_mod'], 'admin' => $chatroom_history['is_admin']);
		}
		
		$response['chat_history'] = $chat_history;
	}

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($response);
	close_session();
	exit;

?>