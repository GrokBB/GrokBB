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
	 * Removes an element from an array
	 *
	 * @param	array	$array	The array to use
	 * @param	string	$element	The string to remove from the array
	 * @return	array	The new array
	*/
	function array_delete($array, $element) {
		return array_diff($array, array($element));
	}
	
	/**
	 * Calculates the size of a database
	 *
	 * @param	string	$database	The name of the database to check
	 * @return	int		The size in bytes
	*/
	function CalcFullDatabaseSize($database) {
		global $db;
	 
		$result = $db->execute("
			SHOW TABLES 
			FROM " . $db->escape_string($database) . "
		");

		if (!$result) 
		{ 
			return -1; 
		}
	 
		$table_count = $db->count_select();
		$size = 0;
	 
		for ($i=0; $i < $table_count; $i++) 
		{
			$tname = $db->table_name($result, $i);
			
			$result2 = $db->execute("
				SHOW TABLE STATUS 
				FROM " . $db->escape_string($database) . " 
				LIKE '" . $db->escape_string($tname) . "'
			");
			
			$data = $db->fetch_array($result2);
			$size += ($data['Index_length'] + $data['Data_length']);
		};
	 
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		
		for ($i = 0; $size > 1024; $i++) 
		{ 
			$size /= 1024; 
		}
		
		return round($size, 2) . $units[$i];
	}
	
	/**
	 * Removes a directory
	 *
	 * @param	string	$dir	The directory to remove
	*/
	function remove_dir($dir) 
	{ 
		if (is_dir($dir)) 
		{ 
			$objects = scandir($dir); 
			
			foreach ($objects as $object) 
			{ 
				if ($object != "." AND $object != "..") 
				{ 
					if (filetype($dir."/".$object) == "dir") 
					{
						rrmdir($dir."/".$object); 
					}
					else
					{
						unlink($dir."/".$object); 
					}
				} 
			} 
			
			reset($objects); 
			rmdir($dir); 
		} 
	}
	
	/**
	 * Splits an sql file to be readable
	 *
	 * @param	string	$sql	The SQL to split
	 * @param	string	$delimiter	The delimiter on where to split the SQL
	 * @return	array	The SQL statements in an array
	*/
	function split_sql_file($sql, $delimiter)
	{
		$sql = str_replace("\r" , '', $sql);
		$data = preg_split('/' . preg_quote($delimiter, '/') . '$/m', $sql);

		$data = array_map('trim', $data);

		$end_data = end($data);

		if (empty($end_data))
		{
			unset($data[key($data)]);
		}

		return $data;
	}
	
	/**
	 * Removes remakes from the SQL
	 *
	 * @sql	string	$sql	The SQL to remove remakes from
	*/
	function remove_remarks(&$sql)
	{
		$sql = preg_replace('/\n{2,}/', "\n", preg_replace('/^#.*$/m', "\n", $sql));
	}
	
	/**
	 * Flushs the headers
	 *
	*/
	function flush_headers()
	{
		echo(str_repeat(' ',256));

		if (ob_get_length())
		{            
			@ob_flush();
			@flush();
		}    
		
		@ob_start();
	}
	 
	/**
	 * Checks to see if the install folder exists
	 *
	 * @return	bool	True if the folder exists; false if not
	*/
	function check_install_folder()
	{	
		$dir = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . AC_FOLDER_INSTALL . DIRECTORY_SEPARATOR . "index.php";
		$install = true;
		
		if (file_exists($dir)) 
		{
			$install = false;
		}
		
		return $install;
	}
	
	/**
	 * Checks to see if the file is writable
	 *
	 * @return	bool	True if the file is writable; false if not
	*/
	function check_config_file()
	{
		$dir = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . "config.php";
		$write = true;
		
		if (is_file_writable($dir)) 
		{
			$write = false;
		
			$config_permissions = substr(sprintf('%o', fileperms($dir)), -4);
			
			if ($config_permissions == "0644" OR $config_permissions == "0640")
			{
				$write = true;
			}
		}
		
		return $write;
	}
	
	
	/**
	 * Gets all the folders within the specified directory
	 *
	 * @return array An array of all the folders
	*/
	function get_folders($directory)
	{
		$directorylist = array();
		$startdir = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR;
		
		$ignoredDirectory[] = '.'; 
		$ignoredDirectory[] = '..';
		
		if (is_dir($startdir))
		{
			if ($dh = opendir($startdir))
			{
				while (($folder = readdir($dh)) !== false)
				{
					if (!(array_search($folder, $ignoredDirectory) > -1))
					{
						if (filetype($startdir . $folder) == "dir")
						{
							$directorylist[$startdir . $folder]['name'] = $folder;
							$directorylist[$startdir . $folder]['path'] = $startdir;
						}
					}
				}
				closedir($dh);
		   }
		}
		
		return $directorylist;
	}
	
	/**
	 * Convert numeric theme to the theme's folder name
	 *
	 * @param	string	$theme	The current variable for theme
	 * @return	string	The theme's folder name
	*/
	function convert_numeric_theme($theme)
	{
		global $db;
		
		if (is_numeric($theme)) 
		{
			$result = $db->execute("
				SELECT folder 
				FROM arrowchat_themes 
				WHERE id='" . $db->escape_string($theme) . "'
			");
			
			if ($result AND $db->count_affected() > 0) 
			{
				$row = $db->fetch_array($result);
				$theme = $row['folder'];
			} 
			else 
			{
				$theme = "new_facebook";
			}
		}
		
		return $theme;
	}

	/**
	 * Check if a file has write permissions
	 *
	 * @param	string	$file	The path to the file to be checked
	 * @return	bool	True if it can be written; false if it cannot
	*/
	function is_file_writable($file)
	{
		if (strtolower(substr(PHP_OS, 0, 3)) === 'win' OR !function_exists('is_writable'))
		{
			if (file_exists($file))
			{
				// Canonicalise path to absolute path
				if (is_dir($file))
				{
					// Test directory by creating a file inside the directory
					$result = @tempnam($file, 'i_w');

					if (is_string($result) AND file_exists($result))
					{
						unlink($result);

						// Ensure the file is actually in the directory (returned realpathed)
						return (strpos($result, $file) === 0) ? true : false;
					}
				}
				else
				{
					$handle = @fopen($file, 'r+');

					if (is_resource($handle))
					{
						fclose($handle);
						return true;
					}
				}
			}
			else
			{
				// file does not exist test if we can write to the directory
				$dir = dirname($file);

				if (file_exists($dir) AND is_dir($dir) AND is_file_writable($dir))
				{
					return true;
				}
			}

			return false;
		}
		else
		{
			return is_writable($file);
		}
	}
	
?>