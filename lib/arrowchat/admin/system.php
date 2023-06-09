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
		$do = "update";
	}
	
	// ####################### START SUBMIT/POST DATA ########################
	
	// Config Settings Submit Processor
	if (var_check('config_submit')) 
	{
		$new_base_url = get_var('config_base_url');
		$heart_beat_test = true;
		
		if (get_var('config_buddy_list_heart_beat') >= get_var('config_online_timeout')) 
		{
			$heart_beat_test = false;
		}
		
		// Add a slash to the end of the base URL is one doesn't exist
		if (substr($new_base_url, -1) != "/")
		{
			$new_base_url = $new_base_url . "/";
		}
		
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'base_url' THEN '" . $new_base_url . "'
				WHEN config_name = 'login_url' THEN '" . get_var('config_login_url') . "'
				WHEN config_name = 'online_timeout' THEN '" . get_var('config_online_timeout') . "'
				WHEN config_name = 'heart_beat' THEN '" . get_var('config_heart_beat') . "'
				WHEN config_name = 'buddy_list_heart_beat' THEN '" . get_var('config_buddy_list_heart_beat') . "'
				WHEN config_name = 'idle_time' THEN '" . get_var('config_idle_time') . "'
				WHEN config_name = 'push_on' THEN '" . get_var('push_on') . "'
				WHEN config_name = 'push_ssl' THEN '" . get_var('push_ssl') . "'
				WHEN config_name = 'push_publish' THEN '" . trim(get_var('push_publish')) . "'
				WHEN config_name = 'push_subscribe' THEN '" . trim(get_var('push_subscribe')) . "'
			END WHERE config_name IN ('channel_name', 'base_url', 'login_url', 'online_timeout', 'disable_smilies', 'heart_beat', 'buddy_list_heart_beat', 'idle_time', 'push_on', 'push_ssl', 'push_publish', 'push_subscribe')
		");
					
		if ($result && $heart_beat_test) 
		{
			$base_url = $new_base_url;
			$login_url = get_var('config_login_url');
			$online_timeout = get_var('config_online_timeout');
			$heart_beat = get_var('config_heart_beat');
			$buddy_list_heart_beat = get_var('config_buddy_list_heart_beat');
			$idle_time = get_var('config_idle_time');
			$push_on = get_var('push_on');
			$push_ssl = get_var('push_ssl');
			$push_publish = trim(get_var('push_publish'));
			$push_subscribe = trim(get_var('push_subscribe'));
			
			update_config_file();
			$msg = "Your settings were successfully saved.";
		} 
		else if (!$heart_beat_test)
		{
			$error = "The buddy list heart beat cannot be equal to or lower than the online timeout.  Please adjust the settings.";
		}
		else
		{
			$error = "There was a database error.  Please try again.";
		}
	}
	
	// Auto Upgrade Step 1 Processor
	if (var_check('auto-step-1')) 
	{
		$do = "step1";
	}
	
	// Auto Upgrade Step 2 Processor
	if (var_check('auto-step-2')) 
	{
		$do = "step2";
	}
	
	// Auto Upgrade Step 3 Processor
	if (var_check('auto-step-3')) 
	{
		$do = "step3";
	}
	
	// Auto Upgrade Step 4 Processor
	if (var_check('auto-step-4')) 
	{
		$do = "step4";
	}
	
	// Auto Upgrade Step 5 Processor
	if (var_check('auto-step-5')) 
	{
		$do = "step5";
	}
	
	// Maintenance Submit Processor
	if (var_check('maintenance_submit')) 
	{
		$_SESSION['clean_private_messages'] = get_var('clean_private_messages');
		$_SESSION['clean_inactive_guests'] = get_var('clean_inactive_guests');
		$_SESSION['clean_inactive_users'] = get_var('clean_inactive_users');
		$_SESSION['clean_cr_messages'] = get_var('clean_cr_messages');
		$_SESSION['clean_cr_rooms'] = get_var('clean_cr_rooms');
		$_SESSION['clean_cr_users'] = get_var('clean_cr_users');
		$_SESSION['clean_notifications'] = get_var('clean_notifications');
		$_SESSION['counter'] = 0;
		
		header("Location: system.php?do=maintenance2");
	}
	
	// Maintenance Processing
	if ($do == 'maintenance2')
	{
		if (empty($_SESSION['clean_private_messages']) AND empty($_SESSION['clean_inactive_guests']) AND empty($_SESSION['clean_inactive_users']) AND empty($_SESSION['clean_cr_messages']) AND empty($_SESSION['clean_cr_rooms']) AND empty($_SESSION['clean_cr_users']) AND empty($_SESSION['clean_notifications']))
		{
			header("Location: system.php?do=maintenance");
		}
		
		if (!empty($_SESSION['clean_private_messages']))
		{
			$cleaning_message = "Cleaning Private Messages";
			
			$result = $db->execute("
				SELECT COUNT(*)
				FROM arrowchat
				WHERE (sent + (3600 * 24 * 28)  < " . time() . ")
					OR (sent + (3600 * 24 * 14)  < " . time() . " AND user_read = 1)
			");
			
			if ($row = $db->fetch_array($result))
			{
				$no_rows = $row['COUNT(*)'];
				
				if (empty($_SESSION['counter']))
					$_SESSION['original_count'] = $no_rows;
				
				if ($no_rows < 5000) 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat
							WHERE (sent + (3600 * 24 * 28)  < " . time() . ")
								OR (sent + (3600 * 24 * 14)  < " . time() . " AND user_read = 1)
						");
						$_SESSION['counter'] = -1;
						$_SESSION['clean_private_messages'] = 0;
					}
					
					$cleaning_percent = 100;
					$_SESSION['counter']++;
				}
				else 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat
							WHERE (sent + (3600 * 24 * 28)  < " . time() . ")
								OR (sent + (3600 * 24 * 14)  < " . time() . " AND user_read = 1)
							LIMIT 5000
						");
						$cleaning_percent = round((5000 * $_SESSION['counter']) / $_SESSION['original_count'] * 100);
					}
					
					$_SESSION['counter']++;
				}
			}
		}
		
		if (!empty($_SESSION['clean_inactive_guests']) AND empty($_SESSION['clean_private_messages']))
		{
			$cleaning_message = "Cleaning Inactive Guests";
			
			$result = $db->execute("
				SELECT COUNT(*)
				FROM arrowchat_status
				WHERE session_time + (3600 * 24 * 14)  < " . time() . "
					AND userid LIKE 'g%'
			");
			
			if ($row = $db->fetch_array($result))
			{
				$no_rows = $row['COUNT(*)'];
				
				if (empty($_SESSION['counter']))
					$_SESSION['original_count'] = $no_rows;
				
				if ($no_rows < 5000) 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_status
							WHERE session_time + (3600 * 24 * 14)  < " . time() . "
								AND userid LIKE 'g%'
						");
						$_SESSION['counter'] = -1;
						$_SESSION['clean_inactive_guests'] = 0;
					}
					
					$cleaning_percent = 100;
					$_SESSION['counter']++;
				}
				else 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_status
							WHERE session_time + (3600 * 24 * 14)  < " . time() . "
									AND userid LIKE 'g%'
							LIMIT 5000
						");
						$cleaning_percent = round((5000 * $_SESSION['counter']) / $_SESSION['original_count'] * 100);
					}
					
					$_SESSION['counter']++;
				}
			}
		}
		
		if (!empty($_SESSION['clean_inactive_users']) AND empty($_SESSION['clean_inactive_guests']) AND empty($_SESSION['clean_private_messages']))
		{
			$cleaning_message = "Cleaning Inactive Users";
			
			$result = $db->execute("
				SELECT COUNT(*)
				FROM arrowchat_status
				WHERE session_time + (3600 * 24 * 28)  < " . time() . "
			");
			
			if ($row = $db->fetch_array($result))
			{
				$no_rows = $row['COUNT(*)'];
				
				if (empty($_SESSION['counter']))
					$_SESSION['original_count'] = $no_rows;
				
				if ($no_rows < 5000) 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_status
							WHERE session_time + (3600 * 24 * 28)  < " . time() . "
						");
						$_SESSION['counter'] = -1;
						$_SESSION['clean_inactive_users'] = 0;
					}
					
					$cleaning_percent = 100;
					$_SESSION['counter']++;
				}
				else 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_status
							WHERE session_time + (3600 * 24 * 28)  < " . time() . "
							LIMIT 5000
						");
						$cleaning_percent = round((5000 * $_SESSION['counter']) / $_SESSION['original_count'] * 100);
					}
					
					$_SESSION['counter']++;
				}
			}
		}
		
		if (!empty($_SESSION['clean_cr_messages']) AND empty($_SESSION['clean_inactive_users']) AND empty($_SESSION['clean_inactive_guests']) AND empty($_SESSION['clean_private_messages']))
		{
			$cleaning_message = "Cleaning Chat Room Messages";
			
			$result = $db->execute("
				SELECT COUNT(*)
				FROM arrowchat_chatroom_messages
				WHERE sent + (3600 * 24 * 7)  < " . time() . "
			");
			
			if ($row = $db->fetch_array($result))
			{
				$no_rows = $row['COUNT(*)'];
				
				if (empty($_SESSION['counter']))
					$_SESSION['original_count'] = $no_rows;
				
				if ($no_rows < 5000) 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_chatroom_messages
							WHERE sent + (3600 * 24 * 7)  < " . time() . "
						");
						$_SESSION['counter'] = -1;
						$_SESSION['clean_cr_messages'] = 0;
					}
					
					$cleaning_percent = 100;
					$_SESSION['counter']++;
				}
				else 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_chatroom_messages
							WHERE sent + (3600 * 24 * 7)  < " . time() . "
							LIMIT 5000
						");
						$cleaning_percent = round((5000 * $_SESSION['counter']) / $_SESSION['original_count'] * 100);
					}
					
					$_SESSION['counter']++;
				}
			}
		}
		
		if (!empty($_SESSION['clean_cr_rooms']) AND empty($_SESSION['clean_cr_messages']) AND empty($_SESSION['clean_inactive_users']) AND empty($_SESSION['clean_inactive_guests']) AND empty($_SESSION['clean_private_messages']))
		{
			$cleaning_message = "Cleaning User Created Chat Rooms";
			
			$result = $db->execute("
				SELECT COUNT(*)
				FROM arrowchat_chatroom_rooms
				WHERE session_time + (3600 * 24 * 7)  < " . time() . "
					AND length != '0'
			");
			
			if ($row = $db->fetch_array($result))
			{
				$no_rows = $row['COUNT(*)'];
				
				if (empty($_SESSION['counter']))
					$_SESSION['original_count'] = $no_rows;
				
				if ($no_rows < 5000) 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_chatroom_rooms
							WHERE session_time + (3600 * 24 * 7)  < " . time() . "
								AND length != '0'
						");
						$_SESSION['counter'] = -1;
						$_SESSION['clean_cr_rooms'] = 0;
					}
					
					$cleaning_percent = 100;
					$_SESSION['counter']++;
				}
				else 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_chatroom_rooms
							WHERE session_time + (3600 * 24 * 7)  < " . time() . "
								AND length != '0'
							LIMIT 5000
						");
						$cleaning_percent = round((5000 * $_SESSION['counter']) / $_SESSION['original_count'] * 100);
					}
					
					$_SESSION['counter']++;
				}
			}
		}
		
		if (!empty($_SESSION['clean_cr_users']) AND empty($_SESSION['clean_cr_rooms']) AND empty($_SESSION['clean_cr_messages']) AND empty($_SESSION['clean_inactive_users']) AND empty($_SESSION['clean_inactive_guests']) AND empty($_SESSION['clean_private_messages']))
		{
			$cleaning_message = "Cleaning Chat Room Users";
			
			$result = $db->execute("
				SELECT COUNT(*)
				FROM arrowchat_chatroom_users
				WHERE session_time + (3600 * 24 * 14)  < " . time() . "
			");
			
			if ($row = $db->fetch_array($result))
			{
				$no_rows = $row['COUNT(*)'];
				
				if (empty($_SESSION['counter']))
					$_SESSION['original_count'] = $no_rows;
				
				if ($no_rows < 5000) 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_chatroom_users
							WHERE session_time + (3600 * 24 * 14)  < " . time() . "
						");
						$_SESSION['counter'] = -1;
						$_SESSION['clean_cr_users'] = 0;
					}
					
					$cleaning_percent = 100;
					$_SESSION['counter']++;
				}
				else 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_chatroom_users
							WHERE session_time + (3600 * 24 * 14)  < " . time() . "
							LIMIT 5000
						");
						$cleaning_percent = round((5000 * $_SESSION['counter']) / $_SESSION['original_count'] * 100);
					}
					
					$_SESSION['counter']++;
				}
			}
		}
		
		if (!empty($_SESSION['clean_notifications']) AND empty($_SESSION['clean_cr_users']) AND empty($_SESSION['clean_cr_rooms']) AND empty($_SESSION['clean_cr_messages']) AND empty($_SESSION['clean_inactive_users']) AND empty($_SESSION['clean_inactive_guests']) AND empty($_SESSION['clean_private_messages']))
		{
			$cleaning_message = "Cleaning Notifications";
			
			$result = $db->execute("
				SELECT COUNT(*)
				FROM arrowchat_notifications
				WHERE alert_time + (3600 * 24 * 28)  < " . time() . "
			");
			
			if ($row = $db->fetch_array($result))
			{
				$no_rows = $row['COUNT(*)'];
				
				if (empty($_SESSION['counter']))
					$_SESSION['original_count'] = $no_rows;
				
				if ($no_rows < 5000) 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_notifications
							WHERE alert_time + (3600 * 24 * 28)  < " . time() . "
						");
						$_SESSION['counter'] = -1;
						$_SESSION['clean_notifications'] = 0;
					}
					
					$cleaning_percent = 100;
					$_SESSION['counter']++;
				}
				else 
				{
					if ($_SESSION['counter'] != 0)
					{
						$db->execute("
							DELETE FROM arrowchat_notifications
							WHERE alert_time + (3600 * 24 * 28)  < " . time() . "
							LIMIT 5000
						");
						$cleaning_percent = round((5000 * $_SESSION['counter']) / $_SESSION['original_count'] * 100);
					}
					
					$_SESSION['counter']++;
				}
			}
		}

		if ($cleaning_percent > 100)
			$cleaning_percent = 100;
	}
	
	// Repair Processor
	if (var_check('repair_submit')) 
	{
		$result = $db->execute("
			INSERT IGNORE INTO arrowchat_config (
				config_name, 
				config_value, 
				is_dynamic
			) 
			VALUES  ('theme', 'new_facebook_full', 0), 
					('base_url', '" . $db->escape_string($base_url) . "', 0), 
					('online_timeout', '120', 0), 
					('disable_smilies', '0', 0), 
					('auto_popup_chatbox', '1', 0), 
					('heart_beat', '3', 0), 
					('language', 'en', 0), 
					('idle_time', '3', 0), 
					('install_time', '" . $db->escape_string($install_time) . "', 0), 
					('chatrooms_on', '0', 0), 
					('notifications_on', '0', 0), 
					('hide_bar_on', '1', 0), 
					('applications_on', '0', 0), 
					('popout_chat_on', '1', 0), 
					('theme_change_on', '0', 0), 
					('disable_avatars', '0', 0), 
					('disable_buddy_list', '1', 0), 
					('search_number', '5', 0), 
					('chat_maintenance', '0', 0), 
					('announcement', '', 0), 
					('admin_chat_all', '1', 0), 
					('admin_view_maintenance', '1', 0), 
					('user_chatrooms', '0', 0), 
					('user_chatrooms_flood', '10', 0), 
					('user_chatrooms_length', '30', 0), 
					('guests_can_view', '0', 0), 
					('video_chat', '0', 0), 
					('us_time', '1', 0), 
					('file_transfer_on', '0', 0), 
					('width_applications', '16', 0), 
					('width_buddy_list', '189', 0), 
					('width_chatrooms', '16', 0), 
					('buddy_list_heart_beat', '60', 0),
					('bar_fixed', '0', 0),
					('bar_fixed_alignment', 'center', 0),
					('bar_fixed_width', '900', 0),
					('bar_padding', '15', 0),
					('chatroom_auto_join', '0', 0),
					('chat_display_type', '1', 0),
					('chatroom_history_length', '60', 0),
					('disable_arrowchat', '0', 0),
					('enable_mobile', '1', 0),
					('guests_can_chat', '0', 0),
					('guests_chat_with', '1', 0),
					('push_on', '0', 0),
					('push_publish', '', 0),
					('push_subscribe', '', 0),
					('push_secret', '', 0),
					('show_full_username', '0', 0),
					('users_chat_with', '3', 0),
					('hide_admins_buddylist', '0', 0),
					('show_bar_links_right', '0', 0),
					('enable_chat_animations', '1', 0),
					('hide_applications_menu', '0', 0),
					('guest_name_change', '1', '0'),
					('guest_name_duplicates', '0', '0'),
					('guest_name_bad_words', 'fuck,cunt,nigger,shit,admin,administrator,mod,moderator,support', '0'),
					('admin_background_color', '', '0'),
					('admin_text_color', '', '0'),
					('facebook_app_id', '', '0'),
					('blocked_words', 'fuck,[shit],nigger,[cunt],[ass],asshole', '0'),
					('desktop_notifications', '0', '0'),
					('login_url', '', '0'),
					('max_upload_size', '5', '0'),
					('enable_moderation', '0', '0'), 
					('chatroom_transfer_on', '0', '0'), 
					('chatroom_message_length', '150', '0'), 
					('push_ssl', '0', '0'),
					('window_top_padding', '70', '0'),
					('video_chat_selection', '1', '0'), 
					('video_chat_width', '900', '0'), 
					('video_chat_height', '600', '0'), 
					('tokbox_api', '', '0'), 
					('tokbox_secret', '', '0'),
					('chatroom_default_names', '0', '0'), 
					('group_disable_arrowchat', '', '0'), 
					('group_disable_video', '', '0'), 
					('group_disable_apps', '', '0'), 
					('group_disable_rooms', '', '0'), 
					('group_disable_uploads', '', '0'), 
					('group_disable_sending_private', '', '0'), 
					('group_disable_sending_rooms', '', '0'),
					('group_enable_mode', '0', '0'),
					('online_list_on', '1', '0')
		");
		
		$result2 = $db->execute("
			DROP TABLE IF EXISTS `arrowchat_status`
		");
		
		$result3 = $db->execute('
			CREATE TABLE IF NOT EXISTS `arrowchat_status` (
			  `userid` varchar(50) NOT NULL,
			  `guest_name` varchar(50) default NULL,
			  `message` text,
			  `status` varchar(10) default NULL,
			  `theme` int(3) unsigned default NULL,
			  `popout` int(11) unsigned default NULL,
			  `typing` text,
			  `hide_bar` tinyint(1) unsigned default NULL,
			  `play_sound` tinyint(1) unsigned default \'1\',
			  `window_open` tinyint(1) unsigned default NULL,
			  `only_names` tinyint(1) unsigned default NULL,
			  `chatroom_window` varchar(6) NOT NULL default \'-1\',
			  `chatroom_stay` varchar(6) NOT NULL default \'0\',
			  `chatroom_show_names` tinyint(1) unsigned default NULL,
			  `chatroom_block_chats` tinyint(1) unsigned default NULL,
			  `chatroom_sound` tinyint(1) unsigned default NULL,
			  `announcement` tinyint(1) unsigned NOT NULL default \'1\',
			  `unfocus_chat` text,
			  `focus_chat` varchar(50) default NULL,
			  `last_message` text,
			  `clear_chats` text,
			  `apps_bookmarks` text,
			  `apps_other` text,
			  `apps_open` int(10) unsigned default NULL,
			  `apps_load` text,
			  `block_chats` text,
			  `session_time` int(20) unsigned NOT NULL,
			  `is_admin` tinyint(1) unsigned NOT NULL default \'0\',
			  `is_mod` tinyint(1) unsigned NOT NULL default \'0\',
			  `hash_id` varchar(20) NOT NULL,
			  `ip_address` varchar(40) default \'\',
			  PRIMARY KEY  (`userid`),
			  KEY `hash_id` (`hash_id`),
			  KEY `session_time` (`session_time`)
			)
		');
		
		if ($result && $result2 && $result3) 
		{
			update_config_file();
			$msg = "The repair has been successfully completed";
		}
	}
	
	$row = $db->fetch_row("
		SELECT email 
		FROM arrowchat_admin
	");
	$admin_email = $row->email;
	
	// Admin Settings Submit Processor
	if (var_check('admin_submit')) 
	{
		$admin_new_password = get_var('admin_new_password');
		$admin_confirm_password = get_var('admin_confirm_password');
		$admin_email = get_var('admin_email');
		$admin_old_password = get_var('admin_old_password');
		
		if (!empty($admin_new_password) OR !empty($admin_confirm_password)) 
		{
			if ($admin_new_password != $admin_confirm_password) 
			{
				$error = "Your new password and confirmation passwords do not match.";
			}
			
			if (!empty($admin_new_password) AND empty($admin_confirm_password)) 
			{
				$error = "You must supply a confirmation password.";
			}
			
			if (empty($admin_new_password) AND !empty($admin_confirm_password)) 
			{
				$error = "You must supply a new password.";
			}
		}
		
		if (empty($admin_email)) 
		{
			$error = "The admin email cannot be blank.";
		}
		
		if (empty($admin_old_password)) 
		{
			$error = "You must input your old password.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_admin
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				
				$old_password = $row['password'];
				$old_email = $row['email'];
				$admin_old_password = md5($admin_old_password);
				$admin_new_password = md5($admin_new_password);
				
				if ($admin_old_password != $old_password) 
				{
					$error = "Your old password is not correct.";
				} 
				
				if (empty($error)) 
				{
					if (empty($_POST['admin_new_password'])) 
					{
						$admin_new_password = $old_password;
					}
					
					if (empty($_POST['admin_email'])) 
					{
						$admin_email = $old_email;
					}
					
					$result = $db->execute("
						UPDATE arrowchat_admin 
						SET password = '" . $db->escape_string($admin_new_password) . "', 
							email = '" . $db->escape_string($admin_email) . "'
					");

					if ($result) 
					{
						$msg = "Your settings were successfully saved.";
					} 
					else 
					{
						$error = "There is a database error.  Please try again.";
					}
				}
			} 
			else 
			{
				$error = "There is a database error.  Please try again.";
			}
		}
	}
	
	// Language Active Processor
	if ($do == "language") 
	{
		if (!empty($_REQUEST['activate'])) 
		{
			$result = $db->execute("
				UPDATE arrowchat_config 
				SET config_value = '" . get_var('activate') . "' 
				WHERE config_name = 'language'
			");

			if ($result) 
			{
				$msg = "Your language has been successfully activated.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.";
			}
		}
	}

	$smarty->assign('msg', $msg);
	$smarty->assign('error', $error);

	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_header.tpl");
	require(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_system.php");
	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_footer.tpl");
	
	flush_headers();
	
	
	// ####################### START AUTOMATIC UPGRADE PROCESSES ########################
	if ($do == "step3") 
	{
		// Authenticate that the user has an active subscription
		echo '<script type="text/javascript">document.getElementById(\'authenticating\').innerHTML = "Authenticating...";</script>';
		flush_headers();
		
		// fopen causes a "no data received" error on some servers, so change to fpen for now
		//$fp = @fpen("http://www.arrowchat.com/validate.php?u=" . get_var('validate_username') . "&p=" . get_var('validate_password'), "r");
		
		if ($fp) 
		{
			$validation = @fread($fp, 99); 
			$validation_pieces = explode(":", $validation);
			
			if ($validation_pieces[0] == "1") 
			{
				echo '<script type="text/javascript">document.getElementById(\'authenticating\').innerHTML = "Authenticating...Validated!";</script>';
				flush_headers();
			} 
			else if ($validation_pieces[0] == "2")
			{
				echo '<script type="text/javascript">document.getElementById(\'download\').innerHTML = "' . $validation_pieces[1] . '";</script>';
				echo '<script type="text/javascript">document.getElementById(\'continue\').innerHTML = "<dl class=\'selectionBox submitBox\'><dt></dt><dd><div class=\'floatr\' style=\'float: right;\'><a class=\'fwdbutton\' onclick=\'document.forms[0].submit(); return false\'><span>Back to Login Details</span></a><input type=\'hidden\' name=\'auto-step-2\' value=\'1\' /></div></dd></dl>";</script>';
				flush_headers();
				exit;
			} 
			else 
			{
				echo '<script type="text/javascript">document.getElementById(\'download\').innerHTML = "There was an error, please try again.";</script>';
				echo '<script type="text/javascript">document.getElementById(\'continue\').innerHTML = "<dl class=\'selectionBox submitBox\'><dt></dt><dd><div class=\'floatr\' style=\'float: right;\'><a class=\'fwdbutton\' onclick=\'document.forms[0].submit(); return false\'><span>Back to Login Details</span></a><input type=\'hidden\' name=\'auto-step-2\' value=\'1\' /></div></dd></dl>";</script>';
				flush_headers();
				exit;
			}
		} 
		else 
		{
			echo '<script type="text/javascript">document.getElementById(\'download\').innerHTML = "We could not make a connection to ArrowChat.com.  Either the site is down or your fopen does not allow remote URLs.";</script>';
			echo '<script type="text/javascript">document.getElementById(\'continue\').innerHTML = "<dl class=\'selectionBox submitBox\'><dt></dt><dd><div class=\'floatr\' style=\'float: right;\'><a class=\'fwdbutton\' onclick=\'document.forms[0].submit(); return false\'><span>Back to Login Details</span></a><input type=\'hidden\' name=\'auto-step-2\' value=\'1\' /></div></dd></dl>";</script>';
			flush_headers();
			exit;
		}
		
		fclose ($fp);
		$key = $validation_pieces[1];
		
		if (empty($key)) 
		{
			echo '<script type="text/javascript">document.getElementById(\'download\').innerHTML = "There was a problem with your key, please try again or contact ArrowChat support.";</script>';
			echo '<script type="text/javascript">document.getElementById(\'continue\').innerHTML = "<dl class=\'selectionBox submitBox\'><dt></dt><dd><div class=\'floatr\' style=\'float: right;\'><a class=\'fwdbutton\' onclick=\'document.forms[0].submit(); return false\'><span>Back to Login Details</span></a><input type=\'hidden\' name=\'auto-step-2\' value=\'1\' /></div></dd></dl>";</script>';
			flush_headers();
			exit;
		}
		
		sleep(2);
		
		// Start downloading ArrowChat if it doesn't already exist in the cache folder
		if (file_exists(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "arrowchat_v" . get_var('url') . ".zip")) 
		{
			echo '<script type="text/javascript">document.getElementById(\'download\').innerHTML = "ArrowChat already downloaded...";</script>';
			flush_headers();
		} 
		else 
		{
			set_time_limit(300);

			$remoteFile = "http://www.arrowchat.com/update-download.php?file=ac&id=" . $key;
			$dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR;

			$file = @fopen($remoteFile, "r");

			echo '<script type="text/javascript">document.getElementById(\'download\').innerHTML = "Downloading ArrowChat...";</script>';
			flush_headers();

			if (!$file) 
			{
				echo '<script type="text/javascript">document.getElementById(\'download\').innerHTML = "Unable to open remote file.  Please download the file manually from the <a href=\'http://www.arrowchat.com\' target=\'_blank\'>arrowchat.com</a> member\'s area and place it in the cache folder.  Do not rename the file.";</script>';
				flush_headers();
				exit;
			}
			
			$line = '';

			while (!feof ($file)) 
			{
				$line .= fgets ($file, 4096);
			}

			file_put_contents(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "arrowchat_v" . get_var('url') . ".zip",  $line);
			fclose($file);
		}
		
		sleep(2);
		
		// Start downloading the mySQL file if it doesn't already exist in the cache folder
		if (file_exists(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "mysql_v" . get_var('url') . ".zip")) 
		{
			echo '<script type="text/javascript">document.getElementById(\'download2\').innerHTML = "mySQL already downloaded...";</script>';
			flush_headers();
		} 
		else 
		{
			set_time_limit(300);

			$remoteFile = "http://www.arrowchat.com/update-download-mysql.php?file=ac&id=" . $key;
			$dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR;

			$file = @fopen($remoteFile, "r");

			echo '<script type="text/javascript">document.getElementById(\'download2\').innerHTML = "Downloading mySQL...";</script>';
			flush_headers();

			if (!$file) 
			{
				echo '<script type="text/javascript">document.getElementById(\'download2\').innerHTML = "Unable to open remote file.  Please download the file manually from the <a href=\'http://www.arrowchat.com\' target=\'_blank\'>arrowchat.com</a> member\'s area and place it in the cache folder.  Do not rename the file.";</script>';
				flush_headers();
				exit;
			}
			
			$line = '';

			while (!feof ($file)) 
			{
				$line .= fgets ($file, 4096);
			}

			file_put_contents(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "mysql_v" . get_var('url') . ".zip",  $line);
			fclose($file);
		}
		
		sleep(2);
		
		// Put ArrowChat into maintenance mode for installation
		echo '<script type="text/javascript">document.getElementById(\'maintenance\').innerHTML = "Putting ArrowChat in maintenance mode...";</script>';
		flush_headers();
		
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'chat_maintenance' THEN '1' 
			END WHERE config_name IN ('chat_maintenance')
		");

		update_config_file();
		
		sleep(1);
		
		// Unzip the ArrowChat zip file and overwrite all files
		echo '<script type="text/javascript">document.getElementById(\'unpacking\').innerHTML = "Unpacking ArrowChat...";</script>';
		flush_headers();
		
		$zip = new ZipArchive;
		if ($zip->open(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "arrowchat_v" . get_var('url') . ".zip") === TRUE) 
		{
			$zip->extractTo(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
			$zip->close();
		} 
		else 
		{
			echo '<script type="text/javascript">document.getElementById(\'database\').innerHTML = "Unable to unzip the ArrowChat file. Error Code: ' . $zip . '";</script>';
			flush_headers();
			exit;
		}
		
		sleep(2);
		
		// Unzip the mySQL zip file
		echo '<script type="text/javascript">document.getElementById(\'unpacking2\').innerHTML = "Unpacking mySQL...";</script>';
		flush_headers();
		
		$zip = new ZipArchive;
		if ($zip->open(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "mysql_v" . get_var('url') . ".zip") === TRUE) 
		{
			$zip->extractTo(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
			$zip->close();
		} 
		else 
		{
			echo '<script type="text/javascript">document.getElementById(\'database\').innerHTML = "Unable to unzip the mySQL file. Please try again.";</script>';
			flush_headers();
			exit;
		}
		
		sleep(2);
		
		// Start running all the SQL statements that were in the update
		echo '<script type="text/javascript">document.getElementById(\'database\').innerHTML = "Updating database...";</script>';
		flush_headers();
		
		$dbms_schema = dirname(dirname(__FILE__)) . 'mysql_schema.sql';

		$remove_remarks = "remove_remarks";
		$delimiter = ";";

		$sql_query = @file_get_contents($dbms_schema);
		$remove_remarks($sql_query);

		$sql_query = split_sql_file($sql_query, $delimiter);

		foreach ($sql_query as $sql)
		{
			$db->execute($sql);
		}
		
		sleep(2);
		
		// Bring ArrowChat out of maintenance mode
		echo '<script type="text/javascript">document.getElementById(\'maintenance_out\').innerHTML = "Bringing ArrowChat out of maintenance mode...";</script>';
		flush_headers();
		
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'chat_maintenance' THEN '0' 
			END WHERE config_name IN ('chat_maintenance')
		");

		update_config_file();
		
		sleep(1);
		
		// Delete the ArrowChat zip file
		echo '<script type="text/javascript">document.getElementById(\'unlink\').innerHTML = "Unlinking ArrowChat...";</script>';
		flush_headers();
		
		unlink(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "arrowchat_v" . get_var('url') . ".zip");
		
		sleep(1);
		
		// Delete the SQL zip file
		echo '<script type="text/javascript">document.getElementById(\'unlink2\').innerHTML = "Unlinking mySQL...";</script>';
		flush_headers();
		
		unlink(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . "mysql_v" . get_var('url') . ".zip");
		unlink(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "mysql_schema.sql");
		
		sleep(1);
		
		echo '<script type="text/javascript">document.getElementById(\'continue\').innerHTML = "<dl class=\'selectionBox submitBox\'><dt></dt><dd><div class=\'floatr\' style=\'float: right;\'><a class=\'fwdbutton\' onclick=\'document.forms[0].submit(); return false\'><span>Step 4 - Additional Instructions</span></a><input type=\'hidden\' name=\'auto-step-5\' value=\'1\' /></div></dd></dl>";</script>';
		flush_headers();
	}
?>