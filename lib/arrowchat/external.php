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
	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');
	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'functions/functions_mobile.php');

	$type = get_var('type');

	// ############################ OPTIMIZATION #############################
	//if (!ob_start("ob_gzhandler"))
	//{
		ob_start();
	//}

	// ########################### EXIT CONDITIONS ###########################
	// Exit if the type is not supported
	if ($type != "css" AND $type != "js" AND $type != "djs" AND $type != "pjs" AND $type != "mjs")
	{
		close_session();
		exit;
	}
	
	// Exit if not logged in
	if (!logged_in($userid) AND empty($guests_can_view)) 
	{
		$not_logged_in = 1;
	}
	else
	{
		$not_logged_in = 0;
	}

	// Exit if banned
	if (in_array($_SERVER['REMOTE_ADDR'], $banlist) || in_array($userid, $banlist)) 
	{
		if (!empty($_SERVER['REMOTE_ADDR']))
		{
			close_session();
			exit;
		}
	}

	// Exit if IE8 or lower
	if (isset($_SERVER['HTTP_USER_AGENT'])) 
	{
		if (preg_match('/(?i)msie [1-8]\./', $_SERVER['HTTP_USER_AGENT']))
		{
			close_session();
			exit;
		}
	}

	// Exit if mobile browser
	$mobile_device = 0;
	if (mobile_device_detect()) 
	{
		$mobile_device = 1;
	}
	
	// Exit for group permissions
	if ($group_enable_mode == 1)
	{
		if (check_array_for_match($group_id, $group_disable_arrowchat_sep))
		{
		}
		else
		{
			close_session();
			exit;
		}
	}
	else
	{
		if (check_array_for_match($group_id, $group_disable_arrowchat_sep))
		{
			close_session();
			exit;
		}
	}

	// ############################ PROCESS THEME ############################
	if (is_numeric($theme)) 
	{
		$result = $db->execute("
			SELECT folder 
			FROM arrowchat_themes
			WHERE id = '" . $db->escape_string($theme) . "'
		");

		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			$theme = $row['folder'];
		} 
		else 
		{
			$theme = "new_facebook_full";
		}
	}

	// ############################## START CSS ##############################
	// This is the primary CSS file for ArrowChat
	if ($type == "css") 
	{
		header ("Content-type: text/css; charset=UTF-8");
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600*24*7) . ' GMT');

		require_once (dirname(__FILE__) . '/themes/' . $theme . '/css/style.css');
		
		close_session();
		exit;
	}

	// ############################## START DJS ##############################
	// These are all the dynamic variables that change on each load. This does not cache
	if ($type == "djs") 
	{
		header('Content-type: text/javascript; charset=UTF-8');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		
		$double_check = array();
		$i = 1;
		
		// Mark user as no longer idle on load
		if ($status == "away" || $status == "busy") 
		{
			$db->execute("
				UPDATE arrowchat_status 
				SET status = 'available' 
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
			
			$status = "available";
		}
		
		// Show bar if hide bar is disabled
		if ($hide_bar_on != 1) 
		{
			$hide_bar = 0;
		}
		
		// Show chat bar regardless of maintenance if user is admin
		if ($is_admin == 1 AND $admin_view_maintenance == 1) 
		{
			$chat_maintenance = 0;
		}
		
		// Load another language if lang GET value is set and exists
		if (var_check('lang'))
		{
			$lang = get_var('lang');
			
			if (preg_match("#^[a-z]{2,20}$#i", $lang))
			{
				if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_LANGUAGE . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $lang . ".php"))
				{
					include (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_LANGUAGE . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $lang . ".php");
				}
			}
		}

		// Get the language
		for ($i = 0; $i < count($language); $i++) 
		{
			$settings .= 'lang[' . $i . '] = "' . $language[$i] . '";';
		}
		
		// Get the bar links
		for ($i=0; $i < count($trayicon); $i++) 
		{
			$settings .= "barLinks[" . $i . "] = ['" . implode("', '", $trayicon[$i]) . "'];";
		}
		
		// Get the application bookmarks
		if (empty($apps_bookmarks) OR ($apps_bookmarks == "-1" AND empty($apps_other))) 
		{
			foreach ($apps as $val) 
			{
				if ($val[8] == "1")
				{
					$settings .= "apps[" . $val[0] . "] = ['" . implode("', '", $val) . "','','" . $i . "'];";
					$i++;
				}
				else
				{
					$settings .= "apps[" . $val[0] . "] = ['" . implode("', '", $val) . "','1','" . $i . "'];";
					$i++;
				}
			}
		} 
		else 
		{
			$bookmark_apps = explode(":", $apps_bookmarks);
			
			if ($apps_bookmarks != "-1") 
			{	
				foreach ($bookmark_apps as $val2) 
				{
					if (!empty($val2)) 
					{
						if (!empty($apps[$val2])) 
						{
							$settings .= "apps[" . $val2 . "] = ['" . implode("', '", $apps[$val2]) . "','','" . $i . "'];";
							$i++;
						}
					}
				}
			}
		}
		
		// Get the applications marked as other
		if (!empty($apps_other))
		{
			$other_apps = explode(":", $apps_other);
			
			if ($apps_other != "-1") 
			{
				foreach ($other_apps as $val2) 
				{
					if (!empty($val2)) 
					{
						if (!empty($apps[$val2]))
						{
							$settings .= "apps[" . $val2 . "] = ['" . implode("', '", $apps[$val2]) . "','1','" . $i . "'];";
							$i++;
						}
					}
				}
			}
			
			foreach ($apps as $val) 
			{
				if (!in_array($val[0], $bookmark_apps) AND !in_array($val[0], $other_apps)) 
				{
					$settings .= "apps[" . $val[0] . "] = ['" . implode("', '", $val) . "','','" . $i . "'];";
					$i++;
				}
			}
		}
		
		// Get all the themes
		for ($i = 0; $i < count($themes); $i++) 
		{
			$settings .= "Themes[" . $i . "] = ['" . implode("', '", $themes[$i]) . "'];";
		}
		
		$i=0;
		
		// Get all the smilies
		foreach ($smileys as $pattern => $result) 
		{
			$settings .= "Smiley[" . $i . "] = ['" . $result . "','" . $pattern . "'];";
			$i++;
		}
		
		// Put all the blocked users into an array
		if (!empty($block_chats))
		{
			$block_chats_unserialized = unserialize($block_chats);
			if (!is_array($block_chats_unserialized)) $block_chats_unserialized = array();
			$i=0;
			foreach ($block_chats_unserialized as $id) 
			{
				$settings .= "blockList['" . $id . "'] = ['" . $id . "'];";
				$i++;
			}
		}
		
		// Get all the chat windows and user details that are not in focus
		for ($i = 0; $i < count($unfocus_chat) - 1; $i++) 
		{
			if (!in_array($unfocus_chat[$i], $double_check) AND !empty($unfocus_chat[$i])) 
			{
				// Start Receive User Details
				if (check_if_guest($unfocus_chat[$i]))
				{
					$sql = get_guest_details($unfocus_chat[$i]);
					$result = $db->execute($sql);
				}
				else
				{
					$sql = get_user_details($unfocus_chat[$i]);
					$result = $db->execute($sql);
				}
						
				if ($result AND $db->count_select() > 0) 
				{
					$chat = $db->fetch_array($result);

					if (((time()-$chat['lastactivity']) < $online_timeout) AND $chat['status'] != 'invisible' AND $chat['status'] != 'offline')
					{
						if ($chat['status'] != 'busy' AND $chat['status'] != 'away') 
						{
							$chat['status'] = 'available';
						}
					} 
					else 
					{
						$chat['status'] = 'offline';
					}
					
					if (check_if_guest($unfocus_chat[$i]))
					{
						$link = "#";
						$avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
						$chat['username'] = create_guest_username($unfocus_chat[$i], $chat['guest_name']);
					}
					else
					{
						$link = get_link($chat['link'], $chat['userid']);
						$avatar = get_avatar($chat['avatar'], $chat['userid']);
					}
				}
				// End Receive User Details

				$settings .= 'unfocus_chat[' . $i . '] = "' . $unfocus_chat[$i] . '";';
				$settings .= 'uc_name["' . $unfocus_chat[$i] . '"] = "' . $db->escape_string(strip_tags($chat['username'])) . '";';
				$settings .= 'uc_status["' . $unfocus_chat[$i] . '"] = "' . $chat['status'] . '";';
				$settings .= 'uc_avatar["' . $unfocus_chat[$i] . '"] = "' . $avatar . '";';
				$settings .= 'uc_link["' . $unfocus_chat[$i] . '"] = "' . $link . '";';
				$double_check[] = $unfocus_chat[$i];
			}
		}
		
		// Get the logged in user's avatar
		if (check_if_guest($userid))
		{
			$user_username = create_guest_username($userid, $guest_name);
			$user_avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
			$user_is_guest = 1;
		}
		else
		{
			$user_is_guest = 0;
			$user_username = get_username($userid);
			
			$sql = get_user_details($userid);
			$result = $db->execute($sql);
			
			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				$user_avatar = $row['avatar'];
				$user_avatar = get_avatar($user_avatar, $userid);
			}
			else
			{
				$user_avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
			}
		}
		
		$num_mod_reports = 0;
		if ($is_admin == 1) $is_mod = 1;
		if ($is_admin == 1 OR $is_mod == 1)
		{
			$result = $db->execute("
				SELECT COUNT(id)
				FROM arrowchat_reports
				WHERE (working_time < (" . time() . " - 600)
							OR working_by = '" . $db->escape_string($userid) . "')
					AND completed_time = 0
			");
		
			if ($row = $db->fetch_array($result))
			{
				$num_mod_reports = $row['COUNT(id)'];
			}
		}
		
		// Check and set group permissions
		$disable_sending_private_msg = 0;
		$disable_sending_group_msg = 0;
		
		if (check_array_for_match($group_id, $group_disable_video_sep))
			$video_chat = 0;
			
		if (check_array_for_match($group_id, $group_disable_apps_sep))
			$applications_on = 0;
			
		if (check_array_for_match($group_id, $group_disable_rooms_sep))
			$chatrooms_on = 0;
		
		if (check_array_for_match($group_id, $group_disable_sending_private_sep))
			$disable_sending_private_msg = 1;
			
		if (check_array_for_match($group_id, $group_disable_sending_rooms_sep))
			$disable_sending_group_msg = 1;
		
		// Reverse group settings if enable mode is on
		if ($group_enable_mode == 1)
		{
			$disable_sending_private_msg = 1;
			$disable_sending_group_msg = 1;
			$video_chat = 0;
			$applications_on = 0;
			$chatrooms_on = 0;
			
			if (check_array_for_match($group_id, $group_disable_video_sep))
				$video_chat = 1;
				
			if (check_array_for_match($group_id, $group_disable_apps_sep))
				$applications_on = 1;
				
			if (check_array_for_match($group_id, $group_disable_rooms_sep))
				$chatrooms_on = 1;
			
			if (check_array_for_match($group_id, $group_disable_sending_private_sep))
				$disable_sending_private_msg = 0;
				
			if (check_array_for_match($group_id, $group_disable_sending_rooms_sep))
				$disable_sending_group_msg = 0;
		}
		
		// Get all the rest of the general settings
		$settings .= 'var T=0,';
		$settings .= 'u_theme="' . $theme . '",';
		$settings .= 'u_name="' . $db->escape_string(strip_tags($user_username)) . '",';
		$settings .= 'u_id="' . $userid . '",';
		$settings .= 'u_group=' . json_encode($group_id) . ',';
		$settings .= 'u_hide_bar="' . $hide_bar . '",';
		$settings .= 'u_blist_open="' . $window_open . '",';
		$settings .= 'u_sounds="' . $play_sound . '",';
		$settings .= 'u_chatroom_open="' . $chatroom_window . '",';
		$settings .= 'u_chatroom_stay="' . $chatroom_stay . '",';
		$settings .= 'u_chatroom_block_chats="' . $chatroom_block_chats . '",';
		$settings .= 'u_status="' . $status . '",';
		$settings .= 'u_no_avatars="' . $only_names . '",';
		$settings .= 'u_hash_id="' . $hash_id . '",';
		$settings .= 'u_chat_open="' . $focus_chat . '",';
		$settings .= 'u_chatroom_sound="' . $chatroom_sound . '",';
		$settings .= 'u_chatroom_show_names="' . $chatroom_show_names . '",';
		$settings .= 'u_apps_open="' . $apps_open . '",';
		$settings .= 'u_logged_in="' . $not_logged_in . '",';
		$settings .= 'u_popout_time="' . $popout . '",';
		$settings .= 'u_avatar="' . $user_avatar . '",';
		$settings .= 'u_is_guest="' . $user_is_guest . '",';
		$settings .= 'u_guest_name="' . $guest_name . '",';
		$settings .= 'u_is_mod="' . $is_mod . '",';
		$settings .= 'u_is_admin="' . $is_admin . '",';
		$settings .= 'u_num_mod_reports="' . $num_mod_reports . '",';
		$settings .= 'c_send_priv_msg="' . $disable_sending_private_msg . '",';
		$settings .= 'c_send_room_msg="' . $disable_sending_group_msg . '",';
		$settings .= 'c_chatrooms="' . $chatrooms_on . '",';
		$settings .= 'c_chatroom_auto_join="' . $chatroom_auto_join . '",';
		$settings .= 'c_guests_apps="' . $applications_guests . '",';
		$settings .= 'c_video_chat="' . $video_chat . '",';
		$settings .= 'c_theme_change="' . $theme_change_on . '",';
		$settings .= 'c_notifications="' . $notifications_on . '",';
		$settings .= 'c_chat_maintenance="' . $chat_maintenance . '",';
		$settings .= 'c_guests_login_msg="' . $guests_can_view . '",';
		$settings .= 'c_search_min="' . $search_number . '",';
		$settings .= 'c_us_time="' . $us_time . '",';
		$settings .= 'c_file_transfer="' . $file_transfer_on . '",';
		$settings .= 'c_chatroom_transfer="' . $chatroom_transfer_on . '",';
		$settings .= 'c_width_blist="' . $width_buddy_list . '",';
		$settings .= 'c_width_chatroom="' . $width_chatrooms . '",';
		$settings .= 'c_width_apps="' . $width_applications . '",';
		$settings .= 'c_hide_bar_on="' . $hide_bar_on . '",';
		$settings .= 'c_heart_beat="' . $heart_beat . '",';
		$settings .= 'c_list_heart_beat="' . $buddy_list_heart_beat . '",';
		$settings .= 'c_user_chatrooms="' . $user_chatrooms . '",';
		$settings .= 'c_disable_avatars="' . $disable_avatars . '",';
		$settings .= 'c_disable_arrowchat="' . $disable_arrowchat . '",';
		$settings .= 'c_show_full_name="' . $show_full_username . '",';
		$settings .= 'c_bar_fixed="' . $bar_fixed . '",';
		$settings .= 'c_bar_fixed_alignment="' . $bar_fixed_alignment . '",';
		$settings .= 'c_bar_fixed_width="' . $bar_fixed_width . '",';
		$settings .= 'c_bar_padding="' . $bar_padding . '",';
		$settings .= 'c_window_top_padding=' . $window_top_padding . ',';
		$settings .= 'c_applications_on="' . $applications_on . '",';
		$settings .= 'c_no_apps_menu="' . $hide_applications_menu . '",';
		$settings .= 'c_popout_on="' . $popout_chat_on . '",';
		$settings .= 'c_push_engine="' . $push_on . '",';
		$settings .= 'c_push_publish="' . $push_publish . '",';
		$settings .= 'c_push_subscribe="' . $push_subscribe . '",';
		$settings .= 'c_mobile_device="' . $mobile_device . '",';
		$settings .= 'c_links_right="' . $show_bar_links_right . '",';
		$settings .= 'c_chat_animations="' . $enable_chat_animations . '",';
		$settings .= 'c_disable_smilies="' . $disable_smilies . '",';
		$settings .= 'c_guest_name_change="' . $guest_name_change . '",';
		$settings .= 'c_login_url="' . $login_url . '",';
		$settings .= 'c_admin_bg="' . $admin_background_color . '",';
		$settings .= 'c_admin_txt="' . $admin_text_color . '",';
		$settings .= 'c_desktop_notify="' . $desktop_notifications . '",';
		$settings .= 'c_facebook_app_id="' . $facebook_app_id . '",';
		$settings .= 'c_max_upload_size="' . $max_upload_size . '",';
		$settings .= 'c_max_chatroom_msg="' . $chatroom_message_length . '",';
		$settings .= 'c_enable_moderation="' . $enable_moderation . '",';
		$settings .= 'c_push_ssl="' . $push_ssl . '",';
		$settings .= 'c_video_height="' . $video_chat_height . '",';
		$settings .= 'c_video_width="' . $video_chat_width . '",';
		$settings .= 'c_video_select="' . $video_chat_selection . '",';
		$settings .= 'c_online_list="' . $online_list_on . '",';
		$settings .= 'c_ac_path="' . $base_url . '";';		
			
		require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'js/arrowchat_dynamic.js');	
		
		close_session();
		exit;
	}

	// ############################## START JS ###############################
	// These are the core JavaScript files that will cache
	if ($type == "js") 
	{
		header('Content-type: text/javascript; charset=UTF-8');
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600*24*7) . ' GMT');
		
		if ($mobile_device == 1)
		{	
			if ($enable_mobile == 1)
			{
				echo "// **********Main Script Start**********\n// http://www.arrowchat.com\n";
				require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'js/arrowchat_mobile.js');
			}
			else
			{
				close_session();
				exit;
			}
		}
		else
		{
			// Inclue Template Files
			$file_bar_hide_tab 					= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/bar_hide_tab.php")); 
			$file_bar_show_tab 					= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/bar_show_tab.php")); 
			$file_applications_bookmarks_tab	= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/applications_bookmarks_tab.php"));
			$file_applications_bookmarks_window	= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/applications_bookmarks_window.php"));
			$file_applications_bookmarks_list	= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/applications_bookmarks_list.php"));
			$file_applications_tab				= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/applications_tab.php"));
			$file_applications_window			= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/applications_window.php"));
			$file_notifications_tab				= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/notifications_tab.php"));
			$file_notifications_window			= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/notifications_window.php"));
			$file_mod_tab						= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/mod_tab.php"));
			$file_mod_window					= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/mod_window.php"));
			$file_mod_report					= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/mod_report.php"));
			$file_warnings_display				= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/warnings_display.php"));
			$file_chat_tab						= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/chat_tab.php"));
			$file_chat_window					= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/chat_window.php"));
			$file_buddylist_tab					= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/buddylist_tab.php"));
			$file_buddylist_window				= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/buddylist_window.php"));
			$file_maintenance_tab				= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/maintenance_tab.php"));
			$file_announcements_display			= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/announcements_display.php"));
			$file_chatrooms_tab					= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/chatrooms_tab.php"));
			$file_chatrooms_window				= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/chatrooms_window.php"));
			$file_chatrooms_room				= line_break_replace(get_include_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/chatrooms_room.php"));

			require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'js/arrowchat_libraries.js');
			
			echo "\n\n//**********Templates**********\n";
			require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'js/arrowchat_templates.js');
			
			echo "\n\n// **********Main Script Start**********\n// http://www.arrowchat.com\n";
			require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'js/arrowchat_core.js');
			
			echo "\n\n// **********Applications Pre-loading Start**********\n";
			foreach ($apps as $val) 
			{
				if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . $val[2] . DIRECTORY_SEPARATOR . "preload.php"))
				{
					include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . $val[2] . DIRECTORY_SEPARATOR . "preload.php");
				}
			}
		}
		
		echo "\n/* ArrowChat Version: " . ARROWCHAT_VERSION . " */";
		
		close_session();
		exit;
	}

	// ############################## START POPOUT JS ###############################
	// This includes all the files required for the popout chat windows
	if ($type == "pjs") 
	{
		header('Content-type: text/javascript; charset=UTF-8');
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600*24*7) . ' GMT');
			
		require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_PUBLIC . DIRECTORY_SEPARATOR . 'popout/js/popout_libraries.js');

		echo "\n\n// **********Main Script Start**********\n// http://www.arrowchat.com\n";
		require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_PUBLIC . DIRECTORY_SEPARATOR . 'popout/js/popout_core.js');
		
		close_session();
		exit;
	}
	
	// ############################## START MOBILE JS ###############################
	// This includes all the files required for the mobile chat
	if ($type == "mjs") 
	{
		header('Content-type: text/javascript; charset=UTF-8');
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600*24*7) . ' GMT');
			
		require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_PUBLIC . DIRECTORY_SEPARATOR . 'mobile/includes/js/mobile_libraries.js');

		echo "\n\n// **********Main Script Start**********\n// http://www.arrowchat.com\n";
		require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . AC_FOLDER_PUBLIC . DIRECTORY_SEPARATOR . 'mobile/includes/js/mobile_core.js');
		
		close_session();
		exit;
	}

?>