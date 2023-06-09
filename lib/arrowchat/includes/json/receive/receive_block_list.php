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
	$response = array();
	$blocklist = array();

	// ###################### START NOTIFICATION RECEIVE ######################
	if (logged_in($userid)) 
	{
		$result = $db->execute("
			SELECT block_chats 
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "' 
		");
		
		if ($row = $db->fetch_array($result)) 
		{
			$block_chats_array = unserialize($row['block_chats']);
			
			if (!is_array($block_chats_array))
			{
				$block_chats_array = array();
			}
			
			foreach ($block_chats_array as $id)
			{
				if (check_if_guest($id))
				{
					$username = create_guest_username($id, '', true);
					
					if (empty($username))
					{
						$username = create_guest_username($id, '', false);
					}
				}
				else
				{
					$username = get_username($id);
				}
				
				if (!empty($id) && !empty($username))
				{
					$blocklist[] = array('id' => $id, 'username' => $username);
				}
			}
		}

		if (!empty($blocklist)) 
		{
			$response['blocklist'] = $blocklist;
		}
	}

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($response);
	close_session();
	exit;

?>