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
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');

	// ########################## GET POST DATA ###########################
	$room_id = $_GET['rid'];

	if (strlen($room_id) == 9) 
	{
		$room_id = "0".$room_id;
	}

	$username = get_username($userid);
	
	// ############################# OPENTOK ##############################
	require_once (dirname(__FILE__) . '/OpenTok/vendor/autoload.php');
	use OpenTok\OpenTok;
	use OpenTok\Session;
	use OpenTok\Role; 
	use OpenTok\MediaMode;
			
	if ($video_chat_selection == 2)
	{
		$opentok = new OpenTok($tokbox_api, $tokbox_secret);
		
		if (isset($_REQUEST['rid'])) 
		{
			$sessionId = $_REQUEST['rid'];
		} 
		else 
		{
			$session = $opentok->createSession(array('mediaMode' => MediaMode::ROUTED ));
			$sessionId = $session->getSessionId();
		}
		
		$token = $opentok->generateToken($sessionId);
	}
	

	// ############################ START HTML ############################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html> 
<head> 
	<title><?php echo $language[200]; ?></title> 
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	
	<style> 
	html, body, div, span, applet, object, iframe,
	h1, h2, h3, h4, h5, h6, p, blockquote, pre,
	a, abbr, acronym, address, big, cite, code,
	del, dfn, em, font, img, ins, kbd, q, s, samp,
	small, strike, strong, sub, sup, tt, var,
	dl, dt, dd, ol, ul, li,
	fieldset, form, label, legend,
	table, caption, tbody, tfoot, thead, tr, th, td {
		margin: 0;
		padding: 0;
		border: 0;
		outline: 0;
		font-weight: inherit;
		font-style: inherit;
		font-size: 100%;
		font-family: inherit;
		vertical-align: baseline;
		text-align: center;
	}
	 
	html {
	  height: 100%;
	  width: 100%;
	  overflow: hidden; /* Hides scrollbar in IE */
	}
	 
	body {
	  height: 100%;
	  width: 100%;
	  margin: 0;
	  padding: 0;
	}
	 
	#video_chat {
		position: fixed;
		top: 0px;
		left: 0px;
		bottom: 0px;
		min-width: 300px;
		width: 100%;
		background: #fff;
	} 
	iframe{
	    position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		height: 100%;
		width: 100%;
	}
	</style> 
