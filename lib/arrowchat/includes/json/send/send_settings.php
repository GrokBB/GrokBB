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
	$hide 					= get_var('hide');
	$sound 					= get_var('sound');
	$window 				= get_var('window');
	$name 					= get_var('name');
	$focus_chat 			= get_var('focus_chat');
	$unfocus_chat 			= get_var('unfocus_chat');
	$close_chat 			= get_var('close_chat');
	$clear_user				= get_var('clear_chat');
	$tab_alert				= get_var('tab_alert');
	$announce_read 			= get_var('announce');
	$changed_theme 			= get_var('theme');
	$chatroom_window		= get_var('chatroom_window');
	$chatroom_stay			= get_var('chatroom_stay');
	$chatroom_block_chats	= get_var('chatroom_block_chats');
	$chatroom_sound			= get_var('chatroom_sound');
	$chatroom_show_names	= get_var('chatroom_show_names');
	$chatroom_mod			= get_var('chatroom_mod');
	$chatroom_remove_mod	= get_var('chatroom_remove_mod');
	$chatroom_ban			= get_var('chatroom_ban');
	$chatroom_ban_length	= get_var('chatroom_ban_length');
	$chatroom_id			= get_var('chatroom_id');
	$bookmarks_list			= get_var('arrowchatapplist');
	$block_chat				= get_var('block_chat');
	$unblock_chat			= get_var('unblock_chat');
	$app_keep				= get_var('app_keep');
	$chat_name				= get_var('chat_name');
	$chatroom_welcome_msg	= get_var('chatroom_welcome_msg');
	$chatroom_description	= get_var('chatroom_description');
	$delete_msg				= get_var('delete_msg');
	$delete_name			= get_var('delete_name');
	$chatroom_silence		= get_var('chatroom_silence');
	$chatroom_silence_length= get_var('chatroom_silence_length');
	$report_from			= get_var('report_from');
	$report_about			= get_var('report_about');
	$report_chatroom		= get_var('report_chatroom');
	$flood_message			= get_var('flood_message');
	$flood_seconds			= get_var('flood_seconds');
	$report_id				= get_var('report_id');
	$report_ban				= get_var('report_ban');
	$report_warn			= get_var('report_warn');
	$report_warn_reason		= get_var('report_warn_reason');
	$report_warn_confirm	= get_var('report_warn_confirm');
	$warning_read			= get_var('warning_read');

	// ########################### CHECK USER ID #############################
	if (!logged_in($userid)) 
	{
		exit(0);
	}

	// ######################## START POST HIDE BAR ##########################
	if (!empty($_POST['hide'])) 
	{
		if ($hide == "-1")
		{
			$hide = 0;
		}
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid,
				hide_bar
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($hide) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE hide_bar = '" . $db->escape_string($hide) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ####################### START POST POPOUT CHAT ########################
	if (!empty($_POST['popoutchat'])) 
	{
		if ($_POST['popoutchat'] == "99") 
		{
			$time = 99;
		} 
		else 
		{
			$time = time();
		}
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid,
				popout
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($time) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE popout = '" . $db->escape_string($time) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ######################### START POST SOUND ############################
	if (!empty($_POST['sound'])) 
	{
		if ($sound == "-1")
		{
			$sound = 0;
		}
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid, 
				play_sound
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($sound) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE play_sound = '" . $db->escape_string($sound) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ##################### START POST KEEP WINDOW OPEN #####################
	if (!empty($_POST['window'])) 
	{
		if ($window == "-1") 
		{
			$window = 0;
		
			$db->execute("
				INSERT INTO arrowchat_status (
					userid, 
					window_open
				) 
				VALUES (
					'" . $db->escape_string($userid) . "',
					'" . $db->escape_string($window) . "'
				) 
				ON DUPLICATE KEY 
					UPDATE window_open = '" . $db->escape_string($window) . "'
			");
		} 
		else 
		{
			$db->execute("
				INSERT INTO arrowchat_status (
					userid, 
					window_open, 
					chatroom_window
				) 
				VALUES (
					'" . $db->escape_string($userid) . "',
					'" . $db->escape_string($window) . "', 
					'-1'
				) 
				ON DUPLICATE KEY 
					UPDATE window_open = '" . $db->escape_string($window) . "', chatroom_window = '-1'
			");
		}

		echo "1";
		close_session();
		exit(0);
	}

	// ##################### START POST SHOW ONLY NAMES ######################
	if (!empty($_POST['name'])) 
	{
		if ($name == "-1")
		{
			$name = 0;
		}
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid, 
				only_names
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($name) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE only_names = '" . $db->escape_string($name) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ######################## START POST FOCUS CHAT ########################
	if (!empty($_POST['focus_chat'])) 
	{
		if ($tab_alert == "1") 
		{
			$db->execute("
				UPDATE arrowchat 
				SET arrowchat.user_read = '1', arrowchat.read = '1'
				WHERE arrowchat.from = '" . $db->escape_string($focus_chat) . "' 
					AND arrowchat.to = '" . $db->escape_string($userid) . "' 
					AND arrowchat.user_read = '0'
			");
		}
		
		$result = $db->execute("
			SELECT unfocus_chat, focus_chat 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			$unfocus_chat = $row['unfocus_chat'];
			$unfocus_chat = preg_replace('/(^|:)' . $focus_chat . ':/', ':', $unfocus_chat);
			
			if (substr($unfocus_chat, 0, 1) == ":")
			{
				$unfocus_chat = substr($unfocus_chat, 1);
			}
			
			if (!empty($row['focus_chat']) AND $row['focus_chat'] != $focus_chat) 
			{
				$unfocus_chat .= $row['focus_chat'] . ":";
			}
		}
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid, 
				unfocus_chat, 
				focus_chat
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($unfocus_chat) . "', 
				'" . $db->escape_string($focus_chat) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE focus_chat = '" . $db->escape_string(trim($focus_chat)) . "', unfocus_chat = '" . $db->escape_string(trim($unfocus_chat)) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ###################### START POST UNFOCUS CHAT ########################
	if (!empty($_POST['unfocus_chat'])) 
	{
		if ($tab_alert == "1") 
		{
			$db->execute("
				UPDATE arrowchat 
				SET arrowchat.user_read = '1' , arrowchat.read = '1'
				WHERE arrowchat.from = '" . $db->escape_string($unfocus_chat) . "' 
					AND arrowchat.to = '" . $db->escape_string($userid) . "' 
					AND arrowchat.user_read = '0'
			");
		}
		
		$result = $db->execute("
			SELECT unfocus_chat 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			$unfocus_chat = $row['unfocus_chat'];
			$unfocus_chat .= $_POST['unfocus_chat'] . ":";
			$focus_chat = "";
		}
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid, 
				unfocus_chat, 
				focus_chat
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($unfocus_chat) . "', 
				'" . $db->escape_string($focus_chat) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE focus_chat = '" . $db->escape_string(trim($focus_chat)) . "', unfocus_chat = '" . $db->escape_string(trim($unfocus_chat)) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ######################## START POST CLOSE CHAT ########################
	if (!empty($_POST['close_chat'])) 
	{
		if ($tab_alert == "1") 
		{
			$db->execute("
				UPDATE arrowchat 
				SET arrowchat.user_read = '1', arrowchat.read = '1'
				WHERE arrowchat.from = '" . $db->escape_string($close_chat) . "' 
					AND arrowchat.to = '" . $db->escape_string($userid) . "' 
					AND arrowchat.user_read = '0'
			");
		}
		
		$result = $db->execute("
			SELECT unfocus_chat, focus_chat 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			$unfocus_chat = $row['unfocus_chat'];
			$focus_chat = $row['focus_chat'];
			$focus_chat = preg_replace('/' . $close_chat . '/', '', $focus_chat);
			$unfocus_chat = preg_replace('/(^|:)' . $close_chat . ':/', ':', $unfocus_chat);
			
			if (substr($unfocus_chat, 0, 1) == ":")
			{
				$unfocus_chat = substr($unfocus_chat, 1);
			}
		}
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid, 
				unfocus_chat, 
				focus_chat
			) 
			VALUES (
				'" . $db->escape_string($userid) . "',
				'" . $db->escape_string($unfocus_chat) . "', 
				'" . $db->escape_string($focus_chat) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE focus_chat = '" . $db->escape_string($focus_chat) . "', unfocus_chat = '" . $db->escape_string($unfocus_chat) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ######################## START POST CLEAR CHAT ########################
	if (!empty($_POST['clear_chat'])) 
	{	
		$result = $db->execute("
			SELECT clear_chats
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			$clear_chats = $row['clear_chats'];
			
			if (empty($clear_chats)) 
			{
				$new_clear_chats = array($clear_user => time());
			} 
			else 
			{
				$new_clear_chats = unserialize($clear_chats);
				$new_clear_chats[$clear_user] = time();
			}
		}
		
		$new_clear_chats = serialize($new_clear_chats);
		
		$db->execute("
			INSERT INTO arrowchat_status (
				userid, 
				clear_chats
			) 
			VALUES (
				'" . $db->escape_string($userid) . "', 
				'" . $db->escape_string($new_clear_chats) . "'
			) 
			ON DUPLICATE KEY 
				UPDATE clear_chats = '" . $db->escape_string($new_clear_chats) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}
	
	// ######################## START POST REPORT ########################
	if (!empty($_POST['report_from'])) 
	{	
		if ($enable_moderation == 1) 
		{
			if (empty($report_chatroom))
				$report_chatroom = 0;
				
			$db->execute("
				INSERT INTO arrowchat_reports (
					report_from, 
					report_about,
					report_chatroom,
					report_time
				) 
				VALUES (
					'" . $db->escape_string($report_from) . "', 
					'" . $db->escape_string($report_about) . "',
					'" . $db->escape_string($report_chatroom) . "',
					'" . $db->escape_string(time()) . "'
				)
			");
		}

		echo "1";
		close_session();
		exit(0);
	}

	// ################### START POST ANNOUNCEMENT READ ######################
	if (!empty($_POST['announce'])) 
	{

		$db->execute("
			UPDATE arrowchat_status 
			SET announcement = '1' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		echo "1";
		close_session();
		exit(0);
	}

	// ###################### START POST THEME CHANGE ########################
	if (!empty($_POST['theme'])) 
	{
		$db->execute("
			UPDATE arrowchat_status 
			SET theme = '" . $db->escape_string($changed_theme) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo time();
		close_session();
		exit(0);
	}

	// #################### START POST CHATROOM WINDOW #######################
	if (var_check('chatroom_window')) 
	{
		if ($chatroom_window != "-1") 
		{
			$db->execute("
				UPDATE arrowchat_status 
				SET chatroom_window = '" . $db->escape_string($chatroom_window) . "', 
					window_open = '0' 
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		} 
		else 
		{
			$db->execute("
				UPDATE arrowchat_status 
				SET chatroom_window = '" . $db->escape_string($chatroom_window) . "'
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}

		echo "1";
		close_session();
		exit(0);
	}

	// ##################### START POST CHATROOM STAY ########################
	if (var_check('chatroom_stay')) 
	{
		$db->execute("
			UPDATE arrowchat_status 
			SET chatroom_stay = '" . $db->escape_string($chatroom_stay) . "'
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ################## START POST BLOCK PRIVATE CHATS #####################
	if (var_check('chatroom_block_chats')) 
	{
		if ($chatroom_block_chats == "-1") 
		{
			$chatroom_block_chats = 0;
		}

		$db->execute("
			UPDATE arrowchat_status 
			SET chatroom_block_chats = '" . $db->escape_string($chatroom_block_chats) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		$db->execute("
			UPDATE arrowchat_chatroom_users  
			SET block_chats = '" . $db->escape_string($chatroom_block_chats) . "' 
			WHERE user_id = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}
	
	// ################## START POST CHAT ROOM SOUND #####################
	if (var_check('chatroom_sound')) 
	{
		if ($chatroom_sound == "-1") 
		{
			$chatroom_sound = 0;
		}

		$db->execute("
			UPDATE arrowchat_status 
			SET chatroom_sound = '" . $db->escape_string($chatroom_sound) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}
	
	// ################## START POST CHAT ROOM NAMES #####################
	if (var_check('chatroom_show_names')) 
	{
		if ($chatroom_show_names == "-1") 
		{
			$chatroom_show_names = 0;
		}

		$db->execute("
			UPDATE arrowchat_status 
			SET chatroom_show_names = '" . $db->escape_string($chatroom_show_names) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit(0);
	}

	// ##################### START POST MAKE MODERATOR #######################
	if (var_check('chatroom_mod')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			if (check_if_guest($chatroom_mod))
			{
				$mod_username = create_guest_username($chatroom_mod, '', true);
				
				if (empty($mod_username))
				{
					$mod_username = create_guest_username($chatroom_mod, '', false);
				}
			}
			else
			{
				$sql = get_user_details($chatroom_mod);
				$result = $db->execute($sql);
				
				if ($result AND $db->count_select() > 0) 
				{
					$row = $db->fetch_array($result);
					$mod_username = $row['username'];
				}
			}
			
			$mod_message = $mod_username . $language[106] . $db->escape_string(strip_tags(get_username($userid))) . ".";
			
			$db->execute("
				INSERT INTO arrowchat_chatroom_messages (
					chatroom_id,
					user_id,
					username,
					message,
					global_message,
					sent
				) 
				VALUES (
					'" . $db->escape_string($chatroom_id) . "', 
					'" . $db->escape_string($userid) . "', 
					'Global',
					'" . $mod_message . "',
					'1',
					'" . time() . "'
				)
			");
			
			if ($push_on == 1)
			{
				$arrowpush->publish(array(
					'channel' => 'chatroom' . $chatroom_id,
					'message' => array('chatroommessage' => array("id" => $db->last_insert_id(), "name" => 'Global', "message" => $mod_message, "userid" => $userid, "sent" => time(), "global" => '1'))
				));
			}
			
			$db->execute("
				UPDATE arrowchat_chatroom_users 
				SET is_mod = '1' 
				WHERE user_id = '" . $db->escape_string($chatroom_mod) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
			");
		}

		echo "1";
		close_session();
		exit(0);
	}

	// ################### START POST REMOVE MODERATOR #######################
	if (var_check('chatroom_remove_mod')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			if (check_if_guest($chatroom_remove_mod))
			{
				$mod_username = create_guest_username($chatroom_remove_mod, '', true);
				
				if (empty($mod_username))
				{
					$mod_username = create_guest_username($chatroom_remove_mod, '', false);
				}
			}
			else
			{
				$sql = get_user_details($chatroom_remove_mod);
				$result = $db->execute($sql);
				
				if ($result AND $db->count_select() > 0) 
				{
					$row = $db->fetch_array($result);
					$mod_username = $row['username'];
				}
			}
			
			$mod_message = $mod_username . $language[156] . $db->escape_string(strip_tags(get_username($userid))) . ".";
			
			$db->execute("
				INSERT INTO arrowchat_chatroom_messages (
					chatroom_id,
					user_id,
					username,
					message,
					global_message,
					sent
				) 
				VALUES (
					'" . $db->escape_string($chatroom_id) . "', 
					'" . $db->escape_string($userid) . "', 
					'Global',
					'" . $mod_message . "',
					'1',
					'" . time() . "'
				)
			");
			
			if ($push_on == 1)
			{
				$arrowpush->publish(array(
					'channel' => 'chatroom' . $chatroom_id,
					'message' => array('chatroommessage' => array("id" => $db->last_insert_id(), "name" => 'Global', "message" => $mod_message, "userid" => $userid, "sent" => time(), "global" => '1'))
				));
			}
			
			$db->execute("
				UPDATE arrowchat_chatroom_users 
				SET is_mod = '0' 
				WHERE user_id = '" . $db->escape_string($chatroom_remove_mod) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
			");
		}

		echo "1";
		close_session();
		exit(0);
	}

	// ####################### START POST BAN USER ##########################
	if (var_check('chatroom_ban')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			if (check_if_guest($chatroom_ban))
			{
				$ban_username = create_guest_username($chatroom_ban, '', true);
				
				if (empty($ban_username))
				{
					$ban_username = create_guest_username($chatroom_ban, '', false);
				}
			}
			else
			{
				$sql = get_user_details($chatroom_ban);
				$result = $db->execute($sql);
				
				if ($result AND $db->count_select() > 0) 
				{
					$row = $db->fetch_array($result);
					$ban_username = $row['username'];
				}
			}
			
			$ban_message = $ban_username . $language[107] . $db->escape_string(strip_tags(get_username($userid))) . ".";
			
			$db->execute("
				INSERT INTO arrowchat_chatroom_messages (
					chatroom_id,
					user_id,
					username,
					message,
					global_message,
					sent
				) 
				VALUES (
					'" . $db->escape_string($chatroom_id) . "', 
					'" . $db->escape_string($userid) . "', 
					'Global',
					'" . $ban_message . "',
					'1',
					'" . time() . "'
				)
			");

			if (empty($chatroom_ban_length))
				$ban_message2 = $language[55];
			else
				$ban_message2 = $language[56] . $chatroom_ban_length;
			
			if ($push_on == 1)
			{
				$arrowpush->publish(array(
					'channel' => 'chatroom' . $chatroom_id,
					'message' => array('chatroommessage' => array("id" => $db->last_insert_id(), "name" => 'Global', "message" => $ban_message, "userid" => $userid, "sent" => time(), "global" => '1'))
				));
				
				$arrowpush->publish(array(
					'channel' => 'u' . $chatroom_ban,
					'message' => array('chatroomban' => array("error2" => $ban_message2))
				));
			}
			
			$db->execute("
				DELETE FROM arrowchat_chatroom_banlist
				WHERE user_id = '" . $db->escape_string($chatroom_ban) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
			");
			
			// Get User IP address
			$result = $db->execute("
				SELECT ip_address
				FROM arrowchat_status 
				WHERE userid = '" . $db->escape_string($chatroom_ban) . "'
			");
		
			if ($row = $db->fetch_array($result))
				$user_ip = $row['ip_address'];
			else
				$user_ip = '';
		
			$db->execute("
				INSERT INTO arrowchat_chatroom_banlist (
					user_id, 
					chatroom_id, 
					ban_length, 
					ban_time,
					ip_address
				) 
				VALUES (
					'" . $db->escape_string($chatroom_ban) . "',
					'" . $db->escape_string($chatroom_id) . "',
					'" . $db->escape_string($chatroom_ban_length) . "',
					'" . time() . "',
					'" . $db->escape_string($user_ip) . "'
				)
			");
			
			$db->execute("
				UPDATE arrowchat_chatroom_users 
				SET session_time = '0'
				WHERE user_id = '" . $db->escape_string($chatroom_ban) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
			");
		}
		
		echo "1";
		close_session();
		exit(0);
	}
	
	// ####################### START POST SILENCE USER ##########################
	if (var_check('chatroom_silence')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0 AND is_numeric($chatroom_silence_length)) 
		{
			if (check_if_guest($chatroom_silence))
			{
				$silence_username = create_guest_username($chatroom_silence, '', true);
				
				if (empty($silence_username))
				{
					$silence_username = create_guest_username($chatroom_silence, '', false);
				}
			}
			else
			{
				$sql = get_user_details($chatroom_silence);
				$result = $db->execute($sql);
				
				if ($result AND $db->count_select() > 0) 
				{
					$row = $db->fetch_array($result);
					$silence_username = $row['username'];
				}
			}
			
			$silence_message = $silence_username . $language[163] . $db->escape_string(strip_tags(get_username($userid))) . ".";
			
			$db->execute("
				INSERT INTO arrowchat_chatroom_messages (
					chatroom_id,
					user_id,
					username,
					message,
					global_message,
					sent
				) 
				VALUES (
					'" . $db->escape_string($chatroom_id) . "', 
					'" . $db->escape_string($userid) . "', 
					'Global',
					'" . $silence_message . "',
					'1',
					'" . time() . "'
				)
			");
			
			if ($push_on == 1)
			{
				$arrowpush->publish(array(
					'channel' => 'chatroom' . $chatroom_id,
					'message' => array('chatroommessage' => array("id" => $db->last_insert_id(), "name" => 'Global', "message" => $silence_message, "userid" => $userid, "sent" => time(), "global" => '1'))
				));
			}
			
			// Max silence time is 300 seconds (5 minutes)
			if ($chatroom_silence_length > 300)
				$chatroom_silence_length = 300;
			
			$db->execute("
				UPDATE arrowchat_chatroom_users 
				SET silence_length = '" . $db->escape_string($chatroom_silence_length) . "',
					silence_time = '" . time() . "'
				WHERE user_id = '" . $db->escape_string($chatroom_silence) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
			");
		}
		
		echo "1";
		close_session();
		exit(0);
	}
	
	// ####################### START DELETE CHAT MSG ##########################
	if (var_check('delete_msg')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$db->execute("
				UPDATE arrowchat_chatroom_messages
				SET message = '" . $db->escape_string($language[159] . $delete_name) . "'
				WHERE id = '" . $db->escape_string($delete_msg) . "'
					AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
			");
			
			$db->execute("
				INSERT INTO arrowchat_chatroom_messages (
					chatroom_id,
					user_id,
					username,
					message,
					global_message,
					sent,
					action
				) 
				VALUES (
					'" . $db->escape_string($chatroom_id) . "', 
					'" . $db->escape_string($userid) . "', 
					'" . $db->escape_string($delete_name) . "',
					'" . $db->escape_string($delete_msg) . "',
					'1',
					'" . time() . "',
					'1'
				)
			");
		
			if ($push_on == 1)
			{
				$arrowpush->publish(array(
					'channel' => 'chatroom' . $chatroom_id,
					'message' => array('chatroommessage' => array("id" => $delete_msg, "name" => 'Delete', "message" => $language[159] . $delete_name, "userid" => $userid, "sent" => time(), "global" => '1', "mod" => 0, "admin" => 0, "chatroomid" => $chatroom_id))
				));
			}
		}
		
		echo "1";
		close_session();
		exit(0);
	}
	
	// ####################### START POST WELCOME MSG ##########################
	if (var_check('chatroom_welcome_msg')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$db->execute("
				UPDATE arrowchat_chatroom_rooms
				SET welcome_message = '" . $db->escape_string($chatroom_welcome_msg) . "'
				WHERE id = '" . $db->escape_string($chatroom_id) . "'
			");
		}
		
		echo "1";
		close_session();
		exit(0);
	}
	
	// ####################### START POST DESCRIPTION ##########################
	if (var_check('chatroom_description')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$db->execute("
				UPDATE arrowchat_chatroom_rooms
				SET description = '" . $db->escape_string($chatroom_description) . "'
				WHERE id = '" . $db->escape_string($chatroom_id) . "'
			");
		}
		
		echo "1";
		close_session();
		exit(0);
	}
	
	// ####################### START POST CHAT ROOM FLOOD ##########################
	if (var_check('flood_message')) 
	{
		$result = $db->execute("
			SELECT is_mod, is_admin 
			FROM arrowchat_chatroom_users 
			WHERE user_id = '" . $db->escape_string($userid) . "'
				AND chatroom_id = '" . $db->escape_string($chatroom_id) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0 AND is_numeric($flood_message) AND is_numeric($flood_seconds)) 
		{
			$db->execute("
				UPDATE arrowchat_chatroom_rooms
				SET limit_message_num = '" . $db->escape_string($flood_message) . "',
					limit_seconds_num = '" . $db->escape_string($flood_seconds) . "'
				WHERE id = '" . $db->escape_string($chatroom_id) . "'
			");
		}
		
		echo "1";
		close_session();
		exit(0);
	}

	// #################### START BOOKMARKS UPDATE ########################
	if (var_check('arrowchat_applications')) 
	{
		$update_string = "";
		
		if (empty($bookmarks_list)) 
		{
			$update_string = "-1";
		} 
		else 
		{
			foreach ($bookmarks_list as $val) 
			{
				$update_string = $update_string.$val.":";
			}
		}
		
		$db->execute("
			UPDATE arrowchat_status 
			SET apps_bookmarks = '" . $db->escape_string($update_string) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit (0);
	}

	// ################# START OTHER APPLICATIONS UPDATE ##################
	if (var_check('arrowchat_other_applications')) 
	{
		$update_string = "";
		
		if (empty($bookmarks_list)) 
		{
			$update_string = "-1";
		} 
		else 
		{
			foreach ($bookmarks_list as $val) 
			{
				$update_string = $update_string.$val.":";
			}
		}
		
		$db->execute("
			UPDATE arrowchat_status 
			SET apps_other = '" . $db->escape_string($update_string) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit (0);
	}
	
	// ################# START BLOCK CHAT ##################
	if (var_check('block_chat')) 
	{	
		if ($userid == $block_chat)
		{
			// Cannot block yourself
			echo "-1";
			close_session();
			exit (0);
		}
		
		$result = $db->execute("
			SELECT is_admin 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($block_chat) . "'
				AND (is_admin = '1'
					OR is_mod = '1')
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			// Cannot block admin/mod
			echo "-1";
			close_session();
			exit (0);
		}
		
		$result = $db->execute("
			SELECT block_chats 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			if (!empty($row['block_chats']))
			{
				$block_chat_array = unserialize($row['block_chats']);
			}
			else
			{
				$block_chat_array = array();
			}
		}
		else
		{
			$block_chat_array = array();
		}
		
		if (!in_array($block_chat, $block_chat_array))
		{
			$block_chat_array[] = $block_chat;
		}
		
		$block_chat_serialized = serialize($block_chat_array);
		
		$db->execute("
			UPDATE arrowchat_status 
			SET block_chats = '" . $db->escape_string($block_chat_serialized) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit (0);
	}
	
	// ################# START UNBLOCK CHAT ##################
	if (var_check('unblock_chat')) 
	{	
		$result = $db->execute("
			SELECT block_chats 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			
			if (!empty($row['block_chats']))
			{
				$block_chat_array = unserialize($row['block_chats']);
			}
			else
			{
				$block_chat_array = array();
			}
		}
		else
		{
			$block_chat_array = array();
		}
		
		foreach ($block_chat_array as $key => $value)
		{
			if ($unblock_chat == $value)
			{
				unset($block_chat_array[$key]);
			}
		}
		
		$block_chat_serialized = serialize($block_chat_array);
		
		$db->execute("
			UPDATE arrowchat_status 
			SET block_chats = '" . $db->escape_string($block_chat_serialized) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");

		echo "1";
		close_session();
		exit (0);
	}
	
	// ################# START UNBLOCK CHAT ##################
	if (var_check('app_keep')) 
	{	
		if ($app_keep == "-1")
		{
			$app_keep = "0";
		}
		
		if (!is_numeric($app_keep))
		{
			echo "-1";
			close_session();
			exit (0);
		}
		
		$db->execute("
			UPDATE arrowchat_status 
			SET apps_open = '" . $db->escape_string($app_keep) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
	
		echo "1";
		close_session();
		exit (0);
	}
	
	// ################# GUEST USERNAME ##################
	if (var_check('chat_name'))
	{
		if ($guest_name_change != 1)
		{
			echo "1";
			close_session();
			exit (0);
		}
		
		function in_array_like($referencia, $array)
		{ 
			foreach($array as $ref)
			{ 
				$ref = str_replace(" ", "", $ref);
				
				if (stristr($referencia, $ref))
				{          
					return $ref; 
				}
			}
			
			return false; 
		}
		
		if (empty($chat_name))
		{
			echo $language[120];
			close_session();
			exit (0);
		}
		
		if (!empty($guest_name))
		{
			echo $language[123];
			close_session();
			exit (0);
		}
		
		if (strlen($chat_name) > 25)
		{
			echo $language[125];
			close_session();
			exit (0);
		}
		
		if (preg_match('#\W#', $chat_name))
		{
			echo $language[121];
			close_session();
			exit (0);
		}
		
		$bad_words = explode(",", $guest_name_bad_words);
		
		if ($bad_name = in_array_like($chat_name, $bad_words))
		{
			echo $language[122] . $bad_name;
			close_session();
			exit (0);
		}
		
		if ($guest_name_duplicates != 1)
		{
			$result = $db->execute("
				SELECT userid
				FROM arrowchat_status 
				WHERE LOWER(guest_name) = '" . $db->escape_string(strtolower($chat_name)) . "'
			");
			if ($result AND $db->count_select() > 0) 
			{
				echo $language[124];
				close_session();
				exit (0);
			}
		}
		
		$db->execute("
			UPDATE arrowchat_status 
			SET guest_name = '" . $db->escape_string($chat_name) . "' 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		echo "1";
		close_session();
		exit (0);
	}
	
	// ######################## START POST CLOSE REPORT ########################
	if (var_check('report_id'))
	{	
		if ($is_mod == 1 || $is_admin == 1) 
		{		
			$result = $db->execute("
				SELECT report_about
				FROM arrowchat_reports
				WHERE id = '" . $db->escape_string($report_id) . "'
			");
			
			if ($row = $db->fetch_array($result))
			{
				$db->execute("
					UPDATE arrowchat_reports
					SET completed_by = '" . $db->escape_string($userid) . "',
						completed_time = '" . $db->escape_string(time()) . "'
					WHERE report_about = '" . $db->escape_string($row['report_about']) . "'
						AND completed_time = 0
				");
			}
		}

		echo "1";
		close_session();
		exit(0);
	}
	
	// ######################## START POST REPORT BAN ########################
	if (var_check('report_ban'))
	{	
		if ($is_mod == 1 || $is_admin == 1) 
		{		
			$result = $db->execute("
				SELECT report_about
				FROM arrowchat_reports
				WHERE id = '" . $db->escape_string($report_ban) . "'
			");
			
			if ($row = $db->fetch_array($result))
			{
				$db->execute("
					UPDATE arrowchat_reports
					SET completed_by = '" . $db->escape_string($userid) . "',
						completed_time = '" . $db->escape_string(time()) . "'
					WHERE report_about = '" . $db->escape_string($row['report_about']) . "'
						AND completed_time = 0
				");
				
				$result = $db->execute("
					SELECT ban_userid 
					FROM arrowchat_banlist 
					WHERE ban_userid = '" . $db->escape_string($row['report_about']) . "'
				");

				if ($result AND $db->count_select() > 0) 
				{
					// Ban already exists
				}
				else
				{
					$db->execute("
						INSERT INTO arrowchat_banlist (
							ban_userid,
							banned_by,
							banned_time
						) 
						VALUES (
							'" . $db->escape_string($row['report_about']) . "',
							'" . $db->escape_string($userid) . "',
							'" . $db->escape_string(time()) . "'
						)
					");
					
					require_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . AC_FOLDER_ADMIN . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'functions_update.php');
					update_config_file();
				}
			}
		}

		echo "1";
		close_session();
		exit(0);
	}
	
	// ######################## START POST WARN USER ########################
	if (var_check('report_warn'))
	{	
		if ($is_mod == 1 || $is_admin == 1) 
		{		
			$result = $db->execute("
				SELECT report_about
				FROM arrowchat_reports
				WHERE id = '" . $db->escape_string($report_warn) . "'
			");
			
			if ($row = $db->fetch_array($result))
			{
				$result = $db->execute("
					SELECT id
					FROM arrowchat_warnings
					WHERE user_id = '" . $db->escape_string($row['report_about']) . "'
						AND warning_time > (" . time() . " - 86400)
				");

				if ($result AND $db->count_select() > 0 AND $report_warn_confirm != 1) 
				{
					// User has been warned in the past 24 hours, let the reporter handler know that before processing
					echo "2";
					close_session();
					exit(0);
				}
				else
				{
					$db->execute("
						UPDATE arrowchat_reports
						SET completed_by = '" . $db->escape_string($userid) . "',
							completed_time = '" . $db->escape_string(time()) . "'
						WHERE report_about = '" . $db->escape_string($row['report_about']) . "'
							AND completed_time = 0
					");
					
					$db->execute("
						INSERT INTO arrowchat_warnings (
							user_id,
							warn_reason,
							warned_by,
							warning_time,
							user_read
						) 
						VALUES (
							'" . $db->escape_string($row['report_about']) . "',
							'" . $db->escape_string($report_warn_reason) . "',
							'" . $db->escape_string($userid) . "',
							'" . $db->escape_string(time()) . "',
							'0'
						)
					");
					
					if ($push_on == 1)
					{
						$arrowpush->publish(array(
							'channel' => 'u' . $row['report_about'],
							'message' => array('warning' => array("data" => $report_warn_reason, "read" => "0"))
						));
					}
				}
			}
		}

		echo "1";
		close_session();
		exit(0);
	}
	
	// ################### START POST WARNING READ ######################
	if (!empty($_POST['warning_read'])) 
	{
		$db->execute("
			UPDATE arrowchat_warnings 
			SET user_read = '1' 
			WHERE user_id = '" . $db->escape_string($userid) . "'
		");
		
		echo "1";
		close_session();
		exit(0);
	}
	
?>