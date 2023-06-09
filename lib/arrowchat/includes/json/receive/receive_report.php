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
	$response 		= array();
	$reports		= array();
	$report_info	= array();
	$error			= array();
	$user_cache		= array();
	$user_cache2	= array();
	$temp_array		= array();
	$chat_history 	= array();
	$time 			= time();
	$report_id		= get_var('reportid');

	// ##################### START CHATROOM LIST RECEIVE #####################
	if (logged_in($userid) AND ($is_admin == 1 OR $is_mod == 1))
	{	
		// Get the report information
		$result = $db->execute("
			SELECT *
			FROM arrowchat_reports
			WHERE (working_time < (" . time() . " - 600)
						OR working_by = '" . $db->escape_string($userid) . "')
				AND completed_time = 0
				AND id = '" . $db->escape_string($report_id) . "'
		");
		
		if ($row = $db->fetch_array($result))
		{
			$id = $row['id'];
			$report_about = $row['report_about'];
			$report_from = $row['report_from'];
			$report_time = $row['report_time'];
			$report_chatroom = $row['report_chatroom'];
			
			$fetchid = $row['report_from'];
			
			if (check_if_guest($fetchid))
			{
				$sql = get_guest_details($fetchid);
				$result2 = $db->execute($sql);
				$user = $db->fetch_array($result2);
				
				$from_name = create_guest_username($user['userid'], $user['guest_name']);
			}
			else
			{
				$sql = get_user_details($fetchid);
				$result3 = $db->execute($sql);
				$user = $db->fetch_array($result3);
				
				$from_name = $user['username'];
			}
			
			$fetchid = $row['report_about'];
			
			if (check_if_guest($fetchid))
			{
				$sql = get_guest_details($fetchid);
				$result2 = $db->execute($sql);
				$user = $db->fetch_array($result2);
				
				$about_name = create_guest_username($user['userid'], $user['guest_name']);
			}
			else
			{
				$sql = get_user_details($fetchid);
				$result3 = $db->execute($sql);
				$user = $db->fetch_array($result3);
				
				$about_name = $user['username'];
			}
			
			// Get past warnings
			$result4 = $db->execute("
				SELECT COUNT(id)
				FROM arrowchat_warnings
				WHERE user_id = '" . $db->escape_string($report_about) . "'
			");
			
			if ($row4 = $db->fetch_array($result4))
				$previous_warnings = $row4['COUNT(id)'];
			else
				$previous_warnings = 0;
			
			$report_info[] = array('id' => $id, 'from' => $report_from, 'from_name' =>  $from_name, 'about' => $report_about, 'about_name' => $about_name, 'previous_warnings' => $previous_warnings, 'time' => relative_time($report_time), 'unix' => $report_time);
		}
		else
		{
			// Report is already completed or being worked on, send error
			$error[] = array('t' => '1', 'm' => $language[182]);
			
			$response['error'] = $error;
				
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response);
			close_session();
			exit;
		}
		
		// Mark this user as working on the ticket
		$db->execute("
			UPDATE arrowchat_reports
			SET working_time = " . time() . ",
				working_by = '" . $db->escape_string($userid) . "'
			WHERE report_about = '" . $db->escape_string($report_about) . "'
				AND completed_time = 0
		");
		
		// Return the list of reports about the user
		$result = $db->execute("
			SELECT id, report_time
			FROM arrowchat_reports
			WHERE report_about = '" . $db->escape_string($report_about) . "'
				AND completed_time = 0
			ORDER BY report_time ASC
		");

		while ($row = $db->fetch_array($result)) 
		{
			$reports[] = array('id' => $row['id'], 'time' => relative_time($row['report_time']));
		}
		
		if (!empty($report_chatroom))
		{
			// Get the last ID before the report
			$last_id = 0;
			$result = $db->execute("
				SELECT id
				FROM arrowchat_chatroom_messages
				WHERE (chatroom_id = '" . $db->escape_string($report_chatroom) . "'
					AND sent <= " . $report_time . "
					AND action = 0)
				ORDER BY sent DESC
				LIMIT 1
			");
			if ($row = $db->fetch_array($result))
				$last_id = $row['id'];
			
			// Get Chat room history around report time +/- 50 messages
			$result = $db->execute("
				SELECT id, username, message, sent, user_id, global_message, is_mod, is_admin
				FROM arrowchat_chatroom_messages
				WHERE (chatroom_id = '" . $db->escape_string($report_chatroom) . "'
					AND id <= " . $last_id . "
					AND action = 0)
				ORDER BY sent DESC
				LIMIT 50
			");
			
			while ($chatroom_history = $db->fetch_array($result))
			{
				$temp_array[] = $chatroom_history;
			}
			
			$temp_array = array_reverse($temp_array);
			
			$result = $db->execute("
				SELECT id, username, message, sent, user_id, global_message, is_mod, is_admin
				FROM arrowchat_chatroom_messages
				WHERE (chatroom_id = '" . $db->escape_string($report_chatroom) . "'
					AND id > " . $last_id . "
					AND action = 0)
				ORDER BY sent ASC
				LIMIT 50
			");
			
			while ($chatroom_history = $db->fetch_array($result))
			{
				$temp_array[] = $chatroom_history;
			}
			
			foreach ($temp_array as $chatroom_history)
			{
				$fetchid = $chatroom_history['user_id'];
				$chat_message = $chatroom_history['message'];
				$chat_message = str_replace("\\'", "'", $chat_message);
				$chat_message = str_replace('\\"', '"', $chat_message);
				$chat_message = clickable_links($chat_message);
				
				if (array_key_exists($fetchid, $user_cache))
				{
					$avatar = $user_cache[$fetchid];
				}
				else
				{
					$sql = get_user_details($fetchid);
					$result2 = $db->execute($sql);
					$user = $db->fetch_array($result2);
					$avatar	= get_avatar($user['avatar'], $fetchid);
					$user_cache[$fetchid] = $avatar;
				}
				
				$reportee = 0;
				if ($fetchid == $report_about)
					$reportee = 1;
				
				$chat_history[] = array('id' => $chatroom_history['id'], 'n' => $db->escape_string(strip_tags($chatroom_history['username'])), 'm' => $chat_message, 't' => $chatroom_history['sent'], 'a' => $avatar, 'userid' => $fetchid, 'global' => $chatroom_history['global_message'], 'mod' => $chatroom_history['is_mod'], 'admin' => $chatroom_history['is_admin'], 'reportee' => $reportee);
			}
		}
		else
		{
			// Get the last ID before the report
			$last_id = 0;
			$result = $db->execute("
				SELECT arrowchat.id
				FROM arrowchat
				WHERE ((arrowchat.to = '" . $db->escape_string($report_about) . "' 
							AND arrowchat.from = '" . $db->escape_string($report_from) . "') 
						OR (arrowchat.from = '" . $db->escape_string($report_about) . "' 
							AND arrowchat.to = '" . $db->escape_string($report_from) . "'))
					AND arrowchat.sent <= " . $report_time . "
				ORDER BY arrowchat.id DESC
				LIMIT 1
			");
			if ($row = $db->fetch_array($result))
				$last_id = $row['id'];
				
			// Get private chat history less than 50 messages from report
			$result = $db->execute("
				SELECT arrowchat.id, arrowchat.from, arrowchat.to, arrowchat.message, arrowchat.sent, arrowchat.read
				FROM arrowchat
				WHERE ((arrowchat.to = '" . $db->escape_string($report_about) . "' 
							AND arrowchat.from = '" . $db->escape_string($report_from) . "') 
						OR (arrowchat.from = '" . $db->escape_string($report_about) . "' 
							AND arrowchat.to = '" . $db->escape_string($report_from) . "'))
					AND arrowchat.id <= " . $last_id . "
				ORDER BY arrowchat.sent DESC
				LIMIT 50
			");
			
			while ($chatroom_history = $db->fetch_array($result))
			{
				$temp_array[] = $chatroom_history;
			}
			
			$temp_array = array_reverse($temp_array);
			
			// Get private chat history greater than 50 messages from report
			$result = $db->execute("
				SELECT arrowchat.id, arrowchat.from, arrowchat.to, arrowchat.message, arrowchat.sent, arrowchat.read
				FROM arrowchat
				WHERE ((arrowchat.to = '" . $db->escape_string($report_about) . "' 
							AND arrowchat.from = '" . $db->escape_string($report_from) . "') 
						OR (arrowchat.from = '" . $db->escape_string($report_about) . "' 
							AND arrowchat.to = '" . $db->escape_string($report_from) . "'))
					AND arrowchat.id > " . $last_id . "
				ORDER BY arrowchat.sent ASC
				LIMIT 50
			");
			
			while ($chatroom_history = $db->fetch_array($result))
			{
				$temp_array[] = $chatroom_history;
			}
			
			foreach ($temp_array as $chatroom_history)
			{
				$fetchid = $chatroom_history['from'];
				$chat_message = $chatroom_history['message'];
				$chat_message = str_replace("\\'", "'", $chat_message);
				$chat_message = str_replace('\\"', '"', $chat_message);
				$chat_message = clickable_links($chat_message);
				
				if (array_key_exists($fetchid, $user_cache))
				{
					$avatar = $user_cache[$fetchid];
					$username = $user_cache2[$fetchid];
				}
				else
				{
					if (check_if_guest($fetchid))
					{
						$sql = get_guest_details($fetchid);
						$result2 = $db->execute($sql);
						$user = $db->fetch_array($result2);
						
						$avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
						$user_cache[$fetchid] = $avatar;
						$username = create_guest_username($user['userid'], $user['guest_name']);
						$user_cache2[$fetchid] = $username;
					}
					else
					{
						$sql = get_user_details($fetchid);
						$result3 = $db->execute($sql);
						$user = $db->fetch_array($result3);
						
						$avatar	= get_avatar($user['avatar'], $fetchid);
						$user_cache[$fetchid] = $avatar;
						$username = $user['username'];
						$user_cache2[$fetchid] = $username;
					}
				}
				
				$reportee = 0;
				if ($fetchid == $report_about)
					$reportee = 1;
				
				$chat_history[] = array('id' => $chatroom_history['id'], 'n' => $db->escape_string(strip_tags($username)), 'm' => $chat_message, 't' => $chatroom_history['sent'], 'a' => $avatar, 'userid' => $fetchid, 'global' => 0, 'mod' => 0, 'admin' => 0, 'reportee' => $reportee);
			}
		}
		
		$response['report_info'] = $report_info;
		$response['reports'] = $reports;
		$response['report_history'] = $chat_history;
	}
	else
	{
		echo 1;
		close_session();
		exit;
	}

	header('Content-type: application/json; charset=UTF-8');
	echo json_encode($response);
	close_session();
	exit;

?>