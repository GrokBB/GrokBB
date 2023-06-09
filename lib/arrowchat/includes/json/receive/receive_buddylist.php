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
	$messages 	= array();
	$buddyList 	= array();
	$time 		= time();
	
	if (var_check('popout'))
	{
		$popout = true;
	}
	else
	{
		$popout = false;
	}
	
	if (var_check('mobile'))
	{
		if ($status != 'busy')
		{
			$db->execute("
				UPDATE arrowchat_status 
				SET status = 'busy' 
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}
	}

	// ###################### START BUDDY LIST RECEIVE #######################
	if (logged_in($userid)) 
	{
		// Refresh the user's session
		updateUserSession();
		
		if ($disable_buddy_list == 1 OR check_if_guest($userid) OR NO_FREIND_SYSTEM == 1 OR ($is_admin == 1 AND $admin_chat_all == 1) OR ($is_mod == 1 AND $admin_chat_all == 1))
		{
			$sql = get_online_list($userid,$time);
		} 
		else 
		{
			$sql = get_friend_list($userid,$time);
		}
		
		$result = $db->execute($sql);

		while ($chat = $db->fetch_array($result)) 
		{
			if ($chat['userid'] != $userid) 
			{
				if ((($time-$chat['lastactivity']) < $online_timeout) AND $chat['status'] != 'invisible' AND $chat['status'] != 'offline') 
				{
					if ($chat['status'] != 'busy' AND $chat['status'] != 'away') 
					{
						$chat['status'] = 'available';
					}
				} 
				else 
				{
					if ($chat['status'] == 'invisible') 
					{
						if ($is_admin == 1 OR $is_mod == 1)
						{
							$chat['status'] = 'available';
						}
					} 
					else
					{
						$chat['status'] = 'offline';
					}
				}
				
				$link = get_link($chat['link'], $chat['userid']);
				$avatar = get_avatar($chat['avatar'], $chat['userid']);
				
				if (!empty($block_chats))
				{
					$block_chats_unserialized = unserialize($block_chats);
					
					if (!is_array($block_chats_unserialized))
					{
						$block_chats_unserialized = array();
					}
				}
				else
				{
					$block_chats_unserialized = array();
				}
				
				// Determine if the user should be displayed
				$show_user = false;
				if (check_if_guest($userid))
				{
					if ($guests_chat_with == 1)
					{
						if (check_if_guest($chat['userid']))
							$show_user = true;
					}
					else if ($guests_chat_with == 2)
					{
						$show_user = true;
					}
					else if ($guests_chat_with == 3)
					{
						if (!check_if_guest($chat['userid']))
							$show_user = true;
					}
					else if ($guests_chat_with == 4)
					{
						if ($chat['is_admin'] == 1)
							$show_user = true;
					}
					else
					{
						$show_user = true;
					}
				}
				else
				{
					if ($users_chat_with == 1)
					{
						if (check_if_guest($chat['userid']))
							$show_user = true;
					}
					else if ($users_chat_with == 2)
					{
						$show_user = true;
					}
					else if ($users_chat_with == 3)
					{
						if (!check_if_guest($chat['userid']))
							$show_user = true;
					}
					else if ($guests_chat_with == 4)
					{
						if ($chat['is_admin'] == 1)
							$show_user = true;
					}
					else
					{
						$show_user = true;
					}
				}
				
				if ($hide_admins_buddylist == 1 AND $chat['is_admin'] == 1)
				{
					$show_user = false;
				}
				
				if (($is_admin == 1 AND $admin_chat_all == 1) OR ($is_mod == 1 AND $admin_chat_all == 1))
				{
					$show_user = true;
				}
				
				if (!in_array($chat['userid'], $block_chats_unserialized))
				{
					if (!empty($chat['username']) AND $show_user)
					{
						$buddyList[] = array('id' => $chat['userid'], 'n' => stripslashes($db->escape_string(strip_tags($chat['username']))), 's' => $chat['status'], 'a' => $avatar, 'l' => $link, 'admin' => $chat['is_admin']);
					}
				}
			}
		}
		
		if ($guests_can_chat == 1) 
		{
			$guest_sql = get_guest_list();
			$result = $db->execute($guest_sql);
			
			while ($chat = $db->fetch_array($result)) 
			{
				if ($chat['userid'] != $userid) 
				{
					if ((($time-$chat['lastactivity']) < $online_timeout) AND $chat['status'] != 'invisible' AND $chat['status'] != 'offline') 
					{
						if ($chat['status'] != 'busy' AND $chat['status'] != 'away') 
						{
							$chat['status'] = 'available';
						}
					} 
					else 
					{
						if ($chat['status'] == 'invisible') 
						{
							if ($is_admin == 1 OR $is_mod == 1)
							{
								$chat['status'] = 'available';
							}
						} 
						else
						{
							$chat['status'] = 'offline';
						}
					}
					
					$link = "#";
					$avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
					$chat['username'] = create_guest_username($chat['userid'], $chat['guest_name']);
					
					if (!empty($block_chats))
					{
						$block_chats_unserialized = unserialize($block_chats);
						
						if (!is_array($block_chats_unserialized))
						{
							$block_chats_unserialized = array();
						}
					}
					else
					{
						$block_chats_unserialized = array();
					}
					
					// Determine if the user should be displayed
					$show_user = false;
					if (check_if_guest($userid))
					{
						if ($guests_chat_with == 1)
						{
							if (check_if_guest($chat['userid']))
								$show_user = true;
						}
						else if ($guests_chat_with == 2)
						{
							$show_user = true;
						}
						else if ($guests_chat_with == 3)
						{
							if (!check_if_guest($chat['userid']))
								$show_user = true;
						}
						else if ($guests_chat_with == 4)
						{
							if ($chat['is_admin'] == 1)
								$show_user = true;
						}
						else
						{
							$show_user = true;
						}
					}
					else
					{
						if ($users_chat_with == 1)
						{
							if (check_if_guest($chat['userid']))
								$show_user = true;
						}
						else if ($users_chat_with == 2)
						{
							$show_user = true;
						}
						else if ($users_chat_with == 3)
						{
							if (!check_if_guest($chat['userid']))
								$show_user = true;
						}
						else if ($guests_chat_with == 4)
						{
							if ($chat['is_admin'] == 1)
								$show_user = true;
						}
						else
						{
							$show_user = true;
						}
					}
					
					if ($hide_admins_buddylist == 1 AND $chat['is_admin'] == 1)
					{
						$show_user = false;
					}
					
					if (($is_admin == 1 AND $admin_chat_all == 1) OR ($is_mod == 1 AND $admin_chat_all == 1))
					{
						$show_user = true;
					}
					
					if (!in_array($chat['userid'], $block_chats_unserialized))
					{
						if (!empty($chat['username']) AND $show_user)
						{
							$buddyList[] = array('id' => $chat['userid'], 'n' => stripslashes($db->escape_string(strip_tags($chat['username']))), 's' => $chat['status'], 'a' => $avatar, 'l' => $link, 'admin' => $chat['is_admin']);
						}
					}
				}
			}
		}

		$response['buddylist'] = $buddyList;
	}

	header('Content-type: application/json; charset=UTF-8');
	echo json_encode($response);
	close_session();
	exit;

?>