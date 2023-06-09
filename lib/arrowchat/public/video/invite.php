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
	
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	// ########################## INCLUDE BACK-END ###########################
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	
	$session = $_GET['session'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html> 
<head> 
	<title><?php echo $language[201]; ?></title>
	
	<style>
		html,body{margin:0;padding:0}
		p{
			font-family: arial, verdana;
			font-size: 14px;
			line-height: 1.5em;
			margin: 10px 20px;
		}
		input{
			width: 345px;
			height: 30px;
			font-family: arial, verdana;
			font-size: 14px;
			line-height: 1.5em;
			margin-left: 20px;
			padding: 0 5px;
		}
	</style>
</head>
<body>
	<p><?php echo $language[202]; ?></p>
	<input type="text" value="http<?php if (isset($_SERVER['HTTPS'])) echo 's'; ?>://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); ?>/?rid=<?php echo $session; ?>" />
</body>
</html>