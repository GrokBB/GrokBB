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
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes/admin_init.php");
	
	// Get the page to process
	if (empty($do))
	{
		$do = "manageusers";
	}

	// ####################### START SUBMIT/POST DATA ########################
	
	// Ban IP Submit Processor
	if (var_check('ban_ip_submit')) 
	{
		$ip_addys = explode("\\r\\n", get_var('ban_ip'));
		
		foreach ($ip_addys as $addy) 
		{
			$result = $db->execute("
				SELECT ban_ip 
				FROM arrowchat_banlist 
				WHERE ban_ip = '" . $db->escape_string($addy) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$error = "This IP is already banned.";
			}
			
			if (empty($_POST['ban_ip_submit']))
			{
				$error = "You must enter an IP to ban.";
			}
			
			if (empty($error)) 
			{
				$result = $db->execute("
					INSERT INTO arrowchat_banlist (ban_ip, banned_by, banned_time) 
					VALUES ('" . $db->escape_string($addy) . "', '0', '" . time() . "')
				");

				if (!$result) 
				{
					$error = "There was a database error.  Please try again.";
				}
			}
		}
		
		if (empty($error)) 
		{
			$msg = "IPs successfully banned.";
			update_config_file();
		}
	}
	
	// Unban IP Submit Processor
	if (var_check('unban_ip_submit')) 
	{
		$ips = get_var('unban_ip');
		
		if ($ips) 
		{
			foreach ($ips as $unbans) 
			{
				$result = $db->execute("
					DELETE FROM arrowchat_banlist 
					WHERE ban_id = '" . $unbans . "'
				");

				if (!$result) 
				{
					$error = "There was a database error";
				}
			}
			
			if (empty($error)) 
			{
				$msg = "The IPs were successfully unbaned.";
				update_config_file();
			}
		} 
		else 
		{
			$error = "You did not select any IPs";
		}
	}
	
	// Ban Username Submit Processor
	if (var_check('ban_username_submit')) 
	{
		$usernames = explode("\\r\\n", get_var('ban_username'));
		
		foreach ($usernames as $username) 
		{
			$result = $db->execute("
				SELECT " . DB_USERTABLE_USERID . " 
				FROM " . TABLE_PREFIX . DB_USERTABLE . " 
				WHERE LOWER(" . DB_USERTABLE_NAME . ") = '" . strtolower($db->escape_string($username)) . "'
			");

			$row = $db->fetch_array($result);
			
			if (!$result OR $db->count_select() < 1) 
			{
				$error = "We could not find a user with that username.";
			}
			
			$result = $db->execute("
				SELECT ban_userid 
				FROM arrowchat_banlist 
				WHERE ban_userid = '" . $row[DB_USERTABLE_USERID] . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$error = "This username is already banned.";
			}
			
			if (empty($error)) 
			{
				$result = $db->execute("
					INSERT INTO arrowchat_banlist (ban_userid, banned_by, banned_time) 
					VALUES ('" . $row[DB_USERTABLE_USERID] . "', '0', '" . time() . "')
				");

				if ($result) 
				{
					$msg = "Usernames successfully banned.";
					update_config_file();
				}
				else
				{
					$error = "There was a database error.  Please try again.";
				}
			}
		}
	}
	
	// Unban Username Submit Processor
	if (var_check('unban_username_submit')) 
	{
		$usernames = get_var('unban_username');
		
		if ($usernames) 
		{
			foreach ($usernames as $unbans) 
			{
				$result = $db->execute("
					DELETE FROM arrowchat_banlist 
					WHERE ban_id = '" . $db->escape_string($unbans) . "'
				");

				if ($result) 
				{
					$msg = "The Usernames were successfully unbaned.";
					update_config_file();
				}
				else
				{
					$error = "There was a database error";
				}
			}
		} 
		else 
		{
			$error = "You did not select any IPs";
		}
	}
	
	// User Edit Submit Processor
	if (var_check('user_submit')) 
	{
		$result = $db->execute("
			SELECT session_time
			FROM arrowchat_status
			WHERE userid = '" . get_var('user_id') . "'
		");
		
		if ($result AND $db->count_select() > 0)
		{
		}
		else
		{
			$hash_id = random_string();
			
			$db->execute("
				INSERT INTO arrowchat_status (userid, chatroom_window, chatroom_stay, hash_id, session_time, ip_address) 
				VALUES ('" . $db->escape_string(get_var('user_id')) . "', '-1', '0', '" . $hash_id . "', '" . time() . "', '0')
			");
		}
		
		$result = $db->execute("
			UPDATE arrowchat_status
			SET hide_bar = '" . get_var('hide_bar') . "',
				play_sound = '" . get_var('play_sound') . "', 
				window_open = '" . get_var('window_open') . "', 
				only_names = '" . get_var('only_names') . "', 
				announcement = '" . get_var('announcement') . "', 
				is_admin = '" . get_var('is_admin') . "',
				is_mod = '" . get_var('is_mod') . "'
			WHERE userid = '" . get_var('user_id') . "'
		");

		if ($result) 
		{
			$hide_bar = get_var('hide_bar');
			$play_sound = get_var('play_sound');
			$window_open = get_var('window_open');
			$only_names = get_var('only_names');
			$announcement = get_var('announcement');
			$is_admin = get_var('is_admin');
			$is_mod = get_var('is_mod');
			
			if (empty($is_mod))
			{
				$db->execute("
					UPDATE arrowchat_chatroom_users
					SET is_mod = '0'
					WHERE user_id = '" . get_var('user_id') . "'
				");
			}
			
			$msg = "User settings successfully saved.";
		} 
		else 
		{
			$error = "There was an error saving the user's settings.";
		}
	}
	
	// Delete Guest Name
	if (var_check('guest_name')) 
	{
		$db->execute("
			UPDATE arrowchat_status
			SET guest_name = NULL
			WHERE userid = '" . get_var('id') . "'
		");
		
		if ($result) 
		{
			$msg = "The user's name was deleted.";
		}
		else 
		{
			$error = "There was an error deleting the user's name.";
		}
	}
	
	// Group Permissions Submit Processor
	if (var_check('group_submit')) 
	{
		$group_id = get_var('group_id');
		
		$group_disable_arrowchat_ex = explode(",", $group_disable_arrowchat);
		$group_disable_video_ex = explode(",", $group_disable_video);
		$group_disable_apps_ex = explode(",", $group_disable_apps);
		$group_disable_rooms_ex = explode(",", $group_disable_rooms);
		$group_disable_uploads_ex = explode(",", $group_disable_uploads);
		$group_disable_sending_private_ex = explode(",", $group_disable_sending_private);
		$group_disable_sending_rooms_ex = explode(",", $group_disable_sending_rooms);
		
		if (get_var('group_disable_arrowchat') == 1)
		{
			if (!in_array($group_id, $group_disable_arrowchat_ex))
			{
				$group_disable_arrowchat_ex[] = $group_id;
			}
		}
		else
		{
			if (in_array($group_id, $group_disable_arrowchat_ex))
			{
				$group_disable_arrowchat_ex = array_delete($group_disable_arrowchat_ex, $group_id);
			}
		}
		
		if (get_var('group_disable_video') == 1)
		{
			if (!in_array($group_id, $group_disable_video_ex))
			{
				$group_disable_video_ex[] = $group_id;
			}
		}
		else
		{
			if (in_array($group_id, $group_disable_video_ex))
			{
				$group_disable_video_ex = array_delete($group_disable_video_ex, $group_id);
			}
		}
		
		if (get_var('group_disable_apps') == 1)
		{
			if (!in_array($group_id, $group_disable_apps_ex))
			{
				$group_disable_apps_ex[] = $group_id;
			}
		}
		else
		{
			if (in_array($group_id, $group_disable_apps_ex))
			{
				$group_disable_apps_ex = array_delete($group_disable_apps_ex, $group_id);
			}
		}
		
		if (get_var('group_disable_rooms') == 1)
		{
			if (!in_array($group_id, $group_disable_rooms_ex))
			{
				$group_disable_rooms_ex[] = $group_id;
			}
		}
		else
		{
			if (in_array($group_id, $group_disable_rooms_ex))
			{
				$group_disable_rooms_ex = array_delete($group_disable_rooms_ex, $group_id);
			}
		}
		
		if (get_var('group_disable_uploads') == 1)
		{
			if (!in_array($group_id, $group_disable_uploads_ex))
			{
				$group_disable_uploads_ex[] = $group_id;
			}
		}
		else
		{
			if (in_array($group_id, $group_disable_uploads_ex))
			{
				$group_disable_uploads_ex = array_delete($group_disable_uploads_ex, $group_id);
			}
		}
		
		if (get_var('group_disable_sending_private') == 1)
		{
			if (!in_array($group_id, $group_disable_sending_private_ex))
			{
				$group_disable_sending_private_ex[] = $group_id;
			}
		}
		else
		{
			if (in_array($group_id, $group_disable_sending_private_ex))
			{
				$group_disable_sending_private_ex = array_delete($group_disable_sending_private_ex, $group_id);
			}
		}
		
		if (get_var('group_disable_sending_rooms') == 1)
		{
			if (!in_array($group_id, $group_disable_sending_rooms_ex))
			{
				$group_disable_sending_rooms_ex[] = $group_id;
			}
		}
		else
		{
			if (in_array($group_id, $group_disable_sending_rooms_ex))
			{
				$group_disable_sending_rooms_ex = array_delete($group_disable_sending_rooms_ex, $group_id);
			}
		}
		
		$group_disable_arrowchat_new = ltrim(implode(",", $group_disable_arrowchat_ex), ',');
		$group_disable_video_new = ltrim(implode(",", $group_disable_video_ex), ',');
		$group_disable_apps_new = ltrim(implode(",", $group_disable_apps_ex), ',');
		$group_disable_rooms_new = ltrim(implode(",", $group_disable_rooms_ex), ',');
		$group_disable_uploads_new = ltrim(implode(",", $group_disable_uploads_ex), ',');
		$group_disable_sending_private_new = ltrim(implode(",", $group_disable_sending_private_ex), ',');
		$group_disable_sending_rooms_new = ltrim(implode(",", $group_disable_sending_rooms_ex), ',');
		
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'group_disable_arrowchat' THEN '" . $db->escape_string($group_disable_arrowchat_new) . "'
				WHEN config_name = 'group_disable_video' THEN '" . $db->escape_string($group_disable_video_new) . "'
				WHEN config_name = 'group_disable_apps' THEN '" . $db->escape_string($group_disable_apps_new) . "'
				WHEN config_name = 'group_disable_rooms' THEN '" . $db->escape_string($group_disable_rooms_new) . "'
				WHEN config_name = 'group_disable_uploads' THEN '" . $db->escape_string($group_disable_uploads_new) . "'
				WHEN config_name = 'group_disable_sending_private' THEN '" . $db->escape_string($group_disable_sending_private_new) . "'
				WHEN config_name = 'group_disable_sending_rooms' THEN '" . $db->escape_string($group_disable_sending_rooms_new) . "'
			END WHERE config_name IN ('group_disable_arrowchat', 'group_disable_video', 'group_disable_apps', 'group_disable_rooms', 'group_disable_uploads', 'group_disable_sending_private', 'group_disable_sending_rooms')
		");
		
		if ($result) 
		{
			$group_disable_arrowchat = $group_disable_arrowchat_new;
			$group_disable_video = $group_disable_video_new;
			$group_disable_apps = $group_disable_apps_new;
			$group_disable_rooms = $group_disable_rooms_new;
			$group_disable_uploads = $group_disable_uploads_new;
			$group_disable_sending_private = $group_disable_sending_private_new;
			$group_disable_sending_rooms = $group_disable_sending_rooms_new;
		
			update_config_file();
			$msg = "Your settings were successfully saved.";
		} 
		else
		{
			$error = "There was a database error.  Please try again.";
		}
	}
	
	// Group Permissions Settings Submit Processor
	if (var_check('group_settings_submit')) 
	{
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'group_enable_mode' THEN '" . $db->escape_string(get_var('group_enable_mode')) . "'
			END WHERE config_name IN ('group_enable_mode')
		");
		
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'group_disable_arrowchat' THEN ''
				WHEN config_name = 'group_disable_video' THEN ''
				WHEN config_name = 'group_disable_apps' THEN ''
				WHEN config_name = 'group_disable_rooms' THEN ''
				WHEN config_name = 'group_disable_uploads' THEN ''
				WHEN config_name = 'group_disable_sending_private' THEN ''
				WHEN config_name = 'group_disable_sending_rooms' THEN ''
			END WHERE config_name IN ('group_disable_arrowchat', 'group_disable_video', 'group_disable_apps', 'group_disable_rooms', 'group_disable_uploads', 'group_disable_sending_private', 'group_disable_sending_rooms')
		");
					
		if ($result) 
		{
			$group_enable_mode = get_var('group_enable_mode');
			
			update_config_file();
			$msg = "Your settings were successfully saved.";
		} 
		else
		{
			$error = "There was a database error.  Please try again.";
		}
	}	

	$smarty->assign('msg', $msg);
	$smarty->assign('error', $error);

	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_header.tpl");
	require(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_users.php");
	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_footer.tpl");
	
?>