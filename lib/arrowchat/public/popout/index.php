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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-gb" xml:lang="en-gb"> 
<head> 

	<title>Popout Chat</title>
	
	<link type="text/css" rel="stylesheet" media="all" href="<?php echo $base_url; ?>external.php?type=css" charset="utf-8" /> 
	
	<script type="text/javascript" src="<?php echo $base_url; ?><?php echo AC_FOLDER_INCLUDES; ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $base_url; ?>external.php?type=djs" charset="utf-8"></script> 
	<script type="text/javascript" src="<?php echo $base_url; ?>external.php?type=pjs" charset="utf-8"></script> 
	
	<style type="text/css"> 
		body, html {
			margin: 0px;
			padding: 0px;
			height: 100%;
			width: 100%;
			font-size: 11px;
			font-family: 'Lucida Grande', Verdana, Arial;
		}
	</style>
</head>
<body>
	<div id="arrowchat_sound_player_holder"></div>
	<div id="arrowchat_popout_wrapper">
		<div id="arrowchat_popout_left">
			<div id="arrowchat_popout_friends">
				<div id="arrowchat_userslist_available"></div>
				<div id="arrowchat_userslist_busy"></div>
				<div id="arrowchat_userslist_away"></div>
				<div id="arrowchat_userslist_offline"></div>
			</div>
		</div>
		<div id="arrowchat_popout_right">
			<div id="arrowchat_popout_chat">
				<div id="arrowchat_chatroom_message_flyout" class="arrowchat_message_box">
					<div class="arrowchat_message_box_wrapper">
						<div>
							<span class="arrowchat_message_text"></span>
						</div>
					</div>
				</div>	
			</div>
			<div id="arrowchat_popout_open_chats">
				<div id="arrowchat_popout_container">
				</div>
			</div>
		</div>
	</div>
</body>
</html>