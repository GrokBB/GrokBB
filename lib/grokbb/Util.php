<?php
namespace GrokBB;

class Util {
    /**
     * Validates an email address
     *
     * @param  string $email an email address
     * @return bool          TRUE on success
     */
	public static function validateEmail($email) {
	    return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	/**
     * Gets the difference between the current time and another timestamp
     *
     * @param  int    $timestamp a UNIX timestamp
     * @param  string $strOutput the string you are appending too
     * @param  bool   $short     TRUE shortens the string to the largest time period
     * @return string            a string containing the difference in time
     */
	public static function getTimespan($timestamp, $counter = 7, $strOutput = '') {
	    if ($strOutput == '') {
	        if ($timestamp == 0) {
	            return 'Never';
	        } else {
	            $time = time();
	            $diff = abs($time - $timestamp);
	        }
	    } else {
	        $diff = $timestamp;
	    }
	    
	    $strComma = (($strOutput) ? ', ' : '');
	    
	    if ($diff < 60) {
	        $remaining  = 0;
            $strOutput .= $strComma . str_pad($diff, 2, 0, STR_PAD_LEFT) . ' seconds';
            if ($diff == 1) { $strOutput = substr($strOutput, 0, -1); }
            return $strOutput;
        } else if ($diff < 60 * 60) {
            $calc = floor($diff / 60);
            $remaining  = $diff - ($calc * 60);
            $strOutput .= $strComma . str_pad($calc, 2, 0, STR_PAD_LEFT) . ' minutes';
        } else if ($diff < 60 * 60 * 24) {
            $calc = floor($diff / 60 / 60);
            $remaining  = $diff - ($calc * 60 * 60);
            $strOutput .= $strComma . str_pad($calc, 2, 0, STR_PAD_LEFT) . ' hours';
        } else if ($diff < 60 * 60 * 24 * 30.5 /* 7 */) {
            $calc = floor($diff / 60 / 60 / 24);
            $remaining  = $diff - ($calc * 60 * 60 * 24);
            $strOutput .= $strComma . str_pad($calc, 2, 0, STR_PAD_LEFT) . ' days';
        // } else if ($diff < 60 * 60 * 24 * 7 * 4) {
        //     $calc = floor($diff / 60 / 60 / 24 / 7);
        //     $remaining  = $diff - ($calc * 60 * 60 * 24 * 7);
        //     $strOutput .= $strComma . str_pad($calc, 2, 0, STR_PAD_LEFT) . ' weeks';
        } else if ($diff < 60 * 60 * 24 * 365) {
            $calc = floor($diff / 60 / 60 / 24 / 30.5);
            $remaining  = $diff - ($calc * 60 * 60 * 24 * 30.5);
            $strOutput .= $strComma . str_pad($calc, 2, 0, STR_PAD_LEFT) . ' months';
        } else {
            $calc = floor($diff / 60 / 60 / 24 / 365);
            $remaining  = $diff - ($calc * 60 * 60 * 24 * 365);
            $strOutput .= $strComma . str_pad($calc, 2, 0, STR_PAD_LEFT) . ' years';
        }
        
        if ($calc == 1) {
            // remove the "s" when displaying the number one
            $strOutput = substr($strOutput, 0, -1);
        }
        
        $counter--;
        
        if ($counter == 0) { return $strOutput; }
        return Util::getTimespan($remaining, $counter, $strOutput);
	}
	
	/**
     * Adds up the size of all files in a directory
     *
     * @param  string $path a directory path
     * @return int          total size in bytes
     */
	public static function getDirSize($path) {
	    $total = 0;
	    $dir = @scandir($path);
	    
	    if ($dir) {
	        foreach ($dir as $file) {
	            $total += filesize($path . DIRECTORY_SEPARATOR . $file);
	        }
	    }
	    
	    return $total;
	}
	
	/**
     * Sanitizes Cascading Stylesheets
     *
     * @param  string $content the CSS
     * @return string          the sanitized content
     */
	public static function sanitizeCSS($content) {
	    require_once(SITE_BASE_LIB . 'htmlpurifier-4.15.0' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'HTMLPurifier.auto.php');

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('CSS.AllowImportant', true);
        $config->set('CSS.AllowTricky', true);
        $config->set('CSS.MaxImgLength', '1920px');
        $config->set('HTML.Allowed', '');
        
        $hp = new \HTMLPurifier($config);
        return $hp->purify($content);
	}
	
	/**
     * Sanitizes GitHub Flavored Markdown
     *
     * @param  string $content the GF Markdown
     * @return string          the sanitized content
     */
	public static function sanitizeGMD($content) {
	    require_once(SITE_BASE_LIB . 'htmlpurifier-4.15.0' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'HTMLPurifier.auto.php');

        $hp = new \HTMLPurifier(\HTMLPurifier_Config::createDefault());
        return $hp->purify($content);
	}
	
	/**
     * Sanitizes Text
     *
     * @param  string $content the text
     * @return string          the sanitized content
     */
	public static function sanitizeTXT($content) {
	    require_once(SITE_BASE_LIB . 'htmlpurifier-4.15.0' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'HTMLPurifier.auto.php');
        
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', '');
        
        $hp = new \HTMLPurifier($config);
        return $hp->purify($content);
	}
	
	/**
     * Sanitizes a CSS Color Value
     *
     * @param  string $content the color hex or name
     * @return string          the sanitized content
     */
	public static function sanitizeColor($content) {
	    return preg_replace('/[^A-Za-z0-9#]/', '', $content);
	}
	
	/**
     * Sanitizes a CSS Font Family
     *
     * @param  string $content the font name(s)
     * @return string          the sanitized content
     */
	public static function sanitizeFonts($content) {
	    return preg_replace('/[^A-Za-z0-9\'\-\, ]/', '', str_replace('"', '\'', $content));
	}
	
	/**
     * Sanitizes a Board's Name
     *
     * @param  string $content the board name
     * @return string          the sanitized content
     */
	public static function sanitizeBoard($content) {
	    return preg_replace('/[^A-Za-z0-9\- ]/', '', str_replace('_', ' ', $content));
	}
	
	/**
     * Removes large data properties from a session object
     *
     * @param  object $object the object
     * @return object         the sanitized object
     */
	public static function sanitizeSession($object) {
	    if (is_object($object)) {
	        $objectClone = clone $object;
	        
	        foreach($object as $key => $value) {
                if (substr($key, -3) == '_md') {
                    $keyHTML = substr($key, 0, -3);
                    unset($objectClone->$keyHTML);
                    unset($objectClone->$key);
                }
            }
            
            return $objectClone;
	    } else if (is_array($object)) {
	        $objectArray = $object;
	        
	        foreach($object as $key => $value) {
	            $objectArray[$key] = Util::sanitizeSession($value);
	        }
	        
	        return $objectArray;
	    } else {
	        return $object;
	    }
	}
	
	/**
     * Splits long text
     *
     * @param  string $text the text to split
     * @param  int    $size the maximum size of each chunk
     * @param  string $char the character to split with
     * @return string       the split text
     */
	public static function splitLongText($text, $size, $char = ' ') {
	    // only split when the char does not exist
	    if (strpos($text, $char) === false) {
            return chunk_split($text, $size, $char);
        } else {
            return $text;
        }
	}
	
	/**
     * Returns the highlight positions of the text being searched
     *
     * @param  string $text    the text to highlight
     * @param  string $boolean the boolean search string
     * @return array           an array of positions
     */
	public static function highlightText($text, $boolean) {
	    if (trim($boolean) == '') { return false; }
	    
	    // split the boolean string by spaces, treating quoted strings as a single word
        $words = str_getcsv($boolean, ' ');
        
        $lastPosition = 0;
        $eachPosition = array();
        
        foreach ($words as $word) {
            // skipped ignored words
            $char = substr($word, 0, 1);
            if ($char == '-') { continue; }
            
            // look for a wildcard
            $wild = strpos($word, '*');
            
            // remove the boolean characters
            $cleanedWord = str_replace(array('>', '<', '(', ')', '~', '*'), '', $word);
            
            // append a space when NOT doing a wildcard search, otherwise only match up to the *
            $cleanedWord = ($wild === false) ? $cleanedWord . ' ' : substr($cleanedWord, 0, $wild);
            
            // don't include the space in our length
            $cleanedWordLen = strlen(trim($cleanedWord));
            
            while (($lastPosition = stripos($text, $cleanedWord, $lastPosition)) !== false) {
                $eachPosition[] = array('pos' => $lastPosition, 'len' => $cleanedWordLen);
                $lastPosition = $lastPosition + $cleanedWordLen;
            }
        }
        
        return $eachPosition;
	}
	
	/**
     * Converts all the @username mentions to GMD links
     *
     * @param  string $text        the text to parse
     * @param  bool   $returnUsers return the user ids
     * @return string              the parsed text
     */
	public static function parseMentions($text, $returnUsers = false) {
	    $matches = array();
	    $matchedUsers = array();
	    
	    if (preg_match_all("/@([^\s|\,|\/]{2,15})[\s|\,]/", $text, $matches, PREG_PATTERN_ORDER)) {
	        $matches[1] = str_replace('_', ' ', $matches[1]);
	        
	        $users = $GLOBALS['db']->getAll('user', array('username' => array('IN', $matches[1])));
	        
	        if ($users) {
	            foreach ($matches[1] as $key => $match) {
	                foreach ($users as $user) {
    	                if (strtolower($user->username) == strtolower($match)) {
	                        $link = '[@' . $user->username . '](' . SITE_BASE_URL . '/user/view/' . $user->id . ')';
	                        $text = str_replace($matches[0][$key], str_replace('@' . str_replace(' ', '_', $match), $link, $matches[0][$key]), $text);
	                        
	                        $matchedUsers[] = $user->id;
	                        
	                        break;
	                    }
    	            }
    	        }
	        }
	    }

        if ($returnUsers) {
            return array($text, $matchedUsers);
        } else {
	        return $text;
	    }
	}
}
?>