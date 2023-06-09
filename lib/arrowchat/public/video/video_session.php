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
	require_once (dirname(__FILE__) . '/OpenTok/vendor/autoload.php');
	use OpenTok\OpenTok;
	use OpenTok\Session;
	use OpenTok\Role; 
	use OpenTok\MediaMode;
	
	$opentok = new OpenTok($tokbox_api, $tokbox_secret);
	$session = $opentok->createSession(array('mediaMode' => MediaMode::ROUTED ));
	
	echo $session->getSessionId();

?>