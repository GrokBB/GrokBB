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
	 * Login the admin if user details are correct
	 *
	 * @param	string	$username	The username entered
	 * @param	string	$password	The password entered
	 * @return	string	NULL if logged in and the error message if not
	*/
	function admin_login($username, $password)
	{
		global $db;
		global $base_url;
		
		if (!isset($_SESSION['arrowchat_admin' . $base_url]))
		{
			if (!empty($username) AND !empty($password)) 
			{	
				$result = $db->execute("
					SELECT username, password
					FROM arrowchat_admin
					WHERE username = '" . $db->escape_string($username) . "'
				");

				if ($result AND $db->count_select() > 0) 
				{
					$row = $db->fetch_array($result);
				
					if (strtolower($row['username']) == strtolower($_POST['username']) AND $row['password'] == md5($_POST['password'])) 
					{
						$_SESSION['arrowchat_admin' . $base_url] = $row['username'];
						setcookie('arrowchat_admin' . $base_url, md5($_POST['password']), time() + 3600 * 24);
						$error = NULL;
					} 
					else
					{
						$error = "You have entered an invalid username and/or password.  Please try again.";
					}	
				} 
				else 
				{
					$error = "You have entered an invalid username and/or password.  Please try again.";
				}
			}
			else
			{
				$error = "You have not filled in the username or password.  Please try again.";
			}
		}
		
		return $error;
	}
	
	/**
	 * Check and see if the computer is already logged in
	 *
	 * @param string $error An error message if one exists
	*/
	function admin_check_login($error)
	{
		global $db;
		global $smarty;
		global $base_url;
		
		if (isset($_SESSION['arrowchat_admin' . $base_url])) 
		{

		} 
		else if (!empty($_COOKIE['arrowchat_admin' . $base_url]))
		{
			$result = $db->execute("
				SELECT username, password
				FROM arrowchat_admin
				WHERE password = '" . $db->escape_string($_COOKIE['arrowchat_admin' . $base_url]) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				
				$_SESSION['arrowchat_admin' . $base_url] = $row['username'];
			}
			else
			{
				$smarty->assign('error', $error);
				
				$smarty->display(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "layout/pages_login.tpl");
				exit();
			}
		}
		else
		{
			$smarty->assign('error', $error);
			
			$smarty->display(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "layout/pages_login.tpl");
			exit();
		}
	}
	
	/**
	 * Logout of the admin panel by removing cookies and sessions
	 *
	*/
	function admin_logout()
	{
		global $base_url;
		
		unset($_SESSION['arrowchat_admin' . $base_url]);
		setcookie('arrowchat_admin' . $base_url, '', time() - 3600);
		session_write_close();
		header("Location: ./");	
	}
	
?>