</head> 
<body> 
	<div id="video_chat">
	<?php
		if ($video_chat_selection == 1) {
	?>
		<script type='text/javascript'> 
		var tinychat = { room: "arrowchat{<?php echo $room_id; ?>}", join: "auto", api: "none", change: "none", nick: "{<?php echo $username; ?>}", colorbk: "0xffffff"};
		</script> 
		<script src="https://tinychat.com/js/embed.js"></script> 
		<div id="client"></div> 
	<?php
		} else if ($video_chat_selection == 2) {
	?>
		<style> 
		html, body, div, span, applet, object, iframe,
		h1, h2, h3, h4, h5, h6, p, blockquote, pre,
		a, abbr, acronym, address, big, cite, code,
		del, dfn, em, font, img, ins, kbd, q, s, samp,
		small, strike, strong, sub, sup, tt, var,
		dl, dt, dd, ol, ul, li,
		fieldset, form, label, legend,
		table, caption, tbody, tfoot, thead, tr, th, td {
			margin: 0;
			padding: 0;
			border: 0;
			outline: 0;
			font-weight: inherit;
			font-style: inherit;
			font-size: 100%;
			font-family: inherit;
			vertical-align: baseline;
			font-family:'Lucida	Grande',Verdana,Arial,sans-serif;
			font-size: 11px;
		}

		object {
			float: left;
		}

		#videobar {
			float: top;	
		}

		#publisher-controls {
			display: none;
		}

		#push-to-talk {
			padding-top: 5px;
			display: none;
		}

		#devicePanelContainer {
			position: absolute;
			left: 250px;
			top: 10px;
			display:none;
		}

		#devicePanelCloseButton {
			position: relative;
			z-index: 10;
			margin-left: 285px;
			margin-right: 12px;
			padding: 3px;
			text-align: center;
			font-size: 11px;
			background-color: lightgrey;
		}
		#devicePanelBackground {
			background-color: lightgrey;
			width: 340px;
			height: 230px;
		}
		#devicePanelInset #devicePanel {
			position: relative;
			top: -74px;
			left: -9px;
		}

		a.settingsClose:link,
		a.settingsClose:visited,
		a.settingsClose:hover {
			text-decoration: none;
			cursor: pointer;
		}

		table {
			clear: both;
		}

		td {
			vertical-align: top;
			padding-right: 15px;
		}

		.publisherContainer {
			float: left;
		}

		.subscriberContainer {
			width: 264px;
			margin-left: 4px;
			float:left;
		}

		html, body {
			margin: 0px;
			padding: 0px;
			background: #000;
			overflow: hidden;
		}

		#navigation {
			position: fixed;
			bottom: 0px;
			background: #f4f4f4;
			border-top: 1px solid #cecece;
			color: #888;
			width: 100%;
			left: 0px;
			padding: 0px;
			display: none;
		}

		#navigation img {
			border: 0;
			float: right;
			margin-left: 5px;
		}

		#navigation_elements {
			margin: 5px;
			height: 20px;
		}

		#unpublishLink {
			display: none;
		}

		#loading {
			width: 310px;
			padding-top: 120px;
			margin: 0 auto;
			text-align: center;
		}

		#loading {
			width: 250px;
			padding-top: 120px;
			margin: 0 auto;
			text-align: center;
		}

		#canvas {
			margin: 0 auto;
			display: none;
		}

		#myCamera, #otherCamera {
			float: left;
		}

		.camera {
		}

		#endcall {
			width: 250px;
			padding-top: 120px;
			margin: 0 auto;
			text-align: center;
			display: none;
		}
		.button {
			font-size: 12px;
			height: 20px;
			line-height: 22px;
			padding: 0 10px;
			border: none;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			display: inline-block;
			vertical-align: middle;
			font-weight: 400;
			margin: 0;
			text-decoration: none;
			text-align: center;
			background: #0F97F9;
			color: #FFF;
			text-shadow: none;
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			-moz-transition-property: background;
			-o-transition-property: background;
			-webkit-transition-property: background;
			transition-property: background;
			-moz-transition-duration: .3s;
			-o-transition-duration: .3s;
			-webkit-transition-duration: .3s;
			transition-duration: .3s;
			cursor: pointer;
			float: left;
			margin-left: 5px;
		}
		.button.right{float: right;background:#f90f0f}
		.OT_widget-container{background:#fff!important}
		.OT_fit-mode-cover .OT_video-poster{background-position:center center !important;background-size:auto !important}
		.OT_root .OT_video-loading{background: url(<?php echo $base_url; ?>public/video/OpenTok/img/ajax-loader.gif) no-repeat;}
		.OT_publisher, .OT_subscriber{display:inline-block !important}
		</style> 
		<script src='https://static.opentok.com/v2/js/opentok.min.js'></script>
		<script type="text/javascript">
			var apiKey = "<?php echo $tokbox_api; ?>";
			var sessionId = "<?php echo $sessionId; ?>";
			var token = "<?php echo $token; ?>";
			var session;
			var publisher;
			var subscribers = {};
			var totalStreams = 0;

			if (OT.checkSystemRequirements() != OT.HAS_REQUIREMENTS) {
				alert('Sorry, but your computer configuration does not meet minimum requirements for video chat.');
			} else {
				var session = OT.initSession(apiKey, sessionId);
				
				session.on('sessionConnected', function(event) {
					hide('loading');
					show('canvas');

					for (var i = 0; i < event.streams.length; i++) {
						if (event.streams[i].connection.connectionId != session.connection.connectionId) {
							totalStreams++;
						}
						addStream(event.streams[i]);
					}

					publish();
					resizeWindow();
					show('navigation');
					show('unpublishLink');
					show('disconnectLink');
					hide('publishLink');
				});
				
				session.on('sessionDisconnected', function(event) {
					publisher = null;
				});
				
				session.on('connectionCreated', function(event) {
				});
				
				session.on('connectionDestroyed', function(event) {
				});
				
				session.on('streamCreated', function(event) {
					for (var i = 0; i < event.streams.length; i++) {
						if (event.streams[i].connection.connectionId != session.connection.connectionId) {
							totalStreams++;
						}
						addStream(event.streams[i]);
					}
					resizeWindow();
				});
				
				session.on('streamDestroyed', function(event) {
					for (var i = 0; i < event.streams.length; i++) {
						if (event.streams[i].connection.connectionId != session.connection.connectionId) {
							totalStreams--;
						}
					}
					resizeWindow();
				});
			}
			
			function connect() {
				session.connect(token, function(error) {
					publish();
				});
			}
			
			function disconnect() {
				unpublish();
				session.disconnect();
				hide('navigation');
				show('endcall');
				var div = document.getElementById('canvas');	
				div.parentNode.removeChild(div);
				window.resizeTo(300,330);
			}
			
			function publish() {
				if (!publisher) {
					var parentDiv = document.getElementById("myCamera");
					var div = document.createElement('div');		
					div.setAttribute('id', 'opentok_publisher');
					parentDiv.appendChild(div);
					publisher = OT.initPublisher("opentok_publisher",{resolution: '1280x720', frameRate: 30, width:<?php echo $video_chat_width; ?>, height:<?php echo $video_chat_height; ?>, style:{backgroundImageURI:"http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); ?>/OpenTok/img/audio-only.png"}});
					session.publish(publisher); 	
					resizeWindow();
					show('unpublishLink');
					hide('publishLink');
				}
			}

			function unpublish() {
				if (publisher) {
					session.unpublish(publisher);
				}

				publisher = null;
				show('publishLink');
				hide('unpublishLink');
				resizeWindow();
			}
			
			function addStream(stream) {
				if (stream.connection.connectionId == session.connection.connectionId) {
					return;
				}

				var div = document.createElement('div');
				var divId = stream.streamId;
				div.setAttribute('id', divId);
				div.setAttribute('class', 'camera');
				document.getElementById('otherCamera').appendChild(div);
				var params = {width: '<?php echo $video_chat_width; ?>', height: '<?php echo $video_chat_height; ?>'};
				subscribers[stream.streamId] = session.subscribe(stream, divId, {resolution: '1280x720', frameRate: 30, width:<?php echo $video_chat_width; ?>, height:<?php echo $video_chat_height; ?>, style:{backgroundImageURI:"http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); ?>/OpenTok/img/audio-only.png"}});
			}
			
			function resizeWindow() {
				if (publisher) {
					width = (totalStreams+1)*(<?php echo $video_chat_width; ?>+30);
					document.getElementById('canvas').style.width = (totalStreams+1)*<?php echo $video_chat_width; ?>+'px';
				} else {
					width = (totalStreams)*(<?php echo $video_chat_width; ?>+30);
					document.getElementById('canvas').style.width = (totalStreams)*<?php echo $video_chat_width; ?>+'px';
				}

				if (width < <?php echo $video_chat_width; ?>+30) { width = <?php echo $video_chat_width; ?>+30; } 
				if (width < 300) { width = 300; }
				window.resizeTo(width,<?php echo $video_chat_height; ?>+165);
			}
		  
			function show(id) {
				document.getElementById(id).style.display = 'inline-block';
			}

			function hide(id) {
				document.getElementById(id).style.display = 'none';
			}
			
			function position() {
				var h = <?php echo $video_chat_height; ?>;

				if( typeof( window.innerWidth ) == 'number' ) {
					h = window.innerHeight;
				} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
					h = document.documentElement.clientHeight;
				} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
					h = document.body.clientHeight;
				}
				
				if (document.getElementById('canvas') && document.getElementById('canvas').style.display != 'none') {
					if (h > <?php echo $video_chat_height; ?>){
						offset = (h-30-<?php echo $video_chat_height; ?>)/2;
						document.getElementById('canvas').style.marginTop = offset+'px';
					} else {
						document.getElementById('canvas').style.marginTop = '0px';
					}
				}
			}
			function inviteUser() {
				window.open ('invite.php?session='+sessionId, 'inviteusers',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=1, width=400,height=120"); 
			}
		</script>
		
		<div id="loading"><img src="OpenTok/img/ajax-loader.gif"></div>
		<div id="endcall"><?php echo $language[203]; ?></div>
		<div id="canvas">
			<div id="myCamera" class="publisherContainer"></div>
			<div id="otherCamera"><div>
		</div>
		<div id="navigation">
			<div id="navigation_elements">
				<a href="#" onclick="javascript:disconnect();" class="button right" id="disconnectLink"><?php echo $language[204]; ?></a>
				<a href="#" onclick="javascript:inviteUser()" class="button" id="inviteLink"><?php echo $language[205]; ?></a>
				<a href="#" onclick="javascript:publish()" class="button" id="publishLink">T<?php echo $language[206]; ?></a>
				<a href="#" onclick="javascript:unpublish()" class="button" id="unpublishLink"><?php echo $language[207]; ?></a>
				<div style="clear:both"></div>
			</div>
			<div style="clear:both"></div>
		</div>
		<script type="text/javascript">
			window.resizeTo(300,330);
			connect();
			window.onload = function() { position(); }
			window.onresize = function() { position(); }
		</script>
	<?php
		} else if ($video_chat_selection == 3) {
	?>
		<iframe id="iframe1" src="https://appear.in/<?php echo $_SERVER['HTTP_HOST'] . $room_id; ?>" frameborder="0" height="<?php echo $video_chat_height; ?>" width="<?php echo $video_chat_width; ?>" seamless="yes"></iframe>
	<?php
		} else if ($video_chat_selection == 4) {
	?>
		<iframe id="iframe1" src="https://opentokrtc.com/<?php echo $_SERVER['HTTP_HOST'] . $room_id; ?>" frameborder="0" height="<?php echo $video_chat_height; ?>" width="<?php echo $video_chat_width; ?>" seamless="yes"></iframe>
	<?php
		}
	?>
	</div>
</body>
</html>