<?php
namespace GrokBB;

class Reply extends API {
	/**
     * The reply's ID
     * @var int
     */
	public $id;
	
	/**
     * The reply's content in HTML
     * @var string
     */
	public $content = '';
	
	/**
     * The reply's content in Markdown
     * @var string
     */
	public $contentMD = '';
	
	/**
     * The date the reply was created
     * @var int
     */
	public $created;
	
	/**
     * The date the reply was last updated
     * @var int
     */
	public $updated;
	
	/**
     * Selects a reply by its ID (not restricted by the logged in user)
     *
     * @param  string $id the reply's ID
     * @return object     a Reply object
     */
	function __construct($id = 0) {
	    $this->id = $id;
	}
	
	/**
     * Creates a reply
     *
     * @param  int    $tid     the topic id
     * @param  int    $pid     the reply parent id
     * @param  string $content the reply content
     * @param  int    $qid     the reply quoted id
     * @return array           an API response
     */
	public function create($tid, $pid, $content, $qid = false) {
	    if (trim($content) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter some content.';
		    
		    return $this->getResponse();
	    }
	    
		$topic = $GLOBALS['db']->getOne('topic', array('id' => $tid));
		$topicBoard = $GLOBALS['db']->getOne('board', array('id' => $topic->id_bd));
		
		if ($pid > 0) {
            $reply = $GLOBALS['db']->getOne('reply', array('id' => $pid));
            if ($reply->id_tc != $topic->id) { $topic = false; }
        }
        
        $time = time();
        
		if ($topic && $topic->deleted == 0 && \GrokBB\Board::isArchived($topicBoard) == false) {
		    try {
		        list($content, $mentions) = Util::parseMentions($content, true);
		        
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
		        $contentHTML = $gmd->parse($content);
		        $contentSafe = Util::sanitizeGMD($contentHTML);
		        
		        $record = array(
		            'id_tc' => $tid,
                    'id_ry' => $pid,
                    'id_ur' => $_SESSION['user']->id,
                    'content' => $contentSafe,
                    'content_md' => $content,
                    'created' => $time,
                    'created_ipaddress' => ip2long($_SERVER['HTTP_X_FORWARDED_FOR'])
                );
                
		        $rid = $GLOBALS['db']->insert('reply', $record);
		        
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'stats SET counter_replies = counter_replies + 1');
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board SET counter_replies = counter_replies + 1 WHERE id = ' . $topic->id_bd);
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'topic SET counter_replies = counter_replies + 1 WHERE id = ' . (int) $tid);
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'reply SET counter_replies = counter_replies + 1 WHERE id = ' . (int) $pid);
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'user SET counter_replies = counter_replies + 1 WHERE id = ' . $_SESSION['user']->id);
		        
		        $ubStats = $GLOBALS['db']->getOne('user_board_stats', array('id_ur' => $_SESSION['user']->id, 'id_bd' => $topic->id_bd));
		        
		        if ($ubStats) {
		            $GLOBALS['db']->update('user_board_stats', array('id' => $ubStats->id, 'counter_replies' => $ubStats->counter_replies + 1));
		        } else {
		            $GLOBALS['db']->insert('user_board_stats', array('id_ur' => $_SESSION['user']->id, 'id_bd' => $topic->id_bd, 'counter_replies' => 1));
		        }
		        
		        $gmd = new \cebe\markdown\GithubMarkdown();
	            $gmd->enableNewlines = true;
	            
		        $userRep = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
		        $toBoard = SITE_BASE_URL . '/g/' . str_replace(' ', '_', $topicBoard->name);
		        $replied = $toBoard . '/view/' . $topic->id . '#' . (($pid) ? 'open' . $pid : '') . 'reply' . $rid;
	            $message = '[' . $_SESSION['user']->username . '](' . $userRep . ') has replied to you in [' . $topicBoard->name . '](' . $toBoard . ')&nbsp;&raquo;&nbsp;[' . $topic->title . '](' . $replied . ')';
	            
	            $messageHTML = $gmd->parse($message);
		        $messageSafe = Util::sanitizeGMD($messageHTML);
		        $subjectSafe = 'New Reply - ' . $topic->title;
		        
		        $messageToUR = ($pid > 0) ? $reply->id_ur : $topic->id_ur;
		        
		        if ($messageToUR != $_SESSION['user']->id) {
    	            $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $messageToUR,
                              'id_ry' => $rid,
                              'subject' => $subjectSafe,
                              'content' => $messageSafe,
                              'content_md' => $message,
                              'rcvd' => $time)
                    );
		        }
		        
		        if ($pid > 0 && $qid > 0) {
		            $quote = $GLOBALS['db']->getOne('reply', array('id' => $qid));
    		        
    		        if ($quote->id_ur != $reply->id_ur && $quote->id_ur != $_SESSION['user']->id) {
        		        $GLOBALS['db']->insert('message', 
                            array('id_ur' => $_SESSION['user']->id,
                                  'id_to' => $quote->id_ur,
                                  'id_ry' => $rid,
                                  'subject' => $subjectSafe,
                                  'content' => $messageSafe,
                                  'content_md' => $message,
                                  'rcvd' => $time)
                        );
                    }
		        }
		        
		        if ($mentions) {
		            // do not send duplicate notifications out
    		        $mentions = array_diff($mentions, array($_SESSION['user']->id, $messageToUR, ((isset($quote)) ? $quote->id_ur : 0)));
    		        
    		        if ($mentions) {
    		            $message = '[' . $_SESSION['user']->username . '](' . $userRep . ') has mentioned you in [' . $topicBoard->name . '](' . $toBoard . ')&nbsp;&raquo;&nbsp;[' . $topic->title . '](' . $replied . ')';
    		                
		                $messageHTML = $gmd->parse($message);
        		        $messageSafe = Util::sanitizeGMD($messageHTML);
        		        $subjectSafe = 'New Mention - ' . $topic->title;
        		        
    		            foreach ($mentions as $mention) {
    		                $GLOBALS['db']->insert('message', 
                                array('id_ur' => $_SESSION['user']->id,
                                      'id_to' => $mention,
                                      'id_ry' => $rid,
                                      'subject' => $subjectSafe,
                                      'content' => $messageSafe,
                                      'content_md' => $message,
                                      'rcvd' => $time)
                            );
    		            }
    		        }
    		    }
		        
		        $this->result = true;
    		    $this->msg = $rid;
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Create Reply - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Saves a reply for later
     *
     * @param  int $rid the reply id
     * @return array    an API response
     */
	public function saveForLater($rid) {
	    $reply = $GLOBALS['db']->getOne('reply', array('id' => $rid));
		
		if ($reply && $_SESSION['user']->id) {
		    try {
		        $record = array(
    	            'id_ur' => $_SESSION['user']->id,
    	            'id_ry' => $rid,
                    'saved' => time()
                );
                
		        $GLOBALS['db']->insert('user_reply', $record);
		        
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'reply SET counter_saves = counter_saves + 1 WHERE id = ' . (int) $rid);
		        
		        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Save Reply - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Unsaves a reply for later
     *
     * @param  int $rid the reply id
     * @return array    an API response
     */
	public function unsaveForLater($rid) {
	    try {
	        $where = array(
	            'id_ur' => $_SESSION['user']->id,
	            'id_ry' => $rid
            );
            
	        $GLOBALS['db']->delete('user_reply', $where);
	        
	        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'reply SET counter_saves = counter_saves - 1 WHERE id = ' . (int) $rid);
	        
	        $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
        } catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Unsave Reply - Error #1';
		    
		    return $this->getResponse();
        }
	}
	
	/**
     * Updates a reply's content
     *
     * @param  int    $rid     the reply id
     * @param  string $content the reply content
     * @return array           an API response
     */
	public function updateContent($rid, $content) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('reply', array('id' => $rid));
	    } else {
	        $owns = $GLOBALS['db']->getOne('reply', array('id' => $rid, 'id_ur' => $_SESSION['user']->id));
	        if ($owns->deleted > 0 && $owns->deleted_id_ur != $_SESSION['user']->id) { $owns = false; }
	    }
	    
	    if ($owns) {
    		$topic = $GLOBALS['db']->getOne('topic', array('id' => $owns->id_tc));
    		if ($_SESSION['user']->isModerator && $_SESSION['user']->isModerator != $topic->id_bd) { $owns = false; }
    		
    		$topicBoard = $GLOBALS['db']->getOne('board', array('id' => $topic->id_bd));
		}
		
		$time = time();
		
		if ($owns && \GrokBB\Board::isArchived($topicBoard) == false) {
		    try {
		        list($content, $mentions) = Util::parseMentions($content, true);
		        
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
		        $contentHTML = $gmd->parse($content);
		        $contentSafe = Util::sanitizeGMD($contentHTML);
		        
		        $record = array(
		            'id' => $rid,
                    'content' => $contentSafe,
                    'content_md' => $content,
                    'updated' => $time,
                    'updated_ipaddress' => ip2long($_SERVER['HTTP_X_FORWARDED_FOR']),
                    'updated_id_ur' => $_SESSION['user']->id
                );
                
		        $GLOBALS['db']->update('reply', $record);
		        
		        if ($mentions) {
		            $mentions = array_diff($mentions, array($_SESSION['user']->id));
    		        
    		        if ($mentions) {
    		            $userRep = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
        		        $toBoard = SITE_BASE_URL . '/g/' . str_replace(' ', '_', $topicBoard->name);
        		        $replied = $toBoard . '/view/' . $topic->id . '#' . (($owns->id_ry) ? 'open' . $owns->id_ry : '') . 'reply' . $rid;
        		        $message = '[' . $_SESSION['user']->username . '](' . $userRep . ') has mentioned you in [' . $topicBoard->name . '](' . $toBoard . ')&nbsp;&raquo;&nbsp;[' . $topic->title . '](' . $replied . ')';
    		            
		                $messageHTML = $gmd->parse($message);
        		        $messageSafe = Util::sanitizeGMD($messageHTML);
        		        $subjectSafe = 'New Mention - ' . $topic->title;
        		        
    		            foreach ($mentions as $mention) {
    		                $GLOBALS['db']->insert('message', 
                                array('id_ur' => $_SESSION['user']->id,
                                      'id_to' => $mention,
                                      'id_ry' => $rid,
                                      'subject' => $subjectSafe,
                                      'content' => $messageSafe,
                                      'content_md' => $message,
                                      'rcvd' => $time)
                            );
    		            }
    		        }
    		    }
    		    
		        $this->result = true;
		        
		        if ($owns->deleted > 0) {
		            $this->msg = '<div class="uk-alert uk-alert-danger" data-uk-alert>This reply has been deleted.</div>';
		        } else {
    		        $this->msg = $contentSafe;
    		    }
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Reply - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Gets a reply's markdown content
     *
     * @param  int    $rid the reply id
     * @return array       an API response
     */
	public function getContentMD($rid) {
	    $owns = $GLOBALS['db']->getOne('reply', array('id' => $rid));
		
		if ($owns) {
		    try {
		        $this->result = true;
    		    $this->msg = $owns->content_md;
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Get Reply - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Deletes a reply
     *
     * @param  int   $rid the reply id
     * @return array      an API response
     */
	public function delete($rid) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('reply', array('id' => $rid));
	    } else {
	        $owns = $GLOBALS['db']->getOne('reply', array('id' => $rid, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    if ($owns) {
    		$topic = $GLOBALS['db']->getOne('topic', array('id' => $owns->id_tc));
    		if ($_SESSION['user']->isModerator && $_SESSION['user']->isModerator != $topic->id_bd) { $owns = false; }
    		
    		$topicBoard = $GLOBALS['db']->getOne('board', array('id' => $topic->id_bd));
		}
		
		if ($owns && \GrokBB\Board::isArchived($topicBoard) == false) {
		    try {
		        $GLOBALS['db']->update('reply', array('id' => $rid, 'deleted' => time(), 'deleted_id_ur' => $_SESSION['user']->id));
		        
		        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Delete Reply - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Restores a reply
     *
     * @param  int   $rid the reply id
     * @return array      an API response
     */
	public function restore($rid) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('reply', array('id' => $rid));
	    } else {
	        $owns = $GLOBALS['db']->getOne('reply', array('id' => $rid, 'id_ur' => $_SESSION['user']->id, 'deleted_id_ur' => $_SESSION['user']->id));
	    }
	    
	    if ($owns) {
    		$topic = $GLOBALS['db']->getOne('topic', array('id' => $owns->id_tc));
    		if ($_SESSION['user']->isModerator && $_SESSION['user']->isModerator != $topic->id_bd) { $owns = false; }
    		
    		$topicBoard = $GLOBALS['db']->getOne('board', array('id' => $topic->id_bd));
		}
		
		if ($owns && \GrokBB\Board::isArchived($topicBoard) == false) {
		    try {
		        $GLOBALS['db']->update('reply', array('id' => $rid, 'deleted' => 0, 'deleted_id_ur' => 0));
		        
		        $this->result = true;
    		    $this->msg = $owns->content;
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Restore Reply - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Gets the moderation info for a reply
     *
     * @param  int   $rid a reply id
     * @return array      an API response
     */
	public function getModerateInfo($rid) {
	    if (isset($_SESSION['user']) && $_SESSION['user']->isModerator) {
	        $reply = $GLOBALS['db']->getOne('reply', array('id' => $rid));
	        
	        if ($reply) {
	            $owns = $GLOBALS['db']->getOne('board b INNER JOIN ' . DB_PREFIX . 'topic t ON b.id = t.id_bd', array('t.id' => $reply->id_tc), false, array('b.*'));
	            $user = $GLOBALS['db']->getOne('user', array('id' => $reply->id_ur));
	            
	            $reply->username = $user->username;
	            $reply->userid = $user->id;
	            
	            if ($owns->type == 1 || $owns->type == 2) {
    	            $approved  = $GLOBALS['db']->getOne('user_board_approved', array('id_bd' => $owns->id, 'id_ur' => $reply->id_ur));
    	            $moderator = $GLOBALS['db']->getOne('user_board_moderator', array('id_bd' => $owns->id, 'id_ur' => $reply->id_ur));
    	            
    	            if ($approved || $moderator || $user->id == $owns->id_ur) {
        	            $reply->approved = 1;
        	        }
    	        }
    	        
	            if ($banned = $GLOBALS['db']->getOne('user_board_banned', array('id_bd' => $owns->id, 'id_ur' => $reply->id_ur))) {
    	            $reply->banned = $banned->banned;
    	        }
    	        
    	        $reply->points = 0;
    	        
    	        if ($replyPoints = $GLOBALS['db']->getOne('reply_points', array('id_ry' => $reply->id, 'id_ur' => $_SESSION['user']->id))) {
    	            $reply->points = $replyPoints->points;
    	        }
    	        
    	        $reply->duplicateIPs = array('topic' => array(), 'reply' => array());
    	        
    	        if ($reply->created_ipaddress > 0) {
        	        $dupeIPs = $GLOBALS['db']->getAll('topic t INNER JOIN ' . DB_PREFIX . 'user u ON t.id_ur = u.id', array('t.id_bd' => $owns->id, 't.id_ur' => array('<>', $reply->id_ur), 
        	            't.created_ipaddress' => $reply->created_ipaddress), 't.created DESC', array('t.id as topicid', 't.title as topictitle', 'u.id as userid', 'u.username as username'), 5);
        	        
        	        if ($dupeIPs) {
        	            foreach ($dupeIPs as $dupe) {
        	                $reply->duplicateIPs['topic'][] = array(
        	                    'topicid' => $dupe->topicid,
        	                    'topictitle' => $dupe->topictitle,
        	                    'userid' => $dupe->userid,
        	                    'username' => $dupe->username,
        	                );
        	            }
        	        }
        	        
        	        $dupeIPs = $GLOBALS['db']->getAll('reply r INNER JOIN ' . DB_PREFIX . 'topic t ON r.id_tc = t.id INNER JOIN ' . DB_PREFIX . 'user u ON r.id_ur = u.id', array('t.id_bd' => $owns->id, 'r.id_ur' => array('<>', $reply->id_ur), 
        	            'r.created_ipaddress' => $reply->created_ipaddress, 't.id_ur' => array('<> r.id_ur')), 'r.created DESC', array('r.id as replyid', 'r.id_ry as replyopen', 't.id as topicid', 't.title as topictitle', 'u.id as userid', 'u.username as username'), 5);
        	        
        	        if ($dupeIPs) {
        	            foreach ($dupeIPs as $dupe) {
        	                $reply->duplicateIPs['reply'][] = array(
        	                    'replyid' => $dupe->replyid,
        	                    'replyopen' => $dupe->replyopen,
        	                    'topicid' => $dupe->topicid,
        	                    'topictitle' => $dupe->topictitle,
        	                    'userid' => $dupe->userid,
        	                    'username' => $dupe->username,
        	                );
        	            }
        	        }
    	        }
    	        
    	        $this->result = true;
    		    $this->msg = $reply;
    		    
    		    return $this->getResponse();
    		} else {
    		    $this->result = false;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
    		}
	    } else {
	        $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
	    }
	}
	
	/**
     * Adds moderator points for a specific reply
     *
     * @param  int   $rid    the reply id
     * @param  int   $uid    the user id
     * @param  int   $points number of points
     * @return array         an API response
     */
	public function addModeratorPoints($rid, $uid, $points) {
	    if ($_SESSION['user']->isModerator) {
	        $user  = $GLOBALS['db']->getOne('user', array('id' => $uid));
    	    $reply = $GLOBALS['db']->getOne('reply', array('id' => $rid));
    	    $topic = $GLOBALS['db']->getOne('topic', array('id' => $reply->id_tc));
	        
	        if ($user && $reply && $topic && $_SESSION['user']->isModerator == $topic->id_bd && $points <= 3) {
	            $replyPoints = $GLOBALS['db']->getOne('reply_points', array('id_ry' => $reply->id, 'id_ur' => $_SESSION['user']->id));
    	        
    	        if ($replyPoints && $replyPoints->points > 0) {
    	            $GLOBALS['db']->update('reply_points', array('id' => $replyPoints->id, 'points' => 0));
    	            $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'user_board_stats SET counter_points = counter_points - ' . $replyPoints->points . ' WHERE id_ur = ' . $user->id . ' AND id_bd = ' . $topic->id_bd);
    	            $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board SET counter_points = counter_points - ' . $replyPoints->points . ' WHERE id = ' . $topic->id_bd);
    	        }
    	        
    	        if ($replyPoints) {
    	            $GLOBALS['db']->update('reply_points', array('id' => $replyPoints->id, 'points' => $points));
    	        } else {
    	            $GLOBALS['db']->insert('reply_points', array('id_ry' => $reply->id, 'id_ur' => $_SESSION['user']->id, 'points' => $points));
    	        }
    	        
    	        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'user_board_stats SET counter_points = counter_points + ' . $points . ' WHERE id_ur = ' . $user->id . ' AND id_bd = ' . $topic->id_bd);
    	        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board SET counter_points = counter_points + ' . $points . ' WHERE id = ' . $topic->id_bd);
    	        
    	        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
    		} else {
    		    $this->result = false;
    		    $this->msg = '';
    		    
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