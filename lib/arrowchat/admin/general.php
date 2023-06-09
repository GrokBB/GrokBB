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
		$do = "chatsettings";
	}
	
	// ####################### START SUBMIT/POST DATA ########################
	
	// Chat Features Submit Processor
	if (var_check('chatfeatures_submit')) 
	{
		if (!is_numeric(get_var('video_chat_width')) OR !is_numeric(get_var('video_chat_height')))
			$error = "The video chat width and height must be a number only.";
		
		if (empty($error))
		{
			$result = $db->execute("
				UPDATE arrowchat_config 
				SET config_value = CASE 
					WHEN config_name = 'hide_bar_on' THEN '" . get_var('hide_bar_on') . "'
					WHEN config_name = 'chatrooms_on' THEN '" . get_var('chatrooms_on') . "'
					WHEN config_name = 'notifications_on' THEN '" . get_var('notifications_on') . "'
					WHEN config_name = 'applications_on' THEN '" . get_var('applications_on') . "'
					WHEN config_name = 'popout_chat_on' THEN '" . get_var('popout_chat_on') . "'
					WHEN config_name = 'theme_change_on' THEN '" . get_var('theme_change_on') . "'
					WHEN config_name = 'enable_mobile' THEN '" . get_var('enable_mobile') . "'
					WHEN config_name = 'video_chat' THEN '" . get_var('video_chat') . "'
					WHEN config_name = 'file_transfer_on' THEN '" . get_var('file_transfer_on') . "' 
					WHEN config_name = 'desktop_notifications' THEN '" . get_var('desktop_notifications') . "' 
					WHEN config_name = 'max_upload_size' THEN '" . get_var('max_upload_size') . "' 
					WHEN config_name = 'chatroom_transfer_on' THEN '" . get_var('chatroom_transfer_on') . "' 
					WHEN config_name = 'enable_moderation' THEN '" . get_var('enable_moderation') . "' 
					WHEN config_name = 'video_chat_selection' THEN '" . get_var('video_chat_selection') . "' 
					WHEN config_name = 'video_chat_width' THEN '" . get_var('video_chat_width') . "' 
					WHEN config_name = 'video_chat_height' THEN '" . get_var('video_chat_height') . "' 
					WHEN config_name = 'tokbox_api' THEN '" . get_var('tokbox_api') . "' 
					WHEN config_name = 'tokbox_secret' THEN '" . get_var('tokbox_secret') . "' 
					WHEN config_name = 'online_list_on' THEN '" . get_var('online_list_on') . "'
				END WHERE config_name IN ('hide_bar_on', 'chatrooms_on', 'notifications_on', 'applications_on', 'popout_chat_on', 'theme_change_on', 'enable_mobile', 'video_chat', 'file_transfer_on', 'desktop_notifications', 'max_upload_size', 'chatroom_transfer_on', 'enable_moderation', 'video_chat_selection', 'video_chat_width', 'video_chat_height', 'tokbox_api', 'tokbox_secret', 'online_list_on')
			");
						
			if ($result) 
			{
				$hide_bar_on = get_var('hide_bar_on');
				$chatrooms_on = get_var('chatrooms_on');
				$notifications_on = get_var('notifications_on');
				$applications_on = get_var('applications_on');
				$popout_chat_on = get_var('popout_chat_on');
				$theme_change_on = get_var('theme_change_on');
				$enable_mobile = get_var('enable_mobile');
				$video_chat = get_var('video_chat');
				$file_transfer_on = get_var('file_transfer_on');
				$desktop_notifications = get_var('desktop_notifications');
				$max_upload_size = get_var('max_upload_size');
				$chatroom_transfer_on = get_var('chatroom_transfer_on');
				$enable_moderation = get_var('enable_moderation');
				$video_chat_selection = get_var('video_chat_selection');
				$video_chat_width = get_var('video_chat_width');
				$video_chat_height = get_var('video_chat_height');
				$tokbox_api = get_var('tokbox_api');
				$tokbox_secret = get_var('tokbox_secret');
				$online_list_on = get_var('online_list_on');
			
				update_config_file();
				$msg = "Your settings were successfully saved.";
			} 
			else
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Chat Settings Submit Processor
	if (var_check('chatsettings_submit')) 
	{
		$guest_name_bad_words = trim(get_var('guest_name_bad_words'));
		if (substr($guest_name_bad_words, -1, 1) == ",") $guest_name_bad_words = substr($guest_name_bad_words, 0, -1);
		
		$blocked_words = trim(get_var('blocked_words'));
		if (substr($blocked_words, -1, 1) == ",") $blocked_words = substr($blocked_words, 0, -1);
		
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'disable_avatars' THEN '" . get_var('disable_avatars') . "'
				WHEN config_name = 'disable_smilies' THEN '" . get_var('disable_smilies') . "'
				WHEN config_name = 'disable_arrowchat' THEN '" . get_var('disable_arrowchat') . "'
				WHEN config_name = 'disable_buddy_list' THEN '" . get_var('disable_buddy_list') . "'
				WHEN config_name = 'search_number' THEN '" . get_var('search_number') . "'
				WHEN config_name = 'chat_maintenance' THEN '" . get_var('chat_maintenance') . "' 
				WHEN config_name = 'admin_chat_all' THEN '" . get_var('admin_chat_all') . "'
				WHEN config_name = 'admin_view_maintenance' THEN '" . get_var('admin_view_maintenance') . "'
				WHEN config_name = 'guests_can_view' THEN '" . get_var('guests_can_view') . "'
				WHEN config_name = 'guests_can_chat' THEN '" . get_var('guests_can_chat') . "'
				WHEN config_name = 'guests_chat_with' THEN '" . get_var('guests_chat_with') . "'
				WHEN config_name = 'guest_name_change' THEN '" . get_var('guest_name_change') . "'
				WHEN config_name = 'guest_name_duplicates' THEN '" . get_var('guest_name_duplicates') . "'
				WHEN config_name = 'guest_name_bad_words' THEN '" . $guest_name_bad_words . "'
				WHEN config_name = 'users_chat_with' THEN '" . get_var('users_chat_with') . "'
				WHEN config_name = 'show_full_username' THEN '" . get_var('show_full_username') . "'
				WHEN config_name = 'us_time' THEN '" . get_var('us_time') . "'
				WHEN config_name = 'hide_admins_buddylist' THEN '" . get_var('hide_admins_buddylist') . "'
				WHEN config_name = 'blocked_words' THEN '" . $blocked_words . "'
			END WHERE config_name IN ('disable_avatars', 'disable_smilies', 'disable_arrowchat', 'disable_buddy_list', 'search_number', 'chat_maintenance', 'admin_chat_all', 'admin_view_maintenance', 'guests_can_view', 'guests_can_chat', 'guests_chat_with', 'guest_name_change', 'guest_name_duplicates', 'guest_name_bad_words', 'users_chat_with', 'show_full_username', 'us_time', 'hide_admins_buddylist', 'blocked_words')
		");
					
		if ($result) 
		{	
			$disable_avatars = get_var('disable_avatars');
			$disable_smilies = get_var('disable_smilies');
			$disable_arrowchat = get_var('disable_arrowchat');
			$disable_buddy_list = get_var('disable_buddy_list');
			$search_number = get_var('search_number');
			$chat_maintenance = get_var('chat_maintenance');
			$admin_chat_all = get_var('admin_chat_all');
			$admin_view_maintenance = get_var('admin_view_maintenance');
			$guests_can_view = get_var('guests_can_view');
			$guests_can_chat = get_var('guests_can_chat');
			$guest_name_change = get_var('guest_name_change');
			$guest_name_duplicates = get_var('guest_name_duplicates');
			$guests_chat_with = get_var('guests_chat_with');
			$users_chat_with = get_var('users_chat_with');
			$show_full_username = get_var('show_full_username');
			$us_time = get_var('us_time');
			$hide_admins_buddylist = get_var('hide_admins_buddylist');

			update_config_file();
			include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_CACHE . DIRECTORY_SEPARATOR . 'data_admin_options.php');
			$msg = "Your settings were successfully saved.";
		} 
		else
		{
			$error = "There was a database error.  Please try again.";
		}
	}
	
	// Chat Style Submit Processor
	if (var_check('chatstyle_submit')) 
	{
		if (!is_numeric(get_var('width_applications')) OR !is_numeric(get_var('width_buddy_list')) OR !is_numeric(get_var('width_chatrooms')))
		{
			$error = "You input a non numerical value for one of the widths.  Please enter a number only not including the px.";
		}
			
		if (empty($error)) 
		{
			if (get_var('width_applications') > 200 OR get_var('width_applications') < 16)
			{
				$error = "Your applications width is either above 200px or under 16px.  Please correct the value.";
			}
				
			if (get_var('width_buddy_list') > 200 OR get_var('width_buddy_list') < 16)
			{
				$error = "Your buddy list width is either above 200px or under 16px.  Please correct the value.";
			}
				
			if (get_var('width_chatrooms') > 200 OR get_var('width_chatrooms') < 16)
			{
				$error = "Your chatrooms width is either above 200px or under 16px.  Please correct the value.";
			}
		}
	
		if (empty($error)) 
		{
			$result = $db->execute("
				UPDATE arrowchat_config 
				SET config_value = CASE 
					WHEN config_name = 'width_buddy_list' THEN '" . get_var('width_buddy_list') . "'
					WHEN config_name = 'width_chatrooms' THEN '" . get_var('width_chatrooms') . "'
					WHEN config_name = 'width_applications' THEN '" . get_var('width_applications') . "'
					WHEN config_name = 'bar_fixed' THEN '" . get_var('bar_fixed') . "'
					WHEN config_name = 'bar_fixed_width' THEN '" . get_var('bar_fixed_width') . "'
					WHEN config_name = 'bar_fixed_alignment' THEN '" . get_var('bar_fixed_alignment') . "'
					WHEN config_name = 'bar_padding' THEN '" . get_var('bar_padding') . "'
					WHEN config_name = 'window_top_padding' THEN '" . get_var('window_top_padding') . "'
					WHEN config_name = 'enable_chat_animations' THEN '" . get_var('enable_chat_animations') . "'
					WHEN config_name = 'admin_background_color' THEN '" . get_var('admin_background_color') . "'
					WHEN config_name = 'admin_text_color' THEN '" . get_var('admin_text_color') . "'
				END WHERE config_name IN ('width_buddy_list', 'width_chatrooms', 'width_applications', 'bar_fixed', 'bar_fixed_width', 'bar_fixed_alignment', 'bar_padding', 'window_top_padding', 'enable_chat_animations', 'admin_background_color', 'admin_text_color')
			");
						
			if ($result) 
			{
				$width_buddy_list = get_var('width_buddy_list');
				$width_applications = get_var('width_applications');
				$width_chatrooms = get_var('width_chatrooms');
				$bar_fixed = get_var('bar_fixed');
				$bar_fixed_width = get_var('bar_fixed_width');
				$bar_fixed_alignment = get_var('bar_fixed_alignment');
				$bar_padding = get_var('bar_padding');
				$window_top_padding = get_var('window_top_padding');
				$enable_chat_animations = get_var('enable_chat_animations');
				$admin_background_color = get_var('admin_background_color');
				$admin_text_color = get_var('admin_text_color');
				
				update_config_file();
				$msg = "Your settings were successfully saved.";
			} 
			else
			{
				$error = "There was a database error.  Please try again.";
			}	
		}
	}
	
	$smarty->assign('msg', $msg);
	$smarty->assign('error', $error);

	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_header.tpl");
	require(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_general.php");
	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_footer.tpl");
	
?>