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
	require_once (dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "bootstrap.php");
	
	// ########################## START MAIN SCRIPT ###########################
	$get_file 	= get_var('file');
	$dir 		= dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . AC_FOLDER_UPLOADS;
 
	$dh = opendir($dir);
 
	// Get each filename in the upload directory to see if it exists
	while (($file = readdir($dh)) !== false) 
	{
		$test = strstrb($file, ".");
		
		if (strcasecmp($test, $get_file) == 0) 
		{
			$tmp = explode('.', $file);
			$extension = strtolower(end($tmp));
			$contentType = "application/zip";
			
			if ($extension == "jpg" || $extension == "jpeg" || $extension == "gif" || $extension == "png")
			{
				$content = getimagesize($file);
				$contentType = $content['mime'];
			}

			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename=$file");
			header("Content-Type: $contentType");
			header("Content-Transfer-Encoding: binary");
			
			readfile($dir . "/" . $file);		
		}
	}
	
	die("The file was not found or was deleted.");
	closedir($dh);

?>