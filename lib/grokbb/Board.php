<?php
namespace GrokBB;

class Board extends API {
	/**
     * The board's ID
     * @var int
     */
	public $id;
	
	/**
     * A list of banned words in names
     * @var int
     */
	protected $banned = array('grokbb');
	
	/**
     * Selects a board by their ID
     *
     * @param  string $id the board's ID
     * @return object     a Board object
     */
	function __construct($id = 0) {
		$this->id = $id;
	}
	
	/**
     * Creates a board
     *
     * @param  int    $plan the plan (0 = yearly / 1 = monthly)
     * @param  int    $type the type (0 = public / 1 = private / 2 = moderated)
     * @param  string $name the name
     * @return array        an API response
     */
	public function create($plan, $type, $name) {
	    // all plans are FREE until launch
	    // $responseSent = $this->validatePlan($plan, $type);
	    // $responsePlan = json_decode($responseSent);
	    // if ($responsePlan->result == false) {
	    //     return $responseSent;
	    // }
		
		$responseSent = $this->validateName($name);
	    $responseName = json_decode($responseSent);
	    if ($responseName->result == false) {
	        return $responseSent;
	    }
	    
	    // make sure it hasn't been taken
	    if ($GLOBALS['db']->getOne('board', array('name' => $name))) {
	        $this->result = false;
		    $this->msg = 'This name is not available.';
		    
		    return $this->getResponse();
	    }
	    
	    try {
            $bid = $GLOBALS['db']->insert('board', 
                array('id_ur' => $_SESSION['user']->id,
                      'plan' => 4, // $plan,
                      'type' => $type,
                      'name' => $name,
                      'created' => time(),
                      'expires' => strtotime('+30 days'))
            );
            
            // create the default topic category
            $GLOBALS['db']->insert('board_category', array('id_bd' => $bid, 'name' => 'General', 'defcat' => 1));
            
            // create the default topic settings
            $GLOBALS['db']->insert('board_settings', array('id_bd' => $bid));
            
            $this->result = true;
    	    $this->msg = $bid;
    	    
    	    return $this->getResponse();
        } catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Create Board - Error #1';
		    
		    return $this->getResponse();
        }
	}
	
	/**
     * Updates a board
     *
     * @param  int    $id      the board id
     * @param  int    $plan    the plan (0 = yearly / 1 = monthly)
     * @param  int    $type    the type (0 = public / 1 = private / 2 = moderated)
     * @param  string $name    the name
     * @param  string $tagline the tagline
     * @param  array  $tags    the tags
     * @param  string $hbc     the header's background color
     * @param  string $hbr     the header's background repeat
     * @param  string $bra     the request access text
     * @return array           an API response
     */
	public function update($id, $plan, $type, $name, $tagline = '', $tags = array(), $hbc = false, $hbr = false, $bra = '') {
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $id, 'id_ur' => $_SESSION['user']->id));
	    
	    if ($owns) {
	        if ($owns->plan == 4 || ($owns->stripe_id !== '0' && $owns->stripe_cancelled == 0)) {
	            $plan = $owns->plan;
	        } else {
        	    $responseSent = $this->validatePlan($plan, $type);
        	    $responsePlan = json_decode($responseSent);
        	    if ($responsePlan->result == false) {
        	        return $responseSent;
        	    }
    		}
    		
    		$responseSent = $this->validateName($name);
    	    $responseName = json_decode($responseSent);
    	    if ($responseName->result == false) {
    	        return $responseSent;
    	    }
    	    
    	    // make sure it hasn't been taken
    	    if ($GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => array('<>', $_SESSION['user']->id)))) {
    	        $this->result = false;
    		    $this->msg = 'This name is not available.';
    		    
    		    return $this->getResponse();
    	    }
    	    
    	    try {
    	        $taglineSafe = Util::sanitizeTXT($tagline);
    	        $tag1Safe = Util::sanitizeTXT($tags[0]);
    	        $tag2Safe = Util::sanitizeTXT($tags[1]);
    	        $tag3Safe = Util::sanitizeTXT($tags[2]);
    	        
                $GLOBALS['db']->update('board', 
                    array('id' => $owns->id,
                          'plan' => $plan,
                          'type' => $type,
                          'name' => $name,
                          'desc_tagline' => $taglineSafe,
                          'tag1' => $tag1Safe,
                          'tag2' => $tag2Safe,
                          'tag3' => $tag3Safe,
                          'updated' => time())
                );
                
                $settings = array('id_bd' => $owns->id);
                    
                if ($hbc !== false) { $settings['header_back_color'] = Util::sanitizeColor($hbc); }
                if ($hbr !== false) { $settings['header_back_repeat'] = (int) $hbr; }
                
                if ($owns->type == 1) {
                    $gmd = new \cebe\markdown\GithubMarkdown();
    	            $gmd->enableNewlines = true;
    	            
        	        $braHTML = $gmd->parse($bra);
    	            $braSafe = Util::sanitizeGMD($braHTML);
    	            
        	        $settings['board_request_access'] = $braSafe;
        	        $settings['board_request_access_md'] = $bra;
        	    }
                
                $GLOBALS['db']->update('board_settings', $settings, 'id_bd');
                
                $this->result = true;
        	    $this->msg = '';
        	    
        	    return $this->getResponse();
            } catch (Exception $e) {
                $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Board - Error #1';
    		    
    		    return $this->getResponse();
            }
        }
        
        $this->result = false;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Validates a board's plan
     *
     * @param  int $plan the plan (0 = yearly / 1 = monthly)
     * @param  int $type the type (0 = public / 1 = private / 2 = moderated)
     * @return array     an API response
     */
	public function validatePlan($plan, $type) {
	    // make sure the plan is valid
		if ($plan < 0 || $plan > 1) {
		    $this->result = false;
		    $this->msg = 'You have selected an invalid plan.';
		    
		    return $this->getResponse();
		}
		
		// make sure the type is valid
		if ($type < 0 || $type > 2) {
		    $this->result = false;
		    $this->msg = 'You have selected an invalid type.';
		    
		    return $this->getResponse();
		}
		
		$this->result = true;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Validates a board's name
     *
     * @param  string $name the name
     * @return array        an API response
     */
	public function validateName($name) {
	    // make sure we have at least 1 character
		if (strlen($name) < 1) {
		    $this->result = false;
		    $this->msg = 'You must enter a name.';
		    
		    return $this->getResponse();
		}
		
	    // make sure we have 30 characters or less
		if (strlen($name) > 30) {
		    $this->result = false;
		    $this->msg = 'Your name is too long.';
		    
		    return $this->getResponse();
		}
		
		// make sure there are no spaces on either side of the name
		if (substr($name, 0, 1) == ' ' || substr($name, -1) == ' ') {
		    $this->result = false;
		    $this->msg = 'Your name can\'t start / end with a space.';
		    
		    return $this->getResponse();
		}
		
		// make sure there are no double spaces
		if (strpos($name, '  ') !== false) {
		    $this->result = false;
		    $this->msg = 'Your name can\'t have double spaces.';
		    
		    return $this->getResponse();
		}
		
	    // make sure it has valid characters
		if (preg_match('/[^A-Za-z0-9\- ]/', $name)) {
		    $this->result = false;
		    $this->msg = 'Your name has invalid characters.';
		    
		    return $this->getResponse();
		}
		
		if ($_SESSION['user']->id > 1) {
    		// make sure it doesn't contain any banned words
    		foreach ($this->banned as $word) {
        		if (stripos($name, $word) !== false) {
        		    $this->result = false;
        		    $this->msg = 'This name is not available.';
        		    
        		    return $this->getResponse();
        		}
    	    }
	    }
	    
	    $this->result = true;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Stripe Subscription
     *
     * @param  int    $id    the board id
     * @param  object $token the stripe token
     * @param  int    $plan  the plan (0 = yearly / 1 = monthly)
     * @return array         an API response
     */
	function subscribe($id, $token, $plan) {
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $id, 'id_ur' => $_SESSION['user']->id));
	    
	    if ($owns && ($owns->stripe_id === '0' || $owns->stripe_cancelled > 0)) {
	        $responseSent = $this->validatePlan($plan, $owns->type);
    	    $responsePlan = json_decode($responseSent);
    	    if ($responsePlan->result == false) {
    	        return $responseSent;
    	    }
    	    
	        try {
	            require_once(SITE_BASE_LIB . 'stripe-3.12.1' . DIRECTORY_SEPARATOR . 'init.php');
	            
	            \Stripe\Stripe::setApiKey((CC_LIVE) ? CC_LIVE_SK : CC_TEST_SK);
	            
                $stripe = \Stripe\Customer::create(array(
                    'email' => $token['email'],
                    'source'  => $token['id'],
                    'plan' => ($plan == 1) ? 'Monthly' : 'Yearly',
                    'trial_end' => (time() > $owns->expires) ? 'now' : $owns->expires
                ));
                
                $update = $GLOBALS['db']->update('board', 
                    array('id' => $owns->id,
                          'plan' => $plan,
                          'stripe_id' => $stripe->id,
                          'stripe_cancelled' => 0,
                          'stripe_error' => '',
                          'updated' => time())
                );
                
                if ($update) {
                    if (empty($_SESSION['user']->email)) {
                        $_SESSION['user']->email = $token['email'];
                        $GLOBALS['db']->update('user', array('id' => $_SESSION['user']->id, 'email' => $_SESSION['user']->email));
                    }
                    
                    $this->result = true;
            	    $this->msg = '';
            	    
            	    return $this->getResponse();
            	} else {
            	    $this->result = false;
            	    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Subscribe - Error #1';
            	    
            	    return $this->getResponse();
            	}
            } catch(\Stripe\Error\Base $e) {
                $body = $e->getJsonBody();
                
                $stripeError = "HTTP Status: " . $e->getHttpStatus() . "\nStripe Error: " . print_r($body['error'], true);
                
                $GLOBALS['db']->update('board', array('id' => $owns->id, 'stripe_error' => $stripeError));
                
                $this->result = false;
        	    $this->msg = $body['error']['message'];
        	    
        	    return $this->getResponse();
            }
	    } else {
	        $this->result = false;
    	    $this->msg = '';
    	    
    	    return $this->getResponse();
	    }
	}
	
	/**
     * Stripe Subscription (for an archived board)
     *
     * @param  int    $id    the board id
     * @param  object $token the stripe token
     * @param  int    $plan  the plan (0 = yearly / 1 = monthly)
     * @return array         an API response
     */
	function subscribeArchived($id, $token, $plan) {
	    // make sure the board is archived
	    $board = $GLOBALS['db']->getOne('board', array('id' => $id));
	    
	    if (\GrokBB\Board::isArchived($board)) {
	        $responseSent = $this->validatePlan($plan, $board->type);
    	    $responsePlan = json_decode($responseSent);
    	    if ($responsePlan->result == false) {
    	        return $responseSent;
    	    }
    	    
	        try {
	            require_once(SITE_BASE_LIB . 'stripe-3.12.1' . DIRECTORY_SEPARATOR . 'init.php');
	            
	            \Stripe\Stripe::setApiKey((CC_LIVE) ? CC_LIVE_SK : CC_TEST_SK);
	            
                $stripe = \Stripe\Customer::create(array(
                    'email' => $token['email'],
                    'source'  => $token['id'],
                    'plan' => ($plan == 1) ? 'Monthly' : 'Yearly'
                ));
                
                $update = $GLOBALS['db']->update('board', 
                    array('id' => $board->id,
                          'id_ur' => $_SESSION['user']->id,
                          'plan' => $plan,
                          'stripe_id' => $stripe->id,
                          'stripe_cancelled' => 0,
                          'stripe_error' => '',
                          'updated' => time())
                );
                
                if ($update) {
                    if (empty($_SESSION['user']->email)) {
                        $_SESSION['user']->email = $token['email'];
                        $GLOBALS['db']->update('user', array('id' => $_SESSION['user']->id, 'email' => $_SESSION['user']->email));
                    }
                    
                    $this->result = true;
            	    $this->msg = '';
            	    
            	    return $this->getResponse();
            	} else {
            	    $this->result = false;
            	    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Subscribe Archived - Error #1';
            	    
            	    return $this->getResponse();
            	}
            } catch(\Stripe\Error\Base $e) {
                $body = $e->getJsonBody();
                
                $stripeError = "HTTP Status: " . $e->getHttpStatus() . "\nStripe Error: " . print_r($body['error'], true);
                
                $GLOBALS['db']->update('board', array('id' => $board->id, 'stripe_error' => $stripeError));
                
                $this->result = false;
        	    $this->msg = $body['error']['message'];
        	    
        	    return $this->getResponse();
            }
	    } else {
	        $this->result = false;
    	    $this->msg = '';
    	    
    	    return $this->getResponse();
	    }
	}
	
	/**
     * Stripe Retrieve
     *
     * @param  int    $id       the board id
     * @return object or string the stripe object or error message
     */
	function subscribeInfo($id) {
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $id, 'id_ur' => $_SESSION['user']->id));
	    
	    if ($owns) {
	        try {
	            require_once(SITE_BASE_LIB . 'stripe-3.12.1' . DIRECTORY_SEPARATOR . 'init.php');
	            
	            \Stripe\Stripe::setApiKey((CC_LIVE) ? CC_LIVE_SK : CC_TEST_SK);
	            
                $stripe = \Stripe\Customer::retrieve($owns->stripe_id);
                
                return $stripe;
            } catch(\Stripe\Error\Base $e) {
                $body = $e->getJsonBody();
                return $body['error']['message'];
            }
        }
	}
	
	/**
     * Stripe Cancellation
     *
     * @param  int   $id the board id
     * @return array     an API response
     */
	function cancel($id) {
	    if ($_SESSION['user']->isAdmin) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $id));
	    } else {
	        // make sure the logged in user owns this board
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $id, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    if ($owns) {
	        try {
	            require_once(SITE_BASE_LIB . 'stripe-3.12.1' . DIRECTORY_SEPARATOR . 'init.php');
	            
	            \Stripe\Stripe::setApiKey((CC_LIVE) ? CC_LIVE_SK : CC_TEST_SK);
	            
                $stripe = \Stripe\Customer::retrieve($owns->stripe_id);
                $renews = (time() < $owns->expires) ? $owns->expires : $stripe->subscriptions->data[0]->current_period_end;
                $result = $stripe->delete();
                
                if ($result->deleted) {
                    $update = $GLOBALS['db']->update('board', array('id' => $owns->id, 'expires' => $renews, 'stripe_cancelled' => time()));
                    
                    $this->result = true;
            	    $this->msg = '';
            	    
            	    return $this->getResponse();
                } else {
                    $this->result = false;
            	    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Cancel - Error #1';
            	    
            	    return $this->getResponse();
                }
                
                return $stripe;
            } catch(\Stripe\Error\Base $e) {
                $body = $e->getJsonBody();
                
                $stripeError = "HTTP Status: " . $e->getHttpStatus() . "\nStripe Error: " . print_r($body['error'], true);
                
                error_log($stripeError);
                
                $GLOBALS['db']->update('board', array('id' => $owns->id, 'stripe_error' => $stripeError));
                
                $this->result = false;
        	    $this->msg = $body['error']['message'];
        	    
        	    return $this->getResponse();
            }
        }
	}
	
	/**
     * Uploads a header image
     *
     * @param  string $name the board's name
     * @param  string $hbc  the header's background color
     * @param  string $hbr  the header's background repeat
     * @return bool         TRUE on success
     */
	public function uploadHeader($name, $hbc = false, $hbr = false) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
    	    $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
    	    
    	    if (!file_exists($dir)) { mkdir($dir); }
    	    
    	    $imgPath = $dir . DIRECTORY_SEPARATOR . 'header.png';
    	    $mResult =  move_uploaded_file($_FILES['files']['tmp_name'][0], $imgPath);
    	    
    	    if ($mResult) {
    	        $mime = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($mime, $imgPath);
                finfo_close($mime);
                
                // make sure it is a valid mime type
                if (in_array($type, array('image/png'))) {
                    $settings = array('id_bd' => $owns->id);
                    
                    if ($hbc !== false) { $settings['header_back_color'] = Util::sanitizeColor($hbc); }
                    if ($hbr !== false) { $settings['header_back_repeat'] = (int) $hbr; }
                    
                    $GLOBALS['db']->update('board_settings', $settings, 'id_bd');
                    $GLOBALS['db']->update('board', array('id' => $owns->id, 'updated' => time()));
                    
                    $this->result = true;
	                $this->msg = '';
                } else {
                    unlink($imgPath);
                    
                    $this->result = false;
    	            $this->msg = 'Upload Failed - You have uploaded an unsupported image type.';
                }
    	    } else {
    	        $this->result = false;
    	        $this->msg = 'Upload Failed - Please try again.';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Updates a board's sidebar
     *
     * @param  string $name the board's name
     * @param  string $desc the board's description
     * @param  int    $mods display the board's moderators (0 = No / 1 = Yes)
     * @param  int    $whos display who is online (0 = No / 1 = Yes)
     * @return array        an API response
     */
	public function setSidebar($name, $desc, $mods, $whos) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
    	    $gmd = new \cebe\markdown\GithubMarkdown();
    	    $gmd->enableNewlines = true;
    	    
	        $descHTML = $gmd->parse($desc);
	        
	        $descSafe = Util::sanitizeGMD($descHTML);
    	    
    	    $GLOBALS['db']->update('board', 
    	        array('id' => $owns->id, 
    	              'updated' => time(), 
    	              'desc_sidebar' => $descSafe, 
    	              'desc_sidebar_md' => $desc, 
    	              'desc_sidebar_mods' => (int) $mods,
    	              'desc_sidebar_whos' => (int) $whos)
    	    );
    	    
    	    $this->result = true;
    	    $this->msg = $descSafe;
    	} else {
    	    $this->result = false;
    	    $this->msg = '';
    	}
	    
	    return $this->getResponse();
	}
	
	/**
     * Updates a board's stylesheet
     *
     * @param  string $name the board's name
     * @param  string $css  the board's stylesheet
     * @param  string $hmc  the header menu color
     * @param  string $hnc  the header name color
     * @param  string $hnf  the header name font
     * @param  string $bc   the button color
     * @param  string $bh   the button hover
     * @param  string $bf   the button font
     * @param  string $btc  the button text color
     * @param  string $bth  the button text hover
     * @param  string $tc   the tag color
     * @return array        an API response
     */
	public function setStylesheet($name, $css, $hmc = false, $hnc = false, $hnf = false, $bc = false, $bh = false, $bf = false, $btc = false, $bth = false, $tc = false) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
    	    $settings = array('id_bd' => $owns->id, 'stylesheet' => Util::sanitizeCSS($css));
                    
            if ($hmc !== false) { $settings['header_menu_color'] = Util::sanitizeColor($hmc); }
            if ($hnc !== false) { $settings['header_name_color'] = Util::sanitizeColor($hnc); }
            if ($hnf !== false) { $settings['header_name_font']  = Util::sanitizeFonts($hnf); }
            if ($bc  !== false) { $settings['button_color'] = Util::sanitizeColor($bc); }
            if ($bh  !== false) { $settings['button_hover'] = Util::sanitizeColor($bh); }
            if ($bf  !== false) { $settings['button_font']  = Util::sanitizeFonts($bf); }
            if ($btc !== false) { $settings['button_text_color'] = Util::sanitizeColor($btc); }
            if ($bth !== false) { $settings['button_text_hover'] = Util::sanitizeColor($bth); }
            if ($tc  !== false) { $settings['tag_color']  = Util::sanitizeColor($tc); }
                          
    	    $GLOBALS['db']->update('board_settings', $settings, 'id_bd');
    	    $GLOBALS['db']->update('board', array('id' => $owns->id, 'updated' => time()));
    	    
    	    $this->result = true;
    	    $this->msg = '';
    	} else {
    	    $this->result = false;
    	    $this->msg = '';
    	}
	    
	    return $this->getResponse();
	}
	
	/**
     * Uploads an image
     *
     * @param  string $name the board's name
     * @return bool         TRUE on success
     */
	public function uploadImage($name) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
    	    $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
    	    
    	    if (!file_exists($dir)) { mkdir($dir); }
    	    
    	    $dir = $dir . DIRECTORY_SEPARATOR . 'images';
    	    
    	    if (!file_exists($dir)) { mkdir($dir); }
    	    
    	    // convert spaces to underscores and remove all non-alphanumeric characters
    	    $imgFile = str_replace(' ', '_', strtolower($_FILES['files']['name'][0]));
    	    $imgFile = preg_replace('/[^\da-z\._]/', '', $imgFile);
    	    
    	    $imgPath = $dir . DIRECTORY_SEPARATOR . $imgFile;
    	    $mResult =  move_uploaded_file($_FILES['files']['tmp_name'][0], $imgPath);
    	    
    	    if ($mResult) {
    	        $mime = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($mime, $imgPath);
                finfo_close($mime);
                
                // make sure it is a valid mime type
                if (in_array($type, array('image/png', 'image/jpeg', 'image/gif'))) {
                    if (round(Util::getDirSize($dir) / 1024 / 1024, 2) > 50) {
                        // delete the uploaded image
                        $this->deleteImage($name, $imgFile);
                        
                        $this->result = false;
    	                $this->msg = 'Upload Failed - You have exceeded your 50 MB limit.';
                    } else {
                        $this->result = true;
    	                $this->msg = '';
    	            }
                } else {
                    $this->deleteImage($name, $imgFile);
                    
                    $this->result = false;
    	            $this->msg = 'Upload Failed - You have uploaded an unsupported image type.';
                }
    	    } else {
    	        $this->result = false;
    	        $this->msg = 'Upload Failed - Please try again.';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Deletes an image
     *
     * @param  string $name the board's name
     * @param  string $file the image to delete
     * @return bool         TRUE on success
     */
	public function deleteImage($name, $file) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
    	    $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
    	    $dir = $dir . DIRECTORY_SEPARATOR . 'images';
    	    
    	    $imgPath = $dir . DIRECTORY_SEPARATOR . $file;
    	    $uResult = unlink($imgPath);
    	    
    	    if ($uResult) {
    	        $this->result = true;
	            $this->msg = '';
    	    } else {
    	        $this->result = false;
    	        $this->msg = 'Delete Failed - Please try again.';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Creates a category
     *
     * @param  string $name    the board's name
     * @param  string $nameCat the category's name
     * @param  string $color   the category's color
     * @param  int    $private is it for moderators only
     * @return bool            TRUE on success
     */
	public function createCategory($name, $nameCat, $color, $private) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
	    if ($owns) {
    	    // make sure it has valid characters
    		if (preg_match('/[^A-Za-z0-9\!\@\#\$\%\^\&\*\(\)\[\]\{\}\-\+\=\|\'\:\.\;\~\`\,\?\/\ ]/', $nameCat)) {
    		    $this->result = false;
    		    $this->msg = 'Your name has invalid characters. You can only use letters, numbers, and a few special characters.';
    		} else {
    		    try {
            	    $cid = $GLOBALS['db']->insert('board_category', 
            	        array('id_bd' => $owns->id, 'name' => $nameCat, 'color' => Util::sanitizeColor($color), 'private' => (int) $private)
            	    );
            	    
            	    $this->result = true;
            	    $this->msg = $cid;
            	} catch (Exception $e) {
                    $this->result = false;
        		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Create Category - Error #1';
        		    
        		    return $this->getResponse();
                }
        	}
    	} else {
    	    $this->result = false;
    	    $this->msg = '';
    	}
	    
	    return $this->getResponse();
	}
	
	/**
     * Uploads a category
     *
     * @param  string $name    the board's name
     * @param  string $nameCat the category's name
     * @param  string $color   the category's color
     * @return bool            TRUE on success
     */
	public function uploadCategory($name, $nameCat, $color) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            $responseSent = $this->createCategory($name, $nameCat, $color);
    	    $responseName = json_decode($responseSent);
    	    if ($responseName->result == false) {
    	        return $responseSent;
    	    }
    	    
    	    $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
    	    
    	    if (!file_exists($dir)) { mkdir($dir); }
    	    
    	    $dir = $dir . DIRECTORY_SEPARATOR . 'categories';
    	    
    	    if (!file_exists($dir)) { mkdir($dir); }
    	    
    	    $imgPath = $dir . DIRECTORY_SEPARATOR . $responseName->msg . '.png';
    	    $mResult =  move_uploaded_file($_FILES['files']['tmp_name'][0], $imgPath);
    	    
    	    if ($mResult) {
    	        $mime = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($mime, $imgPath);
                finfo_close($mime);
                
                // make sure it is a valid mime type
                if (in_array($type, array('image/png'))) {
                    try {
                        $pim = new \imageLib($imgPath);
                        $pim->resizeImage(200, 40, 'crop');
                        $pim->saveImage($imgPath);
                        
                        $GLOBALS['db']->update('board_category', array('id' => $responseName->msg, 'image' => 1));
                        
                        $this->result = true;
	                    $this->msg = '';
                    } catch (Exception $e) {
                        $this->result = false;
    	                $this->msg = 'Image Resize Failed - Please upload a new image.';
                    }
                } else {
                    $this->removeCategoryImage($name, $responseName->msg);
                    
                    $this->result = false;
    	            $this->msg = 'Upload Failed - You have uploaded an unsupported image type.';
                }
    	    } else {
    	        $this->result = false;
    	        $this->msg = 'Upload Failed - Please try again.';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Updates a category
     *
     * @param  string $name    the board's name
     * @param  string $id      the category's id
     * @param  string $nameCat the category's name
     * @param  string $color   the category's color
     * @param  int    $private is it for moderators only
     * @return bool            TRUE on success
     */
	public function updateCategory($name, $id, $nameCat, $color, $private) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
	    if ($owns) {
	        $category = $GLOBALS['db']->getOne('board_category', array('id' => $id, 'id_bd' => $owns->id));
            
            if ($category) {
        	    // make sure it has valid characters
        		if (preg_match('/[^A-Za-z0-9\!\@\#\$\%\^\&\*\(\)\[\]\{\}\-\+\=\|\'\:\.\;\~\`\,\?\/\ ]/', $nameCat)) {
        		    $this->result = false;
        		    $this->msg = 'Your name has invalid characters.';
        		} else {
        		    try {
                	    $GLOBALS['db']->update('board_category', 
                	        array('id' => $id, 'name' => $nameCat, 'color' => Util::sanitizeColor($color), 'private' => (int) $private)
                	    );
                	    
                	    $this->result = true;
                	    $this->msg = '';
                	} catch (Exception $e) {
                        $this->result = false;
            		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Category - Error #1';
            		    
            		    return $this->getResponse();
                    }
            	}
            } else {
        	    $this->result = false;
    	        $this->msg = 'Update Failed - Please try again.';
        	}
    	} else {
    	    $this->result = false;
    	    $this->msg = '';
    	}
	    
	    return $this->getResponse();
	}
	
	/**
     * Deletes a category
     *
     * @param  string $name the board's name
     * @param  int    $id   the category to delete
     * @return bool         TRUE on success
     */
	public function deleteCategory($name, $id) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            $del = $GLOBALS['db']->delete('board_category', array('id' => $id, 'id_bd' => $owns->id));
            
            if ($del) {
                // update all associated topics to the default category
                $defcat = $GLOBALS['db']->getOne('board_category', array('id_bd' => $owns->id, 'defcat' => 1));
                $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'topic SET id_bc = ' . $defcat->id . ' WHERE id_bd = ' . $owns->id . ' AND id_bc = ' . $id);
                
                // delete the category's image
    	        $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
        	    $dir = $dir . DIRECTORY_SEPARATOR . 'categories';
        	    
        	    $imgPath = $dir . DIRECTORY_SEPARATOR . $id . '.png';
        	    $uResult = unlink($imgPath);
        	    
    	        $this->result = true;
	            $this->msg = '';
    	    } else {
    	        $this->result = false;
    	        $this->msg = 'Delete Failed - Please try again.';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Changes the default category
     *
     * @param  string $name the board's name
     * @param  int    $id   the default category
     * @return bool         TRUE on success
     */
	public function updateCategoryDefault($name, $id) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            try {
                $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board_category SET defcat = 0 WHERE id_bd = ' . $owns->id);
                $result = $GLOBALS['db']->update('board_category', array('id' => $id, 'defcat' => 1));
                
                $this->result = true;
                $this->msg = '';
            } catch (Exception $e) {
                $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Category Default - Error #1';
    		    
    		    return $this->getResponse();
            }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Updates the category image
     *
     * @param  string $name the board's name
     * @param  int    $id   the category's id
     * @return bool         TRUE on success
     */
	public function updateCategoryImage($name, $id) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            $category = $GLOBALS['db']->getOne('board_category', array('id' => $id, 'id_bd' => $owns->id));
            
            if ($category) {
        	    $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
        	    
        	    if (!file_exists($dir)) { mkdir($dir); }
        	    
        	    $dir = $dir . DIRECTORY_SEPARATOR . 'categories';
        	    
        	    if (!file_exists($dir)) { mkdir($dir); }
        	    
        	    $imgPath = $dir . DIRECTORY_SEPARATOR . $id . '.png';
        	    $mResult =  move_uploaded_file($_FILES['files']['tmp_name'][0], $imgPath);
        	    
        	    if ($mResult) {
        	        $mime = finfo_open(FILEINFO_MIME_TYPE);
                    $type = finfo_file($mime, $imgPath);
                    finfo_close($mime);
                    
                    // make sure it is a valid mime type
                    if (in_array($type, array('image/png'))) {
                        try {
                            $pim = new \imageLib($imgPath);
                            $pim->resizeImage(200, 40, 'crop');
                            $pim->saveImage($imgPath);
                            
                            $GLOBALS['db']->update('board_category', array('id' => $id, 'image' => 1));
                            
                            $this->result = true;
    	                    $this->msg = '';
                        } catch (Exception $e) {
                            $this->result = false;
        	                $this->msg = 'Image Resize Failed - Please upload a new image.';
                        }
                    } else {
                        $this->removeCategoryImage($name, $id);
                        
                        $this->result = false;
        	            $this->msg = 'Upload Failed - You have uploaded an unsupported image type.';
                    }
        	    } else {
        	        $this->result = false;
        	        $this->msg = 'Upload Failed - Please try again.';
        	    }
        	} else {
        	    $this->result = false;
    	        $this->msg = 'Upload Failed - Please try again.';
        	}
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Removes the category's image
     *
     * @param  string $name the board's name
     * @param  int    $id   the category id
     * @return bool         TRUE on success
     */
	public function removeCategoryImage($name, $id) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            $GLOBALS['db']->update('board_category', array('id' => $id, 'image' => 0));
            
            // delete the category's image
	        $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
    	    $dir = $dir . DIRECTORY_SEPARATOR . 'categories';
    	    
    	    $imgPath = $dir . DIRECTORY_SEPARATOR . $id . '.png';
    	    $uResult = unlink($imgPath);
            
            $this->result = true;
            $this->msg = '';
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Updates a board's settings
     *
     * @param  string $name the board's name
     * @param  string $tcn  the content name
     * @param  string $tcd  the content description
     * @param  string $tra  the request access text
     * @param  int    $tap  allow topics to have polls (0 = No / 1 = Yes)
     * @return array        an API response
     */
	public function updateSettings($name, $tcn, $tcd, $tra, $tap) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
    	    $gmd = new \cebe\markdown\GithubMarkdown();
    	    $gmd->enableNewlines = true;
    	    
    	    $tcnSafe = Util::sanitizeTXT($tcn);
    	    
	        $tcdHTML = $gmd->parse($tcd);
	        $tcdSafe = Util::sanitizeGMD($tcdHTML);
	        
	        $topicSettings = array(
    	        'id_bd' => $owns->id, 
    	        'topic_content_name' => $tcnSafe,
    	        'topic_content_desc' => $tcdSafe, 
    	        'topic_content_desc_md' => $tcd,
    	        'topic_allowpolls' => (int) $tap
    	    );
    	    
    	    if ($owns->type == 2) {
    	        $traHTML = $gmd->parse($tra);
	            $traSafe = Util::sanitizeGMD($traHTML);
	            
    	        $topicSettings['topic_request_access'] = $traSafe;
    	        $topicSettings['topic_request_access_md'] = $tra;
    	    }
    	    
    	    $GLOBALS['db']->update('board_settings', $topicSettings, 'id_bd');
    	    
    	    $GLOBALS['db']->update('board', array('id' => $owns->id, 'updated' => time()));
    	    
    	    $this->result = true;
    	    $this->msg = '';
    	} else {
    	    $this->result = false;
    	    $this->msg = '';
    	}
	    
	    return $this->getResponse();
	}
	
	/**
     * Adds a favorite board
     *
     * @param  int   $bid the board id
     * @return array      an API response
     */
	public function addFavorite($bid) {
	    $board = $GLOBALS['db']->getOne('board', array('id' => $bid));
		
		if ($board && $_SESSION['user']->id) {
		    try {
		        $record = array(
    	            'id_ur' => $_SESSION['user']->id,
    	            'id_bd' => $bid,
                    'added' => time()
                );
                
		        $GLOBALS['db']->insert('user_board', $record);
		        
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board SET counter_users = counter_users + 1 WHERE id = ' . (int) $bid);
		        
		        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Add Favorite - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Removes a favorite board
     *
     * @param  int   $bid the board id
     * @return array      an API response
     */
	public function remFavorite($bid) {
	    try {
	        $exists = $GLOBALS['db']->getOne('user_board', array('id_ur' => $_SESSION['user']->id, 'id_bd' => $bid, 'deleted' => 0));
	        
	        if ($exists) {
    	        $GLOBALS['db']->update('user_board', array('id' => $exists->id, 'deleted' => time()));
    	        
    	        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board SET counter_users = counter_users - 1 WHERE id = ' . (int) $bid);
    	        
    	        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
    		} else {
    		    $this->result = false;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
    		}
        } catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Remove Favorite - Error #1';
		    
		    return $this->getResponse();
        }
	}
	
	/**
     * Creates an announcement
     *
     * @param  string $bid     the board id
     * @param  string $subject the announcement subject
     * @param  string $content the announcement content
     * @param  int    $id      the announcement id (for drafts only)
     * @return array           an API response
     */
	public function saveAnnouncement($bid, $subject, $content, $id = 0) {
	    if (trim($subject) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a subject.';
		    
		    return $this->getResponse();
	    }
	    
	    if (trim($content) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter an announcement.';
		    
		    return $this->getResponse();
	    }
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
		
		if ($owns) {
		    try {
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $contentHTML = $gmd->parse($content);
	            
		        $contentSafe = Util::sanitizeGMD($contentHTML);
		        $subjectSafe = Util::sanitizeTXT($subject);
		        
		        $record = array(
		            'id_bd' => $bid,
                    'subject' => $subjectSafe,
                    'content' => $contentSafe,
                    'content_md' => $content,
                    'updated' => time()
                );
                
		        if ($id > 0) {
		            $record['id'] = $id;
		            $aid = $GLOBALS['db']->update('board_announcement', $record);
		        } else {
    		        $aid = $GLOBALS['db']->insert('board_announcement', $record);
                }
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Save Announcement - Error #1';
    		    
    		    return $this->getResponse();
	        }
	        
		    $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Edits an announcement
     *
     * @param  int   $bid the board id
     * @param  int   $aid the announcement id
     * @return array      an API response
     */
	public function editAnnouncement($bid, $aid) {
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            $announcement = $GLOBALS['db']->getOne('board_announcement', array('id' => $aid, 'id_bd' => $bid));
            
            if ($announcement) {
                $this->result = true;
	            $this->msg = $announcement;
    	    } else {
    	        $this->result = false;
    	        $this->msg = '';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Sends an announcement
     *
     * @param  int   $bid  the board id
     * @param  int   $aid  the announcement id
     * @param  int   $send 0 = no / 1 = yes
     * @return array       an API response
     */
	public function sendAnnouncement($bid, $aid, $send = 1) {
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            $update = $GLOBALS['db']->update('board_announcement', 
                array('id' => $aid, 'id_bd' => $bid, 'sent' => (($send) ? time() : 0)));
            
            if ($update) {
                $this->result = true;
	            $this->msg = '';
    	    } else {
    	        $this->result = false;
    	        $this->msg = '';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Receive an announcement
     *
     * @param  int   $aid the announcement id
     * @return array      an API response
     */
	public function rcvdAnnouncement($aid) {
	    $announcement = $GLOBALS['db']->getOne('board_announcement', array('id' => $aid));
	    
	    if ($announcement) {
            // make sure the logged in user is subscribed to this board
	        $subscribed = $GLOBALS['db']->getOne('user_board', array('id_bd' => $announcement->id_bd, 'id_ur' => $_SESSION['user']->id, 'deleted' => 0));
            
            if ($subscribed) {
                $ubaWhere = array('id_ur' => $_SESSION['user']->id, 'id_ba' => $announcement->id);
                
                $read = $GLOBALS['db']->getOne('user_board_announcement', $ubaWhere);
                
                if ($read == false) {
                    $ubaWhere['read'] = time();
                    
                    // mark the announcement as read
                    $GLOBALS['db']->insert('user_board_announcement', $ubaWhere);
                }
                
                $this->result = true;
	            $this->msg = $announcement;
    	    } else {
    	        $this->result = false;
    	        $this->msg = '';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Deletes an announcement
     *
     * @param  int  $bid the board id
     * @param  int  $aid the announcement id
     * @return bool      TRUE on success
     */
	public function deleteAnnouncement($bid, $aid) {
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    
        if ($owns) {
            $delete = $GLOBALS['db']->update('board_announcement', array('id' => $aid, 'deleted' => time()));
            
            if ($delete) {
                $this->result = true;
	            $this->msg = '';
    	    } else {
    	        $this->result = false;
    	        $this->msg = '';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Gets the popular boards
     *
     * @param  int   $limit  the amount to return
     * @param  array $search the WHERE clause to search on
     * @return array         the query result
     */
	public static function getPop($limit, $search = false) {
	    $calc = '((%col% / (SELECT MAX(%col%) + 1 FROM ' . DB_PREFIX . 'board)) * 100)';
        
        // a board's users are weighted at 40% and topics at 60%
        $wgts = array('counter_users' => '0.40', 'counter_topics' => '0.60');
        
        $sortSQL = '(';
        
        foreach ($wgts as $wgtCol => $wgtVal) {
            $sortSQL .= '(' . str_replace('%col%', $wgtCol, $calc) . ' * ' . $wgtVal . ') + ';
        }
        
        $sortSQL = substr($sortSQL, 0, -3) . ') DESC';
        
        if ($search && is_array($search)) {
            return $GLOBALS['db']->getAll('board b INNER JOIN ' . DB_PREFIX . 'topic t ON b.id = t.id_bd', $search, $sortSQL, 
                array('b.*', 'GROUP_CONCAT(t.id) AS topics'), $limit, false, 'b.id');
        } else {
            return $GLOBALS['db']->getAll('board', false, $sortSQL, false, $limit);
        }
	}
	
	/**
     * Gets the new boards
     *
     * @param  int   $limit  the amount to return
     * @param  array $search the WHERE clause to search on
     * @return array         the query result
     */
	public static function getNew($limit, $search = false) {
	    // the board must be at least a week old
        $oneWeek = time() - (86400 * 7);
        
        $where = array('b.created' => array('< ', $oneWeek));
        
        if ($search && is_array($search)) {
            $where = array_merge($where, $search);
            
            return $GLOBALS['db']->getAll('board b INNER JOIN ' . DB_PREFIX . 'topic t ON b.id = t.id_bd', $where, 'b.created DESC', 
                array('b.*', 'GROUP_CONCAT(t.id) AS topics'), $limit, false, 'b.id');
        } else {
            return $GLOBALS['db']->getAll('board b', $where, 'b.created DESC', false, $limit);
        }
	}
	
	/**
     * Gets the old boards
     *
     * @param  int   $limit  the amount to return
     * @param  array $search the WHERE clause to search on
     * @return array         the query result
     */
	public static function getOld($limit, $search = false) {
	    if ($search && is_array($search)) {
            return $GLOBALS['db']->getAll('board b INNER JOIN ' . DB_PREFIX . 'topic t ON b.id = t.id_bd', $search, 'b.created ASC', 
                array('b.*', 'GROUP_CONCAT(t.id) AS topics'), $limit, false, 'b.id');
        } else {
            return $GLOBALS['db']->getAll('board b', false, 'b.created ASC', false, $limit);
        }
	}
	
	/**
     * Gets the rising boards
     *
     * @param  int   $limit  the amount to return
     * @param  array $search the WHERE clause to search on
     * @return array         the query result
     */
	public static function getRising($limit, $search = false) {
	    $oneMnth = time() - (86400 * 30);

        $favRise = $GLOBALS['db']->custom('SELECT id_bd, COUNT(id) AS cnt FROM ' . DB_PREFIX . 'user_board WHERE added > ' . $oneMnth . ' AND deleted = 0 GROUP BY id_bd ORDER BY COUNT(id) DESC, added DESC');
        $favMax  = ($favRise) ? $favRise[0]->cnt : 0;
        
        $topRise = $GLOBALS['db']->custom('SELECT id_bd, COUNT(id) AS cnt FROM ' . DB_PREFIX . 'topic WHERE created > ' . $oneMnth . ' GROUP BY id_bd ORDER BY COUNT(id) DESC, created DESC');
        $topMax  = ($topRise) ? $topRise[0]->cnt : 0;
        
        $wgtRise = array();
        
        // a board's users are weighted at 40% and topics at 60%
        
        foreach ($favRise as $rise) {
            if (isset($wgtRise[$rise->id_bd])) {
                $wgtRise[$rise->id_bd] += ($rise->cnt / $favMax) * 100 * 0.4;
            } else {
                $wgtRise[$rise->id_bd]  = ($rise->cnt / $favMax) * 100 * 0.4;
            }
        }
        
        foreach ($topRise as $rise) {
            if (isset($wgtRise[$rise->id_bd])) {
                $wgtRise[$rise->id_bd] += ($rise->cnt / $topMax) * 100 * 0.6;
            } else {
                $wgtRise[$rise->id_bd]  = ($rise->cnt / $topMax) * 100 * 0.6;
            }
        }
        
        arsort($wgtRise);
        
        $brdsWgt = array_slice(array_keys($wgtRise), 0, $limit);
        if (!$brdsWgt) { $brdsWgt = array(0); }
        
        $where = array('b.id' => array('IN', $brdsWgt));
        
        if ($search && is_array($search)) {
            $where = array_merge($where, $search);
            
            return $GLOBALS['db']->getAll('board b INNER JOIN ' . DB_PREFIX . 'topic t ON b.id = t.id_bd', $where, 'FIELD(b.id, ' . implode(',', $brdsWgt) . ')', 
                array('b.*', 'GROUP_CONCAT(t.id) AS topics'), $limit, false, 'b.id');
        } else {
            return $GLOBALS['db']->getAll('board b', $where, 'FIELD(b.id, ' . implode(',', $brdsWgt) . ')');
        }
	}
	
	/**
     * Gets some random boards
     *
     * @param  int   $limit the amount to return
     * @return array        the query result
     */
	public static function getRandom($limit) {
	    $brdsMax = $GLOBALS['db']->custom('SELECT MAX(id) AS max FROM ' . DB_PREFIX . 'board');
        $brdsMax = $brdsMax[0]->max; $brdsLim = ($brdsMax > $limit) ? $limit : $brdsMax;
        
        $randCnt = 0;
        $randIDs = array();
        
        while ($randCnt < $brdsLim) {
            $id = mt_rand(1, $brdsMax);
            
            if (!in_array($id, $randIDs)) {
                $randIDs[] = $id;
                $randCnt++;
            }
        }
        
        return $GLOBALS['db']->getAll('board', array('id' => array('IN', $randIDs)), 'FIELD(id, ' . implode(',', $randIDs) . ')');
	}
	
	/**
     * Searches the boards
     *
     * @param  int   $limit  the amount to return
     * @param  array $search the WHERE clause to search on
     * @return array         the query result
     */
	public static function search($limit, $search = false) {
	    return $GLOBALS['db']->getAll('board b INNER JOIN ' . DB_PREFIX . 'topic t ON b.id = t.id_bd', $search, false, 
            array('b.*', 'GROUP_CONCAT(t.id) AS topics'), $limit, false, 'b.id');
	}
	
	/**
     * Gets the board tags that match the query
     *
     * @param  string $query the text to search for
     * @return string        the JSON results (in TagHandler format)
     */
	public static function getTags($query) {
	    $querySafe = Util::sanitizeTXT($query);
	    
	    $boardTags = $GLOBALS['db']->custom(
	        "SELECT tag1 AS tag FROM " . DB_PREFIX . "board WHERE tag1 LIKE '{$querySafe}%' UNION
	         SELECT tag2 AS tag FROM " . DB_PREFIX . "board WHERE tag2 LIKE '{$querySafe}%' UNION
	         SELECT tag3 AS tag FROM " . DB_PREFIX . "board WHERE tag3 LIKE '{$querySafe}%'");
	    
	    $tags = array('availableTags' => array());
	    
	    foreach ($boardTags as $bt) {
	        $tags['availableTags'][] = $bt->tag;
	    }
	    
	    return json_encode(array_unique($tags));
	}
	
	/**
     * Is the board archived
     *
     * @param  object a board object
     * @return bool   TRUE on success
     */
	public static function isArchived($board) {
	    // does the board have no subscription or has it been cancelled
	    // and is it now 30 days past the expiration date (grace period)
	    // and make sure the user is not on a FREE subscription plan
	    if (($board->stripe_id === '0' || $board->stripe_cancelled > 0) && 
	        time() > strtotime('+30 days', $board->expires) && $board->plan != 4) {
	        return true;
	    }
	    
	    return false;
	}
	
	/**
     * Is the user approved to post topics / access board
     *
     * @param  int  the board id
     * @return bool TRUE on success
     */
	public static function isApproved($bid) {
	    if (isset($_SESSION['user'])) {
	        // verify user permissions
    	    return $_SESSION['user']->isAdmin || $_SESSION['user']->isOwner == $bid || $_SESSION['user']->isModerator == $bid || 
    	        $GLOBALS['db']->getOne('user_board_approved', array('id_bd' => $bid, 'id_ur' => $_SESSION['user']->id));
	    } else {
	        return false;
	    }
	}
	
	/**
     * Is the user a moderator
     *
     * @param  int  the board id
     * @return bool TRUE on success
     */
	public static function isModerator($bid) {
	    if (isset($_SESSION['user'])) {
	        // verify user permissions
    	    return $_SESSION['user']->isAdmin || $_SESSION['user']->isOwner == $bid ||  
    	        $GLOBALS['db']->getOne('user_board_moderator', array('id_bd' => $bid, 'id_ur' => $_SESSION['user']->id));
	    } else {
	        return false;
	    }
	}
	
	/**
     * Is the user banned
     *
     * @param  int  the board id
     * @return bool TRUE on success
     */
	public static function isBanned($bid) {
	    if (isset($_SESSION['user'])) {
	        return $GLOBALS['db']->getOne('user_board_banned', array('id_bd' => $bid, 'id_ur' => $_SESSION['user']->id));
	    } else {
	        return false;
	    }
	}
	
	/**
     * Sends a topic access request
     *
     * @param  int    $bid     the board id
     * @param  string $message the message
     * @return array           an API response
     */
	public function requestTopicAccess($bid, $message) {
	    if (trim($message) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a message.';
		    
		    return $this->getResponse();
	    }
	    
		$board = $GLOBALS['db']->getOne('board', array('id' => $bid, 'type' => 2));
		$time  = time();
		
		if ($board) {
		    try {
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $messageHTML = $gmd->parse($message);
		        $messageSafe = Util::sanitizeGMD($messageHTML);
		        $subjectSafe = $board->name . ' - Request For Access';
		        
		        $record = array(
		            'id_ur' => $_SESSION['user']->id,
                    'id_to' => $board->id_ur,
                    'subject' => $subjectSafe,
                    'content' => $messageSafe,
                    'content_md' => $message,
                    'sent' => $time
                );
                
		        $mid = $GLOBALS['db']->insert('message', $record);
		        
		        if ($mid) {
        		    $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $board->id_ur,
                              'subject' => $subjectSafe,
                              'content' => $messageSafe,
                              'content_md' => $message,
                              'rcvd' => $time)
                    );
                }
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Request Access - Error #1';
    		    
    		    return $this->getResponse();
	        }
	        
		    $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Sends a board access request
     *
     * @param  int    $bid     the board id
     * @param  string $message the message
     * @return array           an API response
     */
	public function requestBoardAccess($bid, $message) {
	    if (trim($message) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a message.';
		    
		    return $this->getResponse();
	    }
	    
	    if ($_SESSION['user']->isBanned) {
	        $board = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
		    $board = $GLOBALS['db']->getOne('board', array('id' => $bid, 'type' => 1));
		}
		
		$time  = time();
		
		if ($board) {
		    try {
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $messageHTML = $gmd->parse($message);
		        $messageSafe = Util::sanitizeGMD($messageHTML);
		        $subjectSafe = $board->name . ' - Request For Access';
		        
		        $record = array(
		            'id_ur' => $_SESSION['user']->id,
                    'id_to' => $board->id_ur,
                    'subject' => $subjectSafe,
                    'content' => $messageSafe,
                    'content_md' => $message,
                    'sent' => $time
                );
                
		        $mid = $GLOBALS['db']->insert('message', $record);
		        
		        if ($mid) {
        		    $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $board->id_ur,
                              'subject' => $subjectSafe,
                              'content' => $messageSafe,
                              'content_md' => $message,
                              'rcvd' => $time)
                    );
                }
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Request Access - Error #1';
    		    
    		    return $this->getResponse();
	        }
	        
		    $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Adds an approved user
     *
     * @param  int    $bid      the board id
     * @param  string $username the username
     * @return array            an API response
     */
	public function addApprovedUser($bid, $username) {
	    if ($username == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a username.';
		    
		    return $this->getResponse();
	    }
	    
	    if ($_SESSION['user']->isModerator == $bid) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
	        // make sure the logged in user owns this board
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    $user = $GLOBALS['db']->getOne('user', array('username' => $username));
	    $time = time();
	    
	    if ($owns && $user && $user->id != $_SESSION['user']->id && $user->id != $owns->id_ur) {
	        $exists = $GLOBALS['db']->getOne('user_board_approved', array('id_bd' => $bid, 'id_ur' => $user->id));
	        
	        if ($exists) {
	            $this->result = false;
    		    $this->msg = 'This user is already approved.';
    		    
    		    return $this->getResponse();
    		} else {
	            $GLOBALS['db']->insert('user_board_approved', array('id_bd' => $bid, 'id_ur' => $user->id, 'approved' => $time));
    	        
    	        if ($_SESSION['user']->id != $owns->id_ur) {
    	            $gmd = new \cebe\markdown\GithubMarkdown();
		            $gmd->enableNewlines = true;
		            
    	            $userMod = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
    	            $userBan = SITE_BASE_URL . '/user/view/' . $user->id;
    	            
    	            $message = '[' . $_SESSION['user']->username . '](' . $userMod . ') has approved [' . $user->username . '](' . $userBan . ')';
    	            
    	            $messageHTML = $gmd->parse($message);
    		        $messageSafe = Util::sanitizeGMD($messageHTML);
    		        $subjectSafe = $owns->name . ' - User Approved';
    		        
    	            $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $owns->id_ur,
                              'subject' => $subjectSafe,
                              'content' => $messageSafe,
                              'content_md' => $message,
                              'rcvd' => $time)
                    );
    	        }
    	        
    	        $this->result = true;
    		    $this->msg = $user->id;
    		    
    		    return $this->getResponse();
    		}
		} else {
		    $this->result = false;
		    
		    if ($owns && $user && ($user->id == $_SESSION['user']->id || $user->id == $owns->id_ur)) {
		        $this->msg = 'You are in a maze of twisty little passages, all alike.';
		    } else {
		        $this->msg = 'This username does not exist.';
		    }
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Removes an approved user
     *
     * @param  int    $bid the board id
     * @param  string $uid the user id
     * @return array       an API response
     */
	public function remApprovedUser($bid, $uid) {
	    if ($_SESSION['user']->isModerator == $bid) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
    	}
    	
	    $user = $GLOBALS['db']->getOne('user', array('id' => $uid));
	    
	    if ($owns && $user && $user->id != $_SESSION['user']->id && $user->id != $owns->id_ur) {
	        $GLOBALS['db']->delete('user_board_approved', array('id_bd' => $bid, 'id_ur' => $uid));
	        
	        if ($_SESSION['user']->id != $owns->id_ur) {
	            $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $userMod = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
	            $userBan = SITE_BASE_URL . '/user/view/' . $user->id;
	            
	            $message = '[' . $_SESSION['user']->username . '](' . $userMod . ') has removed the approval for [' . $user->username . '](' . $userBan . ')';
	            
	            $messageHTML = $gmd->parse($message);
		        $messageSafe = Util::sanitizeGMD($messageHTML);
		        $subjectSafe = $owns->name . ' - User Access Removed';
		        
	            $GLOBALS['db']->insert('message', 
                    array('id_ur' => $_SESSION['user']->id,
                          'id_to' => $owns->id_ur,
                          'subject' => $subjectSafe,
                          'content' => $messageSafe,
                          'content_md' => $message,
                          'rcvd' => time())
                );
	        }
	        
	        $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    
		    if ($owns && $user && ($user->id == $_SESSION['user']->id || $user->id == $owns->id_ur)) {
		        $this->msg = 'You are in a maze of twisty little passages, all alike.';
		    } else {
		        $this->msg = '';
		    }
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Adds a moderator
     *
     * @param  int    $bid      the board id
     * @param  string $username the username
     * @return array            an API response
     */
	public function addModerator($bid, $username) {
	    if ($username == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a username.';
		    
		    return $this->getResponse();
	    }
	    
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    $user = $GLOBALS['db']->getOne('user', array('username' => $username));
	    
	    if ($owns && $user) {
	        $exists = $GLOBALS['db']->getOne('user_board_moderator', array('id_bd' => $bid, 'id_ur' => $user->id));
	        
	        if ($exists || $owns->id_ur == $user->id) {
	            $this->result = false;
    		    $this->msg = 'This user is already a moderator.';
    		    
    		    return $this->getResponse();
    		} else {
	            $GLOBALS['db']->insert('user_board_moderator', array('id_bd' => $bid, 'id_ur' => $user->id, 'added' => time()));
    	        
    	        $this->result = true;
    		    $this->msg = $user->id;
    		    
    		    return $this->getResponse();
    		}
		} else {
		    $this->result = false;
		    $this->msg = 'This username does not exist.';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Removes a moderator
     *
     * @param  int    $bid the board id
     * @param  string $uid the user id
     * @return array       an API response
     */
	public function remModerator($bid, $uid) {
	    // make sure the logged in user owns this board
	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    $user = $GLOBALS['db']->getOne('user_board_moderator', array('id_bd' => $bid, 'id_ur' => $uid));
	    
	    if ($owns && $user) {
	        $GLOBALS['db']->delete('user_board_moderator', array('id_bd' => $bid, 'id_ur' => $uid));
	        
	        $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Adds a banned user
     *
     * @param  int    $bid      the board id
     * @param  string $username the username
     * @return array            an API response
     */
	public function addBanned($bid, $username) {
	    if ($username == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a username.';
		    
		    return $this->getResponse();
	    }
	    
	    if ($_SESSION['user']->isModerator == $bid) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
	        // make sure the logged in user owns this board
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    $user = $GLOBALS['db']->getOne('user', array('username' => $username));
	    $time = time();
	    
	    if ($owns && $user && $user->id != $_SESSION['user']->id && $user->id != $owns->id_ur) {
	        $exists = $GLOBALS['db']->getOne('user_board_banned', array('id_bd' => $bid, 'id_ur' => $user->id));
	        
	        if ($exists) {
	            $this->result = false;
    		    $this->msg = 'This user is already banned.';
    		    
    		    return $this->getResponse();
    		} else {
	            $GLOBALS['db']->insert('user_board_banned', array('id_bd' => $bid, 'id_ur' => $user->id, 'banned' => $time));
    	        
    	        if ($_SESSION['user']->id != $owns->id_ur) {
    	            $gmd = new \cebe\markdown\GithubMarkdown();
		            $gmd->enableNewlines = true;
		            
    	            $userMod = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
    	            $userBan = SITE_BASE_URL . '/user/view/' . $user->id;
    	            
    	            $message = '[' . $_SESSION['user']->username . '](' . $userMod . ') has banned [' . $user->username . '](' . $userBan . ')';
    	            
    	            $messageHTML = $gmd->parse($message);
    		        $messageSafe = Util::sanitizeGMD($messageHTML);
    		        $subjectSafe = $owns->name . ' - User Banned';
    		        
    	            $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $owns->id_ur,
                              'subject' => $subjectSafe,
                              'content' => $messageSafe,
                              'content_md' => $message,
                              'rcvd' => $time)
                    );
    	        }
    	        
    	        $this->result = true;
    		    $this->msg = $user->id;
    		    
    		    return $this->getResponse();
    		}
		} else {
		    $this->result = false;
		    
		    if ($owns && $user && ($user->id == $_SESSION['user']->id || $user->id == $owns->id_ur)) {
		        $this->msg = 'You are in a maze of twisty little passages, all alike.';
		    } else {
		        $this->msg = 'This username does not exist.';
		    }
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Removes a banned user
     *
     * @param  int    $bid the board id
     * @param  string $uid the user id
     * @return array       an API response
     */
	public function remBanned($bid, $uid) {
	    if ($_SESSION['user']->isModerator == $bid) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
    	}
    	
	    $user = $GLOBALS['db']->getOne('user', array('id' => $uid));
	    
	    if ($owns && $user && $user->id != $_SESSION['user']->id && $user->id != $owns->id_ur) {
	        $GLOBALS['db']->delete('user_board_banned', array('id_bd' => $bid, 'id_ur' => $uid));
	        
	        if ($_SESSION['user']->id != $owns->id_ur) {
	            $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $userMod = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
	            $userBan = SITE_BASE_URL . '/user/view/' . $user->id;
	            
	            $message = '[' . $_SESSION['user']->username . '](' . $userMod . ') has removed the ban on [' . $user->username . '](' . $userBan . ')';
	            
	            $messageHTML = $gmd->parse($message);
		        $messageSafe = Util::sanitizeGMD($messageHTML);
		        $subjectSafe = $owns->name . ' - User Granted Access';
		        
	            $GLOBALS['db']->insert('message', 
                    array('id_ur' => $_SESSION['user']->id,
                          'id_to' => $owns->id_ur,
                          'subject' => $subjectSafe,
                          'content' => $messageSafe,
                          'content_md' => $message,
                          'rcvd' => time())
                );
	        }
	        
	        $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    
		    if ($owns && $user && ($user->id == $_SESSION['user']->id || $user->id == $owns->id_ur)) {
		        $this->msg = 'You are in a maze of twisty little passages, all alike.';
		    } else {
		        $this->msg = '';
		    }
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Reports a topic to the board owner and all moderators
     *
     * @param  int    $bid     the board id
     * @param  int    $tid     the topic id
     * @param  string $message the message
     * @return array           an API response
     */
	public function reportTopic($bid, $tid, $message) {
	    if (trim($message) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a message.';
		    
		    return $this->getResponse();
	    }
	    
	    $topic = $GLOBALS['db']->getOne('topic', array('id_bd' => $bid, 'id' => $tid));
		
		$time  = time();
		
		if ($topic) {
		    $board = $GLOBALS['db']->getOne('board', array('id' => $bid));
		    
		    try {
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
		        $reporter = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
	            $topicURL = SITE_BASE_URL . '/g/' . str_replace(' ', '_', $board->name) . '/view/' . $topic->id;
	            
		        $message = "[" . $_SESSION['user']->username . "](" . $reporter . ") has reported on [" . $topic->title . "](" . $topicURL . ")\n\n" . $message;
		        
	            $messageHTML = $gmd->parse($message);
		        $messageSafe = Util::sanitizeGMD($messageHTML);
		        $subjectSafe = $board->name . ' - Topic Reported';
		        
		        $record = array(
		            'id_ur' => $_SESSION['user']->id,
                    'id_to' => $board->id_ur,
                    'subject' => $subjectSafe,
                    'content' => $messageSafe,
                    'content_md' => $message,
                    'sent' => $time
                );
                
		        $mid = $GLOBALS['db']->insert('message', $record);
		        
		        if ($mid) {
        		    $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $board->id_ur,
                              'subject' => $subjectSafe,
                              'content' => $messageSafe,
                              'content_md' => $message,
                              'rcvd' => $time)
                    );
                    
                    // get all non-banned moderators and copy them on the message
                    $moderators = $GLOBALS['db']->getAll('user_board_moderator ubm LEFT JOIN ' . DB_PREFIX . 'user_board_banned ubb ON ubm.id_bd = ubb.id_bd ' .
                                                         'AND ubm.id_ur = ubb.id_ur', array('ubm.id_bd' => $board->id, 'ubb.id' => array('IS NULL')), false, array('ubm.*'));
                    
                    if ($moderators) {
                        foreach ($moderators as $moderator) {
                            $GLOBALS['db']->insert('message', 
                                array('id_ur' => $_SESSION['user']->id,
                                      'id_to' => $moderator->id_ur,
                                      'subject' => $subjectSafe,
                                      'content' => $messageSafe,
                                      'content_md' => $message,
                                      'rcvd' => $time)
                            );
                        }
                    }
                }
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Report Topic - Error #1';
    		    
    		    return $this->getResponse();
	        }
	        
		    $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Uploads a badge
     *
     * @param  string $name   the board's name
     * @param  int    $width  the badge's width
     * @param  int    $height the badge's height
     * @param  string $desc   the badge's description
     * @return bool           TRUE on success
     */
	public function uploadBadge($name, $width, $height, $desc) {
	    $name = Util::sanitizeBoard($name);
	    
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('board', array('name' => $name));
	        if ($_SESSION['user']->isModerator != $owns->id) { $owns = false; }
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    }
	    
        if ($owns) {
            $bid = $GLOBALS['db']->insert('board_badge', 
    	        array('id_bd' => $owns->id, 'width' => $width, 'height' => $height, 'desc' => Util::sanitizeTXT($desc))
    	    );
    	    
    	    $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
    	    
    	    if (!file_exists($dir)) { mkdir($dir); }
    	    
    	    $dir = $dir . DIRECTORY_SEPARATOR . 'badges';
    	    
    	    if (!file_exists($dir)) { mkdir($dir); }
    	    
    	    $imgPath = $dir . DIRECTORY_SEPARATOR . $bid . '.svg';
    	    $mResult =  move_uploaded_file($_FILES['files']['tmp_name'][0], $imgPath);
    	    
    	    if ($mResult) {
    	        $mime = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($mime, $imgPath);
                finfo_close($mime);
                
                // make sure it is a valid mime type
                if (in_array($type, array('image/svg+xml', 'text/html'))) {
                    $this->result = true;
                    $this->msg = '';
                } else {
                    unlink($imgPath);
                    
                    $this->result = false;
    	            $this->msg = 'Upload Failed - You have uploaded an unsupported image type.';
                }
    	    } else {
    	        $this->result = false;
    	        $this->msg = 'Upload Failed - Please try again.';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Updates a badge
     *
     * @param  string $name   the board's name
     * @param  string $id     the badge's id
     * @param  int    $width  the badge's width
     * @param  int    $height the badge's height
     * @param  string $desc   the badge's description
     * @return bool           TRUE on success
     */
	public function updateBadge($name, $id, $width, $height, $desc) {
	    $name = Util::sanitizeBoard($name);
	    
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('board', array('name' => $name));
	        if ($_SESSION['user']->isModerator != $owns->id) { $owns = false; }
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    if ($owns) {
	        $badge = $GLOBALS['db']->getOne('board_badge', array('id' => $id, 'id_bd' => $owns->id));
            
            if ($badge) {
        	    try {
            	    $GLOBALS['db']->update('board_badge', 
            	        array('id' => $id, 'width' => $width, 'height' => $height, 'desc' => Util::sanitizeTXT($desc))
            	    );
            	    
            	    $this->result = true;
            	    $this->msg = '';
            	} catch (Exception $e) {
                    $this->result = false;
        		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Badge - Error #1';
        		    
        		    return $this->getResponse();
                }
            } else {
        	    $this->result = false;
    	        $this->msg = 'Update Failed - Please try again.';
        	}
    	} else {
    	    $this->result = false;
    	    $this->msg = '';
    	}
	    
	    return $this->getResponse();
	}
	
	/**
     * Deletes a badge
     *
     * @param  string $name the board's name
     * @param  int    $id   the badge to delete
     * @return bool         TRUE on success
     */
	public function deleteBadge($name, $id) {
	    $name = Util::sanitizeBoard($name);
	    
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('board', array('name' => $name));
	        if ($_SESSION['user']->isModerator != $owns->id) { $owns = false; }
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    }
	    
        if ($owns) {
            $del = $GLOBALS['db']->delete('board_badge', array('id' => $id, 'id_bd' => $owns->id));
            
            if ($del) {
                // TODO: remove all user associations
                
                // delete the category's image
    	        $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
        	    $dir = $dir . DIRECTORY_SEPARATOR . 'badges';
        	    
        	    $imgPath = $dir . DIRECTORY_SEPARATOR . $id . '.svg';
        	    $uResult = unlink($imgPath);
        	    
    	        $this->result = true;
	            $this->msg = '';
    	    } else {
    	        $this->result = false;
    	        $this->msg = 'Delete Failed - Please try again.';
    	    }
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Updates the badge image
     *
     * @param  string $name the board's name
     * @param  int    $id   the badge's id
     * @return bool         TRUE on success
     */
	public function updateBadgeImage($name, $id) {
	    $name = Util::sanitizeBoard($name);
	    
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('board', array('name' => $name));
	        if ($_SESSION['user']->isModerator != $owns->id) { $owns = false; }
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
	    }
	    
        if ($owns) {
            $badge = $GLOBALS['db']->getOne('board_badge', array('id' => $id, 'id_bd' => $owns->id));
            
            if ($badge) {
        	    $dir = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $owns->id;
        	    
        	    if (!file_exists($dir)) { mkdir($dir); }
        	    
        	    $dir = $dir . DIRECTORY_SEPARATOR . 'badges';
        	    
        	    if (!file_exists($dir)) { mkdir($dir); }
        	    
        	    $imgPath = $dir . DIRECTORY_SEPARATOR . $id . '.svg';
        	    $mResult =  move_uploaded_file($_FILES['files']['tmp_name'][0], $imgPath);
        	    
        	    if ($mResult) {
        	        $mime = finfo_open(FILEINFO_MIME_TYPE);
                    $type = finfo_file($mime, $imgPath);
                    finfo_close($mime);
                    
                    // make sure it is a valid mime type
                    if (in_array($type, array('image/svg+xml', 'text/html'))) {
                        $this->result = true;
	                    $this->msg = '';
                    } else {
                        unlink($imgPath);
                        
                        $this->result = false;
        	            $this->msg = 'Upload Failed - You have uploaded an unsupported image type.';
                    }
        	    } else {
        	        $this->result = false;
        	        $this->msg = 'Upload Failed - Please try again.';
        	    }
        	} else {
        	    $this->result = false;
    	        $this->msg = 'Upload Failed - Please try again.';
        	}
        } else {
            $this->result = false;
    	    $this->msg = '';
        }
        
        return $this->getResponse();
	}
	
	/**
     * Awards a badge to a user
     *
     * @param  int    $bid      the board id
     * @param  int    $bad      the badge id
     * @param  string $username the username
     * @return array            an API response
     */
	public function addBadgeUser($bid, $bad, $username) {
	    if ($username == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a username.';
		    
		    return $this->getResponse();
	    }
	    
	    if ($_SESSION['user']->isModerator == $bid) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
	        // make sure the logged in user owns this board
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    $badge = $GLOBALS['db']->getOne('board_badge', array('id' => $bad, 'id_bd' => $bid));
	    $user  = $GLOBALS['db']->getOne('user', array('username' => $username));
	    
	    if ($owns && $badge && $user) {
	        $exists = $GLOBALS['db']->getOne('user_board_badge', array('id_bb' => $bad, 'id_ur' => $user->id));
	        
	        if ($exists) {
	            $this->result = false;
    		    $this->msg = 'The user has already been awarded this badge.';
    		    
    		    return $this->getResponse();
    		} else {
	            $GLOBALS['db']->insert('user_board_badge', array('id_bb' => $bad, 'id_ur' => $user->id, 'awarded' => time()));
    	        
    	        $this->result = true;
    		    $this->msg = $user->id;
    		    
    		    return $this->getResponse();
    		}
		} else {
		    $this->result = false;
		    $this->msg = 'This username does not exist.';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Removes a badge from a user
     *
     * @param  int    $bid the board id
     * @param  int    $bad the badge id
     * @param  string $uid the user id
     * @return array       an API response
     */
	public function remBadgeUser($bid, $bad, $uid) {
	    if ($_SESSION['user']->isModerator == $bid) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
    	}
    	
    	$badge = $GLOBALS['db']->getOne('board_badge', array('id' => $bad, 'id_bd' => $bid));
	    $user  = $GLOBALS['db']->getOne('user', array('id' => $uid));
	    
	    if ($owns && $badge && $user) {
	        $GLOBALS['db']->delete('user_board_badge', array('id_bb' => $bad, 'id_ur' => $uid));
	        
	        $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Gets the users that have been awarded the badge
     *
     * @param  int    $bid the board id
     * @param  int    $bad the badge id
     * @return array       an API response
     */
	public function getBadgeUsers($bid, $bad) {
	    if ($_SESSION['user']->isModerator == $bid) {
	        $owns = $GLOBALS['db']->getOne('board', array('id' => $bid));
	    } else {
    	    // make sure the logged in user owns this board
    	    $owns = $GLOBALS['db']->getOne('board', array('id' => $bid, 'id_ur' => $_SESSION['user']->id));
    	}
    	
    	$badge = $GLOBALS['db']->getOne('board_badge', array('id' => $bad, 'id_bd' => $bid));
	    
	    if ($owns && $badge) {
	        $badgeUsers = $GLOBALS['db']->getAll('user_board_badge ubb INNER JOIN ' . DB_PREFIX . 'user u ON ubb.id_ur = u.id', 
	            array('id_bb' => $bad), 'ubb.awarded DESC', array('ubb.*', 'u.username'));
	        
	        $return = array();
	        
	        foreach ($badgeUsers as $bu) {
	            $return[] = array('userid' => $bu->id_ur, 'username' => $bu->username, 'awarded' => \GrokBB\Util::getTimespan($bu->awarded, 2) . ' ago');
	        }
	        
	        $this->result = true;
	        $this->msg = $return;
	        
	        return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Adds a tag
     *
     * @param  string $name the board name
     * @param  string $tag  the tag name
     * @return array        an API response
     */
	public function addTag($name, $tag) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
    	$owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
		
		if ($owns) {
		    try {
		        $tagSafe = Util::sanitizeTXT($tag);
		        
		        $record = array(
		            'id_bd' => $owns->id,
                    'name'  => $tagSafe
                );
                
		        $tagId = $GLOBALS['db']->insert('board_tag', $record);
		        
		        $this->result = true;
    		    $this->msg = $tagId;
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Create Tag - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Deletes a tag
     *
     * @param  string $name the board name
     * @param  string $tag  the tag name
     * @return array        an API response
     */
	public function delTag($name, $tag) {
	    $name = Util::sanitizeBoard($name);
	    
	    // make sure the logged in user owns this board
    	$owns = $GLOBALS['db']->getOne('board', array('name' => $name, 'id_ur' => $_SESSION['user']->id));
    	
    	if ($owns) {
    	    try {
    	        $tagSafe = Util::sanitizeTXT($tag);
    	        
    	        $where = array(
    	            'id_bd' => $owns->id,
                    'name'  => $tagSafe
                );
                
    	        $GLOBALS['db']->delete('board_tag', $where);
    	        $GLOBALS['db']->delete('topic_tag', $where);
    	        
    	        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
                $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Delete Tag - Error #1';
    		    
    		    return $this->getResponse();
            }
        } else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
}
?>