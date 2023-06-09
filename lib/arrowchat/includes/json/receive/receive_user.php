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
	$time 		= time();

	// ########################### GET POST DATA #############################
	$fetchid = get_var('userid');

	// ###################### START USER DATA RECEIVE ########################
	if (!empty($fetchid)) 
	{
		$sql = get_user_details($fetchid);
		$result = $db->execute($sql);
		
		if ($result AND $db->count_select() > 0) 
		{
			$chat = $db->fetch_array($result);

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
				
				} 
				else
				{
					$chat['status'] = 'offline';
				}
			}

			$link = get_link($chat['link'], $fetchid);
			$avatar = get_avatar($chat['avatar'], $fetchid);

			$response =  array('id' => $chat['userid'], 'n' => $db->escape_string(strip_tags($chat['username'])), 's' => $chat['status'], 'a' => $avatar, 'l' => $link);

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response);
			close_session();
			exit;
		} 
		else if (check_if_guest($fetchid))
		{
			$sql = get_guest_details($fetchid);
			$result = $db->execute($sql);
			$chat = $db->fetch_array($result);

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
				
				} 
				else
				{
					$chat['status'] = 'offline';
				}
			}

			$link = "#";
			$avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
			$chat['username'] = create_guest_username($chat['userid'], $chat['guest_name']);

			$response =  array('id' => $chat['userid'], 'n' => $db->escape_string(strip_tags($chat['username'])), 's' => $chat['status'], 'a' => $avatar, 'l' => $link);

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response);
			close_session();
			exit;
		}
		else 
		{
			$db->execute("
				UPDATE arrowchat 
				SET arrowchat.read = '1', 
					arrowchat.user_read = '1' 
				WHERE arrowchat.from = '" . $db->escape_string($fetchid) . "' 
					OR arrowchat.to = '" . $db->escape_string($fetchid) . "'
			");		
			
			$result = $db->execute("
				SELECT unfocus_chat, focus_chat, typing 
				FROM arrowchat_status 
				WHERE userid = '" . $db->escape_string($userid) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				
				$unfocus_chat 	= $row['unfocus_chat'];
				$focus_chat   	= $row['focus_chat'];
				$typing 		= $row['typing'];
			
				$focus_chat = str_replace($fetchid, "", $focus_chat);
				$unfocus_chat = str_replace($fetchid.":", "", $unfocus_chat);
				$typing = preg_replace("#:$fetchid/[0-9]+#", "", $typing);
				
				$db->execute("
					UPDATE arrowchat_status
					SET unfocus_chat = '" . $db->escape_string($unfocus_chat) . "',
						focus_chat = '" . $db->escape_string($focus_chat) . "',
						typing = '" . $db->escape_string($typing) . "'
					WHERE userid = '" . $db->escape_string($userid) . "'
				");
			}
			
			close_session();
			exit;
		}
	}

?>