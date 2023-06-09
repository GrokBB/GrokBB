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

	// ########################### GET POST DATA #############################
	$typing 	= get_var('typing');
	$untype 	= get_var('untype');

	// ######################### START POST TYPING ###########################
	if (!empty($_POST['typing'])) 
	{
		$result = $db->execute("
			SELECT typing 
			FROM arrowchat_status
			WHERE userid = '" . $db->escape_string($typing) . "'
		");

		if ($result AND $db->count_select() > 0 AND logged_in($userid)) 
		{
			$row = $db->fetch_array($result);
			$old_data = $row['typing'];
			
			if (empty($untype)) 
			{
				if (!empty($old_data)) 
				{
					if (preg_match("#:$userid/[0-9]+#", $old_data, $matches)) 
					{
						$typing_insert = str_replace($matches[0], ":".$userid."/".time()."", $old_data);
					
					} 
					else 
					{
						$typing_insert = $old_data.":".$userid."/".time();
					}
				} 
				else 
				{
					$typing_insert = ":".$userid."/".time();
				}
				
				if ($push_on == 1)
				{
					$arrowpush->publish(array(
						'channel' => 'u' . $typing,
						'message' => array('typing' => array("id" => $userid))
					));
				}
			} 
			else 
			{
				if (preg_match("#:$userid/[0-9]+#", $old_data, $matches)) 
				{
					$typing_insert = str_replace($matches[0], ":".$userid."/0", $old_data);
				
				}
				else 
				{
					$typing_insert = ":".$userid."/0";
				}
				
				if ($push_on == 1)
				{
					$arrowpush->publish(array(
						'channel' => 'u' . $typing,
						'message' => array('nottyping' => array("id" => $userid))
					));
				}
			}
			
			$db->execute("
				UPDATE arrowchat_status
				SET typing = '" . $db->escape_string($typing_insert) . "' 
				WHERE userid = '" . $db->escape_string($typing) . "'
			");
		}

		echo "1";
		close_session();
		exit(0);
	}

?>