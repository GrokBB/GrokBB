<?php
namespace GrokBB;

class API {
    /**
     * The API result
     * @var bool
     */
	protected $result = false;
	
	/**
     * The API message
     * @var string
     */
	
	protected $msg = 'No Message';
	
	/**
     * Returns the API response
     *
     * @return array a JSON encoded API response
     */
	public function getResponse($result = null, $msg = null) {
	    $response = array('result' => (($result === null) ? $this->result : $result), 'msg' => (($msg === null) ? $this->msg : $msg));
	    return json_encode($response);
	} 
}
?>