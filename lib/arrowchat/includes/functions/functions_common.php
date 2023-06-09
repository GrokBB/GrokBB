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
	
	/**
	 * Checks the cookie for an existing guest account.  If one exists, return
	 * the user ID.  If not, create a guest account and add the cookie.
	 *
	 * @return	string	The user ID of the guest
	*/
	function check_guest_hash()
	{
		global $db;
		
		if (isset($_COOKIE['arrowchat_guest_hash']))
		{
			$result = $db->execute("
				SELECT userid
				FROM arrowchat_status
				WHERE hash_id = '".$db->escape_string($_COOKIE['arrowchat_guest_hash'])."'
			");
			
			if ($result AND $db->count_select() > 0)
			{
				// Guest exists, get hash
				$row = $db->fetch_array($result);
				
				$userid = $row['userid'];
			}
			else
			{
				$userid = create_guest_hash();
			}
		}
		else
		{
			$userid = create_guest_hash();
		}
		
		return $userid;
	}
	
	/**
	 * Creates a guest hash and returns the user id
	 *
	 * @return	string	The user ID of the guest
	*/
	function create_guest_hash()
	{
		global $db;
		
		$hash_id = random_string();
		
		$userid = "g" . rand(1, 9999999);
		
		$result = $db->execute("
			INSERT INTO arrowchat_status (userid, chatroom_window, chatroom_stay, hash_id, session_time) 
			VALUES ('" . $db->escape_string($userid) . "', '-1', '0', '" . $hash_id . "', '" . time() . "')
		");
		
		if ($result)
		{
			$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? '.' . $_SERVER['HTTP_HOST'] : false;
			setcookie('arrowchat_guest_hash', $hash_id, time() + 3600 * 24 * 365 * 10, '/', $domain, false);
		}
		
		return $userid;
	}
	
	/**
	 * Creates a guest username and returns it
	 * @param	string	$userId		The user ID of the guest
	 * @param	string	$guest_name	The name of the guest.
	 * @param	bool	$get_username True if you want to get the username via SQL
	 * @return	string	The username of a guest
	*/
	function create_guest_username($userId, $guest_name = '', $get_username = false)
	{
		global $language;
		global $db;
		global $guest_name_change;
		
		if ($get_username && $guest_name_change == 1)
		{
			$result = $db->execute("
				SELECT DISTINCT arrowchat_status.guest_name
				FROM arrowchat_status
				WHERE arrowchat_status.userid = '" . $db->escape_string($userId) . "'
			");
			
			$row = $db->fetch_array($result);
			$guest_name2 = $row['guest_name'];
		}
		else
		{
			if (empty($guest_name) || $guest_name_change != 1)
			{
				$guest_name2 = $language[83] . " " . substr($userId, 1);
			}
			else
			{
				$guest_name2 = $guest_name;
			}
		}
		
		return $guest_name2;
	}
	
	/**
	 * Checks if the user ID is a guest
	 *
	 * @return	bool	True if guest, false if not
	*/
	function check_if_guest($userid)
	{
		if ($userid[0] == "g")
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Returns the SQL to get guest details
	 *
	 * @param	string	$userid	The user to retrieve details for
	 * @return the SQL statement to retrieve the guest details
	 */
	function get_guest_details($userid)
	{
		global $db;
		
		$sql = ("
			SELECT DISTINCT arrowchat_status.userid, arrowchat_status.session_time lastactivity, arrowchat_status.status, arrowchat_status.guest_name, arrowchat_status.is_admin
			FROM arrowchat_status
			WHERE arrowchat_status.userid = '" . $db->escape_string($userid) . "'
		");
		
	   return $sql;
	}
	
	/**
	 * Returns the SQL to get guest users
	 *
	 * @return the SQL statement to retrieve all guest users
	 */
	function get_guest_list() 
	{
		global $online_timeout;
		
		$sql = ("
			SELECT DISTINCT arrowchat_status.userid, arrowchat_status.session_time lastactivity, arrowchat_status.status, arrowchat_status.guest_name, arrowchat_status.is_admin
			FROM arrowchat_status
			WHERE arrowchat_status.session_time > (" . time() . " - " . $online_timeout . " - 60)
				AND LEFT(arrowchat_status.userid, 1) = 'g'
			ORDER BY arrowchat_status.userid ASC
		");
		
	   return $sql;
	}
	
	/**
	 * Gets POST or GET variables safely
	 *
	 * @param	string	$var	The post/get variable to get
	 * @return	string	The value of the variable, false if it does not exist
	*/
	function get_var($var)
	{
		global $db;
		
		$value = "0";
		
		if (isset($_GET[$var]) AND $_GET[$var] !== false)
		{
			$value = $_GET[$var];
		}
		
		if (isset($_POST[$var]) AND $_POST[$var] !== false)
		{
			$value = $_POST[$var];
		}
		
		if (isset($_POST[$var . '_x']) AND $_POST[$var . '_x'] !== false)
		{
			$value = $_POST[$var . '_x'];
		}
		
		return $value;
	}
	
	/**
	 * Replaces line breaks in a string with a space
	 *
	 * @param	string	$string	The string
	 * @return	string	The string with line breaks converted to space
	*/
	function line_break_replace($string) 
	{
		return stripslashes(trim(preg_replace("/\s+/", " ", $string)));
	}
	
	/**
	 * Puts the contents of a file into a string
	 *
	 * @param	string	$filename	The path to the file
	 * @return	string	The contents of the file
	*/
	function get_include_contents($filename) 
	{
		if (file_exists($filename)) 
		{
			if ($contents = stripslashes(file_get_contents($filename, true)))
			return $contents;
		} 
		else 
		{
			return "We could not find that file.  Check that it exists in the theme templates folder.";
		}
	}
	
	/**
	 *
	 * Updates the user session with the current time
	 *
	*/
	function updateUserSession() 
	{
		global $db;
		global $userid;
		global $popout;
		global $popout_time;
		
		if ($popout AND $popout_time != "99") 
		{
			$db->execute("
				UPDATE arrowchat_status 
				SET session_time = '" . time() . "', popout = '" . time() . "' 
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		} 
		else 
		{
			$db->execute("
				UPDATE arrowchat_status 
				SET session_time = '" . time() . "' 
				WHERE userid = '" . $db->escape_string($userid) . "'
			");
		}
	}
	
	/**
	 * Used to emulate the before needle on strstr for PHP below v5.3
	 * @param	string	$h
	 * @param	string	$n
	 * @return	string
	*/
	function strstrb($h, $n)
	{
		$tmp = explode($n, $h, 2);
		return array_shift($tmp);
	}
	
	/**
	 * Checks if a POST or GET exists
	 *
	 * @param	string	$var	The post/get variable to check
	 * @return	bool	True if it exists; false if not
	*/
	function var_check($var)
	{
		$value = false;
		
		if (isset($_GET[$var]) AND $_GET[$var] !== false)
		{
			$value = true;
		}
		
		if (isset($_POST[$var]) AND $_POST[$var] !== false)
		{
			$value = true;
		}
		
		if (isset($_POST[$var . '_x']) AND $_POST[$var . '_x'] !== false)
		{
			$value = true;
		}
		
		return $value;
	}

	/**
	 * Gets a random hash combination if the user does not have a hash 
	 *
	 * @return	string	a random letter and number combination
	 */
	function random_string() 
	{
		$length = 20;
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$string ='';
		
		for ($p = 0; $p < $length; $p++) 
		{
			$string .= $characters[mt_rand(0, strlen($characters) - 1)];
		}
		
		return $string;
	}

	/**
	 * Turns text URLs into HTML links
	 *
	 * @param	string	$s	The text you would link to convert
	 * @return	string	The converted HTML text
	*/
	function text_to_url($s) 
	{
		return preg_replace("! \b((([a-z]{3,5}://))". "[-a-z0-9.]{2,}\.[a-z]{2,4}". "(:[0-9]+)?". "(/([^\s]*[^\s,.])?)?". "(\?([^\s]*[^\s,.])?)?)\b!i",  "<a target=\"_blank\" href=\"\\1\">\\1</a>", $s);
	}
	
	/**
	 * Turns a unix time stamp into relative text (ex: 56 seconds ago)
	 *
	 * @param	integer	$message_time	The unix time you with to convert
	 * @return	string	The time in relative text
	*/
	function relative_time($message_time) 
	{
		global $language;
		
		$longago = time() - $message_time;
		
		if ($longago < 60) 
		{
			$longago = $longago . (($longago == 1) ? " ".$language[71] : " ".$language[72]);
		} 
		else if ($longago >= 60 && $longago < 3600) 
		{
			$longago = round($longago / 60) . ((round($longago / 60) == 1) ? " ".$language[73] : " ".$language[74]);
		} 
		else if ($longago >= 3600 && $longago < 86400) 
		{
			$longago = round($longago / 3600) . ((round($longago / 3600) == 1) ? " ".$language[75] : " ".$language[76]);
		} 
		else if ($longago >= 86400 && $longago < 2073600) 
		{
			$longago = round($longago / 86400) . ((round($longago / 86400) == 1) ? " ".$language[77] : " ".$language[78]);
		} 
		else if ($longago >= 2073600 && $longago < 24883200) 
		{
			$longago = round($longago / 2073600) . ((round($longago / 2073600) == 1) ? " ".$language[79] : " ".$language[80]);
		} 
		else if ($longago >= 24883200) 
		{
			$longago = round($longago / 24883200) . ((round($longago / 24883200) == 1) ? " ".$language[81] : " ".$language[82]);
		}
			
		return $longago;
	}
	
	/**
	 * Gets the markup for notifications and returns the string that should be displayed
	 *
	 * @param	integar $author_id The user ID of the person who sent the notification
	 * @param	string $author_name The username of the person who sent the notification
	 * @param	integar $type The type of notification being retrieved
	 * @param	integar $message_time The unix time of when the message was sent
	 * @param	string $misc1 A miscellaneous string that can be used for anything
	 * @param	string $misc2 A miscellaneous string that can be used for anything
	 * @param	string $misc3 A miscellaneous string that can be used for anything
	 * @return	string	The notification the way it should be displayed
	 */
	function get_markup($author_id, $author_name, $type, $message_time, $misc1, $misc2, $misc3) 
	{
		global $db;
		
		$markup = "";
		$longago = relative_time($message_time);
	
		$result = $db->execute("
			SELECT markup 
			FROM arrowchat_notifications_markup 
			WHERE type='".$type."'
		");
		
		if ($result AND $db->count_select() > 0) 
		{
			$row = $db->fetch_array($result);
			$markup = $row['markup'];
			
			$markup = str_replace("{author_name}", $author_name, $markup);
			$markup = str_replace("{author_id}", $author_id, $markup);
			$markup = str_replace("{longago}", $longago, $markup);
			$markup = str_replace("{message_time}", $message_time, $markup);
			$markup = str_replace("{misc1}", $misc1, $markup);
			$markup = str_replace("{misc2}", $misc2, $markup);
			$markup = str_replace("{misc3}", $misc3, $markup);
		}
		
		return $markup;
	}
	
	/**
	 * Checks if a user is logged in
	 *
	 * @param	string	$userid	The user ID to check
	 * @return	bool	Returns true if logged in; false if not
	*/
	function logged_in($userid = NULL) 
	{
		if (isset($userid))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks if a value exists in a multi-dimensional array
	 *
	 * @param	string	$needle The needle
	 * @param	string	$haystack The haystack
	 * @return	bool	Returns true if value exists, false if not
	*/
	function in_array_r($needle, $haystack) 
	{
		foreach ($haystack as $item) 
		{
			if ($item === $needle OR (is_array($item) AND in_array_r($needle, $item))) 
			{
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Counts the number of days between two dates
	 *
	 * @param	int		$a	The start date
	 * @param	int		$b	The end date
	 * @return	int		The number of days
	*/
	function count_days( $a, $b ) 
	{
		$gd_a = getdate( $a );
		$gd_b = getdate( $b );

		$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
		$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );

		return round( abs( $a_new - $b_new ) / 86400 );
	}
	
	/**
	 * Closes a PHP session
	 *
	*/
	function close_session()
	{
		@session_write_close();
	}
	
	/**
	 * Turns a string link into HTML
	 *
	 * @param	string	$s	The string to convert
	 * @return	string	The converted links into HTML
	*/
	function clickable_links($s) 
	{
		return preg_replace("!((([a-z]{3,5}://))". "[-a-z0-9.]{2,}\.[a-z]{2,4}". "(:[0-9]+)?". "(/([^\s]*[^\s,.])?)?". "(\?([^\s]*[^\s,.])?)?)!i",  "<a target=\"_blank\" href=\"\\1\">\\1</a>", $s);
	}
	
	/**
	 * Checks if the user agent is a bot
	 *
	 * @return	bool	True for a bot and false for not a bot
	*/
	function is_bot()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|alexa|froogle|inktomi|looksmart|firefly|directory|jeeves|tecnoseek|infoseek|galaxy|scooter|slurp|appie|fast|webbug|spade|zyborg|rabaz|feedfetcher|snoop|mediapartners|yandex|stackrambler/i', $_SERVER['HTTP_USER_AGENT'])) 
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Gets the user's IP address
	 *
	 * @return	string	The user's IP address
	*/
	function get_client_ip() 
	{
		$ipaddress = '';
		
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if (getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if (getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if (getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if (getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if (getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
			
		return $ipaddress;
	}
	
	/**
	 * Checks two multi-dimensional arrays for matches
	 *
	 * @param array	The needle array
	 * @param array the haystack array
	 * @return	bool	True if there is a match
	*/
	function check_array_for_match($needle, $haystack)
	{
		foreach ($needle as $value)
		{
			if (in_array($value, $haystack))
			{
				return true;
			}
		}
		
		return false;
	}

?>