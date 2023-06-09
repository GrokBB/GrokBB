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
	$to 		= get_var('to');
	$message 	= get_var('message');

	// ######################### START POST MESSAGE ##########################
	if (!empty($_POST['to']) AND !empty($_POST['message'])) 
	{
		if (logged_in($userid)) 
		{
			$result = $db->execute("
				SELECT block_chats
				FROM arrowchat_status
				WHERE userid = '" . $db->escape_string($to) . "'
			");
			
			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				
				$block_chats_unserialized = unserialize($row['block_chats']);
				
				if (!is_array($block_chats_unserialized))
				{
					$block_chats_unserialized = array();
				}
				
				if (in_array($userid, $block_chats_unserialized))
				{
					echo "-1";
					close_session();
					exit(0);
				}
			}
			
			$db->execute("
				INSERT INTO arrowchat (
					arrowchat.from,
					arrowchat.to,
					arrowchat.message,
					arrowchat.sent,
					arrowchat.read
				) 
				VALUES (
					'" . $db->escape_string($userid) . "', 
					'" . $db->escape_string($to) . "',
					'" . $db->escape_string(sanitize($message)) . "', 
					'" . time() . "', 
					'0'
				)
			");
			
			$last_id = $db->last_insert_id();
			
			// Update message history totals
			$result = $db->execute("
				SELECT sent
				FROM arrowchat
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
								user_messages
							) 
							VALUES (
								'" . date('Ymd', $date1+(86400*$i)) . "',
								'0'
							) 
							ON DUPLICATE KEY 
								UPDATE user_messages = user_messages
						");	
					}
				}
				
				$db->execute("
					INSERT INTO arrowchat_graph_log (
						date,
						user_messages
					) 
					VALUES (
						'" . $db->escape_string($insert_date) . "',
						'1'
					) 
					ON DUPLICATE KEY 
						UPDATE user_messages = (user_messages + 1)
				");
			}
			else
			{
				$db->execute("
					INSERT INTO arrowchat_graph_log (
						date,
						user_messages
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
					'channel' => 'u' . $_POST['to'],
					'message' => array('messages' => array("id" => $last_id, "from" => $userid, "message" => sanitize($message), "sent" => time(), "self" => "0", "old" => "0"))
				));
				
				$arrowpush->publish(array(
					'channel' => 'u' . $userid,
					'message' => array('messages' => array("id" => $last_id, "from" => $_POST['to'], "message" => sanitize($message), "sent" => time(), "self" => "1", "old" => "0"))
				));
			}

			echo $last_id;
			close_session();
			exit(0);
		}
	}

?>