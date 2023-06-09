			<div class="title_bg"> 
				<div class="title">General</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
<?php
	if ($do == "chatfeatures") 
	{
?>
					<div class="subtitle">General Features</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>Bar Features</dt>
							<dd>
								<ul>
									<li>
										<label for="hide_bar_on">
											<input type="checkbox" id="hide_bar_on" name="hide_bar_on" <?php if($hide_bar_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Hide Bar
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to completely hide the ArrowChat bar.
								</p>
							</dd>
						</dl>
						<?php
							if (ARROWCHAT_EDITION == "lite")
							{
						?>
							<input type="hidden" name="chatrooms_on" value="0" />
							<input type="hidden" name="applications_on" value="0" />
							<input type="hidden" name="notifications_on" value="0" />
							<input type="hidden" name="moderation_on" value="0" />
						<?php
							} else {
						?>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="chatrooms_on">
											<input type="checkbox" id="chatrooms_on" name="chatrooms_on" <?php if($chatrooms_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Chat Rooms
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to use the chat rooms feature.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="applications_on">
											<input type="checkbox" id="applications_on" name="applications_on" <?php if($applications_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Applications
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to use the applications menu.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="notifications_on">
											<input type="checkbox" id="notifications_on" name="notifications_on" <?php if($notifications_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Site Notifications
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to use the site notifications feature. This feature must first be installed correctly to function. Please visit http://www.arrowchat.com/support/ for documentation on installation.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="enable_moderation">
											<input type="checkbox" id="enable_moderation" name="enable_moderation" <?php if($enable_moderation == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Moderation
										</label>
									</li>
								</ul>
								<p class="explain">
									Enables a report abuse/spam button for users and the moderation tab in the chat bar for moderators and admins.
								</p>
							</dd>
						</dl>
					<?php
						}
					?>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="enable_mobile">
											<input type="checkbox" id="enable_mobile" name="enable_mobile" <?php if($enable_mobile == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Chat Tab on Mobile Devices
										</label>
									</li>
								</ul>
								<p class="explain">
									This will enable a small floating tab for mobile devices that will register the user as logged in, display the buddy list count, and number of unread messages. Clicking it will take them to the full <a href="../public/mobile/">mobile application</a>.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="desktop_notifications">
											<input type="checkbox" id="desktop_notifications" name="desktop_notifications" <?php if($desktop_notifications == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Desktop Notifications
										</label>
									</li>
								</ul>
								<p class="explain">
									If enabled, users on Google Chrome will receive desktop notifications when a new message is received.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="online_list_on">
											<input type="checkbox" id="online_list_on" name="online_list_on" <?php if($online_list_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Online List
										</label>
									</li>
								</ul>
								<p class="explain">
									Unchecking this will hide the online list. One-on-one chat can still be initiated through chat rooms or via a link using our API.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>User Features</dt>
							<dd>
								<ul>
									<li>
										<label for="popout_chat_on">
											<input type="checkbox" id="popout_chat_on" name="popout_chat_on" <?php if($popout_chat_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Users to Pop Out the Chat
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to use pop out chat.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="theme_change_on">
											<input type="checkbox" id="theme_change_on" name="theme_change_on" <?php if($theme_change_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Users to Change Themes
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to change their theme to any theme that is currently set as active. Unchecking this will enable the default theme for all users.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>Video Chat Features</dt>
							<dd>
								<ul>
									<li>
										<label for="video_chat">
											<input type="checkbox" id="video_chat" name="video_chat" <?php if($video_chat == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Users to Video Chat
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to video chat with each other.
								</p>
							</dd>
						</dl>
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								$('#video_chat_selection').change(function() {
									if ($('#video_chat_selection').val() == 2) {
										$('.tokbox').show();
									} else {
										$('.tokbox').hide();
									}
								});
							});
						</script>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<label for="video_chat_selection">Video Chat Service</label>
								<select name="video_chat_selection" id="video_chat_selection" style="width: 432px;">
									<option value="1" <?php if (empty($video_chat_selection) OR $video_chat_selection == 1) echo 'selected="selected"'; ?>>Tinychat (free)</option>
								<?php
									if (ARROWCHAT_EDITION == "business")
									{
								?>
									<option value="2" <?php if ($video_chat_selection == 2) echo 'selected="selected"'; ?>>Tokbox (paid; requires SSL)</option>
									<option value="3" <?php if ($video_chat_selection == 3) echo 'selected="selected"'; ?>>appear.in (free)</option>
									<option value="4" <?php if ($video_chat_selection == 4) echo 'selected="selected"'; ?>>OpenTok RTC (free)</option>
								<?php
									}
								?>
								</select>
								<p class="explain">
									The video chat service that you would like users to use.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<label for="video_chat_height">Default Video Height</label>
								<input type="text" id="video_chat_height" class="selectionText" name="video_chat_height" value="<?php echo $video_chat_height; ?>" />
								<p class="explain">
									The default video height in pixels.  Enter a number only.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<label for="video_chat_width">Default Video Width</label>
								<input type="text" id="video_chat_width" class="selectionText" name="video_chat_width" value="<?php echo $video_chat_width; ?>" />
								<p class="explain">
									The default video width in pixels.  Enter a number only.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox tokbox" <?php if ($video_chat_selection != 2) echo 'style="display:none"'; ?>>
							<dt></dt>
							<dd>
								<label for="tokbox_api">Tokbox API Key</label>
								<input type="text" id="tokbox_api" class="selectionText" name="tokbox_api" value="<?php echo $tokbox_api; ?>" />
								<p class="explain">
									Your Tokbox API key. Both your API key and secret key are available in your tokbox account after creating an account and signing in on <a href="https://tokbox.com/" target="_blank">tokbox.com</a>.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox tokbox" <?php if ($video_chat_selection != 2) echo 'style="display:none"'; ?>>
							<dt></dt>
							<dd>
								<label for="tokbox_secret">Tokbox Secret</label>
								<input type="text" id="tokbox_secret" class="selectionText" name="tokbox_secret" value="<?php echo $tokbox_secret; ?>" />
								<p class="explain">
									Your Tokbox secret key.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>File Upload Features</dt>
							<dd>
								<ul>
									<li>
										<label for="file_transfer_on">
											<input type="checkbox" id="file_transfer_on" name="file_transfer_on" <?php if($file_transfer_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Private File Transfers
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to transfer files with each other. CHMOD your uploads folder to 777 before enabling this feature.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="chatroom_transfer_on">
											<input type="checkbox" id="chatroom_transfer_on" name="chatroom_transfer_on" <?php if($chatroom_transfer_on == 1) echo 'checked="checked"';  ?> value="1" />
											Enable Chat Room File Transfers
										</label>
									</li>
								</ul>
								<p class="explain">
									Allows users to transfer files in chat rooms. CHMOD your uploads folder to 777 before enabling this feature.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<label for="max_upload_size">Max Upload Size</label>
								<input type="text" id="max_upload_size" class="selectionText" name="max_upload_size" value="<?php echo $max_upload_size; ?>" />
								<p class="explain">
									The maximum amount of MB that a user can upload.  Enter a number only.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="chatfeatures_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>
<?php
	if ($do == "embedcodes") 
	{
?>
			   <script type="text/javascript">
					jQuery(document).ready(function($) {
						$("#user_chat_select").click(function() {
							$('#user_chat').select();
						});
						$("#chat_rooms_select").click(function() {
							$('#chat_rooms').select();
						});
						$("#buddy_list_select").click(function() {
							$('#buddy_list').select();
						});
						$('#uc_width').keyup(function() {
							updateUserChat();
						});
						$('#uc_height').keyup(function() {
							updateUserChat();
						});
						$('#uc_border').click(function() {
							updateUserChat();
						});
						$('#cr_width').keyup(function() {
							updateChatRooms();
						});
						$('#cr_height').keyup(function() {
							updateChatRooms();
						});
						$('#cr_border').click(function() {
							updateChatRooms();
						});
						$('#chatroom_auto_join').change(function() {
							updateChatRooms();
						});
						$('#bl_width').keyup(function() {
							updateBuddyList();
						});
						$('#bl_height').keyup(function() {
							updateBuddyList();
						});
						$('#bl_max_results').keyup(function() {
							updateBuddyList();
						});
					});
					function updateUserChat() {
						var width = $('#uc_width').val();
						var height = $('#uc_height').val();
						var border = $('#uc_border').attr('checked');
						if (border) {
							border = 1;
						} else {
							border = 0;
						}
						$("#user_chat").val('<iframe src="<?php echo $base_url; ?>public/popout/" width="'+width+'" height="'+height+'" frameborder="0" style="border:'+border+'px solid #aaa"></iframe>');
					}
					function updateChatRooms() {
						var width = $('#cr_width').val();
						var height = $('#cr_height').val();
						var border = $('#cr_border').attr('checked');
						var room = $('#chatroom_auto_join').val();
						if (border) {
							border = 1;
						} else {
							border = 0;
						}
						$("#chat_rooms").val('<iframe src="<?php echo $base_url; ?>public/chatroom/?id='+room+'" width="'+width+'" height="'+height+'" frameborder="0" style="border:'+border+'px solid #aaa"></iframe>');
					}
					function updateBuddyList() {
						var width = $('#bl_width').val();
						var height = $('#bl_height').val();
						var max_results = $('#bl_max_results').val();
						var bracket = '<';
						$("#buddy_list").val('<link type="text/css" rel="stylesheet" media="all" href="<?php echo $base_url; ?>public/list/css/style.css" charset="utf-8" />'+bracket+'script type="text/javascript">var ac_max_results = '+max_results+';'+bracket+'/script><script type="text/javascript" src="<?php echo $base_url; ?>public/list/js/list_core.js" charset="utf-8">'+bracket+'/script><div id="arrowchat_public_list" style="width:'+width+'; height:'+height+';"></div>');
					}
				</script>
					<div class="subtitle">Embed Codes</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt><label>Buddy List Embed Code</label></dt>
							<dd>
								<ul>
									<li>
										<label>
											
										</label>
									</li>
								</ul>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Width</label></dt>
							<dd>
								<input type="text" id="bl_width" class="selectionText" value="170px" />
								<p class="explain">
									Enter the width, in pixels, that you want the box to be.  You can enter "100%" also.  You need to enter "px" or "%" after the number.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Height</label></dt>
							<dd>
								<input type="text" id="bl_height" class="selectionText" value="162px" />
								<p class="explain">
									Enter the height, in pixels, that you want the box to be.  You can enter "100%" also.  You need to enter "px" or "%" after the number.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Max Results</label></dt>
							<dd>
								<input type="text" id="bl_max_results" class="selectionText" value="0" />
								<p class="explain">
									Enter the maximum number of users that you would like to display.  Entering 0 will display all.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Code</label></dt>
							<dd>
								<textarea id="buddy_list" class="selectionArea">&lt;link type="text/css" rel="stylesheet" media="all" href="<?php echo $base_url; ?>public/list/css/style.css" charset="utf-8" /&gt; 
&lt;script type="text/javascript"&gt;var ac_max_results = 0;&lt;/script&gt;
&lt;script type="text/javascript" src="<?php echo $base_url; ?>public/list/js/list_core.js" charset="utf-8"&gt;&lt;/script&gt;
&lt;div id="arrowchat_public_list"&gt;&lt;/div&gt;</textarea>
								<p>
									<a title="Select all the code" href="javascript:;" id="buddy_list_select">Select All</a><br /><br />
								</p>
								<p class="explain">
									Please understand that this code must be put on a page where the ArrowChat djs script code and jQuery is on.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt><label>Chat Rooms Embed Code</label></dt>
							<dd>
								<ul>
									<li>
										<label>
											
										</label>
									</li>
								</ul>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Width</label></dt>
							<dd>
								<input type="text" id="cr_width" class="selectionText" value="600" />
								<p class="explain">
									Enter the width, in pixels, that you want the box to be.  You can enter "100%" also.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Height</label></dt>
							<dd>
								<input type="text" id="cr_height" class="selectionText" value="300" />
								<p class="explain">
									Enter the height, in pixels, that you want the box to be.  You can enter "100%" also.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="chatroom_auto_join">Room</label>
							</dt>
							<dd>
								<select name="chatroom_auto_join" id="chatroom_auto_join" style="width: 432px;">
									<option value="0" <?php if (empty($chatroom_auto_join)) echo 'selected="selected"'; ?>>Select a Room</option>
								<?php
									$result = $db->execute("
										SELECT * 
										FROM arrowchat_chatroom_rooms 
										ORDER BY id ASC
									");

									if ($result AND $db->count_select() > 0) 
									{
										while ($row = $db->fetch_array($result)) 
										{
								?>
									<option value="<?php echo $row['id']; ?>" <?php if ($chatroom_auto_join == $row['id']) echo 'selected="selected"'; ?>><?php echo $row['name']; ?></option>
								<?php
										}
									}
								?>
								</select>
							<p class="explain">
								Select the room that you would like to embed.  Please note that password-protected rooms must obtain access from the bar first.
							</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="cr_border">
											<input type="checkbox" id="cr_border" name="cr_border" checked="checked" value="1" />
											Enable border
										</label>
									</li>
								</ul>
								<p class="explain">
									Puts a border around the embed.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Code</label></dt>
							<dd>
								<textarea id="chat_rooms" class="selectionArea">&lt;iframe src="<?php echo $base_url; ?>public/chatroom/" width="600" height="300" frameborder="0" style="border:1px solid #aaa"&gt;&lt;/iframe&gt;</textarea>
								<p>
									<a title="Select all the code" href="javascript:;" id="chat_rooms_select">Select All</a><br /><br />
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt><label>User Chat Embed Code</label></dt>
							<dd>
								<ul>
									<li>
										<label>
											
										</label>
									</li>
								</ul>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Width</label></dt>
							<dd>
								<input type="text" id="uc_width" class="selectionText" value="600" />
								<p class="explain">
									Enter the width, in pixels, that you want the box to be.  You can enter "100%" also.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Height</label></dt>
							<dd>
								<input type="text" id="uc_height" class="selectionText" value="300" />
								<p class="explain">
									Enter the height, in pixels, that you want the box to be.  You can enter "100%" also.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="uc_border">
											<input type="checkbox" id="uc_border" name="uc_border" checked="checked" value="1" />
											Enable border
										</label>
									</li>
								</ul>
								<p class="explain">
									Puts a border around the embed.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Code</label></dt>
							<dd>
								<textarea id="user_chat" class="selectionArea">&lt;iframe src="<?php echo $base_url; ?>public/popout/" width="600" height="300" frameborder="0" style="border:1px solid #aaa"&gt;&lt;/iframe&gt;</textarea>
								<p>
									<a title="Select all the code" href="javascript:;" id="user_chat_select">Select All</a><br /><br />
								</p>
							</dd>
						</dl>
					</fieldset>
<?php
	}
?>
<?php
	if ($do == "chatsettings") 
	{
?>
					<div class="subtitle">General Settings</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="guests_can_view">
											<input type="checkbox" id="guests_can_view" name="guests_can_view" <?php if($guests_can_view == 1) echo 'checked="checked"'; ?> value="1" />
											Guests Can View the Bar
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will allow guests to be able to view the chat bar with bar links and applications (if enabled) and a message telling them to register or login.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="guests_can_chat">
											<input type="checkbox" id="guests_can_chat" name="guests_can_chat" <?php if($guests_can_chat == 1) echo 'checked="checked"'; ?> value="1" />
											Guests Can Chat
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will allow guests to be able to chat without logging in.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="guest_name_change">
											<input type="checkbox" id="guest_name_change" name="guest_name_change" <?php if($guest_name_change == 1) echo 'checked="checked"'; ?> value="1" />
											Guests Can Change Name
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will allow guests to be able to change their name while chatting instead of the generic "Guest {Random Number}".
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="guest_name_duplicates">
											<input type="checkbox" id="guest_name_duplicates" name="guest_name_duplicates" <?php if($guest_name_duplicates == 1) echo 'checked="checked"'; ?> value="1" />
											Guests Can Have Duplicate Names
										</label>
									</li>
								</ul>
								<p class="explain">
									If guests are allowed to change their name, checking this will allow guests to have the same name.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="guest_name_bad_words">Guest Name Blocked Words</label>
							</dt>
							<dd>
								<input type="text" id="guest_name_bad_words" class="selectionText" name="guest_name_bad_words" value="<?php echo $guest_name_bad_words; ?>" />
								<p class="explain">
									SEPARATE WITH COMMAS. Enter words that you would like to be blocked when guests choose a name. Caution: Entering "ass" would also block "grass".
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="disable_buddy_list">
										<?php
											if (NO_FREIND_SYSTEM == 1)
											{
										?>
											<input type="checkbox" id="disable_buddy_list" disabled="disabled" checked="checked" 
value="1" />
											<input type="hidden" name="disable_buddy_list" value="1" />
										<?php
											} else {
										?>
											<input type="checkbox" id="disable_buddy_list" name="disable_buddy_list" <?php if($disable_buddy_list == 1) echo 'checked="checked"'; ?> value="1" />
										<?php
											}
										?>
											Show All Online Users
										</label>
									</li>
								</ul>
								<p class="explain">
								<?php
									if (NO_FREIND_SYSTEM == 1)
									{
								?>
									This feature has been disabled because we detected that your site has no friend system.  You can change this in the includes/config.php file.
								<?php
									} else {
								?>
									Checking this will disable the friend's list and everyone on your site will be able to chat with anyone online.
								<?php
									}
								?>
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="hide_admins_buddylist">
											<input type="checkbox" id="hide_admins_buddylist" name="hide_admins_buddylist" <?php if($hide_admins_buddylist == 1) echo 'checked="checked"'; ?> value="1" />
											Hide Administrators
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will hide all administrators from the buddy list.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Guests Chat With</label></dt>
							<dd>
								<ul>
									<li>
										<label for="chat_with_guests_1">
											<input type="radio" name="guests_chat_with" value="1" id="chat_with_guests_1" <?php if($guests_chat_with == 1) echo 'checked="checked"'; ?> /> Guests Only
										</label>
									</li>
									<li>
										<label for="chat_with_guests_2">
											<input type="radio" name="guests_chat_with" value="2" id="chat_with_guests_2" <?php if($guests_chat_with == 2) echo 'checked="checked"'; ?> /> Guests and Members
										</label>
									</li>
									<li>
										<label for="chat_with_guests_3">
											<input type="radio" name="guests_chat_with" value="3" id="chat_with_guests_3" <?php if($guests_chat_with == 3) echo 'checked="checked"'; ?> /> Members Only
										</label>
									</li>
									<li>
										<label for="chat_with_guests_4">
											<input type="radio" name="guests_chat_with" value="4" id="chat_with_guests_4" <?php if($guests_chat_with == 4) echo 'checked="checked"'; ?> /> Admins Only
										</label>
									</li>
								</ul>
								<p class="explain">
									Select what guest users will see in their buddy list if guest chat is enabled.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Users Chat With</label></dt>
							<dd>
								<ul>
									<li>
										<label for="chat_with_members_1">
											<input type="radio" name="users_chat_with" value="1" id="chat_with_members_1" <?php if($users_chat_with == 1) echo 'checked="checked"'; ?> /> Guests Only
										</label>
									</li>
									<li>
										<label for="chat_with_members_2">
											<input type="radio" name="users_chat_with" value="2" id="chat_with_members_2" <?php if($users_chat_with == 2) echo 'checked="checked"'; ?> /> Guests and Members
										</label>
									</li>
									<li>
										<label for="chat_with_members_3">
											<input type="radio" name="users_chat_with" value="3" id="chat_with_members_3" <?php if($users_chat_with == 3) echo 'checked="checked"'; ?> /> Members Only
										</label>
									</li>
									<li>
										<label for="chat_with_members_4">
											<input type="radio" name="users_chat_with" value="4" id="chat_with_members_4" <?php if($users_chat_with == 4) echo 'checked="checked"'; ?> /> Admins Only
										</label>
									</li>
								</ul>
								<p class="explain">
									Select what logged in users will see in their buddy list.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="disable_avatars">
											<input type="checkbox" id="disable_avatars" name="disable_avatars" <?php if($disable_avatars == 1) echo 'checked="checked"'; ?> value="1" />
											Disable the Use of Avatars
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will disable avatars across your entire chat.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="disable_smilies">
											<input type="checkbox" id="disable_smilies" name="disable_smilies" <?php if($disable_smilies == 1) echo 'checked="checked"'; ?> value="1" />
											Disable the Use of Smilies
										</label>
									</li>
								</ul>
								<p class="explain">
									Check this to disable smilies in chat.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="us_time">
											<input type="checkbox" id="us_time" name="us_time" <?php if($us_time == 1) echo 'checked="checked"'; ?> value="1" />
											Enable AM/PM Time Format
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking will use the am/pm time format. Unchecking it will use the 24 hour time format.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="show_full_username">
											<input type="checkbox" id="show_full_username" name="show_full_username" <?php if($show_full_username == 1) echo 'checked="checked"'; ?> value="1" />
											Enable Showing Full Names
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will show the full names in chat instead of omitting things past a space.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="search_number">Chat Search Threshold</label>
							</dt>
							<dd>
								<input type="text" id="search_number" class="selectionText" name="search_number" value="<?php echo $search_number; ?>" />
								<p class="explain">
									The amount of users that need to be online before the chat will show the search in the buddy list.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="blocked_words">Blocked Words</label>
							</dt>
							<dd>
								<input type="text" id="blocked_words" class="selectionText" name="blocked_words" value="<?php echo $blocked_words; ?>" />
								<p class="explain">
									SEPARATE WITH COMMAS. Enter the word in square brackets for an exact match. For example, entering ass would also block grass but entering [ass] would only block that word.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="admin_chat_all">
											<input type="checkbox" id="admin_chat_all" name="admin_chat_all" <?php if($admin_chat_all == 1) echo 'checked="checked"'; ?> value="1" />
											ArrowChat Admins/Mods Can View All Online
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will allow administrators and moderators to view all online users instead of just friends. You can set a user as an admin by clicking 'Manage Users'.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="admin_view_maintenance">
											<input type="checkbox" id="admin_view_maintenance" name="admin_view_maintenance" <?php if($admin_view_maintenance == 1) echo 'checked="checked"'; ?> value="1" />
											ArrowChat Admins Ignore Maintenance Mode
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will allow administrators to still be able to use the chat bar when maintenance mode is enabled.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="chat_maintenance">
											<input type="checkbox" id="chat_maintenance" name="chat_maintenance" <?php if($chat_maintenance == 1) echo 'checked="checked"'; ?> value="1" />
											Enable Chat Maintenance
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will disable chat on your site with a message saying it is down for maintenance.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="disable_arrowchat">
											<input type="checkbox" id="disable_arrowchat" name="disable_arrowchat" <?php if($disable_arrowchat == 1) echo 'checked="checked"'; ?> value="1" />
											Disable ArrowChat
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will completely disable ArrowChat on your site until it is unchecked again.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="chatsettings_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>

<?php
	if ($do == "chatstyle") {
?>
					<link rel="stylesheet" href="includes/css/colorpicker.css" type="text/css" media="screen">
					<script type="text/javascript" src="includes/js/colorpicker.js"></script> 
					<script type="text/javascript">
						$(document).ready(function() {
							$( "#bar_left, #bar_right" ).sortable({
								connectWith: ".horzlist",
								axis: "x",
								items: "li:not(.ui-state-disabled)",
								placeholder: "ui-state-highlight",
								update: function() {
									var b = $(this).parent().attr('id');
									if (b == "left") {
										$(this).find('img').removeClass('border-left');
										$(this).find('img').addClass('border-right');
									} else {
										$(this).find('img').removeClass('border-right');
										$(this).find('img').addClass('border-left');
									}
								}
							}).disableSelection();
							$('#width_buddy_list').slider({
								value: <?php echo $width_buddy_list; ?>,
								min: 16,
								max: 200,
								step: 1,
								slide: function ( event, ui ) {
									$('#width_buddy_list_amt').val( ui.value );
									$('#width_buddy_list_amt2').html( ui.value );
								}
							});
							$('#width_chatrooms').slider({
								value: <?php echo $width_chatrooms; ?>,
								min: 16,
								max: 200,
								step: 1,
								slide: function ( event, ui ) {
									$('#width_chatrooms_amt').val( ui.value );
									$('#width_chatrooms_amt2').html( ui.value );
								}
							});
							$('#width_applications').slider({
								value: <?php echo $width_applications; ?>,
								min: 16,
								max: 200,
								step: 1,
								slide: function ( event, ui ) {
									$('#width_applications_amt').val( ui.value );
									$('#width_applications_amt2').html( ui.value );
								}
							});
							$('#bar_fixed_width').slider({
								value: <?php echo $bar_fixed_width; ?>,
								min: 500,
								max: 1200,
								step: 1,
								slide: function ( event, ui ) {
									$('#bar_fixed_width_amt').val( ui.value );
									$('#bar_fixed_width_amt2').html( ui.value );
								}
							});
							$('#bar_padding').slider({
								value: <?php echo $bar_padding; ?>,
								min: -2,
								max: 300,
								step: 1,
								slide: function ( event, ui ) {
									$('#bar_padding_amt').val( ui.value );
									$('#bar_padding_amt2').html( ui.value );
								}
							});
							$('#window_top_padding').slider({
								value: <?php echo $window_top_padding; ?>,
								min: 0,
								max: 300,
								step: 5,
								slide: function ( event, ui ) {
									$('#window_top_padding_amt').val( ui.value );
									$('#window_top_padding_amt2').html( ui.value );
								}
							});
							$('#admin_background_color').ColorPicker({
								onSubmit: function(hsb, hex, rgb, el) {
									$(el).val(hex);
									$(el).ColorPickerHide();
								},
								onBeforeShow: function() {
									$(this).ColorPickerSetColor(this.value);
								},
								onChange: function (hsb, hex, rgb) {
									$('#admin_background_color').css('backgroundColor', '#' + hex);
									$('#admin_background_color').val(hex);
								}
							}).bind('keyup', function() {
								$(this).ColorPickerSetColor(this.value);
								$('#admin_background_color').css('backgroundColor', '#' + this.value);
							});
							$('#admin_text_color').ColorPicker({
								onSubmit: function(hsb, hex, rgb, el) {
									$(el).val(hex);
									$(el).ColorPickerHide();
								},
								onBeforeShow: function() {
									$(this).ColorPickerSetColor(this.value);
								},
								onChange: function (hsb, hex, rgb) {
									$('#admin_text_color').css('backgroundColor', '#' + hex);
									$('#admin_text_color').val(hex);
								}
							}).bind('keyup', function() {
								$(this).ColorPickerSetColor(this.value);
								$('#admin_text_color').css('backgroundColor', '#' + this.value);
							});
							$('#admin_text_color').css('backgroundColor', '#<?php echo $admin_text_color; ?>');
							$('#admin_background_color').css('backgroundColor', '#<?php echo $admin_background_color; ?>');
						});
					</script>
					<style type="text/css">
						.ui-state-highlight { height: 1.5em; line-height: 1.2em; border: 1px solid #000; }
						.border-right {
							border-right: 1px solid #999;
						}
						.border-left {
							border-left: 1px solid #999;
						}
						.horzlist {
							min-width: 16px;
							height: 30px;
						}
						.horzlist li
						{
							float: left;
							list-style-type: none;
							height: 30px;
							min-width: 16px;
							cursor: e-resize;
						}
						.ui-state-highlight { 
							background-color: #cecece;
							border: 1px dashed #333;
							width: 110px;
						}
						.ui-state-disabled {
							cursor: auto !important;
						}
						#left {
							float: left;
						}
						#right {
							float: right;
						}
					</style>
					<div class="subtitle">General Settings</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="width_buddy_list">Buddy List Button Width</label>
							</dt>
							<dd>
								<div id="width_buddy_list" class="slider"></div><div id="width_buddy_list_amt2" class="slider-number"><?php echo $width_buddy_list; ?></div>
								<input type="hidden" id="width_buddy_list_amt" name="width_buddy_list" value="<?php echo $width_buddy_list; ?>" />
								<p class="explain">
									The width, in pixels, of the buddy list button. Settings this to 25px or below will display the icon only without the language file.  Default: 189
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="width_chatrooms">Chat Rooms Button Width</label>
							</dt>
							<dd>
								<div id="width_chatrooms" class="slider"></div><div id="width_chatrooms_amt2" class="slider-number"><?php echo $width_chatrooms; ?></div>
								<input type="hidden" id="width_chatrooms_amt" name="width_chatrooms" value="<?php echo $width_chatrooms; ?>" />
								<p class="explain">
									The width, in pixels, of the chat room button. Settings this to 25px or below will display the icon only without the language file.  Default: 16
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="width_applications">Applications Button Width</label>
							</dt>
							<dd>
								<div id="width_applications" class="slider"></div><div id="width_applications_amt2" class="slider-number"><?php echo $width_applications; ?></div>
								<input type="hidden" id="width_applications_amt" name="width_applications" value="<?php echo $width_applications; ?>" />
								<p class="explain">
									The width, in pixels, of the applications button. Settings this to 25px or below will display the icon only without the language file.  Default: 16
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="bar_fixed">
											<input type="checkbox" id="bar_fixed" name="bar_fixed" <?php if($bar_fixed == 1) echo 'checked="checked"'; ?> value="1" />
											Enable Fixed Width Bar
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will make the bar a fixed width instead of fluid (growing and shrinking with window size).
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Fixed Bar Alignment</label></dt>
							<dd>
								<ul>
									<li>
										<label for="bar_fixed_alignment_1">
											<input type="radio" name="bar_fixed_alignment" value="left" id="bar_fixed_alignment_1" <?php if($bar_fixed_alignment == "left") echo 'checked="checked"'; ?> /> Left
										</label>
									</li>
									<li>
										<label for="bar_fixed_alignment_2">
											<input type="radio" name="bar_fixed_alignment" value="center" id="bar_fixed_alignment_2" <?php if($bar_fixed_alignment == "center") echo 'checked="checked"'; ?> /> Center
										</label>
									</li>
									<li>
										<label for="bar_fixed_alignment_3">
											<input type="radio" name="bar_fixed_alignment" value="right" id="bar_fixed_alignment_3" <?php if($bar_fixed_alignment == "right") echo 'checked="checked"'; ?> /> Right
										</label>
									</li>
								</ul>
								<p class="explain">
									If a fixed bar is enabled, choose whether it is left, center, or right aligned.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="bar_fixed_width">Fixed Bar Width</label>
							</dt>
							<dd>
								<div id="bar_fixed_width" class="slider"></div><div id="bar_fixed_width_amt2" class="slider-number"><?php echo $bar_fixed_width; ?></div>
								<input type="hidden" id="bar_fixed_width_amt" name="bar_fixed_width" value="<?php echo $bar_fixed_width; ?>" />
								<p class="explain">
									If a fixed bar is enabled, enter the width of the bar in pixels.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="bar_padding">Bar Padding</label>
							</dt>
							<dd>
								<div id="bar_padding" class="slider"></div><div id="bar_padding_amt2" class="slider-number"><?php echo $bar_padding; ?></div>
								<input type="hidden" id="bar_padding_amt" name="bar_padding" value="<?php echo $bar_padding; ?>" />
								<p class="explain">
									Enter the number, in pixels, that the bar should appear from the left and right edges of the window.  Default: 15
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="window_top_padding">Window Top Padding</label>
							</dt>
							<dd>
								<div id="window_top_padding" class="slider"></div><div id="window_top_padding_amt2" class="slider-number"><?php echo $window_top_padding; ?></div>
								<input type="hidden" id="window_top_padding_amt" name="window_top_padding" value="<?php echo $window_top_padding; ?>" />
								<p class="explain">
									The padding, in pixels, of the space between the buddy list/notification's maximum height and the top of the browser window.  Default: 70
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="enable_chat_animations">
											<input type="checkbox" id="enable_chat_animations" name="enable_chat_animations" <?php if($enable_chat_animations == 1) echo 'checked="checked"'; ?> value="1" />
											Enable Chat Message Animations
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will enable animations when chat messages are received.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="admin_background_color">Admin Background Color</label>
							</dt>
							<dd>
								<input type="text" id="admin_background_color" class="selectionText" name="admin_background_color" value="<?php echo $admin_background_color; ?>" />
								<p class="explain">
									Specify a special background color for ArrowChat admins in the buddy list.  This should be in hex format without a leading #.  Leave blank for no special distinction.  Further customization can be done with the "arrowchat_buddylist_admin_1" CSS class.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="admin_text_color">Admin Text Color</label>
							</dt>
							<dd>
								<input type="text" id="admin_text_color" class="selectionText" name="admin_text_color" value="<?php echo $admin_text_color; ?>" />
								<p class="explain">
									Specify a special text color for ArrowChat admins in the buddy list.  This should be in hex format without a leading #.  Leave blank for no special distinction.  Further customization can be done with the "arrowchat_buddylist_admin_1" CSS class.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="chatstyle_submit" value="1" />
							</div>
						</dd>
					</dl>
					<!--
					<table cellspacing="0" cellpadding="0" class="module_table">
						<tr>
							<td colspan="2" style="font-size: 11px;">
								<span style="font-size: 14px;">Placement Editor</span><br />
								Drag and drop the features on the bar anywhere you want.  Each item must either float on the left or the right side.  The buddy list cannot be moved.  Features that are not enabled will not show up!<br /><br /><br /><br />
								<div id="placement_bar" style="width: 680px; height: 30px; border-left: 1px solid #999; border-right: 1px solid #999; border-top: 1px solid #999; background: url(images/images_core.png);">
									<div id="left">
										<ul id="bar_left" class="horzlist">
											<li class="ui-state-default" id="move_6"><img title="Bar Links" id="img_6" class="list_image border-right" src="images/button_barlinks.png" /></li>
											<li class="ui-state-default" id="move_1"><img title="Applications" id="img_1" class="list_image border-right" src="images/button_applications.png" /></li>

										</ul>
									</div>
									<div id="right">
										<ul id="bar_right" class="horzlist">
											<li class="ui-state-default ui-state-disabled" id="move_5"><img title="Buddy List Cannot Be Moved" id="img_5" class="list_image border-left" src="images/button_chat.png" /></li>
											<li class="ui-state-default" id="move_2"><img title="Chatrooms" id="img_2" class="list_image border-left" src="images/button_chatrooms.png" /></li>
											<li class="ui-state-default" id="move_3"><img title="Notifications" id="img_3" class="list_image border-left" src="images/button_notifications.png" /></li>
											<li class="ui-state-default" id="move_4"><img title="Hide/Show Bar" id="img_4" class="list_image border-left" src="images/button_hide.png" /></li>

										</ul>
									</div>
								</div>
								<br /><br />
							</td>
						</tr>
					</table>
					-->
<?php
	}
?>

					
					</form>

				</div>
			</div>