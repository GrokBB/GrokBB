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

	if (preg_match('#public/mobile#', $_SERVER['REQUEST_URI']))
		$home_url = "../../../";
	else
		$home_url = "../../";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Mobile Chat</title>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
		<meta name="apple-touch-fullscreen" content="yes" />
		
		<link rel="apple-touch-icon" href="images/apple-touch-icon.png"/> 
		<link rel="stylesheet" href="<?php echo $base_url; ?><?php echo AC_FOLDER_PUBLIC; ?>/mobile/includes/css/jquery-mobile.css" />
		<link type="text/css" rel="stylesheet" id="arrowchat_css" media="all" href="<?php echo $base_url; ?><?php echo AC_FOLDER_PUBLIC; ?>/mobile/includes/css/style.css" charset="utf-8" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>

		<script type="text/javascript" src="<?php echo $base_url; ?><?php echo AC_FOLDER_INCLUDES; ?>/js/jquery.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $base_url; ?><?php echo AC_FOLDER_PUBLIC; ?>/mobile/includes/js/jquery-mobile.js"></script>
		<script type="text/javascript" src="<?php echo $base_url; ?>external.php?type=djs" charset="utf-8"></script> 
		<script type="text/javascript" src="<?php echo $base_url; ?>external.php?type=mjs" charset="utf-8"></script> 
	</head>
    <body>
        <div data-role="page" id="page1">
            <div data-theme="b" data-role="header" data-position="fixed" data-tap-toggle="false">
                <h3>
                    <?php echo $language[110]; ?>
					
                </h3>
				<a data-role="button" id="home-button" data-iconshadow="false" data-iconpos="notext" data-ajax="false" data-theme="b" href="<?php echo $home_url; ?>" data-icon="home" data-shadow="false" data-corners="false"></a>
				<a id="settings-button" data-iconpos="notext" data-iconshadow="false" data-theme="b" data-rel="dialog" data-transition="slidedown" href="#settings-page" data-icon="gear" data-shadow="false" data-corners="false"></a>
            </div>
            <div data-role="content">
				<ul id="buddylist-container-chatroom" data-role="listview" data-divider-theme="c" data-inset="false"></ul>
				<ul id="buddylist-container-recent" data-role="listview" data-divider-theme="c" data-inset="false"></ul>
                <ul id="buddylist-container-available" data-role="listview" data-divider-theme="c" data-inset="false"></ul>
				<ul id="buddylist-container-away" data-role="listview" data-divider-theme="c" data-inset="false"></ul>
            </div>
        </div>
		<div data-role="page" id="page2">
            <div data-theme="b" data-role="header" data-position="fixed" data-tap-toggle="false">
                <h3 id="username-header">
					<?php echo $language[110]; ?>
					
                </h3>
                <a data-role="button" id="back-button" data-direction="reverse" data-transition="slide" data-theme="b" href="#page1" data-icon="arrow-l" data-iconshadow="false" data-iconpos="left" class="back_buttons">
					<?php echo $language[113]; ?>
				</a>
            </div>
            <div data-role="content" class="chat_user_content">
            </div>
            <div data-theme="d" data-role="footer" data-position="fixed" data-tap-toggle="false">
                <div data-role="fieldcontain">
					<div style="width:100%; float:left; margin-top:-5px;padding-right:80px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing: border-box;">
						<input id="textinput1" placeholder="<?php echo $language[208]; ?>" value="" type="text" />
					</div>
					<a id="send_button" data-role="button" data-inline="true" data-transition="none" data-theme="b" href="javascript:;" style="float:right; margin-right: 10px; margin-top:-32px">
						<?php echo $language[114]; ?>
					</a>
                </div>
            </div>
		</div>
		<div data-role="page" id="page3">
			<div data-role="panel" data-theme="a" data-position="right" id="user-panel">
				<ul id="chatroom-users-list" data-role="listview" data-divider-theme="c" data-inset="false"></ul>
			</div>
            <div data-theme="b" data-role="header" data-position="fixed" data-tap-toggle="false">
                <h3 id="chatroom-header">
					<?php echo $language[128]; ?>
                </h3>
                <a data-role="button" id="back-button-chatroom" data-direction="reverse" data-transition="slide" data-theme="b" href="#page1" data-icon="arrow-l" data-iconshadow="false" data-iconpos="left" class="back_buttons">
					<?php echo $language[113]; ?>
				</a>
				<a data-role="button" id="users-button-chatroom" data-display="push" data-theme="b" data-icon="bars" href="#user-panel" data-iconpos="notext"></a>
            </div>
            <div data-role="content" class="chat_room_content">
            </div>
            <div data-theme="d" data-role="footer" data-position="fixed" data-tap-toggle="false">
                <div data-role="fieldcontain">
					<div style="width:100%; float:left; margin-top:-5px;padding-right:80px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing: border-box;">
						<input id="textinput2" maxlength="<?php echo $chatroom_message_length; ?>" placeholder="<?php echo $language[208]; ?>" value="" type="text" />
					</div>
					<a id="send_button_chatroom" data-role="button" data-inline="true" data-iconshadow="false" data-transition="none" data-theme="b" href="javascript:;" style="float:right; margin-right: 10px; margin-top:-32px">
						<?php echo $language[114]; ?>
					</a>
                </div>
            </div>
		</div>
		<div data-role="page" id="password-page">
			<div data-theme="b" data-role="header" data-tap-toggle="false">
				<h1><?php echo $language[128]; ?></h1>
			</div>
			<div data-theme="d" data-role="content" id="chatroom-message">
				<label for="room-password"><?php echo $language[138]; ?></label>
				<input type="text" name="room-password" id="room-password" value="" />
				<a href="#page3" data-theme="b" data-role="button" id="submit-chatroom-password"><?php echo $language[139]; ?></a>
			</div>
		</div>
		<div data-role="dialog" id="user-error">
			<div data-theme="b" data-role="header" data-tap-toggle="false">
				<h1><?php echo $language[110]; ?></h1>
			</div>
			<div data-theme="d" data-role="content" id="user-error-content"></div>
		</div>
		<div data-role="dialog" id="chatroom-error">
			<div data-theme="b" data-role="header" data-tap-toggle="false">
				<h1><?php echo $language[128]; ?></h1>
			</div>
			<div data-theme="d" data-role="content" id="chatroom-error-content"></div>
		</div>
		<div data-role="dialog" id="user-options">
			<div data-theme="b" data-role="header" data-tap-toggle="false">
				<h1></h1>
			</div>
			<div data-theme="d" data-role="content" id="user-options-content"></div>
		</div>
		<div data-role="page" id="settings-page">
			<div data-theme="b" data-role="header" data-tap-toggle="false">
				<h1><?php echo $language[129]; ?></h1>
			</div>
			<div data-theme="d" data-role="content">
				<div style="margin-bottom:60px" id="chatroom-settings-container">
					<div style="float:left; width: 60%; margin-top: 11px; font-size:18px">
						<?php echo $language[130]; ?>
					</div>
					<div style="float:right;">
						<form>
							<select name="flip-show-chatroom" id="flip-show-chatroom" data-role="slider">
								<option value="off"><?php echo $language[133]; ?></option>
								<option value="on"><?php echo $language[132]; ?></option>
							</select>
						</form>
					</div>
				</div>
				<div>
					<div style="float:left; width: 60%; margin-top: 11px; font-size:18px">
						<?php echo $language[131]; ?>
					</div>
					<div style="float:right;">
						<form>
							<select name="flip-show-idle" id="flip-show-idle" data-role="slider">
								<option value="off"><?php echo $language[133]; ?></option>
								<option value="on"><?php echo $language[132]; ?></option>
							</select>
						</form>
					</div>
					<div class="arrowchat_clearfix"></div>
				</div>
				<div>
					<div style="float:left; width: 60%; margin-top: 11px; font-size:18px">
						<?php echo $language[211]; ?>
					</div>
					<div style="float:right;">
						<form>
							<select name="flip-hide-mobile" id="flip-hide-mobile" data-role="slider">
								<option value="off"><?php echo $language[133]; ?></option>
								<option value="on"><?php echo $language[132]; ?></option>
							</select>
						</form>
					</div>
					<div class="arrowchat_clearfix"></div>
				</div>
			</div>
		</div>
    </body>
</html>