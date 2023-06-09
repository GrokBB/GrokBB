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
		$do = "managethemes";
	}
	
	// ####################### START SUBMIT/POST DATA ########################
	
	// Theme Update Processor
	if (var_check('update')) 
	{
		setcookie("arrowchat_update_checked", "0", time() - 86400);
		
		if (empty($_GET['id'])) 
		{
			$error = "There was no theme ID to update.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				SELECT folder, version 
				FROM arrowchat_themes 
				WHERE id = '" . get_var('id') . "'
			");
			
			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
						
				if (!is_file(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $row['folder'] . DIRECTORY_SEPARATOR . "install_config.php")) 
				{
					$error = "The theme is missing an install_config.php file.";
				}
				
				if (empty($error)) 
				{
					require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $row['folder'] . DIRECTORY_SEPARATOR . "install_config.php");
					
					if ($row['version'] == $theme_version)
					{
						$error = "Your theme is already up-to-date or you haven't uploaded the new theme files to your server. Download the new files from the ArrowChat store and overwrite all the files on your server.";
					}
					
					if (empty($error)) 
					{
						$result = $db->execute("
							UPDATE arrowchat_themes 
							SET version = '" . $db->escape_string($theme_version) . "' 
							WHERE id = '" . get_var('id') . "'
						");
						
						if ($result) 
						{
							$msg = "Your theme was updated successfully.";
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
				$error = "We couldn't find that theme.  Please try again.";
			}
		}	
	}
	
	// Theme Install Submit Processor
	if (var_check('theme_install_submit')) 
	{	
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_themes 
			WHERE arrowchat_themes.folder = '" . get_var('theme_folder') . "' 
				OR arrowchat_themes.name = '" . get_var('theme_name') . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$error = "There is already a theme with this name or folder location.";
		}

		if (empty($_POST['theme_name'])) 
		{
			$error = "You must enter a name for this theme.";
		}
		
		if (empty($_POST['theme_folder'])) 
		{
			$error = "You must enter a folder location for this theme.";
		}
		
		if (!is_file(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . get_var('theme_folder') . DIRECTORY_SEPARATOR . "install_config.php")) 
		{
			$error = "The theme is missing an install_config.php file.";
		}
		
		if (empty($error)) 
		{
			include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . get_var('theme_folder') . DIRECTORY_SEPARATOR . "install_config.php");
		
			if (get_var('theme_default') == 1) 
			{
				$db->execute("
					UPDATE arrowchat_themes 
					SET arrowchat_themes.default = '0'
				");
				
				$db->execute("
					UPDATE arrowchat_config 
						SET config_value = '" . get_var('theme_folder') . "' 
						WHERE config_name = 'theme'
				");
				
				$_POST['theme_active'] = 1;
			}
			
			$result = $db->execute("
				INSERT INTO arrowchat_themes (
					arrowchat_themes.folder, 
					arrowchat_themes.name, 
					arrowchat_themes.active, 
					arrowchat_themes.update_link, 
					arrowchat_themes.version, 
					arrowchat_themes.default
				) 
				VALUES (
					'" . get_var('theme_folder') . "', 
					'" . get_var('theme_name') . "', 
					'" . get_var('theme_active') . "', 
					'" . $db->escape_string($update_link) . "', 
					'" . $db->escape_string($theme_version) . "', 
					'" . get_var('theme_default') . "'
				)
			");
			
			update_config_file();
			
			if ($result) 
			{
				$msg = "Your theme was installed successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Theme Default Submit Processor
	if (var_check('theme_default'))
	{
		$theme_id = get_var('theme_default');
		
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_themes 
			WHERE arrowchat_themes.id = '" . $theme_id . "'
		");
		
		if ($result AND $db->count_select() <= 0) 
		{
			$error = "We could not find a theme with that ID.";
		}
		
		if (empty($error)) 
		{
			$row = $db->fetch_array($result);
			
			$db->execute("
				UPDATE arrowchat_themes 
				SET arrowchat_themes.default = '0'
			");
			
			$db->execute("
				UPDATE arrowchat_config 
				SET config_value = '" . $row['folder'] . "' 
				WHERE config_name = 'theme'
			");
		
			$result = $db->execute("
				UPDATE arrowchat_themes 
				SET arrowchat_themes.active = '1', 
					arrowchat_themes.default = '1' 
				WHERE id = '" . $theme_id . "'
			");
			
			update_config_file();
			
			if ($result) 
			{
				$msg = "Your default theme was updated successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Theme Edit Processor
	if (var_check('theme_edit_submit')) 
	{
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_themes 
			WHERE (arrowchat_themes.folder = '" . get_var('theme_folder') . "' 
					OR arrowchat_themes.name = '" . get_var('theme_name') . "') 
				AND arrowchat_themes.id != '" . get_var('theme_id') . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$error = "There is already a theme with this name or folder location.";
		}

		if (empty($_POST['theme_name'])) 
		{
			$error = "You must enter a name for this theme.";
		}
		
		if (empty($_POST['theme_folder'])) 
		{
			$error = "You must enter a folder location for this theme.";
		}
		
		if (empty($error)) 
		{
			if ($_POST['theme_default'] == 1) 
			{
				$db->execute("
					UPDATE arrowchat_themes 
					SET arrowchat_themes.default = '0'
				");
				
				$db->execute("
					UPDATE arrowchat_config 
					SET config_value = '" . get_var('theme_folder') . "' 
					WHERE config_name = 'theme'
				");
			}
			
			$result = $db->execute("
				UPDATE arrowchat_themes 
				SET arrowchat_themes.folder = '" . get_var('theme_folder') . "', 
					arrowchat_themes.name = '" . get_var('theme_name') . "', 
					arrowchat_themes.active = '" . get_var('theme_active') . "', 
					arrowchat_themes.default = '" . get_var('theme_default') . "' 
				WHERE id = '" . get_var('theme_id') . "'
			");
			
			update_config_file();
			
			if ($result) 
			{
				$msg = "Your theme was updated successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
		
		$_GET['id'] = get_var('theme_id');
	}
	
	// Save Smilies Processor
	if (var_check('smiley_submit')) 
	{
		for ($i = 1; $i < get_var('smiley_count'); $i++) 
		{
			$name = get_var('smiley_name_' . $i);
			$pattern = get_var('smiley_pattern_' . $i);
			$smileyid = get_var('smiley_id_' . $i);
			
			$result = $db->execute("
				UPDATE arrowchat_smilies 
				SET arrowchat_smilies.name = '" . $db->escape_string($name) . "', 
					arrowchat_smilies.code = '" . stripslashes($db->escape_string($pattern)) . "' 
				WHERE arrowchat_smilies.id = '" . $db->escape_string($smileyid) . "'
			");
		}
		
		if ($result) 
		{
			$msg = "Smilies updated successfully.";
			update_config_file();
		} 
		else 
		{
			$error = "There was a database error.  Please try again.";
		}
	}
	
	// Add Smiley Processor
	if (var_check('add_smiley_submit')) 
	{
		if (empty($_POST['add_smiley_name'])) 
		{
			$error = "You must enter a name for this smiley.";
		}
		
		if (empty($_POST['add_smiley_pattern'])) 
		{
			$error = "You must enter a pattern for this smiley.";
		}
		
		if (empty($error)) 
		{
			$result = $db->execute("
				INSERT INTO arrowchat_smilies (
					name, 
					code
				) 
				VALUES (
					'" . get_var('add_smiley_name') . "', 
					'" . get_var('add_smiley_pattern') . "'
				)
			");
			
			if ($result) 
			{
				update_config_file();
				$msg = "Your smiley was added successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Delete Smiley Processor
	if (var_check('deletesmiley')) 
	{
		if (empty($_GET['deletesmiley'])) 
		{
			$error = "There was no smiley ID to delete.";
		}
		
		$result = $db->execute("
			SELECT id 
			FROM arrowchat_smilies 
			WHERE id = '" . get_var('deletesmiley') . "'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$result = $db->execute("
				DELETE FROM arrowchat_smilies 
				WHERE id = '" . get_var('deletesmiley') . "'
			");
			
			if ($result) 
			{
				update_config_file();
				$msg = "Your smiley was deleted successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		} 
		else 
		{
			$error = "There is no smiley with that ID.";
		}
	}
	
	// Activate Theme Processor
	if (var_check('activate')) 
	{
		if (empty($_GET['id'])) 
		{
			$error = "There was no theme ID to activate.";
		}
		
		$result = $db->execute("
			SELECT arrowchat_themes.default 
			FROM arrowchat_themes 
			WHERE id = '" . get_var('id') . "' 
				AND arrowchat_themes.default = '1'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$error = "You may not deactivate a default theme.";
		}
		
		if (empty($error)) 
		{
			$active = 1;
			
			if (get_var('activate') == 1) 
			{
				$db->execute("
					UPDATE arrowchat_status 
					SET theme = NULL 
					WHERE theme = '" . get_var('id') . "'
				");
				
				$active = 0;
			}
			
			$result = $db->execute("
				UPDATE arrowchat_themes 
				SET active = '" . $db->escape_string($active) . "' 
				WHERE id = '" . get_var('id') . "'
			");
			
			update_config_file();
			
			if ($result) 
			{
				$msg = "Your theme was activated/deactivated successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Delete Theme Processor
	if (var_check('delete')) 
	{
		if (empty($_GET['delete'])) 
		{
			$error = "There was no theme ID to delete.";
		}
		
		$result = $db->execute("
			SELECT arrowchat_themes.default 
			FROM arrowchat_themes 
			WHERE id = '" . get_var('delete') . "' 
				AND arrowchat_themes.default = '1'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$error = "You may not delete a default theme.";
		}
		
		if (empty($error)) 
		{
			$db->execute("
				UPDATE arrowchat_status 
				SET theme = NULL 
				WHERE theme = '" . get_var('delete') . "'
			");
				
			$result = $db->execute("
				DELETE FROM arrowchat_themes 
				WHERE id = '" . get_var('delete') . "'
			");
			
			update_config_file();
			
			if ($result) 
			{
				$msg = "Your theme was deleted successfully.";
			} 
			else 
			{
				$error = "There was a database error.  Please try again.";
			}
		}
	}
	
	// Save Template Processor
	if (var_check('save_template_submit')) 
	{
		if (file_exists(get_var('template_file')) AND is_writable(get_var('template_file'))) 
		{
			$template_data = str_replace("\\r\\n", "
",  get_var('template_data'));
			$fh = @fopen(get_var('template_file'), 'w');
			fwrite($fh, stripslashes($template_data));
			fclose($fh);
			
			$msg = "The file was been updated successfully.";
		} 
		else 
		{
			$error = "The file does not exist or is not writable.";
		}
	}

	$smarty->assign('msg', $msg);
	$smarty->assign('error', $error);

	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_header.tpl");
	require(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_themes.php"); 
	$smarty->display(dirname(__FILE__) . DIRECTORY_SEPARATOR . "layout/pages_footer.tpl");
	
?>