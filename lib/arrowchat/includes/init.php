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
	
	// Deny hacking attempt
	if (!defined('IN_ARROWCHAT'))
	{
		exit;
	}
	
	// ############################ INITILIZATION ############################
	$userid 				= get_user_id();
	$status					= '';
	$settings 				= '';
	$popout					= '99';
	$hide_bar				= '0';
	$play_sound				= '1';
	$window_open			= '0';
	$chatroom_window		= '-1';
	$chatroom_stay			= '0';
	$chatroom_block_chats	= '0';
	$chatroom_show_names	= '0';
	$only_names				= '0';
	$unfocus_chat			= '0';
	$focus_chat				= '0';
	$is_admin				= '0';
	$is_mod					= '0';
	$apps_bookmarks			= '';
	$apps_other				= '';
	$hash_id				= '';
	$block_chats 			= NULL;
	$chatroom_sound 		= '1';
	$apps_open				= '0';
	$guest_name 			= '';
	$user_ip				= get_client_ip();
	
	// Put the group permissions into arrays
	$group_disable_arrowchat_sep		= explode(",", $group_disable_arrowchat);
	$group_disable_video_sep			= explode(",", $group_disable_video);
	$group_disable_apps_sep				= explode(",", $group_disable_apps);
	$group_disable_rooms_sep			= explode(",", $group_disable_rooms);
	$group_disable_uploads_sep			= explode(",", $group_disable_uploads);
	$group_disable_sending_private_sep	= explode(",", $group_disable_sending_private);
	$group_disable_sending_rooms_sep	= explode(",", $group_disable_sending_rooms);
	
	// Create a guest user ID if enabled
	if (!logged_in($userid) AND $guests_can_chat == 1)
	{
		$userid = check_guest_hash();
	}
	
	// Exit if banned
	if (in_array($user_ip, $banlist) || in_array($userid, $banlist)) 
	{
		if (!empty($user_ip))
		{
			close_session();
			exit;
		}
	}
	
	// Get Group ID
	if (check_if_guest($userid))
	{
		$group_id = array('acg');
	}
	else
	{
		$group_id = get_group_id($userid);
	}
	
	// Get file permissions for groups here so it can be passed to other files
	if ($group_enable_mode == 1)
	{
		if (check_array_for_match($group_id, $group_disable_uploads_sep)) 
		{
		}
		else
		{
			$chatroom_transfer_on = 0;
			$file_transfer_on = 0;
		}
	}
	else
	{
		if (check_array_for_match($group_id, $group_disable_uploads_sep)) 
		{
			$chatroom_transfer_on = 0;
			$file_transfer_on = 0;
		}
	}

	// ############################ GET USER DATA ############################
	if (logged_in($userid)) 
	{
		$result = $db->execute("
			SELECT theme, status, popout, hash_id, hide_bar, is_admin, play_sound, window_open, chatroom_window, chatroom_stay, chatroom_block_chats, only_names, unfocus_chat, focus_chat, apps_bookmarks, apps_other, block_chats, chatroom_sound, apps_open, guest_name, chatroom_show_names, ip_address, is_mod
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0)
		{
			$row = $db->fetch_array($result);
			
			if (isset($row['popout'])) 
			{
				$popout = $row['popout'];
				$popout_time = $popout;
				
				if ((time() - $popout < ($buddy_list_heart_beat +7)) AND $popout != "99") 
				{
					$popout = 1;
				}
			}
			
			if (isset($row['hide_bar']))
			{
				$hide_bar = $row['hide_bar'];
			}
			
			if (isset($row['play_sound']))
			{
				$play_sound = $row['play_sound'];
			}
			
			if (isset($row['guest_name']))
			{
				$guest_name = $row['guest_name'];
			}
			
			if (isset($row['window_open']))
			{
				$window_open = $row['window_open'];
			}
			
			if (isset($row['chatroom_window']))
			{
				$chatroom_window = $row['chatroom_window'];
			}
			
			if (isset($row['chatroom_stay']))
			{
				$chatroom_stay = $row['chatroom_stay'];
			}
			
			if (isset($row['apps_open']))
			{
				$apps_open = $row['apps_open'];
			}
			
			if (isset($row['chatroom_block_chats']))
			{
				$chatroom_block_chats = $row['chatroom_block_chats'];
			}
			
			if (isset($row['chatroom_sound']))
			{
				$chatroom_sound = $row['chatroom_sound'];
			}
			
			if (isset($row['chatroom_show_names']))
			{
				$chatroom_show_names = $row['chatroom_show_names'];
			}
			
			if (isset($row['only_names']))
			{
				$only_names = $row['only_names'];
			}
			
			if (isset($row['is_admin']))
			{
				$is_admin = $row['is_admin'];
			}
			
			if (isset($row['is_mod']))
			{
				$is_mod = $row['is_mod'];
			}
			
			if (isset($row['theme']) AND $theme_change_on == 1 AND ($chat_maintenance != 1 OR ($admin_view_maintenance == 1 AND $is_admin == 1)))
			{
				$theme 	= $row['theme'];
			}	
			
			if (!empty($row['unfocus_chat'])) 
			{
				$unfocus_chat = $row['unfocus_chat'];
				$unfocus_chat = explode(":", $unfocus_chat);
			}
			
			if (!empty($row['hash_id'])) 
			{
				$hash_id = $row['hash_id'];
			} 
			else 
			{
				$hash_id = random_string();
				
				$db->execute("
					UPDATE arrowchat_status 
					SET hash_id='" . $hash_id . "' 
					WHERE userid = '" . $db->escape_string($userid) . "'
				");
			}
			
			if (!empty($row['focus_chat']))
			{
				$focus_chat = $row['focus_chat'];
			}
			
			if (!empty($row['apps_bookmarks']))
			{
				$apps_bookmarks = $row['apps_bookmarks'];
			}
			
			if (!empty($row['apps_other']))
			{
				$apps_other = $row['apps_other'];
			}
			
			if (!empty($row['status']))
			{
				$status = $row['status'];
			}
			
			if (!empty($row['block_chats']))
			{
				$block_chats = $row['block_chats'];
			}
			
			if ($row['ip_address'] != $user_ip)
			{
				$db->execute("
					UPDATE arrowchat_status 
					SET ip_address =  '" . $db->escape_string($user_ip) . "'
					WHERE userid = '" . $db->escape_string($userid) . "'
				");
			}
		} 
		else 
		{
			$hash_id = random_string();
			
			$db->execute("
				INSERT INTO arrowchat_status (userid, chatroom_window, chatroom_stay, hash_id, session_time, ip_address) 
				VALUES ('" . $db->escape_string($userid) . "', '-1', '0', '" . $hash_id . "', '" . time() . "', '" . $db->escape_string($user_ip) . "')
			");
		}
		
	}

?>