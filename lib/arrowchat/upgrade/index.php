<?php

	/*
	|| #################################################################### ||
	|| #                             ArrowChat                            # ||
	|| # ---------------------------------------------------------------- # ||
	|| #         Copyright ©2010 GloTouch LLC. All Rights Reserved.       # ||
	|| # This file may not be redistributed in whole or significant part. # ||
	|| # ---------------- ARROWCHAT IS NOT FREE SOFTWARE ---------------- # ||
	|| #   http://www.arrowchat.com | http://www.arrowchat.com/license/   # ||
	|| #################################################################### ||
	*/
	
	if (file_exists(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . "integration.php")) require_once (dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . "integration.php"); else die("The includes/integration.php file does not exist.  We recommend running the install folder instead of the upgrade folder.");
	
	if (!function_exists('get_group_id'))
	{
		function get_group_id(){return array(1);}
		$group_disable_arrowchat = 0;
		$group_disable_apps = 0;
		$group_disable_video = 0;
		$group_disable_rooms = 0;
		$group_disable_uploads  = 0;
		$group_disable_sending_private = 0;
		$group_disable_sending_rooms = 0;
	}
	
	// Start Session and Include Core Files
	session_start();
	require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "bootstrap.php");

	// Check Admin is Logged In
	require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_ADMIN . DIRECTORY_SEPARATOR . "includes/admin_init.php");
	
	// Set Upgrade Ran Test Variable to False
	$ran = false;
	
	// Do all the upgrades
	if (isset($_POST['upgrade']) || isset($_POST['upgrade_x'])) 
	{
		$val = $db->execute("SELECT 1 FROM `arrowchat_reports`");
		
		if ($val !== FALSE)
		{
			$result = $db->execute("
				INSERT IGNORE INTO arrowchat_config (config_name, config_value, is_dynamic)
				VALUES ('window_top_padding', '70', '0'), ('video_chat_selection', '1', '0'), ('video_chat_width', '900', '0'), ('video_chat_height', '600', '0'), ('tokbox_api', '', '0'), ('tokbox_secret', '', '0'), ('chatroom_default_names', '0', '0'), ('group_disable_arrowchat', '', '0'), ('group_disable_video', '', '0'), ('group_disable_apps', '', '0'), ('group_disable_rooms', '', '0'), ('group_disable_uploads', '', '0'), ('group_disable_sending_private', '', '0'), ('group_disable_sending_rooms', '', '0'), ('group_enable_mode', '0', '0'), ('online_list_on', '1', '0')
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_rooms`
				ADD `disallowed_groups` text AFTER `limit_seconds_num`
			");
		}
		else
		{
			$result = $db->execute("
				INSERT IGNORE INTO arrowchat_config (config_name, config_value, is_dynamic)
				VALUES ('max_upload_size', '5', '0'), ('enable_moderation', '0', '0'), ('chatroom_transfer_on', '0', '0'), ('chatroom_message_length', '150', '0'), ('push_ssl', '0', '0'), ('window_top_padding', '70', '0'), ('video_chat_selection', '1', '0'), ('video_chat_width', '900', '0'), ('video_chat_height', '600', '0'), ('tokbox_api', '', '0'), ('tokbox_secret', '', '0'), ('chatroom_default_names', '0', '0'), ('group_disable_arrowchat', '', '0'), ('group_disable_video', '', '0'), ('group_disable_apps', '', '0'), ('group_disable_rooms', '', '0'), ('group_disable_uploads', '', '0'), ('group_disable_sending_private', '', '0'), ('group_disable_sending_rooms', '', '0'), ('group_enable_mode', '0', '0'), ('online_list_on', '1', '0')
			");
			
			$result = $db->execute("
				TRUNCATE arrowchat_smilies
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_smilies`
				MODIFY `code` varchar(20)
			");
			
			$result = $db->execute("
				UPDATE arrowchat_status
				SET chatroom_stay = 0
			");
			
			$result = $db->execute("
				INSERT IGNORE INTO arrowchat_smilies (id, name, code)
				VALUES (1, 'smile', ':)'), (2, 'big_grin', ':D'), (3, 'wink', ';)'), (4, 'agape', ':o'), (5, 'bored', ':|'), (6, 'crying', ':''('), (7, 'tongue', ':p'), (8, 'confused', ':s'), (9, 'smile', ':-)'), (10, 'frown', ':-('), (11, 'wink', ';-)'), (12, 'agape', ':-o'), (13, 'bored', ':-|'), (14, 'tongue', ':-p'), (15, 'confused', ':-s'), (16, 'mad', '>:('), (17, 'dead', 'X('), (18, 'delicious', '[delicious]'), (19, 'dont_cry', '[dontcry]'), (20, 'evil', '[evil]'), (21, 'evil_grin', '[evilgrin]'), (22, 'impatient', '[impatient]'), (23, 'inlove', '<3'), (24, 'kiss', ':-*'), (25, 'nerdy', '[nerd]'), (26, 'not_even', '[noteven]'), (27, 'oh_rly', '[ohrly]'), (28, 'shocked', '[shocked]'), (29, 'sick', '[sick]'), (30, 'sing', '[sing]'), (31, 'stress', '[stress]'), (32, 'sunglasses_1', '8)'), (33, 'whistle', '[whistle]'), (34, 'yawn', '[yawn]'), (35, 'zipped', ':X'), (36, 'frown', ':(')
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_rooms`
				ADD `description` varchar(100) collate utf8_bin default '' AFTER `name`,
				ADD `welcome_message` varchar(255) collate utf8_bin default '' AFTER `description`,
				ADD `image` varchar(100) collate utf8_bin default '' AFTER `welcome_message`,
				ADD `limit_message_num` int(5) NOT NULL default '3' AFTER `image`,
				ADD `limit_seconds_num` int(5) NOT NULL default '10' AFTER `limit_message_num`,
				ADD `disallowed_groups` text AFTER `limit_seconds_num`
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_status`
				ADD `chatroom_show_names` tinyint(1) unsigned default NULL AFTER `chatroom_stay`,
				ADD `is_mod` tinyint(1) unsigned NOT NULL default '0' AFTER `is_admin`,
				ADD `ip_address` varchar(40) default '' AFTER `is_mod`
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_status`
				MODIFY `chatroom_stay` varchar(6) NOT NULL default '0',
				MODIFY `chatroom_window` varchar(6) NOT NULL default '-1'
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_messages`
				ADD `action` tinyint(1) unsigned default '0' AFTER `sent`
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_banlist`
				ADD `id` int(10) PRIMARY KEY AUTO_INCREMENT FIRST
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_banlist`
				ADD `ip_address` varchar(40) default NULL AFTER `ban_time`
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_users` DROP PRIMARY KEY
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_users`
				ADD UNIQUE KEY(`user_id`,`chatroom_id`)
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_chatroom_users`
				ADD `silence_length` int(3) unsigned default NULL AFTER `block_chats`,
				ADD `silence_time` int(15) unsigned default NULL AFTER `silence_length`
			");
			
			$result = $db->execute("
				ALTER TABLE `arrowchat_banlist`
				ADD `banned_by` varchar(25) NOT NULL AFTER `ban_ip`,
				ADD `banned_time` int(20) unsigned NOT NULL AFTER `banned_by`
			");
			
			$result = $db->execute("
				CREATE TABLE IF NOT EXISTS `arrowchat_reports` (
					`id` int(25) unsigned NOT NULL auto_increment,
					`report_from` varchar(25) NOT NULL,
					`report_about` varchar(25) NOT NULL,
					`report_chatroom` int(10) unsigned NOT NULL,
					`report_time` int(20) unsigned NOT NULL,
					`working_by` varchar(25) NOT NULL,
					`working_time` int(20) unsigned NOT NULL,
					`completed_by` varchar(25) NOT NULL,
					`completed_time` int(20) unsigned NOT NULL,
					PRIMARY KEY  (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8
			");
			
			$result = $db->execute("
				CREATE TABLE IF NOT EXISTS `arrowchat_warnings` (
					`id` int(25) unsigned NOT NULL auto_increment,
					`user_id` varchar(25) NOT NULL,
					`warn_reason` text,
					`warned_by` varchar(25) NOT NULL,
					`warning_time` int(20) unsigned NOT NULL,
					`user_read` tinyint(1) unsigned NOT NULL default '0',
					PRIMARY KEY  (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8
			");
		}

		if ($result)
		{
			update_config_file();
			$ran = true;
		}
		else
		{
			die("There was a mySQL error or the upgrade was already run.  Make sure your MySQL user has the ability to create tables.");
		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr"> 
<head profile="http://gmpg.org/xfn/11"> 
 
	<title>ArrowChat | Upgrade Installation</title> 
 
</head> 
 
<body> 

	<div style="font-size: 14px; font-family: arial, verdana; margin: 0 auto; width: 700px; margin-top: 50px;">
	
		<span style="font-weight: bold; font-size: 18px;">ArrowChat Upgrade Process</span>
		
		<br /><br />
		
<?php
	if ($ran) {
?>
		Your ArrowChat installation has been upgraded.  It is safe to delete the upgrade folder.
<?php
	} else {
?>
		Running this upgrade process will make the necessary upgrades to your ArrowChat database structure.  This is not requried on a fresh install of ArrowChat.  It is safe to delete the upgrade folder after this process is run or on a fresh install of ArrowChat.
		
		<br /><br />
		
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data"> 
		
			<input type="submit" name="upgrade" value="Run Upgrade" />
		
		</form>
<?php
	}
?>
	
	</div>

</body>
</html>