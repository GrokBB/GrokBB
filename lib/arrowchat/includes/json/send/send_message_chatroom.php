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
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'functions_send.php');

	// ########################### GET POST DATA #############################
	$chatroomid = get_var('chatroomid');
	$message 	= get_var('message');
	$s_message	= sanitize($message);
	
	// Get the username of the user sending the message
	if (check_if_guest($userid))
	{
		$username = strip_tags(create_guest_username($userid, $guest_name));
	}
	else
	{
		$username = strip_tags(get_username($userid));
	}

	// ######################### START POST MESSAGE ##########################
	if (!empty($_POST['message']) && strlen($_POST['message']) <= $chatroom_message_length) 
	{
		if (logged_in($userid)) 
		{
			$chatroom_admin = 0;
			$chatroom_mod = 0;
			
			// Start Message Limit
			$result = $db->execute("
				SELECT limit_message_num, limit_seconds_num
				FROM arrowchat_chatroom_rooms
				WHERE id = '" . $db->escape_string($chatroomid) . "'
			");
			
			$row = $db->fetch_array($result);
			$limit_message_num = $row['limit_message_num'] - 1;
			$limit_seconds_num = $row['limit_seconds_num'];
			
			$result = $db->execute("
				SELECT sent
				FROM arrowchat_chatroom_messages
				WHERE user_id = '" . $db->escape_string($userid) . "'
					AND chatroom_id = '" . $db->escape_string($chatroomid) . "'
				ORDER BY sent desc
				LIMIT " . $db->escape_string($limit_message_num) . ", 1
			");
			
			$first_message = 0;
			$messages_are_limited = false;
			
			if ($row = $db->fetch_array($result))
			{
				$first_message = $row['sent'];
			}
			
			if (time() - $first_message <= $limit_seconds_num)
			{
				$messages_are_limited = true;
				$time_to_talk = $limit_seconds_num - (time() - $first_message);
			}
			// End Message Limit
			
			$result = $db->execute("
				SELECT is_admin, is_mod, silence_length, silence_time
				FROM arrowchat_chatroom_users
				WHERE user_id = '" . $db->escape_string($userid) . "'
					AND chatroom_id = '" . $db->escape_string($chatroomid) . "'
			");
			
			if ($row = $db->fetch_array($result))
			{
				if ($row['is_admin'] == 1 OR $is_admin == 1) 
				{
					$chatroom_admin = 1;
					$messages_are_limited = false;
				}
					
				if ($row['is_mod'] == 1 OR $is_mod == 1)
				{
					$chatroom_mod = 1;
					$messages_are_limited = false;
				}
			}
			
			if (!$messages_are_limited)
			{
				if (empty($row['silence_time']) OR $row['silence_time'] + $row['silence_length'] < time())
				{
					$db->execute("
						INSERT INTO arrowchat_chatroom_messages (
							chatroom_id,
							user_id,
							username,
							message,
							global_message,
							is_mod,
							is_admin,
							sent
						) 
						VALUES (
							'" . $db->escape_string($chatroomid) . "', 
							'" . $db->escape_string($userid) . "', 
							'" . $db->escape_string($username) . "',
							'" . $db->escape_string($s_message) . "',
							'0',
							'" . $db->escape_string($chatroom_mod) . "',
							'" . $db->escape_string($chatroom_admin) . "',
							'" . time() . "'
						)
					");
					
					$last_id = $db->last_insert_id();
					
					// Update message history totals
					$result = $db->execute("
						SELECT sent
						FROM arrowchat_chatroom_messages
						ORDER BY id DESC
						LIMIT 1, 1
					");
					
					$date = time();
					$insert_date = date('Ymd', $date);
					
					if ($row = $db->fetch_array($result))
					{
						$last_date = date('Ymd', $row['sent']);
						
						if ($last_date != $insert_date && !empty($last_date))
						{
							$date1 = strtotime( $last_date );
							$date2 = strtotime( $insert_date );
							
							$days = count_days($date1, $date2);
							for ($i = 0; $i < $days; $i++) {
								$db->execute("
									INSERT INTO arrowchat_graph_log (
										date,
										chat_room_messages
									) 
									VALUES (
										'" . date('Ymd', $date1+(86400*$i)) . "',
										'0'
									) 
									ON DUPLICATE KEY 
										UPDATE chat_room_messages = chat_room_messages
								");	
							}
						}
						
						$db->execute("
							INSERT INTO arrowchat_graph_log (
								date,
								chat_room_messages
							) 
							VALUES (
								'" . $db->escape_string($insert_date) . "',
								'1'
							) 
							ON DUPLICATE KEY 
								UPDATE chat_room_messages = (chat_room_messages + 1)
						");
					}
					else
					{
						$db->execute("
							INSERT INTO arrowchat_graph_log (
								date,
								chat_room_messages
							) 
							VALUES (
								'" . $db->escape_string($insert_date) . "',
								'1'
							) 
						");
					}
					
					if ($push_on == 1)
					{
						$arrowpush->publish(array(
							'channel' => 'chatroom' . $chatroomid,
							'message' => array('chatroommessage' => array("id" => $last_id, "name" => $username, "message" => $s_message, "userid" => $userid, "sent" => time(), "global" => '0', "mod" => $chatroom_mod, "admin" => $chatroom_admin, "chatroomid" => $chatroomid))
						));
					}

					echo $last_id;
				}
				else
				{
					$silence_time = $row['silence_time'] + $row['silence_length'] - time();
					$silence_message = $language[164] . $silence_time . $language[165];
					
					$error[] = array('t' => '1', 'm' => $silence_message);
					$response['error'] = $error;
					header('Content-type: application/json; charset=utf-8');
					echo json_encode($response);
				}
			} else {
				$flood_message = $language[169] . $time_to_talk . $language[170];
				
				$error[] = array('t' => '1', 'm' => $flood_message);
				$response['error'] = $error;
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
			}
			
			close_session();
			exit(0);
		}
	}

?>