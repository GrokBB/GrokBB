<?php
namespace GrokBB;

class Topic extends API {
	/**
     * The topic's ID
     * @var int
     */
	public $id;
	
	/**
     * The topic's title
     * @var string
     */
	public $title = '';
	
	/**
     * The topic's content in HTML
     * @var string
     */
	public $content = '';
	
	/**
     * The topic's content in Markdown
     * @var string
     */
	public $contentMD = '';
	
	/**
     * The date the topic was created
     * @var int
     */
	public $created;
	
	/**
     * The date the topic was last updated
     * @var int
     */
	public $updated;
	
	/**
     * Selects a topic by its ID (not restricted by the logged in user)
     *
     * @param  string $id the topic's ID
     * @return object     a Topic object
     */
	function __construct($id = 0) {
	    $this->id = $id;
	}
	
	/**
     * Creates a topic
     *
     * @param  string $bid     the board id
     * @param  string $cid     the category id
     * @param  string $title   the topic title
     * @param  string $content the topic content
     * @param  string $rid     the reply id
     * @param  int    $sticky  0 = not sticky / 1 = sticky
     * @param  int    $private 0 = not private / 1 = private
     * @param  array  $media   the topic media
     * @return array           an API response
     */
	public function create($bid, $cid, $title, $content, $rid = 0, $sticky = 0, $private = 0, $media = array()) {
	    if (trim($title) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter a title.';
		    
		    return $this->getResponse();
	    }
	    
	    if (trim($content) == '') {
	        $this->result = false;
		    $this->msg = 'You must enter some content.';
		    
		    return $this->getResponse();
	    }
	    
		$board = $GLOBALS['db']->getOne('board', array('id' => $bid));
		$boardCategory = $GLOBALS['db']->getOne('board_category', array('id' => $cid, 'id_bd' => $bid));
		
		$time = time();
		
		if ($board && $boardCategory && \GrokBB\Board::isArchived($board) == false) {
		    try {
		        list($content, $mentions) = Util::parseMentions($content, true);
		        
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
		        $contentHTML = $gmd->parse($content);
		        $contentSafe = Util::sanitizeGMD($contentHTML);
		        $titleSafe = Util::sanitizeTXT($title);
		        
		        $record = array(
		            'id_bd' => $bid,
                    'id_bc' => $cid,
                    'id_ur' => $_SESSION['user']->id,
                    'id_ry' => $rid,
                    'title' => $titleSafe,
                    'content' => $contentSafe,
                    'content_md' => $content,
                    'created' => $time,
                    'created_ipaddress' => ((isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? ip2long($_SERVER['HTTP_X_FORWARDED_FOR']) : 0)
                );
                
                if ($_SESSION['user']->isModerator == $bid) {
                    $record['sticky']  = (int) $sticky;
                    $record['private'] = (int) $private;
                }
                
		        $tid = $GLOBALS['db']->insert('topic', $record);
		        
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'stats SET counter_topics = counter_topics + 1');
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board SET counter_topics = counter_topics + 1 WHERE id = ' . (int) $bid);
		        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'user SET counter_topics = counter_topics + 1 WHERE id = ' . $_SESSION['user']->id);
		        
		        $ubStats = $GLOBALS['db']->getOne('user_board_stats', array('id_ur' => $_SESSION['user']->id, 'id_bd' => $bid));
		        
		        if ($ubStats) {
		            $GLOBALS['db']->update('user_board_stats', array('id' => $ubStats->id, 'counter_topics' => $ubStats->counter_topics + 1));
		        } else {
		            $GLOBALS['db']->insert('user_board_stats', array('id_ur' => $_SESSION['user']->id, 'id_bd' => $bid, 'counter_topics' => 1));
		        }
		        
		        if ($tid && $media) {
		            foreach ($media as $data) {
		                $record = array('id_tc' => $tid, 'url' => str_replace('http://', 'https://', $data['url']));
		                
		                if (trim($data['txt']) != '') {
		                    $record['txt'] = Util::sanitizeTXT($data['txt']);
		                }
		                
		                $GLOBALS['db']->insert('topic_media', $record);
		            }
		        }
		        
		        if ($rid > 0) {
		            $reply = $GLOBALS['db']->getOne('reply', array('id' => $rid));
		        
    		        $gmd = new \cebe\markdown\GithubMarkdown();
    	            $gmd->enableNewlines = true;
    	            
    		        $userRep = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
    		        $toBoard = SITE_BASE_URL . '/g/' . str_replace(' ', '_', $board->name);
    		        $replied = $toBoard . '/view/' . $tid;
    	            $message = '[' . $_SESSION['user']->username . '](' . $userRep . ') has created a new topic to reply to you in [' . $board->name . '](' . $toBoard . ')&nbsp;&raquo;&nbsp;[' . $titleSafe . '](' . $replied . ')';
    	            
    	            $messageHTML = $gmd->parse($message);
    		        $messageSafe = Util::sanitizeGMD($messageHTML);
    		        $subjectSafe = 'New Reply - ' . $titleSafe;
    		        
    	            $GLOBALS['db']->insert('message', 
                        array('id_ur' => $_SESSION['user']->id,
                              'id_to' => $reply->id_ur,
                              'id_tc' => $tid,
                              'subject' => $subjectSafe,
                              'content' => $messageSafe,
                              'content_md' => $message,
                              'rcvd' => $time)
                    );
		        }
		        
		        if ($mentions) {
		            // do not send duplicate notifications out
    		        $mentions = array_diff($mentions, array($_SESSION['user']->id, ((isset($reply)) ? $reply->id_ur : 0)));
    		        
    		        if ($mentions) {
    		            $userRep = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
        		        $toBoard = SITE_BASE_URL . '/g/' . str_replace(' ', '_', $board->name);
        		        $replied = $toBoard . '/view/' . $tid;
    		            $message = '[' . $_SESSION['user']->username . '](' . $userRep . ') has mentioned you in [' . $board->name . '](' . $toBoard . ')&nbsp;&raquo;&nbsp;[' . $titleSafe . '](' . $replied . ')';
    		                
		                $messageHTML = $gmd->parse($message);
        		        $messageSafe = Util::sanitizeGMD($messageHTML);
        		        $subjectSafe = 'New Mention - ' . $titleSafe;
        		        
    		            foreach ($mentions as $mention) {
    		                $GLOBALS['db']->insert('message', 
                                array('id_ur' => $_SESSION['user']->id,
                                      'id_to' => $mention,
                                      'id_tc' => $tid,
                                      'subject' => $subjectSafe,
                                      'content' => $messageSafe,
                                      'content_md' => $message,
                                      'rcvd' => $time)
                            );
    		            }
    		        }
    		    }
    		    
		        $this->result = true;
    		    $this->msg = $tid;
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Create Topic - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Saves a topic for later
     *
     * @param  int $tid the topic id
     * @return array    an API response
     */
	public function saveForLater($tid) {
	    $topic = $GLOBALS['db']->getOne('topic', array('id' => $tid));
		
		if ($topic && $_SESSION['user']->id) {
		    try {
		        $record = array(
    	            'id_ur' => $_SESSION['user']->id,
    	            'id_tc' => $tid,
                    'saved' => time()
                );
                
		        $GLOBALS['db']->insert('user_topic', $record);
		        
		        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Save Topic - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Unsaves a topic for later
     *
     * @param  int $tid the topic id
     * @return array    an API response
     */
	public function unsaveForLater($tid) {
	    try {
	        $where = array(
	            'id_ur' => $_SESSION['user']->id,
	            'id_tc' => $tid
            );
            
	        $GLOBALS['db']->delete('user_topic', $where);
	        
	        $this->result = true;
		    $this->msg = '';
		    
		    return $this->getResponse();
        } catch (Exception $e) {
            $this->result = false;
		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Unsave Topic - Error #1';
		    
		    return $this->getResponse();
        }
	}
	
	/**
     * Adds a tag
     *
     * @param  string $tid  the topic id
     * @param  string $name the tag name
     * @return array        an API response
     */
	public function addTag($tid, $name) {
	    $topic = $GLOBALS['db']->getOne('topic', array('id' => $tid));
		
		if ($topic && $_SESSION['user']->id) {
		    try {
		        $nameSafe = Util::sanitizeTXT($name);
		        
		        $record = array(
		            'id_tc' => $tid,
                    'name'  => $nameSafe
                );
                
                if ($_SESSION['user']->isModerator) {
                    $boardTag = $GLOBALS['db']->getOne('board_tag', array('id_bd' => $_SESSION['board']->id, 'name' => $nameSafe));
                    
                    if ($boardTag) {
                        $record['id_bd'] = $_SESSION['board']->id;
                    } else {
                        $record['id_ur'] = $_SESSION['user']->id;
                    }
                } else {
                    $record['id_ur'] = $_SESSION['user']->id;
                }
                
		        $tagId = $GLOBALS['db']->insert('topic_tag', $record);
		        
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
     * @param  string $tid  the topic id
     * @param  string $name the tag name
     * @return array        an API response
     */
	public function delTag($tid, $name) {
	    $nameSafe = Util::sanitizeTXT($name);
	    
	    // make sure the user owns this tag, otherwise it is a board tag
	    $owns = $GLOBALS['db']->getOne('topic_tag', array('id_tc' => $tid, 'id_ur' => $_SESSION['user']->id, 'name' => $nameSafe));
	    
	    if ($owns || $_SESSION['user']->isModerator) {
    	    try {
    	        $where = array(
    	            'id_tc' => $tid,
                    'name'  => $nameSafe
                );
                
                if ($owns) {
                    $where['id_ur'] = $_SESSION['user']->id;
                } else {
                    $where['id_bd'] = $_SESSION['board']->id;
                }
                
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
	
	/**
     * Updates a topic's title
     *
     * @param  int    $tid   the topic id
     * @param  string $title the topic title
     * @return array         an API response
     */
	public function updateTitle($tid, $title) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid));
	        if ($_SESSION['user']->isModerator != $owns->id_bd) { $owns = false; }
	    } else {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    $topicBoard = $GLOBALS['db']->getOne('board', array('id' => $owns->id_bd));
		
		if ($owns && \GrokBB\Board::isArchived($topicBoard) == false && $owns->locked == false) {
		    try {
		        $titleSafe = Util::sanitizeTXT($title);
		        
		        $GLOBALS['db']->update('topic', array('id' => $tid, 'title' => $titleSafe));
		        
		        $this->result = true;
    		    $this->msg = $titleSafe;
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Topic Title - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Updates a topic's category
     *
     * @param  int    $tid the topic id
     * @param  string $cid the category id
     * @return array       an API response
     */
	public function updateCategory($tid, $cid) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid));
	        if ($_SESSION['user']->isModerator != $owns->id_bd) { $owns = false; }
	    } else {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid, 'id_ur' => $_SESSION['user']->id));
	    }
	    
		$topicCategory = $GLOBALS['db']->getOne('board_category', array('id' => $cid, 'id_bd' => $owns->id_bd));
		$topicBoard = $GLOBALS['db']->getOne('board', array('id' => $owns->id_bd));
		
		if ($owns && $topicCategory && \GrokBB\Board::isArchived($topicBoard) == false && $owns->locked == false) {
		    try {
		        $GLOBALS['db']->update('topic', array('id' => $tid, 'id_bc' => $cid));
		        
		        $this->result = true;
    		    $this->msg = $cid;
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Topic Category - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Updates a topic's content
     *
     * @param  int    $tid     the topic id
     * @param  string $content the topic content
     * @param  array  $media   the topic media
     * @return array           an API response
     */
	public function updateContent($tid, $content, $media = array()) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid));
	        if ($_SESSION['user']->isModerator != $owns->id_bd) { $owns = false; }
	    } else {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid, 'id_ur' => $_SESSION['user']->id));
	        if ($owns->deleted > 0 && $owns->deleted_id_ur != $_SESSION['user']->id) { $owns = false; }
	    }
	    
	    if ($owns) {
		    $topicBoard = $GLOBALS['db']->getOne('board', array('id' => $owns->id_bd));
		}
		
		$time = time();
		
		if ($owns && \GrokBB\Board::isArchived($topicBoard) == false && $owns->locked == false) {
		    try {
		        list($content, $mentions) = Util::parseMentions($content, true);
		        
		        $gmd = new \cebe\markdown\GithubMarkdown();
		        $gmd->enableNewlines = true;
		        
	            $contentHTML = $gmd->parse($content);
		        $contentSafe = Util::sanitizeGMD($contentHTML);
		        
		        $record = array(
		            'id' => $tid,
                    'content' => $contentSafe,
                    'content_md' => $content,
                    'updated' => $time,
                    'updated_ipaddress' => ((isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? ip2long($_SERVER['HTTP_X_FORWARDED_FOR']) : 0),
                    'updated_id_ur' => $_SESSION['user']->id
                );
                
		        $GLOBALS['db']->update('topic', $record);
		        
		        if ($media) {
		            $GLOBALS['db']->delete('topic_media', array('id_tc' => $tid));
		            
		            foreach ($media as $data) {
		                $record = array('id_tc' => $tid, 'url' => str_replace('http://', 'https://', $data['url']));
		                
		                if (trim($data['txt']) != '') {
		                    $record['txt'] = Util::sanitizeTXT($data['txt']);
		                }
		                
		                $GLOBALS['db']->insert('topic_media', $record);
		            }
		        }
		        
		        if ($mentions) {
		            $mentions = array_diff($mentions, array($_SESSION['user']->id));
    		        
    		        if ($mentions) {
    		            $userRep = SITE_BASE_URL . '/user/view/' . $_SESSION['user']->id;
        		        $toBoard = SITE_BASE_URL . '/g/' . str_replace(' ', '_', $topicBoard->name);
        		        $replied = $toBoard . '/view/' . $tid;
        		        $message = '[' . $_SESSION['user']->username . '](' . $userRep . ') has mentioned you in [' . $topicBoard->name . '](' . $toBoard . ')&nbsp;&raquo;&nbsp;[' . $owns->title . '](' . $replied . ')';
    		            
		                $messageHTML = $gmd->parse($message);
        		        $messageSafe = Util::sanitizeGMD($messageHTML);
        		        $subjectSafe = 'New Mention - ' . $owns->title;
        		        
    		            foreach ($mentions as $mention) {
    		                $GLOBALS['db']->insert('message', 
                                array('id_ur' => $_SESSION['user']->id,
                                      'id_to' => $mention,
                                      'id_tc' => $tid,
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
		            $this->msg = 'This topic has been deleted.';
		        } else {
    		        $this->msg = $contentSafe;
    		    }
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Update Topic Content - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Deletes a topic
     *
     * @param  int   $tid the topic id
     * @return array      an API response
     */
	public function delete($tid) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid));
	        if ($_SESSION['user']->isModerator != $owns->id_bd) { $owns = false; }
	    } else {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid, 'id_ur' => $_SESSION['user']->id));
	    }
	    
	    if ($owns) {
		    $topicBoard = $GLOBALS['db']->getOne('board', array('id' => $owns->id_bd));
		}
		
		if ($owns && \GrokBB\Board::isArchived($topicBoard) == false && $owns->locked == false) {
		    try {
		        $GLOBALS['db']->update('topic', array('id' => $tid, 'deleted' => time(), 'deleted_id_ur' => $_SESSION['user']->id));
		        
		        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Delete Topic - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Restores a topic
     *
     * @param  int   $tid the topic id
     * @return array      an API response
     */
	public function restore($tid) {
	    if ($_SESSION['user']->isModerator) {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid));
	        if ($_SESSION['user']->isModerator != $owns->id_bd) { $owns = false; }
	    } else {
	        $owns = $GLOBALS['db']->getOne('topic', array('id' => $tid, 'id_ur' => $_SESSION['user']->id, 'deleted_id_ur' => $_SESSION['user']->id));
	    }
	    
	    if ($owns) {
		    $topicBoard = $GLOBALS['db']->getOne('board', array('id' => $owns->id_bd));
		}
		
		if ($owns && \GrokBB\Board::isArchived($topicBoard) == false && $owns->locked == false) {
		    try {
		        $GLOBALS['db']->update('topic', array('id' => $tid, 'deleted' => 0, 'deleted_id_ur' => 0));
		        
		        $this->result = true;
    		    $this->msg = '';
    		    
    		    return $this->getResponse();
            } catch (Exception $e) {
	            $this->result = false;
    		    $this->msg = (DEBUG) ? $e->getMessage() : 'Could Not Restore Topic - Error #1';
    		    
    		    return $this->getResponse();
	        }
		} else {
		    $this->result = false;
		    $this->msg = '';
		    
		    return $this->getResponse();
		}
	}
	
	/**
     * Records a topic view
     *
     * @param  int  $tid the topic id
     * @return bool      TRUE on success
     */
	public static function view($tid) {
	    $sessID = session_id();
	    $exists = $GLOBALS['db']->getOne('topic_view', array('id_tc' => $tid, 'id_sn' => $sessID));
		
		// only count 1 unique view per session
		if ($exists == false) {
	        $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'topic SET counter_views = counter_views + 1 WHERE id = ' . (int) $tid);
	    }
	    
	    // but track ALL views for New Replies
	    $insert = array('id_tc' => $tid, 'id_sn' => $sessID, 'viewed' => time());
        
        if (isset($_SESSION['user'])) {
            $insert['id_ur'] = $_SESSION['user']->id;
        }
        
        $GLOBALS['db']->insert('topic_view', $insert);
        
        return true;
	}
	
	/**
     * Gets the New Replies since a user last visited
     *
     * @param  int or array $tid a topic id or an array of topic ids
     * @return int or array      # of new replies for each topic
     */
	public static function newReplies($tid) {
	    if (is_array($tid)) {
	        $where = array('IN', $tid);
	    } else {
	        $where = $tid;
	    }
	    
	    if ($_SESSION['user']) {
	        $lastVisits = $GLOBALS['db']->getAll('topic_view', array('id_tc' => $where, 'id_ur' => $_SESSION['user']->id), false, array('*', 'MAX(viewed) as viewed'), false, false, 'id_tc');
		} else {
		    $lastVisits = $GLOBALS['db']->getAll('topic_view', array('id_tc' => $where, 'id_sn' => session_id()), false, array('*', 'MAX(viewed) as viewed'), false, false, 'id_tc');
		}
		
		if (is_array($tid)) {
		    $counters = array();
		    
		    if ($lastVisits) {
    		    $sql = 'SELECT id_tc, COUNT(*) as counter FROM ' . DB_PREFIX . 'reply WHERE ';
    		    
    		    foreach ($lastVisits as $lastVisit) {
    		        $counters[$lastVisit->id_tc] = 0;
    		        $sql .= '(id_tc = ' . $lastVisit->id_tc . ' AND created > ' . $lastVisit->viewed . ') OR ';
    		    }
    		    
    		    $sql = substr($sql, 0, -4) . ' GROUP BY id_tc';
    		    
    		    $newReplies = $GLOBALS['db']->custom($sql);
    		    
    		    if ($newReplies) {
        		    foreach ($newReplies as $nr) {
        		        $counters[$nr->id_tc] = (int) $nr->counter;
        		    }
		        }
		    }
		    
		    foreach ($tid as $t) {
		        if (!isset($counters[$t])) {
		            $counters[$t] = -1;
		        }
		    }
		    
		    return $counters;
		} else {
    		if ($lastVisits) {
    		    $retRepliesCount = 0;
    		    $retReplies = array();
    		    
    		    $newReplies = $GLOBALS['db']->getAll('reply', array('id_tc' => $tid, 'created' => array('>', $lastVisits[0]->viewed)));
    		    
    		    if ($newReplies) {
    		        foreach ($newReplies as $reply) {
    		            $retReplies[$reply->id_ry]++;
    		            $retRepliesCount++;
    		        }
    		    }
    		    
    		    return array($retRepliesCount, $retReplies);
    		} else {
    		    return -1;
    		}
	    }
	}
	
	/**
     * Gets the moderation info for a topic
     *
     * @param  int   $tid a topic id
     * @return array      an API response
     */
	public function getModerateInfo($tid) {
	    if (isset($_SESSION['user']) && $_SESSION['user']->isModerator) {
	        $topic = $GLOBALS['db']->getOne('topic', array('id' => $tid));
	        
	        if ($topic) {
	            $owns = $GLOBALS['db']->getOne('board', array('id' => $topic->id_bd));
	            $user = $GLOBALS['db']->getOne('user', array('id' => $topic->id_ur));
	            
	            $topic->username = $user->username;
	            $topic->userid = $user->id;
	            
	            if ($owns->type == 1 || $owns->type == 2) {
    	            $approved  = $GLOBALS['db']->getOne('user_board_approved', array('id_bd' => $topic->id_bd, 'id_ur' => $topic->id_ur));
    	            $moderator = $GLOBALS['db']->getOne('user_board_moderator', array('id_bd' => $topic->id_bd, 'id_ur' => $topic->id_ur));
    	            
    	            if ($approved || $moderator || $user->id == $owns->id_ur) {
        	            $topic->approved = 1;
        	        }
    	        }
    	        
	            if ($banned = $GLOBALS['db']->getOne('user_board_banned', array('id_bd' => $topic->id_bd, 'id_ur' => $topic->id_ur))) {
    	            $topic->banned = $banned->banned;
    	        }
    	        
    	        $topic->points = 0;
    	        
    	        if ($topicPoints = $GLOBALS['db']->getOne('topic_points', array('id_tc' => $topic->id, 'id_ur' => $_SESSION['user']->id))) {
    	            $topic->points = $topicPoints->points;
    	        }
    	        
    	        $topic->duplicateIPs = array('topic' => array(), 'reply' => array());
    	        
    	        if ($topic->created_ipaddress > 0) {
        	        $dupeIPs = $GLOBALS['db']->getAll('topic t INNER JOIN ' . DB_PREFIX . 'user u ON t.id_ur = u.id', array('t.id_bd' => $topic->id_bd, 't.id_ur' => array('<>', $topic->id_ur), 
        	            't.created_ipaddress' => $topic->created_ipaddress), 't.created DESC', array('t.id as topicid', 't.title as topictitle', 'u.id as userid', 'u.username as username'), 5);
        	        
        	        if ($dupeIPs) {
        	            foreach ($dupeIPs as $dupe) {
        	                $topic->duplicateIPs['topic'][] = array(
        	                    'topicid' => $dupe->topicid,
        	                    'topictitle' => $dupe->topictitle,
        	                    'userid' => $dupe->userid,
        	                    'username' => $dupe->username,
        	                );
        	            }
        	        }
        	        
        	        $dupeIPs = $GLOBALS['db']->getAll('reply r INNER JOIN ' . DB_PREFIX . 'topic t ON r.id_tc = t.id INNER JOIN ' . DB_PREFIX . 'user u ON r.id_ur = u.id', array('t.id_bd' => $topic->id_bd, 'r.id_ur' => array('<>', $topic->id_ur), 
        	            'r.created_ipaddress' => $topic->created_ipaddress, 't.id_ur' => array('<> r.id_ur')), 'r.created DESC', array('r.id as replyid', 'r.id_ry as replyopen', 't.id as topicid', 't.title as topictitle', 'u.id as userid', 'u.username as username'), 5);
        	        
        	        if ($dupeIPs) {
        	            foreach ($dupeIPs as $dupe) {
        	                $topic->duplicateIPs['reply'][] = array(
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
    		    $this->msg = $topic;
    		    
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
     * Makes a topic sticky or not
     *
     * @param  int   $tid    a topic id
     * @param  int   $sticky 0 = not sticky / 1 = sticky
     * @return array         an API response
     */
	public function setSticky($tid, $sticky) {
	    if (isset($_SESSION['user']) && $_SESSION['user']->isModerator) {
	        $GLOBALS['db']->update('topic', array('id' => $tid, 'sticky' => ($sticky) ? time() : 0));
	        
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
     * Makes a topic private or not
     *
     * @param  int   $tid     a topic id
     * @param  int   $private 0 = not private / 1 = private
     * @return array          an API response
     */
	public function setPrivate($tid, $private) {
	    if (isset($_SESSION['user']) && $_SESSION['user']->isModerator) {
	        $GLOBALS['db']->update('topic', array('id' => $tid, 'private' => $private));
	        
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
     * Makes a topic locked or not
     *
     * @param  int   $tid    a topic id
     * @param  int   $locked 0 = not locked / 1 = locked
     * @return array         an API response
     */
	public function setLocked($tid, $locked) {
	    if (isset($_SESSION['user']) && $_SESSION['user']->isModerator) {
	        $GLOBALS['db']->update('topic', array('id' => $tid, 'locked' => ($locked) ? time() : 0));
	        
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
     * Updates the moderator notes
     *
     * @param  int    $tid   a topic id
     * @param  string $notes the notes
     * @return array         an API response
     */
	public function setNotes($tid, $notes) {
	    if (isset($_SESSION['user']) && $_SESSION['user']->isModerator) {
	        $notesSafe = Util::sanitizeTXT($notes);
	        
	        $GLOBALS['db']->update('topic', array('id' => $tid, 'notes' => $notesSafe));
	        
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
     * Gets the topic's title
     *
     * @param  int   $tid a topic id
     * @return array      an API response
     */
	public function getTitle($tid) {
	    $topic = $GLOBALS['db']->getOne('topic', array('id' => $tid), false, array('title'));
        
        $this->result = true;
	    $this->msg = $topic->title;
	    
	    return $this->getResponse();
	}
	
	/**
     * Adds moderator points for a specific topic
     *
     * @param  int   $tid    the topic id
     * @param  int   $uid    the user id
     * @param  int   $points number of points
     * @return array         an API response
     */
	public function addModeratorPoints($tid, $uid, $points) {
	    if ($_SESSION['user']->isModerator) {
	        $user  = $GLOBALS['db']->getOne('user', array('id' => $uid));
    	    $topic = $GLOBALS['db']->getOne('topic', array('id' => $tid));
	        
	        if ($user && $topic && $_SESSION['user']->isModerator == $topic->id_bd && $points <= 3) {
	            $topicPoints = $GLOBALS['db']->getOne('topic_points', array('id_tc' => $topic->id, 'id_ur' => $_SESSION['user']->id));
    	        
    	        if ($topicPoints && $topicPoints->points > 0) {
    	            $GLOBALS['db']->update('topic_points', array('id' => $topicPoints->id, 'points' => 0));
    	            $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'user_board_stats SET counter_points = counter_points - ' . $topicPoints->points . ' WHERE id_ur = ' . $user->id . ' AND id_bd = ' . $topic->id_bd);
    	            $GLOBALS['db']->custom('UPDATE ' . DB_PREFIX . 'board SET counter_points = counter_points - ' . $topicPoints->points . ' WHERE id = ' . $topic->id_bd);
    	        }
    	        
    	        if ($topicPoints) {
    	            $GLOBALS['db']->update('topic_points', array('id' => $topicPoints->id, 'points' => $points));
    	        } else {
    	            $GLOBALS['db']->insert('topic_points', array('id_tc' => $topic->id, 'id_ur' => $_SESSION['user']->id, 'points' => $points));
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