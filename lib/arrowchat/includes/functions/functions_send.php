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
	 * Does everything to turn text into a readable, safe format
	 *
	 * @param	string	$text	The text to convert
	 * @return	string	The new string in a readable, safe format
	*/
	function sanitize($text) 
	{
		global $base_url;
		global $db;
		global $disable_smilies;
		global $language;
		global $smileys;
		global $theme;
		global $blocked_words;
		
		$text = htmlspecialchars($text, ENT_NOQUOTES);
		$text = preg_replace('/\\\[rn]/', '<br/>', $text);
		
		// Get the theme's directory if it is numeric
		if (is_numeric($theme)) 
		{
			$result = $db->execute("
				SELECT folder 
				FROM arrowchat_themes 
				WHERE id='".$theme."'
			");
			
			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				$theme = $row['folder'];
			} 
			else 
			{
				$theme = "new_facebook";
			}
		}
		
		if (!empty($blocked_words))
		{
			$bad_words = explode(",", $blocked_words);
			$container_words = array();
			$exact_match_words = array();
			
			foreach ($bad_words as $word)
			{
				$s_word = trim($word);
				
				if (preg_match('/\[(.*?)\]/', $s_word))
				{
					$exact_match_words[] = trim($s_word, '[]');
				}
				else
				{
					$container_words[] = $s_word;
				}
			}

			if (!empty($exact_match_words))
				$text = preg_replace("/\b(" . implode($exact_match_words,"|") . ")\b/i", "****", $text);
				
			if (!empty($container_words))
				$text = preg_replace("/(" . implode($container_words,"|") . ")/i", "****", $text);
		}
		
		if ($disable_smilies != 1) 
		{ 
			if (!empty($smileys)) 
			{
				foreach ($smileys as $pattern => $result) 
				{
					$pattern = str_replace("\;", ";", $pattern);
					$pattern = htmlspecialchars($pattern);
					$text = str_replace($pattern, '<img class="arrowchat_smiley" height="16" width="16" src="' . $base_url . AC_FOLDER_THEMES . '/' . $theme . '/images/smilies/' . $result . '.png" alt="' . $result . '">', $text);
				}
			}
		}
		
		$text = preg_replace('@video[{](.*)[}]@', '<div class="arrowchat_action_message">' . $language[61] . '<br /><a href="javascript:jqac.arrowchat.videoWith(\'$1\');">' . $language[62] . '</a></div>', $text);
		
		$text = preg_replace('@file[{]([0-9]{13})[}][{](.*)[}]@', '<div class="arrowchat_action_message">' . $language[69] . '<br /><a href="' . $base_url . 'public/download.php?file=$1">$2</a></div>', $text);
		
		$text = preg_replace('@image[{]([0-9]{13})[}][{](.*)[}]@', '<div class="arrowchat_image_message"><img data-id="' . $base_url . 'public/download.php?file=$1" src="' . $base_url . 'public/download.php?file=$1_t" alt="Image" class="arrowchat_lightbox" /></div>', $text);

		return $text;
	}

?>