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
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "includes/admin_init.php");
	
	// Get the page to process
	if (empty($do))
	{
		$do = "appsettings";
	}
		
	// ####################### START SUBMIT/POST DATA ########################
	
	// Applications Update Processor
	if (var_check('update')) 
	{	
		setcookie("arrowchat_update_checked", "0", time() - 86400);
		
		$result = $db->execute("
			SELECT folder, version 
			FROM arrowchat_applications
			WHERE id = '" . $db->escape_string(get_var('id')) . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
					
			if (!is_file(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . $row['folder'] . DIRECTORY_SEPARATOR . "install_config.php")) 
			{
				$error = "The application is missing an install_config.php file.";
			}
			
			if (empty($error)) 
			{
				require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . $row['folder'] . DIRECTORY_SEPARATOR . "install_config.php");
				
				if ($row['version'] == $application_version)
				{
					$error = "Your application is already up-to-date or you haven't uploaded the new application files to your server. Download the new files from the ArrowChat store and overwrite all the files on your server.";
				}
				
				if (empty($error)) 
				{
					$result = $db->execute("
						UPDATE arrowchat_applications
						SET version = '" . $db->escape_string($application_version) . "' 
						WHERE id = '" . $db->escape_string(get_var('id')) . "'
					");
					
					if ($result) 
					{
						$msg = "Your application was updated successfully.";
					} 
					else 
					{
						$error = "There was a database error. Please try again.";
					}
				}
			}
		} 
		else 
		{
			$error = "We couldn't find that application or the ID is missing.  Please try again.";
		}
	}	

	// Application Settings Submit Processor
	if (var_check('application_settings_submit')) 
	{
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'applications_guests' THEN '" . $db->escape_string(get_var('applications_guests')) . "'
				WHEN config_name = 'hide_applications_menu' THEN '" . $db->escape_string(get_var('hide_applications_menu')) . "'
			END WHERE config_name IN ('applications_guests', 'hide_applications_menu')
		");
					
		if ($result) 
		{
			$applications_guests = get_var('applications_guests');
			$hide_applications_menu = get_var('hide_applications_menu');
			
			update_config_file();
			$msg = "Your settings were successfully saved.";
		} 
		else
		{
			$error = "There was a database error.  Please try again.";
		}
	}	

	// Application Settings Processor
	if (var_check('settings') AND $do == "appsettings") 
	{	
		if (!file_exists((dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS .  DIRECTORY_SEPARATOR . get_var('settings') . DIRECTORY_SEPARATOR . "settings.php")))
		{
			$error = "This application does not have a settings page.";
		}
	
		$smarty->assign('error', $error);
		$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_header.tpl");
		@include_once (dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS .  DIRECTORY_SEPARATOR . get_var('settings') . DIRECTORY_SEPARATOR . "settings.php");
		$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_footer.tpl");
		
		exit;
	}
	
	// Activate Bar Link Processor
	if (var_check('activate') AND $do == "traylinks") 
	{
		if (empty($_GET['id'])) 
		{
			$error = "There was no link ID to activate.";
		}
		
		if (empty($error)) 
		{
			$active = 1;
			
			if (get_var('activate') == 1) 
			{
				$active = 0;
			}
			
			$result = $db->execute("
				UPDATE arrowchat_trayicons 
				SET active = '" . $db->escape_string($active) . "' 
				WHERE id = '" . $db->escape_string(get_var('id')) . "'
			");

			if ($result) 
			{
				$msg = "Your bar link was activated/deactivated successfully.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Delete Bar Link Processor
	if (var_check('delete') AND $do == "traylinks") 
	{
		if (empty($_GET['delete'])) 
		{
			$error = "There was no link ID to delete.";
		}
		
		$result = $db->execute("
			SELECT tray_location
			FROM arrowchat_trayicons 
			WHERE id = '" . $db->escape_string(get_var('delete')) . "'
		");

		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			$current_location = $row['tray_location'];
		}
		else
		{
			$error = "That bar link ID does not exist.";
		}
		
		$result = $db->execute("
			UPDATE arrowchat_trayicons
			SET tray_location = (tray_location - 1)
			WHERE tray_location > " . $current_location . "
		");
		
		if (!$result)
		{
			$error = "There was a database error.  Please try again.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				DELETE FROM arrowchat_trayicons 
				WHERE id = '" . $db->escape_string(get_var('delete')) . "'
			");

			if ($result) 
			{
				$msg = "Your bar link was deleted successfully.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Move Bar Link Processor
	if (var_check('move') AND $do == "traylinks") 
	{
		if (empty($_GET['id'])) 
		{
			$error = "There was no bar link ID to move.";
		}
		
		$result = $db->execute("
			SELECT tray_location
			FROM arrowchat_trayicons 
			WHERE id = '" . $db->escape_string(get_var('id')) . "'
		");

		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			$current_location = $row['tray_location'];
		}
		else
		{
			$error = "That bar link ID does not exist.";
		}
		
		if (empty($error))
		{
			$result = $db->execute("
				SELECT MAX(tray_location)
				FROM arrowchat_trayicons 
				ORDER BY tray_location ASC
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				$max_number = $row['MAX(tray_location)'];
			}
			
			if (get_var('move') == "down")
			{
				if ($current_location == $max_number)
				{
					$error = "This bar link cannot be moved down any further.";
				}
				
				if (empty($error))
				{
					$db->execute("
						UPDATE arrowchat_trayicons
						SET tray_location = '" . $current_location . "'
						WHERE tray_location = (" . $current_location . " + 1)
					");
					
					$result = $db->execute("
						UPDATE arrowchat_trayicons
						SET tray_location = " . $current_location . " + 1
						WHERE id = '" . $db->escape_string(get_var('id')) . "'
					");
					
					if ($result)
					{
						$msg = "Your bar link was moved down successfully.";
						update_config_file();
					}
					else
					{
						$error = "There was an error, please try again.";
					}
				}
			}
			else if (get_var('move') == "up")
			{
				if ($current_location == "1")
				{
					$error = "This bar link cannot be moved up any further.";
				}
				
				if (empty($error))
				{
					$db->execute("
						UPDATE arrowchat_trayicons
						SET tray_location = '" . $current_location . "'
						WHERE tray_location = (" . $current_location . " - 1)
					");
					
					$result = $db->execute("
						UPDATE arrowchat_trayicons
						SET tray_location = " . $current_location . " - 1
						WHERE id = '" . $db->escape_string(get_var('id')) . "'
					");
					
					if ($result)
					{
						$msg = "Your bar link was moved up successfully.";
						update_config_file();
					}
					else
					{
						$error = "There was an error, please try again.";
					}
				}
			}
			else
			{
				$error = "The move value was not recognized.";
			}
		}
	}
	
	// Bar Links Settings Submit Processor
	if (var_check('barlink_settings_submit')) 
	{
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'show_bar_links_right' THEN '" . get_var('show_bar_links_right') . "'
			END WHERE config_name IN ('show_bar_links_right')
		");
					
		if ($result) 
		{
			$show_bar_links_right = get_var('show_bar_links_right');
			
			update_config_file();
			$msg = "Your settings were successfully saved.";
		} 
		else
		{
			$error = "There was a database error.  Please try again.";
		}
	}	
	
	// Add Bar Link Processor
	if (var_check('link_add_submit')) 
	{
		if (empty($_POST['link_name'])) 
		{
			$error = "You must enter a name for this link.";
		}
		
		if (empty($_POST['link_icon']) AND empty($_FILES['link_icon_upload']['size'])) 
		{
			$error = "You must enter or upload an icon for this link.";
		}
		
		if (!empty($_POST['link_icon']))
		{
			$icon_filename = $_POST['link_icon'];
		}
		
		if (!empty($_FILES['link_icon_upload']['size']))
		{
			if (($_FILES['link_icon_upload']['type'] != "image/gif") AND ($_FILES['link_icon_upload']['type'] != "image/jpeg") AND ($_FILES['link_icon_upload']['type'] != "image/pjpeg") AND ($_FILES['link_icon_upload']['type'] != "image/png"))
			{
				$error = "The image must be gif, jpeg or png.";
			}
			
			if ($_FILES['link_icon_upload']['size'] > 500000)
			{
				$error = "The image must be under 500kb.";
			}
			
			if ($_FILES['link_icon_upload']['error'] > 0)
			{
				$error = "There was a problem with the upload.  Error code: " . $_FILES['link_icon_upload']['error'];
			}
			
			if (empty($error))
			{
				$icon_filename = $_FILES['link_icon_upload']['name'];
			}
		}
		
		if (empty($error)) 
		{
			if (!empty($_FILES['link_icon_upload']['size']))
			{
				move_uploaded_file($_FILES['link_icon_upload']['tmp_name'], dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . $_FILES['link_icon_upload']['name']);
			}
			
			$result = $db->execute("
				SELECT MAX(tray_location) 
				FROM arrowchat_trayicons
			");
			$row = $db->fetch_array($result);
			
			if (empty($row['MAX(tray_location)']))
			{
				$tray_location = 1;
			}
			else
			{
				$tray_location = $row['MAX(tray_location)'] + 1;
			}
			
			if (empty($_POST['link_tab_width']))
			{
				$tab_width = null;
			}
			else
			{
				$tab_width = get_var('link_tab_width');
			}
				
			$result = $db->execute("
				INSERT INTO arrowchat_trayicons (
					name, 
					icon, 
					location, 
					target, 
					tray_width, 
					tray_name, 
					tray_location, 
					active
				) 
				VALUES (
					'" . $db->escape_string(get_var('link_name')) . "', 
					'" . $db->escape_string($icon_filename) . "', 
					'" . $db->escape_string(get_var('link_url')) . "', 
					'" . $db->escape_string(get_var('link_target')) . "', 
					'" . $db->escape_string($tab_width) . "', 
					'" . $db->escape_string(get_var('link_tab_name')) . "', 
					'" . $db->escape_string($tray_location) . "', 
					'1'
				)
			");

			if ($result) 
			{
				$msg = "Your bar link has been added.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Edit Bar Link Processor
	if (var_check('link_edit_submit')) 
	{
		if (empty($_POST['link_name'])) 
		{
			$error = "You must enter a name for this bar link.";
		}
		
		if (empty($_POST['link_icon'])) 
		{
			$error = "You must enter an icon for this link.";
		}
		
		if (empty($error)) 
		{	
			$result = $db->execute("
				UPDATE arrowchat_trayicons 
				SET name = '" . $db->escape_string(get_var('link_name')) . "', 
					icon = '" . $db->escape_string(get_var('link_icon')) . "', 
					location = '" . $db->escape_string(get_var('link_url')) . "', 
					target = '" . $db->escape_string(get_var('link_target')) . "', 
					tray_width = '" . $db->escape_string(get_var('link_tab_width')) . "', 
					tray_name = '" . $db->escape_string(get_var('link_tab_name')) . "' 
				WHERE id = '" . $db->escape_string(get_var('link_id')) . "'
			");

			if ($result) 
			{
				$msg = "Your bar link was updated successfully.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
		
		$_GET['id'] = get_var('link_id');
	}
	
	// Chatroom Settings Submit Processor
	if (var_check('chatroom_settings_submit')) 
	{
		if ($chatroom_default_names == 1 AND empty($_POST['chatroom_default_names']) AND MSSQL_DATABASE != 1)
		{
			$db->execute("
				ALTER TABLE `arrowchat_status`
				MODIFY `chatroom_show_names` tinyint(1) unsigned default NULL
			");
		}
		
		if ($chatroom_default_names == 0 AND $_POST['chatroom_default_names'] == 1 AND MSSQL_DATABASE != 1)
		{
			$db->execute("
				ALTER TABLE `arrowchat_status`
				MODIFY `chatroom_show_names` tinyint(1) unsigned default 1
			");
		}
		
		$result = $db->execute("
			UPDATE arrowchat_config 
			SET config_value = CASE 
				WHEN config_name = 'user_chatrooms' THEN '" . $db->escape_string(get_var('user_chatrooms')) . "'
				WHEN config_name = 'user_chatrooms_flood' THEN '" . $db->escape_string(get_var('user_chatrooms_flood')) . "'
				WHEN config_name = 'user_chatrooms_length' THEN '" . $db->escape_string(get_var('user_chatrooms_length')) . "'
				WHEN config_name = 'chatroom_auto_join' THEN '" . $db->escape_string(get_var('chatroom_auto_join')) . "'
				WHEN config_name = 'chatroom_history_length' THEN '" . $db->escape_string(get_var('chatroom_history_length')) . "'
				WHEN config_name = 'chatroom_message_length' THEN '" . $db->escape_string(get_var('chatroom_message_length')) . "'
				WHEN config_name = 'chatroom_default_names' THEN '" . get_var('chatroom_default_names') . "'
			END WHERE config_name IN ('user_chatrooms', 'user_chatrooms_flood', 'user_chatrooms_length', 'chatroom_auto_join', 'chatroom_history_length', 'chatroom_message_length', 'chatroom_default_names')
		");
					
		if ($result) 
		{
			$user_chatrooms = get_var('user_chatrooms');
			$user_chatrooms_flood = get_var('user_chatrooms_flood');
			$user_chatrooms_length = get_var('user_chatrooms_length');
			$chatroom_auto_join = get_var('chatroom_auto_join');
			$chatroom_history_length = get_var('chatroom_history_length');
			$chatroom_message_length = get_var('chatroom_message_length');
			$chatroom_default_names = get_var('chatroom_default_names');
			
			update_config_file();
			$msg = "Your settings were successfully saved.";
		} 
		else
		{
			$error = "There was a database error.  Please try again.";
		}
	}
	
	// Add Chatroom Processor
	if (var_check('add_chatroom_submit')) 
	{
		if (empty($_POST['add_chatroom_name'])) 
		{
			$error = "You must enter a name for this chatroom.";
		}
		
		if (empty($_POST['add_chatroom_desc'])) 
		{
			$error = "You must enter a description for this chatroom.";
		}
		
		if (empty($_POST['add_chatroom_length']) AND $_POST['add_chatroom_length'] != "0") 
		{
			$error = "You must enter a length for this chatroom.";
		}
		
		if (empty($_POST['add_chatroom_type'])) 
		{
			$error = "You must enter a type for this chatroom.";
		}
		
		if ($_POST['add_chatroom_type'] == 2 AND empty($_POST['add_chatroom_password'])) 
		{
			$error = "You must specify a password for a password protected chatroom.";
		}
		
		if (!is_numeric($_POST['add_chatroom_length'])) 
		{
			$error = "The chatroom length must be a number only.  Specify in minutes.";
		}
		
		if (!is_numeric($_POST['chatroom_max_users']))
		{
			$error = "The chatroom max users must be a number only.  Enter 0 for unlimited users.";
		}
		
		if (empty($_POST['limit_seconds_num']) OR empty($_POST['limit_message_num']) OR !is_numeric($_POST['limit_seconds_num']) OR !is_numeric($_POST['limit_message_num'])) 
		{
			$error = "The chat room flood selection is empty of invalid.";
		}
		
		if (empty($_POST['add_chatroom_img']) AND empty($_FILES['add_chatroom_img_upload']['size'])) 
		{
			$error = "You must enter or upload an icon for this chat room.";
		}
		
		if (!empty($_POST['add_chatroom_img']))
		{
			$icon_filename = $_POST['add_chatroom_img'];
		}
		
		if (!empty($_FILES['add_chatroom_img_upload']['size']))
		{
			if (($_FILES['add_chatroom_img_upload']['type'] != "image/gif") AND ($_FILES['add_chatroom_img_upload']['type'] != "image/jpeg") AND ($_FILES['add_chatroom_img_upload']['type'] != "image/pjpeg") AND ($_FILES['add_chatroom_img_upload']['type'] != "image/png"))
			{
				$error = "The image must be gif, jpeg or png.";
			}
			
			if ($_FILES['add_chatroom_img_upload']['size'] > 500000)
			{
				$error = "The image must be under 500kb.";
			}
			
			if ($_FILES['add_chatroom_img_upload']['error'] > 0)
			{
				$error = "There was a problem with the upload.  Error code: " . $_FILES['add_chatroom_img_upload']['error'];
			}
			
			if (empty($error))
			{
				$icon_filename = $_FILES['add_chatroom_img_upload']['name'];
			}
		}
		
		if (empty($error)) 
		{		
			if (!empty($_FILES['add_chatroom_img_upload']['size']))
			{
				move_uploaded_file($_FILES['add_chatroom_img_upload']['tmp_name'], dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . $_FILES['add_chatroom_img_upload']['name']);
			}
		
			$result = $db->execute("
				INSERT INTO arrowchat_chatroom_rooms (
					author_id, 
					name, 
					description,
					welcome_message,
					image,
					type, 
					password, 
					length, 
					max_users,
					session_time,
					limit_message_num,
					limit_seconds_num,
					disallowed_groups
				) 
				VALUES (
					'" . $db->escape_string($userid) . "', 
					'" . $db->escape_string(get_var('add_chatroom_name')) . "', 
					'" . $db->escape_string(get_var('add_chatroom_desc')) . "', 
					'" . $db->escape_string(get_var('add_chatroom_welcome_msg')) . "', 
					'" . $db->escape_string($icon_filename) . "',
					'" . $db->escape_string(get_var('add_chatroom_type')) . "', 
					'" . $db->escape_string(get_var('add_chatroom_password')) . "', 
					'" . $db->escape_string(get_var('add_chatroom_length')) . "', 
					'" . $db->escape_string(get_var('chatroom_max_users')) . "',
					'" . time() . "',
					'" . $db->escape_string(get_var('limit_message_num')) . "',
					'" . $db->escape_string(get_var('limit_seconds_num')) . "',
					'" . $db->escape_string(serialize(get_var('add_chatroom_group'))) . "'
				)
			");

			if ($result) 
			{
				$msg = "Your chatroom has been created successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Delete Chatroom Processor
	if (var_check('delete') AND $do == "chatroomsettings") 
	{
		if (empty($_GET['delete'])) 
		{
			$error = "There was no chatroom ID to delete.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				DELETE FROM arrowchat_chatroom_rooms 
				WHERE id = '" . $db->escape_string(get_var('delete')) . "'
			");

			if ($result) 
			{
				$msg = "Your chatroom was deleted successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Edit Chatroom Processor
	if (var_check('chatroom_edit_submit')) 
	{
		if (empty($_POST['edit_chatroom_name'])) 
		{
			$error = "You must enter a name for this chatroom.";
		}
		
		if (empty($_POST['edit_chatroom_desc'])) 
		{
			$error = "You must enter a description for this chatroom.";
		}
		
		if (empty($_POST['edit_chatroom_img'])) 
		{
			$error = "You must enter an icon for this chatroom.";
		}
		
		if (empty($_POST['edit_chatroom_length']) AND $_POST['edit_chatroom_length'] != "0") 
		{
			$error = "You must enter a length for this chatroom.";
		}
		
		if (empty($_POST['edit_chatroom_type'])) 
		{
			$error = "You must enter a type for this chatroom.";
		}
		
		if ($_POST['edit_chatroom_type'] == 2 AND empty($_POST['edit_chatroom_password'])) 
		{
			$error = "You must specify a password for a password protected chatroom.";
		}
		
		if (!is_numeric($_POST['edit_chatroom_length'])) 
		{
			$error = "The chatroom length must be a number only.  Specify in minutes.";
		}
		
		if (!is_numeric($_POST['chatroom_max_users']))
		{
			$error = "The chatroom max users must be a number only.  Enter 0 for unlimited users.";
		}
		
		if (empty($_POST['limit_seconds_num']) OR empty($_POST['limit_message_num']) OR !is_numeric($_POST['limit_seconds_num']) OR !is_numeric($_POST['limit_message_num'])) 
		{
			$error = "The chat room flood selection is empty of invalid.";
		}
		
		if (empty($error)) 
		{	
			$usernames = get_var('unban_username');
			
			if ($usernames) 
			{
				foreach ($usernames as $unbans) 
				{
					$db->execute("
						DELETE FROM arrowchat_chatroom_banlist 
						WHERE user_id = '" . $db->escape_string($unbans) . "'
							AND chatroom_id = '" . $db->escape_string(get_var('chatroom_id')) . "'
					");
				}
			} 
			
			$result = $db->execute("
				UPDATE arrowchat_chatroom_rooms 
				SET name = '" . $db->escape_string(get_var('edit_chatroom_name')) . "', 
					description = '" . $db->escape_string(get_var('edit_chatroom_desc')) . "', 
					welcome_message = '" . $db->escape_string(get_var('edit_chatroom_welcome_msg')) . "', 
					image = '" . $db->escape_string(get_var('edit_chatroom_img')) . "', 
					type = '" . $db->escape_string(get_var('edit_chatroom_type')) . "', 
					password = '" . $db->escape_string(get_var('edit_chatroom_password')) . "', 
					length = '" . $db->escape_string(get_var('edit_chatroom_length')) . "',
					max_users = '" . $db->escape_string(get_var('chatroom_max_users')) . "',
					limit_message_num = '" . $db->escape_string(get_var('limit_message_num')) . "',
					limit_seconds_num = '" . $db->escape_string(get_var('limit_seconds_num')) . "',
					disallowed_groups = '" . $db->escape_string(serialize(get_var('edit_chatroom_group'))) . "'
				WHERE id = '" . $db->escape_string(get_var('chatroom_id')) . "'
			");

			if ($result) 
			{
				$msg = "Your chatroom was updated successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
		
		$_GET['id'] = get_var('link_id');
	}
	
	// Activate Application Processor
	if (var_check('activate') AND $do == "appsettings") 
	{
		if (empty($_GET['id'])) 
		{
			$error = "There was no application ID to activate.";
		}
		
		if (empty($error)) 
		{
			$active = 1;
			
			if (get_var('activate') == 1) 
			{
				$active = 0;
			}
			
			$result = $db->execute("
				UPDATE arrowchat_applications 
				SET active = '" . $db->escape_string($active) . "' 
				WHERE id = '" . $db->escape_string(get_var('id')) . "'
			");

			if ($result) 
			{
				$msg = "Your application was activated/deactivated successfully.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Delete Application Processor
	if (var_check('delete') AND $do == "appsettings") 
	{
		if (empty($_GET['delete'])) 
		{
			$error = "There was no application ID to delete.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				DELETE FROM arrowchat_applications 
				WHERE id = '" . $db->escape_string(get_var('delete')) . "'
			");

			if ($result) 
			{
				$msg = "Your application was deleted successfully.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Application Install Submit Processor
	if (var_check('f') AND $do == "appsettings") 
	{
		require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . get_var('f') . DIRECTORY_SEPARATOR . "install_config.php");
		
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_applications 
			WHERE arrowchat_applications.folder = '" . $db->escape_string($folder_location) . "' 
				OR arrowchat_applications.name = '" . $db->escape_string($application_name) . "'
		");

		if ($result AND $db->count_select() > 0) 
		{
			$error = "There is already an application with this name or folder location.";
		}

		if (empty($application_name)) 
		{
			$error = "The install file did not contain a name for this application.";
		}
		
		if (empty($folder_location)) 
		{
			$error = "The install file did not contain a folder location for this application.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				INSERT INTO arrowchat_applications (
					name, 
					folder, 
					icon, 
					width, 
					height,
					dont_reload,
					link, 
					update_link, 
					version, 
					active
				) 
				VALUES (
					'" . $db->escape_string($application_name) . "', 
					'" . $db->escape_string($folder_location) . "', 
					'" . $db->escape_string($application_icon) . "', 
					'" . $db->escape_string($application_width) . "', 
					'" . $db->escape_string($application_height) . "', 
					'" . $db->escape_string($dont_reload) . "', 
					'" . $db->escape_string($application_link) . "', 
					'" . $db->escape_string($update_link) . "', 
					'" . $db->escape_string($application_version) . "', 
					'1'
				)
			");
			
			@include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . get_var('f') . DIRECTORY_SEPARATOR . "install.php");

			if ($result) 
			{
				$msg = "Your application was installed successfully.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Edit Application Processor
	if (var_check('app_edit_submit')) 
	{
		if (empty($_POST['app_name'])) 
		{
			$error = "You must enter a name for this application.";
		}
		
		if (empty($_POST['app_folder'])) 
		{
			$error = "You must enter a folder location for this application.";
		}
		
		if (empty($_POST['app_icon'])) 
		{
			$error = "You must enter an icon for this application.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				UPDATE arrowchat_applications 
				SET name = '" . $db->escape_string(get_var('app_name')) . "', 
					icon = '" . $db->escape_string(get_var('app_icon')) . "', 
					folder = '" . $db->escape_string(get_var('app_folder')) . "', 
					width = '" . $db->escape_string(get_var('app_width')) . "', 
					height = '" . $db->escape_string(get_var('app_height')) . "', 
					bar_width = '" . $db->escape_string(get_var('bar_width')) . "', 
					bar_name = '" . $db->escape_string(get_var('bar_name')) . "', 
					link = '" . $db->escape_string(get_var('app_url')) . "', 
					dont_reload = '" . $db->escape_string(get_var('dont_reload')) . "',
					show_to_guests = '" . $db->escape_string(get_var('show_to_guests')) . "',
					default_bookmark = '" . $db->escape_string(get_var('default_bookmark')) . "',
					update_link = '" . $db->escape_string(get_var('app_update_url')) . "' 
				WHERE id = '" . $db->escape_string(get_var('app_id')) . "'
			");

			if ($result) 
			{
				$msg = "Your application was updated successfully.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
		
		$_GET['id'] = get_var('link_id');
	}
	
	// Add Notification Processor
	if (var_check('add_notification_submit')) 
	{
		if (empty($_POST['add_notification_name'])) 
		{
			$error = "You must enter a name for this notification.";
		}
		
		if (empty($_POST['add_notification_markup'])) 
		{
			$error = "You must enter markup for this notification.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				SELECT MAX(type) 
				FROM arrowchat_notifications_markup
			");
			$row = $db->fetch_array($result);
			
			if (empty($row['MAX(type)']))
			{
				$type = 1;
			}
			else
			{
				$type = $row['MAX(type)'] + 1;
			}
			
			$markup_data = str_replace("\\r\\n", "
",  get_var('add_notification_markup'));
				
			$result = $db->execute("
				INSERT INTO arrowchat_notifications_markup (
					name, 
					type, 
					markup
				) 
				VALUES (
					'" . $db->escape_string(get_var('add_notification_name')) . "', 
					'" . $db->escape_string($type) . "', 
					'" . $db->escape_string(stripslashes($markup_data)) . "'
				)
			");

			if ($result) 
			{
				$msg = "Your notification was added successfully with type number " . $db->escape_string($type) . ".  You must add a mySQL statement where the action occurs before the notification will work.  Consult the ArrowChat documentation for more information.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Delete Notifications Processor
	if (var_check('delete') AND $do == "notificationsettings") 
	{
		if (empty($_GET['delete'])) 
		{
			$error = "There was no notification ID to delete.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				DELETE FROM arrowchat_notifications_markup 
				WHERE id = '" . $db->escape_string(get_var('delete')) . "'
			");

			if ($result) 
			{
				$msg = "Your notification was deleted successfully.  You should delete the mySQL query when this action is called to avoid unnecessary database calls.";
				update_config_file();
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Edit Notifications Processor
	if (var_check('notification_edit_submit')) 
	{
		if (empty($_POST['edit_notification_name'])) 
		{
			$error = "You must enter a name for this notification.";
		}
		
		if (empty($_POST['edit_notification_markup'])) 
		{
			$error = "You must enter markup for this notification.";
		}
		
		if (empty($error)) 
		{
			$markup_data = str_replace("\\r\\n", "
",  get_var('edit_notification_markup'));

			$result = $db->execute("
				UPDATE arrowchat_notifications_markup 
				SET name = '" . $db->escape_string(get_var('edit_notification_name')) . "', 
					markup = '" . $db->escape_string(stripslashes($markup_data)) . "' 
				WHERE id = '" . $db->escape_string(get_var('notification_id')) . "'
			");

			if ($result) 
			{
				$msg = "Your notification was updated successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
		
		$_GET['id'] = get_var('link_id');
	}

	$smarty->assign('msg', $msg);
	$smarty->assign('error', $error);

	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_header.tpl");
	require(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_manage.php");
	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_footer.tpl");

?>