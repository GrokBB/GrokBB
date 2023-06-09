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
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');

	function makeThumbnails($updir, $img, $id, $MaxWe=139, $MaxHe=130)
	{
		$arr_image_details = getimagesize($img); 
		$width = $arr_image_details[0];
		$height = $arr_image_details[1];

		$percent = 100;
		if($width > $MaxWe) $percent = floor(($MaxWe * 100) / $width);

		if(floor(($height * $percent)/100)>$MaxHe)  
			$percent = (($MaxHe * 100) / $height);

		if($width > $height) 
		{
			$newWidth=$MaxWe;
			$newHeight=round(($height*$percent)/100);
		}
		else
		{
			$newWidth=round(($width*$percent)/100);
			$newHeight=$MaxHe;
		}

		if ($arr_image_details[2] == 1) 
		{
			$imgt = "ImageGIF";
			$imgcreatefrom = "ImageCreateFromGIF";
		}
		if ($arr_image_details[2] == 2) 
		{
			$imgt = "ImageJPEG";
			$imgcreatefrom = "ImageCreateFromJPEG";
		}
		if ($arr_image_details[2] == 3) 
		{
			$imgt = "ImagePNG";
			$imgcreatefrom = "ImageCreateFromPNG";
		}


		if ($imgt) 
		{
			$old_image = $imgcreatefrom($img);
			$new_image = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresized($new_image, $old_image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

			$imgt($new_image, $updir."".$id."_t.jpg");
			
			return;    
		}
	}

	// ###################### START MAIN UPLOAD SCRIPT #######################
	if (!empty($_FILES)) 
	{
	if ($file_transfer_on == 1 || $chatroom_transfer_on == 1)
		{
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_UPLOADS . DIRECTORY_SEPARATOR;
			$fileParts  = pathinfo($_FILES['Filedata']['name']);
			$targetFile =  $targetPath . $db->escape_string($_POST['unixtime']) . "." . $fileParts['extension'];
			$ext = strtolower($fileParts['extension']);
			
			// Exit if the image is not a valid image
			if (function_exists('exif_imagetype'))
			{
				if (!exif_imagetype($tempFile) && ($ext == "jpg" || $ext == "gif" || $ext == "png" || $ext == "jpeg"))
				{
					http_response_code(500);
					exit;
				}
			}
			
			// Make a thumbnail if it is an image
			if ($ext == "jpg" || $ext == "gif" || $ext == "png" || $ext == "jpeg")
				makeThumbnails($targetPath, $tempFile, $db->escape_string($_POST['unixtime']));
			
			// Move the file to the uploads directory
			move_uploaded_file($tempFile, $targetFile);
			
			echo str_replace(dirname(dirname(dirname(dirname(__FILE__)))), '', $targetFile);
		}
		else
		{
			http_response_code(500);
			exit;
		}
	}
	
?>