<?php

	/*
	|| #################################################################### ||
	|| #                             ArrowChat                            # ||
	|| # ---------------------------------------------------------------- # ||
	|| #    Copyright �2010-2012 ArrowSuites LLC. All Rights Reserved.    # ||
	|| # This file may not be redistributed in whole or significant part. # ||
	|| # ---------------- ARROWCHAT IS NOT FREE SOFTWARE ---------------- # ||
	|| #   http://www.arrowchat.com | http://www.arrowchat.com/license/   # ||
	|| #################################################################### ||
	*/
	
	session_start();

	/**
	 * This function returns the user ID of the logged in user on your site.  Technical support will not
	 * help you with this for stand-alone installations.  You must purchase the professional installation
	 * if you are having trouble.
	 *
	 * Suggestion: Check out the other integration files in the functions/integrations directory for
	 * many examples of how this can be done.  The easiest way is to get the user ID through a cookie.
	 *
	 * @return the user ID of the logged in user or NULL if not logged in
	 */
	function get_user_id() 
	{
		global $db;

		$userid = NULL;

		if (!empty($_COOKIE['PHPSESSID'])) 
		{
			$result = $db->execute("
				SELECT data
				FROM " . TABLE_PREFIX . "core_session 
				WHERE id = '" . $db->escape_string($_COOKIE['PHPSESSID']) . "'
			");

			if ($row = $db->fetch_array($result))
			{
				$_SESSION['Zend_Auth']['storage'] = 0;
				session_decode($row['data']);
				
				if (filter_var($_SESSION['Zend_Auth']['storage'], FILTER_VALIDATE_EMAIL))
				{
					$email = $_SESSION['Zend_Auth']['storage'];
					
					$result = $db->execute("
						SELECT user_id
						FROM " . TABLE_PREFIX . DB_USERTABLE . "
						WHERE email = '" . $db->escape_string($email) . "'
					");
					
					if ($row = $db->fetch_array($result))
					{
						$userid = $row['user_id']; 
					}
				} 
				else if (filter_var($_SESSION['Zend_Auth']['storage'], FILTER_VALIDATE_INT)) 
				{
					$userid = $_SESSION['Zend_Auth']['storage'];
				}
			}
		}

		return $userid;
	}

	/**
	 * This function returns the SQL statement for the buddylist of the user.  You should retrieve
	 * all ONLINE friends that the user is friends with.  Do not retrieve offline users.  You can use
	 * global $online_timeout to get the online timeout.
	 * ex: AND (arrowchat_status.session_time + 60 + " . $online_timeout . ") > " . time() . " 
	 *
	 * @param userid the user ID of the person receiving the buddylist
	 * @param the time of the buddylist request
	 * @return the SQL statement to retrieve the user's friend list
	 */
	function get_friend_list($userid, $time)
	{
		global $db;
		global $online_timeout;
		
		$sql = ("
			SELECT DISTINCT " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " userid, " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_NAME . " username, arrowchat_status.session_time lastactivity, " . TABLE_PREFIX . "storage_files.storage_path avatar, " . TABLE_PREFIX . DB_USERTABLE . ".username link, arrowchat_status.is_admin, arrowchat_status.status 
			FROM " . TABLE_PREFIX . DB_FRIENDSTABLE . " 
			JOIN " . TABLE_PREFIX . DB_USERTABLE . " 
				ON  " . TABLE_PREFIX . DB_FRIENDSTABLE . "." . DB_FRIENDSTABLE_FRIENDID . " = " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " 
			LEFT JOIN arrowchat_status 
				ON " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = arrowchat_status.userid 
			LEFT JOIN " . TABLE_PREFIX . "storage_files
				ON (" . TABLE_PREFIX . "storage_files.user_id = " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . "
					OR " . TABLE_PREFIX . "storage_files.parent_id = " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . ")
					AND " . TABLE_PREFIX . "storage_files.parent_file_id = (" . TABLE_PREFIX.DB_USERTABLE . ".photo_id)
					AND " . TABLE_PREFIX . "storage_files.type = 'thumb.icon'
			WHERE " . TABLE_PREFIX . DB_FRIENDSTABLE . "." . DB_FRIENDSTABLE_USERID . " = '" . $db->escape_string($userid) . "' 
				AND " . TABLE_PREFIX . DB_FRIENDSTABLE . "." . DB_FRIENDSTABLE_FRIENDS . " = '1' 
				AND arrowchat_status.session_time > (" . time() . " - " . $online_timeout . " - 60)
			ORDER BY " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_NAME . " ASC
		");
		
		return $sql; 
	}

	/**
	 * This function returns the SQL statement for all online users.  You should retrieve
	 * all ONLINE users regardless of friend status.  Do not retrieve offline users.  You can use
	 * global $online_timeout to get the online timeout.
	 * ex: AND (arrowchat_status.session_time + 60 + " . $online_timeout . ") > " . time() . " 
	 *
	 * @param userid the user ID of the person receiving the buddylist
	 * @param the time of the buddylist request
	 * @return the SQL statement to retrieve all online users
	 */
	function get_online_list($userid, $time) 
	{
		global $db;
		global $online_timeout;
		
		$sql = ("
			SELECT DISTINCT " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " userid, " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_NAME . " username, arrowchat_status.session_time lastactivity, " . TABLE_PREFIX . "storage_files.storage_path avatar, " . TABLE_PREFIX . DB_USERTABLE . ".username link, arrowchat_status.is_admin, arrowchat_status.status 
			FROM " . TABLE_PREFIX . DB_USERTABLE . " 
			JOIN arrowchat_status 
				ON " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = arrowchat_status.userid 
			LEFT JOIN " . TABLE_PREFIX . "storage_files
				ON (" . TABLE_PREFIX . "storage_files.user_id = " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . "
					OR " . TABLE_PREFIX . "storage_files.parent_id = " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . ")
					AND " . TABLE_PREFIX . "storage_files.parent_file_id = (" . TABLE_PREFIX.DB_USERTABLE . ".photo_id)
					AND " . TABLE_PREFIX . "storage_files.type = 'thumb.icon'
			WHERE arrowchat_status.session_time > (" . time() . " - " . $online_timeout . " - 60)
				AND " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " != '" . $db->escape_string($userid) . "' 
			ORDER BY " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_NAME . " ASC
		");
		
		return $sql; 
	}

	/**
	 * This function returns the SQL statement to get the user details of a specific user.  You should
	 * get the user's ID, username, last activity time in unix, link to their profile, avatar, and status.
	 *
	 * @param userid the user ID to get the details of
	 * @return the SQL statement to retrieve the user's defaults
	 */
	function get_user_details($userid) 
	{
		global $db;
		
		$sql = ("
			SELECT " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " userid, " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_NAME . " username, arrowchat_status.session_time lastactivity,  " . TABLE_PREFIX . DB_USERTABLE . ".username link, " . TABLE_PREFIX . "storage_files.storage_path avatar, arrowchat_status.is_admin, arrowchat_status.status 
			FROM " . TABLE_PREFIX . DB_USERTABLE . " 
			LEFT JOIN arrowchat_status 
				ON " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = arrowchat_status.userid 
			LEFT JOIN " . TABLE_PREFIX . "storage_files
				ON (" . TABLE_PREFIX . "storage_files.user_id = " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . "
					OR " . TABLE_PREFIX . "storage_files.parent_id = " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . ")
					AND " . TABLE_PREFIX . "storage_files.parent_file_id = (" . TABLE_PREFIX.DB_USERTABLE . ".photo_id)
					AND " . TABLE_PREFIX . "storage_files.type = 'thumb.icon'
			WHERE " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = '" . $db->escape_string($userid) . "'
		");
		
		return $sql;
	}

	/**
	 * This function returns the profile link of the specified user ID.
	 *
	 * @param userid the user ID to get the profile link of
	 * @return the link of the user ID's profile
	 */
	function get_link($link, $user_id) 
	{
		global $base_url;
		
		if (!empty($link))
		{
			return $base_url . "../profile/" . $link;
		}
		else
		{
			return $base_url . "../profile/" . $user_id;
		}
	}

	/**
	 * This function returns the URL of the avatar of the specified user ID.
	 *
	 * @param userid the user ID of the user
	 * @param image if the image includes more than just a user ID, this param is passed
	 * in from the avatar row in the buddylist and get user details functions.
	 * @return the link of the user ID's avatar
	 */
	function get_avatar($image, $user_id) 
	{
		global $base_url;
		
		if (!empty($image)) 
		{
			return $base_url . "../" . $image;
		} 
		else 
		{
			return $base_url . "../application/modules/User/externals/images/nophoto_user_thumb_icon.png";
		}
		
	}
	
	/**
	 * This function returns the group ID of the user into an array.
	 *
	 * @param userid the user ID of the user
	 * @return an array of groups the user is in or NULL if no groups
	 */ 
	function get_group_id($userid)
	{
		global $db;
		
		$group_ids = array();
      
		$result = $db->execute("
			SELECT level_id
			FROM " . TABLE_PREFIX . DB_USERTABLE . "
			WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($userid) . "'
		");
      
		if ($result AND $db->count_select() > 0) 
		{	 
			if ($row = $db->fetch_array($result))
			{
				$group_ids[] = $row['level_id'];
			}
			 
			return $group_ids;
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * This function returns an array of all the groups and their names so that
	 * the ArrowChat admin panel can manage them.
	 *
	 * @return nested arrays of the group IDs and names. The nested array must follow:
			   array(group id, group name)
	 */
	function get_all_groups()
	{
		global $db;
		
		$groups = array();
		
		$result = $db->execute("
			SELECT level_id, title
			FROM " . TABLE_PREFIX . "authorization_levels
		");
		
		if ($result AND $db->count_select() > 0) 
		{	 
			while ($row = $db->fetch_array($result))
			{
				$groups[] = array($row['level_id'], $row['title']);
			}
			 
			return $groups;
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * This function returns the name of the logged in user.  You should not need to
	 * change this function.
	 *
	 * @param userid the user ID of the user
	 * @return the name of the user
	 */
	function get_username($userid) 
	{ 
		global $db;
		global $language;
		global $show_full_username;
		
		$users_name = $language[83];

		$result = $db->execute("
			SELECT " . DB_USERTABLE_NAME . " name 
			FROM " . TABLE_PREFIX . DB_USERTABLE . " 
			WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($userid) . "'
		");  

		if ($result AND $db->count_select() > 0)  
		{
			$row = $db->fetch_array($result); 
			$users_name = $row['name']; 
		}

		$pieces = explode(" ", $users_name);
		
		if ($show_full_username == 1)
		{
			return $users_name;
		}
		else
		{
			return $pieces[0]; 
		}
	} 
	
	/**
	 * This is an extra function only used for SocialEngine 4
	 * Do not include this function with other integration files
	 */
	function unserializesession($data) 
	{
		if(strlen($data) == 0) 
		{
			return array();
		}

		preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $data, $matchesarray, PREG_OFFSET_CAPTURE);
		$returnArray = array();
		$lastOffset = NULL;
		$currentKey = '';
		
		foreach ($matchesarray[2] as $value) 
		{
			$offset = $value[1];
			
			if (!is_null($lastOffset)) 
			{
				$valueText = substr($data, $lastOffset, $offset - $lastOffset);
				$returnArray[$currentKey] = unserialize($valueText);
			}
			
			$currentKey = $value[0];
			$lastOffset = $offset + strlen($currentKey) + 1;
		}
		
		$valueText = substr($data, $lastOffset);
		$returnArray[$currentKey] = unserialize($valueText);

		return $returnArray;
	}

?>