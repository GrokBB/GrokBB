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

	/*
	 * BEFORE USING THIS: You need to alter the .htaccess file in the cron
	 * directory to allow only requests from your server's IP address, so 
	 * that you are the only one who can run this file.
	*/
	
	// ########################## INCLUDE BACK-END ###########################
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');

	// ########################### CLEAN MESSAGES ############################
	// Deletes 3+ hour old messages, you can adjust the 10800 seconds below
	$db->execute("
		DELETE FROM arrowchat 
		WHERE (arrowchat.read = 1 
				AND ('" . time() . "' - arrowchat.sent) > 10800)
			OR (arrowchat.read = 0
				AND ('" . time() . "' - arrowchat.sent) > 604800)
	");
		
	// ###################### CLEAN CHAT ROOM MESSAGES #######################
	// Deletes 3+ hour old messages, you can adjust the 10800 seconds below
	$db->execute("
		DELETE FROM arrowchat_chatroom_messages 
		WHERE ('" . time() . "' - arrowchat_chatroom_messages.sent) > 10800
	");

	// ######################## CLEAN NOTIFICATIONS ##########################
	// Deletes 5+ day old messages, you can adjust the 432000 seconds below
	$db->execute("
		DELETE FROM arrowchat_notifications 
		WHERE arrowchat_notifications.user_read = 1 
			AND ('" . time() . "' - arrowchat_notifications.alert_time) > 432000
	");

?>