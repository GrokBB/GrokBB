<?php
namespace GrokBB;

class User extends API {
	/**
     * The user's ID
     * @var int
     */
	public $id;
	
	/**
     * The user's username
     * @var string
     */
	public $username;
	
	/**
     * The date the user's account was created
     * @var int
     */
	public $joined;
	
	/**
     * The user's email address
     * @var string
     */
	public $email = '';
	
	/**
     * The date the user's profile was last updated
     * @var int
     */
	public $updated = 0;
	
	
	/**
     * The user's bio
     * @var string
     */
	public $bio = '';
	
	/**
     * The user's permissions
     * @var bool
     */
	public $isAdmin = false;
	public $isOwner = false;
	public $isModerator = false;
	public $isBanned = false;
	
	/**
     * A list of banned words in usernames
     * @var int
     */
	protected $banned = array('root', 'admin', 'moderator', 'guest', 'grokbb');
	
	/**
     * Selects a user by their ID
     *
     * @param  string $id the user's ID
     * @return object     a User object
     */
	function __construct($id = 0) {
		$this->id = $id;
	}
	
	/**
     * Selects a user by their cookie hash
     *
     * @param  string $id the user's cookie hash
     * @return object     a User object
     */
	public function getByHash($hash) {
		$user = $GLOBALS['db']->getOne('user', array('remember' => $hash));
		
		if ($user) {
		    $this->id = $user->id;
		    $this->username = $user->username;
		    $this->joined = $user->joined;
		    $this->email = $user->email;
		    $this->updated = $user->updated;
		    $this->isAdmin = ($this->id == 1) ? true : false;
		    
		    return $this;
		}
		
		return null;
	}
	
