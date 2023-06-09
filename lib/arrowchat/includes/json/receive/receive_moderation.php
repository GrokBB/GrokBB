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
	$reports	= array();
	$time 		= time();

	// ##################### START CHATROOM LIST RECEIVE #####################
	if (logged_in($userid) AND ($is_admin == 1 OR $is_mod == 1))
	{	
		if ($is_admin == 1)
		{
			$result = $db->execute("
				SELECT id, report_from, report_about, report_time, COUNT(id)
				FROM arrowchat_reports
				WHERE (working_time < (" . time() . " - 600)
						OR working_by = '" . $db->escape_string($userid) . "')
					AND completed_time = 0
				GROUP BY report_about
				ORDER BY report_time ASC
				LIMIT 20
			");
		}
		else
		{
			$result = $db->execute("
				SELECT id, report_from, report_about, report_time, COUNT(id)
				FROM arrowchat_reports
				WHERE (working_time < (" . time() . " - 600)
						OR working_by = '" . $db->escape_string($userid) . "')
					AND completed_time = 0
					AND report_about != '" . $db->escape_string($userid) . "'
				GROUP BY report_about
				ORDER BY report_time ASC
				LIMIT 20
			");
		}

		while ($row = $db->fetch_array($result)) 
		{
			$fetchid = $row['report_from'];
			
			if (check_if_guest($fetchid))
			{
				$sql = get_guest_details($fetchid);
				$result2 = $db->execute($sql);
				$user = $db->fetch_array($result2);
				
				$from_name = create_guest_username($user['userid'], $user['guest_name']);
				$from_avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
			}
			else
			{
				$sql = get_user_details($fetchid);
				$result3 = $db->execute($sql);
				$user = $db->fetch_array($result3);
				
				$from_name = $user['username'];
				$from_avatar = get_avatar($user['avatar'], $fetchid);
			}
			
			$fetchid = $row['report_about'];
			
			if (check_if_guest($fetchid))
			{
				$sql = get_guest_details($fetchid);
				$result2 = $db->execute($sql);
				$user = $db->fetch_array($result2);
				
				$about_name = create_guest_username($user['userid'], $user['guest_name']);
				$about_avatar = $base_url . AC_FOLDER_ADMIN . "/images/img-no-avatar.gif";
			}
			else
			{
				$sql = get_user_details($fetchid);
				$result3 = $db->execute($sql);
				$user = $db->fetch_array($result3);
				
				$about_name = $user['username'];
				$about_avatar = get_avatar($user['avatar'], $fetchid);
			}
			
			$reports[] = array('id' => $row['id'], 'from' => $from_name, 'from_pic' => $from_avatar, 'about' => $about_name, 'about_pic' => $about_avatar, 'time' => relative_time($row['report_time']), 'about_num' => $row['COUNT(id)']);
		}
		
		$result = $db->execute("
			SELECT COUNT(id)
			FROM arrowchat_reports
			WHERE (working_time < (" . time() . " - 600)
						OR working_by = '" . $db->escape_string($userid) . "')
				AND completed_time = 0
		");
		
		if ($row = $db->fetch_array($result))
		{
			$total_reports = $row['COUNT(id)'];
		}
		else
		{
			$total_reports = 0;
		}

		$response['total_reports'] = array('count' => $total_reports);
		$response['reports'] = $reports;
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