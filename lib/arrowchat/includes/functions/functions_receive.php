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

	/**
	 *
	 * Gets new notifications
	 *
	*/
	function getNotifications() 
	{
		global $response;
		global $userid;
		global $db;
		global $markup;
			
		if (empty($_SESSION['notifytime']) OR (!empty($_SESSION['notifytime']) AND (time()-$_SESSION['notifytime'] > 30))) 
		{
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_notifications 
				WHERE to_id='" . $db->escape_string($userid) . "' 
					AND (alert_read != '1' OR user_read != '1') 
				ORDER BY alert_time DESC
			");
			
			while ($row = $db->fetch_array($result)) 
			{
				$alert_id		= $row['id'];
				$author_id 		= $row['author_id'];
				$author_name 	= $row['author_name'];
				$type 			= $row['type'];
				$message_time	= $row['alert_time'];
				$misc1			= $row['misc1'];
				$misc2			= $row['misc2'];
				$misc3			= $row['misc3'];
				$test = true;
				
				if (isset($_SESSION['notifications'])) 
				{
					foreach ($_SESSION['notifications'] as $vals) 
					{
						if (in_array($alert_id, $vals)) 
						{
							$test = false;
						}
					}
				}
				
				if ($test OR !isset($_SESSION['notifications'])) 
				{
					$markup = get_markup($author_id, $author_name, $type, $message_time, $misc1, $misc2, $misc3);
					
					$db->execute("
						UPDATE arrowchat_notifications
						SET alert_read = '1'
						WHERE id = '" . $alert_id . "'
					");
					
					$notifications[] = array('alert_id' => $alert_id, 'markup' => $markup, 'type' => $type);
				}
				
				$_SESSION['notifications'][] = array($alert_id);
			}
			
			$_SESSION['notifytime'] = time();
				
			if (!empty($notifications)) 
			{
				$response['notifications'] = $notifications;
			}
		}
	}
	
	/**
	 *
	 * Gets what users are currently typing to the user
	 *
	*/
	function getTyping() 
	{
		global $db;
		global $response;
		global $userid;
		global $typing;
		
		$result = $db->execute("
			SELECT typing 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if (!is_array($_SESSION['typing']))
		{
			$_SESSION['typing'] = array();
		}
		
		if (!is_array($_SESSION['not_typing']))
		{
			$_SESSION['not_typing'] = array();
		}
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			$old_data = $row['typing'];
			
			if (!empty($old_data)) 
			{
				$matches = explode(":", $old_data);
				
				foreach ($matches as $val) 
				{
					if (!empty($val)) 
					{
						$typing_data = explode("/", $val);
						preg_match("#[0-9,a-z]+#", $typing_data[0], $typing_ids);
						$timer = $typing_data[1] + 60;
						
						if ($typing_data[1] == "0" OR time() > $timer) 
						{
							// If the user is not typing
							if (!in_array($typing_ids[0], $_SESSION['not_typing'])) 
							{
								$typing[] = array('typing_id' => $typing_ids[0], 'is_typing' => '-1');
								$_SESSION['not_typing'][] = $typing_ids[0];
							}

							foreach ($_SESSION['typing'] as $index => $val) 
							{
								if ($val == $typing_ids[0])
								{
									unset($_SESSION['typing'][$index]);
								}
							}
						} 
						else 
						{
							// If the user is typing
							if (!in_array($typing_ids[0], $_SESSION['typing'])) 
							{
								$typing[] = array('typing_id' => $typing_ids[0], 'is_typing' => '1');
								$_SESSION['typing'][] = $typing_ids[0];
							}

							foreach ($_SESSION['not_typing'] as $index => $val) 
							{
								if ($val == $typing_ids[0])
								{
									unset($_SESSION['not_typing'][$index]);
								}
							}
						}
					}
				}
			}
		}
		
		if (!empty($typing)) 
		{
			$response['typing'] = $typing;
		}
	}
	
	/**
	 *
	 * Gets any announcements from the administrator
	 *
	*/
	function getAnnouncements() 
	{
		global $db;
		global $response;
		global $userid;
		global $announcement;

		if (empty($_SESSION['announcetime']) OR (!empty($_SESSION['announcetime']) AND (time()-$_SESSION['announcetime'] > 60))) 
		{
			$result = $db->execute("
				SELECT arrowchat_config.config_value announce, arrowchat_status.announcement readstatus 
				FROM arrowchat_status, arrowchat_config 
				WHERE arrowchat_status.userid='" . $db->escape_string($userid) . "'
					AND arrowchat_config.config_name='announcement'
			");

			$row = $db->fetch_array($result);

			if (!empty($row['announce']) AND empty($row['readstatus'])) 
			{
				$announcement[] = array('data' => $row['announce'], 'read' => $row['readstatus']);
			}

			$_SESSION['announcetime'] = time();	
			$response['announcements'] = $announcement;
		}
	}
	
	/**
	 *
	 * Gets any warnings
	 *
	*/
	function getWarnings() 
	{
		global $db;
		global $response;
		global $userid;
		global $warnings;

		if (empty($_SESSION['warntime']) OR (!empty($_SESSION['warntime']) AND (time()-$_SESSION['warntime'] > 60))) 
		{
			$result = $db->execute("
				SELECT warn_reason, user_read
				FROM arrowchat_warnings
				WHERE user_id = '" . $db->escape_string($userid) . "'
					AND user_read = '0'
				ORDER BY warning_time DESC
				LIMIT 1
			");

			$row = $db->fetch_array($result);

			if (!empty($row['warn_reason']) AND empty($row['user_read'])) 
			{
				$warnings[] = array('data' => $row['warn_reason'], 'read' => $row['user_read']);
			}

			$_SESSION['warntime'] = time();	
			$response['warnings'] = $warnings;
		}
	}
	
	/**
	 *
	 * Gets new messages in the chat room
	 *
	*/
	function getChatroom() 
	{
		global $db;
		global $response;
		global $userid;
		global $time;
		global $chatroom;
		global $chatroomid;
		global $long_polling;
		
		if (!is_array($_SESSION['chatroom_mess_ids']))
		{
			$_SESSION['chatroom_mess_ids'] = array();
		}
			
		if (!$long_polling AND isset($_SESSION['chatroom_time'])) 
		{
			$time2 = $_SESSION['chatroom_time'];
		} 
		else 
		{
			$time2 = $time;
		}
	
		$result = $db->execute("
			SELECT m.id, m.username, m.message, m.sent, m.global_message, m.user_id, m.is_mod, m.is_admin, m.action 
			FROM arrowchat_chatroom_messages AS m 
			JOIN arrowchat_chatroom_users AS u 
				ON u.user_id = '" . $db->escape_string($userid) . "'       
				AND u.chatroom_id = '" . $db->escape_string($chatroomid) . "' 
				AND u.session_time != 0 
			WHERE m.chatroom_id = '" . $db->escape_string($chatroomid) . "'      
				AND " . $db->escape_string($time2) . " <= m.sent       
				AND '" . $db->escape_string($userid) . "' != m.user_id  
			ORDER BY m.sent ASC
		");
		
		while ($row = $db->fetch_array($result)) 
		{
			if (!in_array($row['id'], $_SESSION['chatroom_mess_ids'])) 
			{
				$chat_message = $row['message'];
				$chat_message = str_replace("\\'", "'", $chat_message);
				$chat_message = str_replace('\\"', '"', $chat_message);
				$chat_message = clickable_links($chat_message);
			
				$chatroom[] = array('id' => $row['id'], 'userid' => $row['user_id'], 'n' => $row['username'], 'm' => $chat_message, 't' => $row['sent'], 'global' => $row['global_message'],'mod' => $row['is_mod'], 'admin' => $row['is_admin'], "chatroomid" => $chatroomid, 'action' => $row['action']);
				$_SESSION['chatroom_mess_ids'][] = $row['id'];
			}
		}
		
		if (!$long_polling) 
		{
			$_SESSION['chatroom_time'] = time();
		}
		
		if (!empty($chatroom)) 
		{
			$response['chatroom'] = $chatroom;
		}
	}
	
	/**
	 *
	 * Gets new messages from users
	 *
	*/
	function fetchMessages() 
	{
		global $db;
		global $response;
		global $userid;
		global $messages;
		global $last_message;
		global $block_chats;
		global $heart_beat;
		
		$time_it = time();
		
		$result = $db->execute("
			SELECT arrowchat.id, arrowchat.from, arrowchat.to, arrowchat.message, arrowchat.sent, arrowchat.read, arrowchat.user_read 
			FROM arrowchat 
			WHERE arrowchat.to = '" . $db->escape_string($userid) . "' 
				AND (arrowchat.read != 1 
					OR arrowchat.user_read != 1
					OR arrowchat.sent > (" . $time_it . " - " . $db->escape_string($heart_beat) . " - 1))
			ORDER BY arrowchat.id ASC
		");

		$id_check = 0;
		$messages_test = false;
		
		if (!is_array($_SESSION['tab_alert']))
		{
			$_SESSION['tab_alert'] = array();
		}
		
		while ($chat = $db->fetch_array($result)) 
		{
			$self = 0;
			$old = 0;
			
			if ($chat['from'] == $userid) 
			{
				$chat['from'] = $chat['to'];
				$self = 1;
				$old = 1;
			}

			if ($chat['user_read'] == 1 AND $chat['read'] == 1) 
			{
				$old = 1;
			}
			
			$chatid = $chat['from'];
			
			if (!empty($last_message)) 
			{
				if (preg_match("#:$chatid/[0-9,a-z]+#", $last_message, $matches)) 
				{
					$matches2 = explode("/", $matches[0]);
					$num = (int)$matches2[1];
					$time_check = time();
					
					if ($num < $time_check AND $chat['read'] != 1 AND $chat['user_read'] != 1) 
					{
						$last_message = str_replace($matches[0], ":".$chatid."/".time()."", $last_message);
					} 
					else 
					{
						$last_message = $last_message;
					}
				} 
				else 
				{
					$last_message .= ":".$chatid."/".time();
				}
			} 
			else 
			{
				$last_message = ":".$chatid."/".time();
			}
			
			$chat_message = $chat['message'];
			$chat_message = str_replace("\\'", "'", $chat_message);
			$chat_message = str_replace('\\"', '"', $chat_message);
			$chat_message = clickable_links($chat_message);
			
			$block_chats_unserialized = unserialize($block_chats);
		
			if (!is_array($block_chats_unserialized))
			{
				$block_chats_unserialized = array();
			}

			if ( !in_array($chat['id'], $_SESSION['tab_alert']) OR ( ( $chat['sent'] + $heart_beat) > $time_it AND in_array( $chat['id'], $_SESSION['tab_alert'] ) ) )
			{
				if ($old != 1)
				{
					if (!in_array($chat['from'], $block_chats_unserialized))
					{
						$messages[] = array('id' => $chat['id'], 'from' => $chat['from'], 'message' => $chat_message, 'self' => $self, 'old' => $old, 'sent' => $chat['sent']);
						$messages_test = true;
						$_SESSION['tab_alert'][] = $chat['id'];
					}
				}
			}
			
			$id_check = $chat['id'];
		}	
		
		if ($messages_test) 
		{
			$response['messages'] = $messages;
			
			$db->execute("
				UPDATE arrowchat 
				SET arrowchat.read = '1' 
				WHERE arrowchat.to = '" . $db->escape_string($userid) . "' 
					AND arrowchat.id <= '" . $db->escape_string($id_check) . "'
					AND arrowchat.read = '0'
			");

			$db->execute("
				UPDATE arrowchat_status 
				SET last_message = '" . $db->escape_string($last_message) . "' 
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}
	}
	
	/**
	 *
	 * Check if the popout chat is active
	 *
	*/
	function checkPopout() 
	{
		global $response;
		global $popout_time;
		
		if ($popout_time == "99") 
		{
			$response['popout'] = "99";
		}
	}
	
?>