	/**
     * Creates a user
     *
     * @param  string $username a username
     * @param  string $password a password
     * @param  int    $remember 1 = login automatically / 0 = login manually
     * @param  string $email    this should be blank, unless you're a spam bot
     * @return array            an API response
     */
	public function create($username, $password, $remember = 0, $email = '') {
	    $responseUsername = json_decode($this->validateUsername($username));
	    $responsePassword = json_decode($this->validatePassword($password));
	    
	    // the email check is for spam bot protection (we do not ask for this field)
	    if ($responseUsername->result && $responsePassword->result && $email === '') {
	        try {
	            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
	            
	            if ($remember) {
                    $rememberHash = password_hash(microtime(), PASSWORD_DEFAULT);
                } else {
                    $rememberHash = '';
                }
                
	            $uid = $GLOBALS['db']->insert('user', 
	                array('username' => $username,
	                      'password' => $passwordHash,
	                      'remember' => $rememberHash,
	                      'joined' => time())
	            );
	        } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Create User - Error #1';
    		    
    		    return $this->getResponse();
	        }
	        
	        if ($uid) {
	            if (isset($_COOKIE['uid'])) {
        		    setcookie('uid', '', time() - 3600);
        		    unset($_COOKIE['uid']);
    		    }
    		    
    	        if ($remember) {
    	            setcookie('uid', $rememberHash, time() + (86400 * 30), '/', '', false, true);
    	        } else {
    	            $this->login($username, $password, $remember);
    	        }
    	        
    	        $this->result = true;
    	        $this->msg = '';
    	        
    		    return $this->getResponse();
    		} else {
    		    $this->result = false;
    		    $this->msg = (DEBUG) ? $GLOBALS['db']->getErr() : 'Could Not Create User - Error #2';
    		    
    		    return $this->getResponse();
    		}
	    } else {
	        $this->result = false;
		    $this->msg = 'Your username or password is invalid.';
		    
		    return $this->getResponse();
	    }
	}
	
	/**
     * Updates a user
     *
     * @param  string $username    the user's username
     * @param  string $password    the user's current password
     * @param  string $newPassword the user's new password
     * @param  int    $remember    1 = login automatically / 0 = login manually
     * @param  string $email       the user's email address
     * @return array               an API response
     */
	public function update($username, $password, $newPassword, $remember = 0, $email = '') {
	    $user = $GLOBALS['db']->getOne('user', array('username' => $username));
		
		// verify the user's current password
		if ($user && password_verify($password, $user->password)) {
		    if ($email) {
		        // validate the email address
		        $emailIsValid = Util::validateEmail($email);
		    }
		    
		    if (!$email || $emailIsValid) {
    		    if ($newPassword) {
    		        // validate the new password
        	        $newPasswordIsValid = json_decode($this->validatePassword($newPassword));
        	    }
        	    
        	    if (!$newPassword || $newPasswordIsValid->result) {
        	        try {
        	            if ($newPassword) {
        	                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        	            } else {
        	                $newPasswordHash = $user->password;
        	            }
        	            
        	            if ($remember) {
                            $rememberHash = password_hash(microtime(), PASSWORD_DEFAULT);
                        } else {
                            $rememberHash = '';
                        }
                        
        	            $update = $GLOBALS['db']->update('user', 
        	                array('password' => $newPasswordHash,
        	                      'remember' => $rememberHash,
        	                      'email' => $email,
        	                      'id' => $user->id)
        	            );
        	        } catch (Exception $e) {
        	            $this->result = false;
            		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update User - Error #1';
            		    
            		    return $this->getResponse();
        	        }
        	        
        	        if ($update) {
        	            if (isset($_COOKIE['uid'])) {
            	            setcookie('uid', '', time() - 3600);
    		                unset($_COOKIE['uid']);
		                }
		                
            	        if ($remember) {
            	            setcookie('uid', $rememberHash, time() + (86400 * 30), '/', '', false, true);
            	        }
            	        
            	        $_SESSION['user']->email = $email;
            	        
            	        $this->result = true;
            	        $this->msg = '';
            	        
            		    return $this->getResponse();
            		} else {
            		    $this->result = false;
            		    $this->msg = (DEBUG) ? $GLOBALS['db']->getErr() : 'Could Not Update User - Error #2';
            		    
            		    return $this->getResponse();
            		}
        	    } else {
        	        $newPasswordIsValid->msg = str_replace('password', 'new password', $newPasswordIsValid->msg);
        	        return json_encode($newPasswordIsValid);
        	    }
        	} else {
        	    $this->result = false;
    		    $this->msg = 'You have entered an invalid email address.';
    		    
    		    return $this->getResponse();
        	}
    	} else {
    	    $this->result = false;
    	    $this->msg = 'You have entered an incorrect current password.';
    	    
    	    return $this->getResponse();
    	}
	}
	
	/**
     * Validates a username
     *
     * @param  string $username a username
     * @return array            an API response
     */
	public function validateUsername($username) {
	    // make sure we have at least 2 characters
		if (strlen($username) < 2) {
		    $this->result = false;
		    $this->msg = 'Your username must be at least 2 characters.';
		    
		    return $this->getResponse();
		}
		
		// make sure we have 15 characters or less
		if (strlen($username) > 15) {
		    $this->result = false;
		    $this->msg = 'Your username is too long.';
		    
		    return $this->getResponse();
		}
		
		// make sure there are no spaces on either side of the username
		if (substr($username, 0, 1) == ' ' || substr($username, -1) == ' ') {
		    $this->result = false;
		    $this->msg = 'Your username can\'t start / end with a space.';
		    
		    return $this->getResponse();
		}
		
		// make sure there are no double spaces
		if (strpos($username, '  ') !== false) {
		    $this->result = false;
		    $this->msg = 'Your username can\'t have double spaces.';
		    
		    return $this->getResponse();
		}
		
		// make sure it has valid characters
		if (preg_match('/[^A-Za-z0-9\!\@\#\$\%\^\&\*\(\)\[\]\{\}\-\+\=\|\'\:\.\;\~\`\?\ ]/', $username)) {
		    $this->result = false;
		    $this->msg = 'Your username has invalid characters.';
		    
		    return $this->getResponse();
		}
		
		// make sure it doesn't contain any banned words
		foreach ($this->banned as $word) {
    		if (stripos($username, $word) !== false) {
    		    $this->result = false;
    		    $this->msg = 'This username is not available.';
    		    
    		    return $this->getResponse();
    		}
	    }
	    
	    // make sure it hasn't been taken
	    if ($GLOBALS['db']->getOne('user', array('username' => $username))) {
	        $this->result = false;
		    $this->msg = 'This username is not available.';
		    
		    return $this->getResponse();
	    }
	    
		$this->result = true;
	    $this->msg = 'Success! This username is available.';
	    
	    return $this->getResponse();
	}
	
	/**
     * Validates a password
     *
     * @param  string $password a password
     * @return array            an API response
     */
	public function validatePassword($password) {
	    // make sure we have at least 15 characters
		if (strlen($password) < 15) {
		    $this->result = false;
		    $this->msg = 'Your password must be at least 15 characters.';
		    
		    return $this->getResponse();
		}
		
		// splits the password into its UTF-8 characters
		$chars = preg_split('//u', $password, -1, PREG_SPLIT_NO_EMPTY);
        
        // make sure we have at least 2 different characters
        if (count(array_unique($chars)) === 1) {
		    $this->result = false;
		    $this->msg = '¯\_(⊙︿⊙)_/¯&nbsp;&nbsp;&nbsp;Your password sucks!';
		    
		    return $this->getResponse();
		}
		
		$this->result = true;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Logout a user
     *
     * @return array an API response
     */
	public function logout() {
		if (isset($_COOKIE['uid'])) {
		    setcookie('uid', '', time() - 3600);
		    unset($_COOKIE['uid']);
		}
		
		session_destroy();
		
		$this->result = true;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Login a user
     *
     * @param  string $username the user's username
     * @param  string $password the user's password
     * @param  int    $remember 1 = login automatically / 0 = login manually
     * @return array            an API response
     */
	public function login($username, $password, $remember) {
	    $user = $GLOBALS['db']->getOne('user', array('username' => $username));
		
		if ($user) {
		    // the user must wait login attempts ^ 2 after 5 failed attempts
		    $wait = ($user->login_attempts >= 5) ? $user->login_attempts * $user->login_attempts : 0;
		    $left = ($user->login + $wait) - time();
		    
		    if ($left <= 0 && password_verify($password, $user->password)) {
    		    $this->id = $user->id;
    		    $this->username = $user->username;
    		    $this->joined = $user->joined;
    		    $this->email = $user->email;
    		    $this->updated = $user->updated;
    		    $this->isAdmin = ($this->id == 1) ? true : false;
    		    
    		    if (isset($_COOKIE['uid'])) {
        		    setcookie('uid', '', time() - 3600);
        		    unset($_COOKIE['uid']);
    		    }
    		    
        		if ($remember) {
        		    $rememberHash = password_hash(microtime(), PASSWORD_DEFAULT);
                    setcookie('uid', $rememberHash, time() + (86400 * 30), '/', '', false, true);
                } else {
                    $rememberHash = '';
                    $_SESSION['user'] = Util::sanitizeSession($this);
        		}
        		
        		$GLOBALS['db']->update('user', 
        		    array('id' => $user->id, 
        		          'remember' => $rememberHash,
        		          'login' => time(),
        		          'login_attempts' => 0, 
        	              'login_ipaddress' => ip2long($_SERVER['HTTP_X_FORWARDED_FOR']))
        		);
        		
        		$this->result = true;
        	    $this->msg = '';
        	    
        	    return $this->getResponse();
        	} else {
        	    if ($left <= 0) {
        	        $GLOBALS['db']->update('user', 
        	            array('id' => $user->id, 
        	                  'login' => time(),
        	                  'login_attempts' => $user->login_attempts + 1, 
        	                  'login_ipaddress' => ip2long($_SERVER['HTTP_X_FORWARDED_FOR']))
        	        );
        	        
            	    $this->result = false;
            	    $this->msg = 'You have entered an incorrect username or password.';
            	    
            	    return $this->getResponse();
            	} else {
            	    $leftString = Util::getTimespan(time() + $left);
            	    
            	    $this->result = false;
            	    $this->msg = 'You must wait ' . $leftString . ' before trying again.';
            	    
            	    return $this->getResponse();
            	}
        	}
		} else {
		    $this->result = false;
    	    $this->msg = 'You have entered an incorrect username or password.';
    	    
    	    return $this->getResponse();
    	}
	}
	
	/**
     * Resets a user
     *
     * @param  string $username the user's username or email address
     * @return array            an API response
     */
	public function reset($username) {
	    if (trim($username) == '') {
	        $this->result = false;
    	    $this->msg = 'You must enter a username or email address.';
    	    
    	    return $this->getResponse();
	    } else {
    	    $user = $GLOBALS['db']->getOne('user', array('username,email' => $username));
    		
    		if ($user && $username != 'admin') {
    		    if ($user->email) {
    		        // get all resets for this user, that have happened in the last 24 hours
    		        $resetAlready = $GLOBALS['db']->custom('SELECT * FROM ' . DB_PREFIX . 'user_reset WHERE id_ur = ' .  $user->id . ' AND used = 0 AND (sent + 86400) > UNIX_TIMESTAMP()');
    		        
    		        // allow 3 resets every 24 hours
    		        if ($resetAlready == false || count($resetAlready) < 3) {
        		        // bin2hex will double the string length
        		        $tokenToEmail = bin2hex(openssl_random_pseudo_bytes(16));
        		        
        		        $insert = $GLOBALS['db']->insert('user_reset', array('id_ur' => $user->id, 'token'  => $tokenToEmail, 'sent'  => time()));
        		        
        		        if ($insert) {
        		            $resetURL = SITE_BASE_URL . '/user/reset/' . $tokenToEmail;
        		            
        		            $resetMessage  = "Hi $user->username,\r\n\r\n";
        		            $resetMessage .= "Please click on the link below to reset your password. If you did not request this reset then you can ignore this email.\r\n\r\n";
        		            $resetMessage .= "$resetURL\r\n\r\n";
        		            $resetMessage .= "Sincerely,\r\nGrokBB\r\n\r\n";
        		            $resetMessage .= "https://www.grokbb.com";
        		            
        		            $resetHeaders  = "From: GrokBB <reset@grokbb.com>\r\nReply-To: GrokBB <reset@grokbb.com>\r\nReturn-Path: reset@grokbb.com";
        		            
        		            mail($user->email, "GrokBB - Password Reset", $resetMessage, $resetHeaders, "-f reset@grokbb.com");
        		            
                    		$this->result = true;
                    	    $this->msg = 'A link to reset your password has been sent to your email.';
                    	    
                    	    return $this->getResponse();
                    	} else {
                    	    $this->result = false;
                		    $this->msg = (DEBUG) ? $GLOBALS['db']->getErr() : 'Could Not Reset Pasword - Error #1';
                		    
                		    return $this->getResponse();
                    	}
                    } else {
                        $this->result = false;
                	    $this->msg = 'You must wait 24 hours before you can do another reset.';
                	    
                	    return $this->getResponse();
                    }
            	} else {
            	    $this->result = false;
            	    $this->msg = 'You do not have an email address.';
            	    
            	    return $this->getResponse();
            	}
    		} else {
    		    $this->result = false;
        	    $this->msg = 'You have entered an invalid username or email address.';
        	    
        	    return $this->getResponse();
        	}
        }
	}
	
	/**
     * Updates a user's avatar
     *
     * @return bool TRUE on success
     */
	public function avatar() {
	    $uid = $_SESSION['user']->id;
	    $dir = SITE_DATA_DIR . 'users' . DIRECTORY_SEPARATOR . $uid;
	    
	    if (!file_exists($dir)) { mkdir($dir); }
	    
	    $imgPath = $dir . DIRECTORY_SEPARATOR . 'avatar.png';
	    move_uploaded_file($_FILES['files']['tmp_name'][0], $imgPath);
	    
	    $pim = new \imageLib($imgPath);
        $pim->resizeImage(60, 60, 'crop');
        return $pim->saveImage($imgPath);
	}
	
	/**
     * Updates a user's biography
     *
     * @param  string $bio the user's biography
     * @return array       an API response
     */
	public function setBio($bio) {
	    try {
	        $gmd = new \cebe\markdown\GithubMarkdown();
	        $gmd->enableNewlines = true;
	        
	        $bioHTML = $gmd->parse($bio);
	        
	        $bioSafe = Util::sanitizeGMD($bioHTML);
	        
    	    $GLOBALS['db']->update('user', 
    	        array('id' => $_SESSION['user']->id,
    	              'updated' => time(),
    	              'bio' => $bioSafe,
    	              'bio_md' => $bio)
    	    );
    	    
    	    $this->result = true;
    	    $this->msg = '';
    	    
    	    return $this->getResponse();
    	} catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Biography - Error #1';
		    
		    return $this->getResponse();
        }
	}
	
	/**
     * Gets a user's profile
     *
     * @return array an API response
     */
	public function getProfile($uid) {
	    $user = $GLOBALS['db']->getOne('user', array('id' => $uid));
	    
	    if ($user) {
    	    $profile = new \stdClass();
    	    $profile->id = $user->id;
    	    $profile->username = $user->username;
    	    $profile->bio = $user->bio;
    	    
    	    $this->result = true;
    	    $this->msg = $profile;
    	    
    	    return $this->getResponse();
    	} else {
    	    $this->result = false;
    	    $this->msg = '';
    	    
    	    return $this->getResponse();
    	}
	}
	
	/**
     * Adds a friend
     *
     * @param  int $uid the user id
     * @return array    an API response
     */
	public function addFriend($uid) {
	    $user = $GLOBALS['db']->getOne('user', array('id' => $uid));
		
		if ($user && $_SESSION['user']->id) {
		    try {
		        $record = array(
    	            'id_ur' => $_SESSION['user']->id,
    	            'id_fd' => $uid,
                    'added' => time()
                );
                
		        $GLOBALS['db']->insert('user_friend', $record);
		        
		        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Add Friend - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Removes a friend
     *
     * @param  int $uid the user id
     * @return array    an API response
     */
	public function remFriend($uid) {
	    try {
	        $where = array(
	            'id_ur' => $_SESSION['user']->id,
	            'id_fd' => $uid
            );
            
	        $GLOBALS['db']->delete('user_friend', $where);
	        
	        $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
        } catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Remove Friend - Error #1';
		    
		    return $this->getResponse();
        }
	}
	
	/**
     * Gets a user's reputation
     *
     * @param  int    $uid   a user id
     * @param  object $stats a stats object for every user on the page
     * @param  object $board a board object containing the counters
     * @return string        the reputation to display
     */
	public static function reputation($uid, $stats = false, $board = false) {
	    $reputation = '';
	    
	    if ($stats) {
	        $bStatsPoints  = $stats->board[$uid]->counter_points;
	        $bStatsTopics  = $stats->board[$uid]->counter_topics;
	        $bStatsReplies = $stats->board[$uid]->counter_replies;
	        $uStatsTopics  = $stats->users[$uid]->counter_topics;
	        $uStatsReplies = $stats->users[$uid]->counter_replies;
	    } else {
	        // this should only be used when querying the stats for a single user
	        // TODO: add the "gbb_user_board" and "gbb_user" queries
	    }
	    
	    // get the user's reputation in the current board
	    // topic count is weighted at 30%, reply count at 20%, moderator points at 50%
	    if ($board && isset($board->counter_topics) && isset($board->counter_replies) && isset($board->counter_points)) {
	        $tPoints = ($board->counter_topics > 0) ? (($bStatsTopics / $board->counter_topics) * 100) * 0.3 : 0;
	        $rPoints = ($board->counter_replies > 0) ? (($bStatsReplies / $board->counter_replies) * 100) * 0.2 : 0;
	        $mPoints = ($board->counter_points > 0) ? (($bStatsPoints / $board->counter_points) * 100) * 0.5 : 0;
	        
	        $reputation .= round($tPoints + $rPoints + $mPoints) . ' / ';
	    }
	    
	    // get the user's reputation in the entire GrokBB site
	    // topic count is weighted at 60%, reply count at 40%
	    $tPoints = ($_SESSION['gbbstats'] && $_SESSION['gbbstats']->counter_topics > 0) ? (($uStatsTopics / $_SESSION['gbbstats']->counter_topics) * 100) * 0.6 : 0;
        $rPoints = ($_SESSION['gbbstats'] && $_SESSION['gbbstats']->counter_replies > 0) ? (($uStatsReplies / $_SESSION['gbbstats']->counter_replies) * 100) * 0.4 : 0;
        
        $reputation .= round($tPoints + $rPoints);
        
        return $reputation;
	}
	
	/**
     * Searches for a username
     *
     * @param  string $term  the term to search on
     * @param  int    $limit the results limit
     * @return array         an API response
     */
	public function search($term, $limit = 10) {
	    try {
	        $results = array();
	        
	        $users = $GLOBALS['db']->getAll('user', array('username' => array('LIKE', $term . '%')), false, false, $limit);
	        
	        if ($users) {
	            foreach ($users as $user) {
	                $results[] = $user->username;
	            }
	        }
	        
	        $this->result = true;
		    $this->msg = $results;
		    
		    return $this->getResponse();
        } catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Search - Error #1';
		    
		    return $this->getResponse();
        }
	}
	
	/**
     * Gets a user's badges for a specific board
     *
     * @param  int   $uid the user id
     * @param  int   $bid the board id
     * @return array an API response
     */
	public function getBadges($uid, $bid) {
	    try {
	        $return = array();
	        
	        $badges = $GLOBALS['db']->getAll('user_board_badge ubb LEFT JOIN ' . DB_PREFIX . 'board_badge bb ON ubb.id_bb = bb.id', array('bb.id_bd' => $bid, 'ubb.id_ur' => $uid), 'bb.`desc`', array('bb.*'));
	        
	        if ($badges) {
	            foreach ($badges as $badge) {
	                $badge->desc = str_replace('"', '&quot;', $badge->desc);
	                $return[] = $badge;
	            }
	        }
	        
	        $this->result = true;
		    $this->msg = $return;
		    
		    return $this->getResponse();
        } catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Get Badges - Error #1';
		    
		    return $this->getResponse();
        }
	}
}
?>