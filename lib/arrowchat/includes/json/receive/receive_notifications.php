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
	$notifications = array();

	// ###################### START NOTIFICATION RECEIVE ######################
	if (logged_in($userid)) 
	{
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_notifications 
			WHERE to_id = '" . $db->escape_string($userid) . "' 
			ORDER BY alert_time DESC
			LIMIT 10
		");
		
		while ($row = $db->fetch_array($result)) 
		{
			$alert_id		= $row['id'];
			$author_id 		= $row['author_id'];
			$author_name 	= $db->escape_string(strip_tags($row['author_name']));
			$type 			= $row['type'];
			$message_time	= $row['alert_time'];
			$misc1			= $row['misc1'];
			$misc2			= $row['misc2'];
			$misc3			= $row['misc3'];
			
			$markup = get_markup($author_id, $author_name, $type, $message_time, $misc1, $misc2, $misc3);
			
			$notifications[] = array('alert_id' => $alert_id, 'markup' => $markup, 'type' => $type);
		}

		if (!empty($notifications)) 
		{
			$response['notifications'] = $notifications;
		}
	}

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($response);
	close_session();
	exit;

?>