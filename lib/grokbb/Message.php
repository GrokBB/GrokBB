<?php
namespace GrokBB;

class Message extends API {
	/**
     * The message's ID
     * @var int
     */
	public $id;
	
	/**
     * The message's recipient ID
     * @var int
     */
	public $id_to;
	
	/**
     * The message's recipient username
     * @var string
     */
	public $username;
	
	/**
     * The message's subject
     * @var string
     */
	public $subject = '';
	
	/**
     * The message's content in HTML
     * @var string
     */
	public $content = '';
	
	/**
     * The message's content in Markdown
     * @var string
     */
	public $contentMD = '';
	
	/**
     * The date the message was last updated (for drafts only)
     * @var int
     */
	public $updated;
	
	/**
     * The date the message was sent to the recipient
     * @var int
     */
	public $sent;
	
	/**
     * The date the message was read by the recipient
     * @var int
     */
	public $read;
	
	/**
     * Selects a message by its ID (not restricted by the logged in user)
     *
     * @param  string $id the message's ID
     * @return object     a Message object
     */
	function __construct($id = 0) {
	    $this->id = $id;
	}
	
	/**
     * Selects a message by its ID
     *
     * @param  string $id the message's ID
     * @return array      an API response
     */
	public function getById($id) {
		$message = $GLOBALS['db']->getOne('message m INNER JOIN ' . DB_PREFIX . 'user u ON m.id_to = u.id', 
            array('m.id' => $id, 'm.id_ur' => $_SESSION['user']->id, 'm.deleted' => 0), false, array('m.*', 'u.username'));
		
		if ($message) {
		    $this->id = $message->id;
		    $this->id_to = $message->id_to;
		    $this->username = $message->username;
		    $this->subject = $message->subject;
		    $this->content = $message->content;
		    $this->contentMD = $message->content_md;
		    $this->updated = $message->updated;
		    $this->sent = $message->sent;
		    $this->read = $message->read;
		    
		    $this->result = true;
    	    $this->msg = $this;
    	    
    	    return $this->getResponse();
		}
		
		$this->result = false;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Selects a message by its ID (for inbox messages only)
     *
     * @param  string $id the message's ID
     * @return array      an API response
     */
	public function getReply($id) {
		$message = $GLOBALS['db']->getOne('message m INNER JOIN ' . DB_PREFIX . 'user u ON m.id_ur = u.id', 
            array('m.id' => $id, 'm.id_to' => $_SESSION['user']->id, 'm.deleted' => 0, 'm.sent' => 0, 'm.rcvd' => array('>', 0)), false, array('m.*', 'u.username'));
		
		if ($message) {
		    // mark the message as read
		    $this->read($id);
		    
		    $this->id = $message->id;
		    $this->id_ur = $message->id_ur;
		    $this->username = $message->username;
		    $this->subject = $message->subject;
		    $this->content = $message->content;
		    
		    if ($message->id_ry > 0) {
		        $reply = $GLOBALS['db']->getOne('reply', array('id' => $message->id_ry));
		        
		        if ($reply) {
		            $this->content .= '<div class="uk-alert uk-alert-info uk-margin-bottom-remove" data-uk-alert>' . $reply->content . '</div>';
		        }
		    } else if ($message->id_tc > 0) {
		        $topic = $GLOBALS['db']->getOne('topic', array('id' => $message->id_tc));
		        
		        if ($topic) {
		            $this->content .= '<div class="uk-alert uk-alert-info uk-margin-bottom-remove" data-uk-alert>' . $topic->content . '</div>';
		        }
		    }
		    
		    $this->contentMD = $message->content_md;
		    $this->rcvd = $message->rcvd;
		    
		    $this->result = true;
    	    $this->msg = $this;
    	    
    	    return $this->getResponse();
		}
		
		$this->result = false;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Sends a message to a user
     *
     * @param  string $username the user to send to
     * @param  string $subject  the message subject
     * @param  string $content  the message content
     * @param  int    $id       the message id (for drafts only)
     * @return array            an API response
     */
	public function send($username, $subject, $content, $id = 0) {
	    if (trim($username) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a username.';
		    
		    return $this->getResponse();
	    }
	    
	    if (trim($subject) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a subject.';
		    
		    return $this->getResponse();
	    }
	    
	    if (trim($content) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a message.';
		    
		    return $this->getResponse();
	    }
	    
		$user = $GLOBALS['db']->getOne('user', array('username' => $username));
		$time = time();
		
		if ($user) {
		    try {
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $contentHTML = $gmd->parse($content);
	            
		        $contentSafe = Util::sanitizeGMD($contentHTML);
		        $subjectSafe = Util::sanitizeTXT($subject);
		        
		        $record = array(
		            'id_ur' => $_SESSION['user']->id,
                    'id_to' => $user->id,
                    'subject' => $subjectSafe,
                    'content' => $contentSafe,
                    'content_md' => $content,
                    'sent' => $time
                );
                
		        if ($id > 0) {
		            // make sure the logged in user owns this message
		            if ($GLOBALS['db']->getOne('message', array('id' => $id, 'id_ur' => $_SESSION['user']->id))) {
		                $record['id'] = $id;
		                $mid = $GLOBALS['db']->update('message', $record);
		            }
		        } else {
    		        $mid = $GLOBALS['db']->insert('message', $record);
                }
                
		        if ($mid) {
        		    $iid = $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $user->id,
                              'subject' => $subjectSafe,
                              'content' => $contentSafe,
                              'content_md' => $content,
                              'rcvd' => $time)
                    );
                }
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Send Message - Error #1';
    		    
    		    return $this->getResponse();
	        }
	        
		    $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = 'This username does not exist.';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Saves a message draft
     *
     * @param  string $username the user to send to
     * @param  string $subject  the message subject
     * @param  string $content  the message content
     * @param  int    $id       the message id (for drafts only)
     * @return array            an API response
     */
	public function save($username, $subject, $content, $id = 0) {
	    if (trim($username) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a username.';
		    
		    return $this->getResponse();
	    }
	    
	    if (trim($subject) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a subject.';
		    
		    return $this->getResponse();
	    }
	    
	    $user = $GLOBALS['db']->getOne('user', array('username' => $username));
		$time = time();
		
		if ($user) {
		    try {
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $contentHTML = $gmd->parse($content);
	            
		        $contentSafe = Util::sanitizeGMD($contentHTML);
		        $subjectSafe = Util::sanitizeTXT($subject);
		        
		        $record = array(
		            'id_ur' => $_SESSION['user']->id,
                    'id_to' => $user->id,
                    'subject' => $subjectSafe,
                    'content' => $contentSafe,
                    'content_md' => $content,
                    'updated' => $time
                );
                
		        if ($id > 0) {
		            // make sure the logged in user owns this message
		            if ($GLOBALS['db']->getOne('message', array('id' => $id, 'id_ur' => $_SESSION['user']->id))) {
    		            $record['id'] = $id;
    		            $mid = $GLOBALS['db']->update('message', $record);
    		        }
		        } else {
    		        $mid = $GLOBALS['db']->insert('message', $record);
                }
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Save Draft - Error #1';
    		    
    		    return $this->getResponse();
	        }
	        
		    $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
		} else {
		    $this->result = false;
		    $this->msg = 'This username does not exist.';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Marks a message as read
     *
     * @param  string $id the message's ID
     * @return array      an API response
     */
	public function read($id) {
	    // make sure the logged in user owns this message and that it hasn't been marked yet
	    $owns = $GLOBALS['db']->getOne('message', array('id' => $id, 'id_to' => $_SESSION['user']->id, 'sent' => 0, 'rcvd' => array('>', 0), 'read' => 0));
		
		if ($owns) {
		    $time = time();
		    $read = $GLOBALS['db']->update('message', array('id' => $id, 'read' => $time));
		    
		    if ($read) {
		        $sent = $GLOBALS['db']->getOne('message', array('id_ur' => $owns->id_ur, 'id_to' => $owns->id_to, 'rcvd' => 0, 'sent' => $owns->rcvd, 'read' => 0));
                
                if ($sent) {
                    $GLOBALS['db']->update('message', array('id' => $sent->id, 'read' => $time));
                }
                
    		    $this->result = true;
        	    $this->msg = '';
        	    
        	    return $this->getResponse();
        	}
		}
		
		$this->result = false;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Deletes a message by its ID
     *
     * @param  string $id the message's ID
     * @return array      an API response
     */
	public function delete($id) {
		$deleted = $GLOBALS['db']->delete('message', array('id' => $id, 'id_ur' => $_SESSION['user']->id));
		
		if ($deleted) {
		    $this->result = true;
    	    $this->msg = '';
    	    
    	    return $this->getResponse();
		}
		
		$this->result = false;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
	
	/**
     * Deletes a message by its ID (for inbox messages only)
     *
     * @param  string $id the message's ID
     * @return array      an API response
     */
	public function deleteRCVD($id) {
		// make sure the logged in user owns this message and that it hasn't been deleted yet
	    $owns = $GLOBALS['db']->getOne('message', array('id' => $id, 'id_to' => $_SESSION['user']->id, 'sent' => 0, 'rcvd' => array('>', 0), 'deleted' => 0));
		
		if ($owns) {
		    $deleted = $GLOBALS['db']->update('message', array('id' => $id, 'deleted' => time()));
		    
		    if ($deleted) {
		        $this->result = true;
        	    $this->msg = '';
        	    
        	    return $this->getResponse();
        	}
		}
		
		$this->result = false;
	    $this->msg = '';
	    
	    return $this->getResponse();
	}
}
?>