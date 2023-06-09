			<div class="title_bg"> 
				<div class="title">Manage</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">

<?php
	if ($do == "appsettings") 
	{
?>
					<script type='text/javascript'>
						var tip="";
						$(window).load(function() {
							$(document).click( function(e) {
								if (!$(e.target).hasClass('version_link')) {
									$('.itip-tooltip').fadeOut("fast").remove();
									tip = "";
								}
							});
							$('.version_link').click(function() {
								var c = $(this).attr("id").substr(5);
								if (tip != c && tip != "") {
									$('.itip-tooltip').fadeOut("fast").remove();
								}
								if (tip != c) {
									tip = c;
									$(this).iTip({
										'closeButton' : 'false',
										'direction' : 'right',
										'icons' : [
											{
												'id' : 'close',
												'click' : function()
												{
													tip = "";
												}
											},
											{
												'id' : 'download',
												'click' : function()
												{
													window.open('http://www.arrowchat.com/members/store.php?do=purchases','new');
												}
											},
											{
												'id' : 'save',
												'click' : function()
												{
													document.location = 'manage.php?do=appsettings&update=1&id='+c;
												}
											}
										]
									})
									$("#close").remove();
								} else {
									$('.itip-tooltip').fadeOut("fast").remove();
									tip = "";
								}
							});
						});
					</script>
					<div class="subtitle">Installed Applications</div>
					<h2 class="subHeading">Applications</h2>
					<ol class="scrollable">
<?php
		$apps_array = array();
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_applications
		");
		if ($result AND $db->count_select() > 0) 
		{	
			while ($row = $db->fetch_array($result)) 
			{
				$settings = false;
				@include_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . $row['folder'] . DIRECTORY_SEPARATOR . "install_config.php");
				
				$apps_array[] = $row['folder'];

				if (empty($row['update_link']) OR empty($row['version'])) 
				{
					if (empty($row['version'])) 
					{
						$row['version'] = "1.0";
					}
					
					$current_version = $row['version'];
				} 
				else 
				{
					$fp = @fopen($row['update_link'], "r");
					
					if ($fp) 
					{
						$current_version = @fread($fp, 99); 
						fclose ($fp);
					} 
					else 
					{
						$current_version = $row['version'];
					}
				}
?>
						<li class="listItem">
							<a href="manage.php?do=appsettings&delete=<?php echo $row['id']; ?>" title="Delete" class="secondaryContent delete"><span>Delete</span></a>
							<a href="manage.php?do=appsedit&id=<?php echo $row['id']; ?>" class="secondaryContent">Edit</a>
							<a href="<?php if ($settings) echo 'manage.php?do=appsettings&settings='.$row['folder']; else echo 'javascript:;'; ?>" class="secondaryContent <?php if (!$settings) echo "no-settings"; ?>">Settings</a>
							<a href="javascript:;" <?php if ($row['version']!=$current_version) echo "id='itip_".$row['id']."'"; ?> class="secondaryContent version_link <?php if ($row['version']!=$current_version) echo "red"; ?>">v<?php echo $row['version']; ?></a>
							<a href="manage.php?do=appsettings&activate=<?php echo $row['active']; ?>&id=<?php echo $row['id']; ?>" title="<?php if ($row['active']==1) echo "Deactivate"; else echo "Activate"; ?>" class="secondaryContent <?php if ($row['active']==1) echo "deactivate"; else echo "activate"; ?>"><span>Deactivate</span></a>
							<h4>
								<a href="manage.php?do=appsedit&id=<?php echo $row['id']; ?>">
									<img border="0" src="../applications/<?php echo $row['folder']; ?>/images/<?php echo $row['icon']; ?>" style="width: 12px; height: 13px; position:relative; top: 2px" />&nbsp;&nbsp;&nbsp;<?php echo $row['name']; ?>
								</a>
							</h4>
						</li>
<?php
			}
		}
		else
		{
?>
						<li class="listItem">
							<h4>
								<a href="#">
									No Installed Bar Links
								</a>
							</h4>
						</li>
<?php
		}
?>
					</ol>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Manage</div> 
				<div class="module_content">
					<div class="subtitle">Uninstalled Applications</div>
					<table cellspacing="0" cellpadding="0" class="table_table">
						<thead>
							<tr>
								<th>Folder Name</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
<?php
		$folders = get_folders(AC_FOLDER_APPLICATIONS);
		$no_installed = true;
		
		if (!empty($folders)) 
		{
			foreach ($folders as $folder) 
			{
				if (!in_array($folder['name'], $apps_array)) 
				{
					$no_installed = false;
?>
							<tr>
								<td><?php echo $folder['name']; ?></td>
								<td>
<?php
					if (file_exists(dirname(dirname(dirname(__FILE__))). DIRECTORY_SEPARATOR . AC_FOLDER_APPLICATIONS . DIRECTORY_SEPARATOR . $folder['name'] . DIRECTORY_SEPARATOR . "install_config.php")) 
	{
?>
									<a href="manage.php?do=appsettings&f=<?php echo $folder['name']; ?>">Install</a>
<?php
					} 
					else 
					{
?>
									Install File Not Found
<?php
					}
?>
								</td>
							</tr>
<?php
				}
			}
		}
		
		if ($no_installed) 
		{
?>
							<tr>
								<td colspan="2">No Uninstalled Applications</td>
							</tr>
<?php
		}
?>
						</tbody>
					</table>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Application Settings</div> 
				<div class="module_content">
					<form method="post" id="app_settings_submit" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="applications_guests">
											<input type="checkbox" id="applications_guests" name="applications_guests" <?php if($applications_guests == 1) echo 'checked="checked"'; ?> value="1" />
											Show Applications to Guests
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will enable the applications to be shown to users that are not logged in if the bar is enabled to show to guests. Note: You can individually enable/disable guest visibility by editing an application.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="hide_applications_menu">
											<input type="checkbox" id="hide_applications_menu" name="hide_applications_menu" <?php if($hide_applications_menu == 1) echo 'checked="checked"'; ?> value="1" />
											Disable Applications Menu
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will disable the applications menu that lists each application.  All applications will now be bookmarked and displayed on the bar.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></td>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['app_settings_submit'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="application_settings_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>

<?php
	if ($do == "appsedit") 
	{
		if (!empty($msg)) 
		{
?>
					<a href="manage.php?do=appsettings">Click here to go back to applications management</a>
<?php
		} 
		else 
		{
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_applications 
				WHERE id = '" . $db->escape_string(get_var('id')) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				
				if (empty($row['bar_width']))
				{
					$row['bar_width'] = 16;
				}
?>
					<script type="text/javascript">
						$(document).ready(function() {
							$('#app_width').slider({
								value: <?php echo $row['width']; ?>,
								min: 0,
								max: 1000,
								step: 1,
								slide: function ( event, ui ) {
									$('#app_width_amt').val( ui.value );
									$('#app_width_amt2').html( ui.value );
								}
							});
							$('#app_height').slider({
								value: <?php echo $row['height']; ?>,
								min: 0,
								max: 1000,
								step: 1,
								slide: function ( event, ui ) {
									$('#app_height_amt').val( ui.value );
									$('#app_height_amt2').html( ui.value );
								}
							});
							$('#bar_width').slider({
								value: <?php echo $row['bar_width']; ?>,
								min: 16,
								max: 200,
								step: 1,
								slide: function ( event, ui ) {
									$('#bar_width_amt').val( ui.value );
									$('#bar_width_amt2').html( ui.value );
								}
							});
						});
					</script>
					<div class="subtitle">Edit Application</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="app_name">Application Name</label>
							</dt>
							<dd>
								<input type="text" id="app_name" class="selectionText" name="app_name" value="<?php echo $row['name']; ?>" />
								<p class="explain">
									A name for the application. This will be display in the tooltip and title bar for the application.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="app_folder">Application Folder</label>
							</dt>
							<dd>
								<input type="text" id="app_folder" class="selectionText" name="app_folder" value="<?php echo $row['folder']; ?>" />
								<p class="explain">
									The folder location of the application. You should not have to change this.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="app_icon">Application Icon</label>
							</dt>
							<dd>
								<input type="text" id="app_icon" class="selectionText" name="app_icon" value="<?php echo $row['icon']; ?>" />
								<p class="explain">
									The filename of the icon in the application's image folder. Ex: Enter icon.gif for applications/bloons/images/icon.gif
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="app_url">Application URL</label>
							</dt>
							<dd>
								<input type="text" id="app_url" class="selectionText" name="app_url" value="<?php echo $row['link']; ?>" />
								<p class="explain">
									Optional - Sets the URL to open when the application is clicked. Leave blank if application is a popout.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>
								<label for="app_width">Popup Width</label>
							</dt>
							<dd>
								<div id="app_width" class="slider"></div><div id="app_width_amt2" class="slider-number"><?php echo $row['width']; ?></div>
								<input type="hidden" id="app_width_amt" name="app_width" value="<?php echo $row['width']; ?>" />
								<p class="explain">
									The width, in pixels, of the application's popout window.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="app_height">Popup Height</label>
							</dt>
							<dd>
								<div id="app_height" class="slider"></div><div id="app_height_amt2" class="slider-number"><?php echo $row['height']; ?></div>
								<input type="hidden" id="app_height_amt" name="app_height" value="<?php echo $row['height']; ?>" />
								<p class="explain">
									The height, in pixels, of the application's popout window.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="bar_width">Application Button Width</label>
							</dt>
							<dd>
								<div id="bar_width" class="slider"></div><div id="bar_width_amt2" class="slider-number"><?php echo $row['bar_width']; ?></div>
								<input type="hidden" id="bar_width_amt" name="bar_width" value="<?php echo $row['bar_width']; ?>" />
								<p class="explain">
									Optional - Sets the width of the application button on the bar to allow for text.  Enter 16 to show only the icon.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="bar_name">Application Button Text</label>
							</dt>
							<dd>
								<input type="text" id="bar_name" class="selectionText" name="bar_name" value="<?php echo $row['bar_name']; ?>" />
								<p class="explain">
									Optional - Sets text to display on the button to the right of the icon. The tab width must be set wide enough for the text to show.
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
										<label for="dont_reload">
											<input type="checkbox" id="dont_reload" name="dont_reload" <?php if($row['dont_reload'] == 1) echo 'checked="checked"'; ?> value="1" />
											Keep Open on Close
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will keep the application open when it is closed.  Make sure you are positive this is a good idea before checking it.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="default_bookmark">
											<input type="checkbox" id="default_bookmark" name="default_bookmark" <?php if($row['default_bookmark'] == 1) echo 'checked="checked"'; ?> value="1" />
											Default as Bookmark
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will make the application a bookmark by default.  Otherwise, users will have to click the applications menu to see it.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="show_to_guests">
											<input type="checkbox" id="show_to_guests" name="show_to_guests" <?php if($row['show_to_guests'] == 1) echo 'checked="checked"'; ?> value="1" />
											Show to Guests
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will show this application to guests or users that are not logged in.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>
								<label for="app_update_url">Update URL</label>
							</dt>
							<dd>
								<input type="text" id="app_update_url" class="selectionText" name="app_update_url" value="<?php echo $row['update_link']; ?>" />
								<p class="explain">
									This URL should display only the application's current version. It is used to check whether the application is up-to-date. Leave blank if you don't want to check for updates.
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
								<input type="hidden" name="app_id" value="<?php echo $row['id']; ?>" />
								<input type="hidden" name="app_edit_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
			} 
			else 
			{
?>
				This application does not exist.
<?php
			}
		}
	}
?>

<?php
	if ($do == "chatroomsettings") 
	{
?>
					<div class="subtitle">Installed Chat Rooms</div>
					<h2 class="subHeading">Chat Rooms</h2>
					<ol class="scrollable">
<?php
		$theme_array = array();
		
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_chatroom_rooms 
			ORDER BY id ASC
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
			
			$image = "public";
			if ($row['type'] == 2)
			{
				$image = "password";
			}
			else if ($row['type'] == 3)
			{
				$image = "admin";
			}
?>
						<li class="listItem">
							<a href="manage.php?do=chatroomsettings&delete=<?php echo $row['id']; ?>" title="Delete" class="secondaryContent delete"><span>Delete</span></a>
							<a href="manage.php?do=chatroomedit&id=<?php echo $row['id']; ?>" class="secondaryContent">Edit</a>
							<a href="manage.php?do=chatroomlogs&id=<?php echo $row['id']; ?>" class="secondaryContent">Logs</a>
							<h4>
								<a href="manage.php?do=chatroomedit&id=<?php echo $row['id']; ?>">
									<img border="0" src="./images/img-<?php echo $image; ?>.png" style="position:relative; top: 2px" />&nbsp;&nbsp;&nbsp;<?php echo $row['name']; ?>
								</a>
							</h4>
						</li>
<?php
			}
		} 
		else 
		{
?>
						<li class="listItem">
							<h4>
								<a href="#">
									No Installed Chat Rooms
								</a>
							</h4>
						</li>
<?php
		}
?>
					</ol>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Add Chat Room</div> 
				<div class="module_content">
					<form method="post" id="add-chatroom" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Add Chat Room</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="add_chatroom_name">Chat Room Name</label>
							</dt>
							<dd>
								<input type="text" id="add_chatroom_name" class="selectionText" name="add_chatroom_name" value="<?php if (var_check('add_chatroom_name')) echo get_var('add_chatroom_name'); ?>" />
								<p class="explain">
									Enter a name for this chatroom.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="add_chatroom_desc">Chat Room Description</label>
							</dt>
							<dd>
								<input maxlength="100" type="text" id="add_chatroom_desc" class="selectionText" name="add_chatroom_desc" value="<?php if (var_check('add_chatroom_desc')) echo get_var('add_chatroom_desc'); ?>" />
								<p class="explain">
									Enter a short description for this chatroom.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="add_chatroom_welcome_msg">Welcome Message</label>
							</dt>
							<dd>
								<input maxlength="255" type="text" id="add_chatroom_welcome_msg" class="selectionText" name="add_chatroom_welcome_msg" value="<?php if (var_check('add_chatroom_welcome_msg')) echo get_var('add_chatroom_welcome_msg'); ?>" />
								<p class="explain">
									This message will display each time a user enters this chat room.  This also accepts links and HTML, and it can be left blank.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="add_chatroom_img">Chat Room Icon</label>
							</dt>
							<dd>
								<ul>
									<li>
										<label for="add_chatroom_img" style="position:relative">
											<input type="text" id="add_chatroom_img" class="selectionText" name="add_chatroom_img" value="<?php if(var_check('add_chatroom_img')) echo get_var('add_chatroom_img'); ?>" />
											<style>
												.chatroom-icon-select img{width:30px;height:30px;cursor:pointer}
												.chatroom-icon-select div{padding:5px;float:left;}
											</style>
											<script type="text/javascript">
												$(document).ready(function() {
													$(".chatroom-icon-select").bind('mousewheel DOMMouseScroll', function (e) {
														var e0 = e.originalEvent,
															delta = e0.wheelDelta || -e0.detail;
														this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
														e.preventDefault();
													});
													$("#add_chatroom_img").focusin(function() {
														$(".chatroom-icon-select").show("fast");
													});
													var resultsSelected = false;
													$(".chatroom-icon-select").hover(
														function () { resultsSelected = true; },
														function () { resultsSelected = false; }
													);
													$("#add_chatroom_img").blur(function () {
														if (!resultsSelected && !$("#add_chatroom_img").is(":focus")) {  
															$(".chatroom-icon-select").hide("fast"); 
														}
													});
													$(".chatroom-icon-select img").click(function() {
														$("#add_chatroom_img").val('chatroom_'+$(this).attr('alt')+'.png');
													});
													$('body').on('keydown', '#add_chatroom_img', function(e) {
														if (e.which == 9) {
															$(".chatroom-icon-select").hide("fast"); 
														}
													});
												});
											</script>
											<div class="chatroom-icon-select" style="position:absolute; top:22px; left:0; background:#fff; width:300px; height:150px; overflow-y:auto; padding:10px; border:1px solid #cecece; z-index:500; -webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;display:none">
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_back.png" alt="back" title="Back" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_bag.png" alt="bag" title="Bag" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_brightness.png" alt="brightness" title="Brightness" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_camera.png" alt="camera" title="Camera" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_chat.png" alt="chat" title="Chat" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_clock.png" alt="clock" title="Clock" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_cloud.png" alt="cloud" title="Cloud" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_coffee.png" alt="coffee" title="Coffee" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_documents.png" alt="documents" title="Documents" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_folder.png" alt="folder" title="Folder" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_forward.png" alt="forward" title="Forward" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_headphones.png" alt="headphones" title="Headphones" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_home.png" alt="home" title="Home" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_infinity.png" alt="infinity" title="Infinity" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_list.png" alt="list" title="List" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_location.png" alt="location" title="Location" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_lock.png" alt="lock" title="Lock" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_mail.png" alt="mail" title="Mail" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_marker.png" alt="marker" title="Marker" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_mic.png" alt="mic" title="Mic" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_monitor.png" alt="monitor" title="Monitor" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_mouse.png" alt="mouse" title="Mouse" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_music.png" alt="music" title="Music" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_paperclip.png" alt="paperclip" title="Paperclip" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_pause.png" alt="pause" title="Pause" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_phone.png" alt="phone" title="Phone" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_play.png" alt="play" title="Play" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_power.png" alt="power" title="Power" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_printer.png" alt="printer" title="Printer" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_refresh.png" alt="refresh" title="Refresh" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_rewind.png" alt="rewind" title="Rewind" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_search.png" alt="search" title="Search" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_sound.png" alt="sound" title="Sound" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_swap.png" alt="swap" title="Swap" /></div>
												<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_users.png" alt="users" title="Users" /></div>
											</div>
										</label>
									</li>
									<li>
										or
									</li>
									<li>
										<input type="file" name="add_chatroom_img_upload" />
									</li>
								</ul>
								<p class="explain">
									The filename of the icon in the themes's image icon folder. Ex: Enter mail.gif for themes/{theme name}/images/icons/mail.gif.  You can also upload an image.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Chat Room Type</label></dt>
							<dd>
								<ul>
									<li>
										<label for="chatroom_type_1">
											<input type="radio" name="add_chatroom_type" value="1" id="chatroom_type_1" checked="checked" /> Public
										</label>
									</li>
									<li>
										<label for="chatroom_type_2">
											<input type="radio" name="add_chatroom_type" value="2" id="chatroom_type_2" /> Password Protected
										</label>
									</li>
									<li>
										<label for="chatroom_type_3">
											<input type="radio" name="add_chatroom_type" value="3" id="chatroom_type_3" /> Administrators Only
										</label>
									</li>
								</ul>
								<p class="explain">
									Specify which type of Chatroom you would like.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="add_chatroom_password">Chat Room Password</label>
							</dt>
							<dd>
								<input type="text" id="add_chatroom_password" class="selectionText" name="add_chatroom_password" maxlength="25" value="<?php if(var_check('add_chatroom_password')) echo get_var('add_chatroom_password'); ?>" />
								<p class="explain">
									If the chatroom is password protected, enter a password.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="add_chatroom_group">Disallowed Group(s)</label>
							</dt>
							<dd>
								<select multiple="multiple" id="add_chatroom_group" name="add_chatroom_group[]" style="width: 440px; height: 125px;">
									<option value="acg">ArrowChat Guests (acg)</option>
								<?php	
										$groups_list = get_all_groups();

										if (!is_null($groups_list)) 
										{
											foreach ($groups_list as $val)
											{
								?>
									<option value="<?php echo $val[0]; ?>"><?php echo $val[1]; ?> (<?php echo $val[0]; ?>)</option>
								<?php
											}
										}
								?>
								</select>
								<p class="explain">
									Select the group(s) that should not have access to the room.  Hold Ctrl for multiple selections.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="add_chatroom_length">Chat Room Length</label>
							</dt>
							<dd>
								<input type="text" id="add_chatroom_length" class="selectionText" name="add_chatroom_length" maxlength="10" value="<?php if (var_check('add_chatroom_length')) echo get_var('add_chatroom_length'); else echo 0; ?>" />
								<p class="explain">
									The time, in minutes, that the chat room will last before not showing up anymore.  <b>Enter 0 for a chat room that lasts until deleted.</b>
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="chatroom_max_users">Chat Room Max Users</label>
							</dt>
							<dd>
								<input type="text" id="chatroom_max_users" class="selectionText" name="chatroom_max_users" maxlength="10" value="<?php if (var_check('chatroom_max_users')) echo get_var('chatroom_max_users'); else echo 0; ?>" />
								<p class="explain">
									The number of users that can be in a chat room before no one else is allowed in.  <b>Enter 0 for an unlimited number of users.</b>
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="limit_message_num">Messaging Flood</label>
							</dt>
							<dd>
								Users can send 
								<select type="text" id="limit_message_num" class="selectionText" name="limit_message_num" style="width:60px">
									<option value="1" <?php if (get_var('limit_message_num') == 1) echo 'selected="selected"'; ?>>1</option>
									<option value="2" <?php if (get_var('limit_message_num') == 2) echo 'selected="selected"'; ?>>2</option>
									<option value="3" <?php if (get_var('limit_message_num') == 3 OR !get_var('limit_message_num')) echo 'selected="selected"'; ?>>3</option>
									<option value="4" <?php if (get_var('limit_message_num') == 4) echo 'selected="selected"'; ?>>4</option>
									<option value="5" <?php if (get_var('limit_message_num') == 5) echo 'selected="selected"'; ?>>5</option>
									<option value="10" <?php if (get_var('limit_message_num') == 10) echo 'selected="selected"'; ?>>10</option>
									<option value="15" <?php if (get_var('limit_message_num') == 15) echo 'selected="selected"'; ?>>15</option>
									<option value="20" <?php if (get_var('limit_message_num') == 20) echo 'selected="selected"'; ?>>20</option>
								</select>
								messages every 
								<select type="text" id="limit_seconds_num" class="selectionText" name="limit_seconds_num" style="width:60px">
									<option value="1" <?php if (get_var('limit_seconds_num') == 1) echo 'selected="selected"'; ?>>1</option>
									<option value="2" <?php if (get_var('limit_seconds_num') == 2) echo 'selected="selected"'; ?>>2</option>
									<option value="3" <?php if (get_var('limit_seconds_num') == 3) echo 'selected="selected"'; ?>>3</option>
									<option value="4" <?php if (get_var('limit_seconds_num') == 4) echo 'selected="selected"'; ?>>4</option>
									<option value="5" <?php if (get_var('limit_seconds_num') == 5) echo 'selected="selected"'; ?>>5</option>
									<option value="10" <?php if (get_var('limit_seconds_num') == 10 OR !get_var('limit_seconds_num')) echo 'selected="selected"'; ?>>10</option>
									<option value="15" <?php if (get_var('limit_seconds_num') == 15) echo 'selected="selected"'; ?>>15</option>
									<option value="20" <?php if (get_var('limit_seconds_num') == 20) echo 'selected="selected"'; ?>>20</option>
									<option value="25" <?php if (get_var('limit_seconds_num') == 25) echo 'selected="selected"'; ?>>25</option>
									<option value="30" <?php if (get_var('limit_seconds_num') == 30) echo 'selected="selected"'; ?>>30</option>
									<option value="40" <?php if (get_var('limit_seconds_num') == 40) echo 'selected="selected"'; ?>>40</option>
									<option value="50" <?php if (get_var('limit_seconds_num') == 50) echo 'selected="selected"'; ?>>50</option>
									<option value="60" <?php if (get_var('limit_seconds_num') == 60) echo 'selected="selected"'; ?>>60</option>
									<option value="90" <?php if (get_var('limit_seconds_num') == 90) echo 'selected="selected"'; ?>>90</option>
									<option value="120" <?php if (get_var('limit_seconds_num') == 120) echo 'selected="selected"'; ?>>120</option>
								</select>
								seconds.
								<p class="explain">
									Select how much you want to limit messages in this chat room.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['add-chatroom'].submit(); return false">
									<span>Add Chat Room</span>
								</a>
								<input type="hidden" name="add_chatroom_submit" value="1" />
							</div>
						</dd>
					</dl>
					</form>
				</div>
			</div>
			<script type="text/javascript">
				$(document).ready(function() {
					$('#user_chatrooms_flood').slider({
						value: <?php echo $user_chatrooms_flood; ?>,
						min: 0,
						max: 60,
						step: 1,
						slide: function ( event, ui ) {
							$('#user_chatrooms_flood_amt').val( ui.value );
							$('#user_chatrooms_flood_amt2').html( ui.value );
						}
					});
				});
			</script>
			<div class="title_bg"> 
				<div class="title">Chat Room Settings</div> 
				<div class="module_content">
					<form method="post" id="chatroom-settings" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Chat Room Settings</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="user_chatrooms">
											<input type="checkbox" id="user_chatrooms" name="user_chatrooms" <?php if($user_chatrooms == 1) echo 'checked="checked"'; ?> value="1" />
											User Created Chat Rooms
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will allow users to create chat rooms.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="user_chatrooms_length">User Chat Rooms Length</label>
							</dt>
							<dd>
								<input type="text" id="user_chatrooms_length" class="selectionText" name="user_chatrooms_length" value="<?php echo $user_chatrooms_length; ?>" />
								<p class="explain">
									The time, in minutes, that a user created chatroom will last. Enter 0 to have them last indefinitely.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="user_chatrooms_flood">User Chat Rooms Flood</label>
							</dt>
							<dd>
								<div id="user_chatrooms_flood" class="slider"></div><div id="user_chatrooms_flood_amt2" class="slider-number"><?php echo $user_chatrooms_flood; ?></div>
								<input type="hidden" id="user_chatrooms_flood_amt" name="user_chatrooms_flood" value="<?php echo $user_chatrooms_flood; ?>" />
								<p class="explain">
									The time, in minutes, that a user must wait before creating another chat room.
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>
								<label for="chatroom_history_length">Chat Room History Length</label>
							</dt>
							<dd>
								<input type="text" id="chatroom_history_length" class="selectionText" name="chatroom_history_length" value="<?php echo $chatroom_history_length; ?>" />
								<p class="explain">
									Enter the time, in minutes, of how far back a chat room's message history will be pulled up when entering the room.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="chatroom_message_length">Chat Room Maximum Message Length</label>
							</dt>
							<dd>
								<input type="text" id="chatroom_message_length" class="selectionText" name="chatroom_message_length" value="<?php echo $chatroom_message_length; ?>" />
								<p class="explain">
									Enter the maximum amount of characters that can be sent in a single chat room message.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="chatroom_auto_join">Chat Room Auto Join</label>
							</dt>
							<dd>
								<select name="chatroom_auto_join" style="width: 432px;">
									<option value="0" <?php if (empty($chatroom_auto_join)) echo 'selected="selected"'; ?>>None</option>
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
											if ($row['type'] == 1)
											{
								?>
									<option value="<?php echo $row['id']; ?>" <?php if ($chatroom_auto_join == $row['id']) echo 'selected="selected"'; ?>><?php echo $row['name']; ?></option>
								<?php
											}
										}
									}
								?>
								</select>
							<p class="explain">
								This is the chat room you would like ArrowChat to automatically join upon loading (until the user leaves it). Password protected and admin rooms cannot be auto joined.
							</p>
						</dd>
					</dl>
					<dl class="selectionBox">
						<dt></dt>
						<dd>
							<ul>
								<li>
									<label for="chatroom_default_names">
										<input type="checkbox" id="chatroom_default_names" name="chatroom_default_names" <?php if($chatroom_default_names == 1) echo 'checked="checked"'; ?> value="1" />
										Default Chat Rooms to Always Show Usernames
									</label>
								</li>
							</ul>
							<p class="explain">
								Checking this will change the default of only showing user's avatars in chat rooms to showing their names as well. The user will still be able to change this setting.
							</p>
						</dd>
					</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['chatroom-settings'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="chatroom_settings_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>

<?php
	if ($do == "chatroomlogs") 
	{
?>
					<div class="subtitle">Chat Room Logs</div>
					<div class="subExplain"><i>Below is the entire chat room history for this chat room.</i></div>
					<div style="margin: 10px 0 10px; padding: 10px;height: 500px; overflow: auto;" id="chatboxes">
<?php
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_chatroom_messages 
			WHERE chatroom_id = '" . $db->escape_string(get_var('id')) . "' 
			ORDER BY id ASC
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
?>					
						<div style="padding:0px 10px; 5px; margin-bottom: 5px; float: left; background-color: #fff; width: 470px;"><a href="users.php?do=logs&id=<?php echo $row['user_id']; ?>"><b><?php echo $row['username']; ?></b></a>: <?php echo $row['message']; ?></div><div style="padding:0px 10px; 5px;float: right; background-color: #fff; width: 150px;"><?php echo date('M j, Y g:i a', $row['sent']); ?></div><div class="clear"></div>
<?php
				}
			} 
			else 
			{
?>
				This chatroom has no messages.
<?php
			}
?>
					</div>
			<script type="text/javascript">
				var objDiv = document.getElementById("chatboxes");
				objDiv.scrollTop = objDiv.scrollHeight;
			</script>
<?php
	}
?>

<?php
	if ($do == "chatroomedit") 
	{
		if (!empty($msg)) 
		{
?>
					<a href="manage.php?do=chatroomsettings">Click here to go back to chatroom settings</a>
<?php
		} 
		else 
		{
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_chatroom_rooms 
				WHERE id = '" . $db->escape_string(get_var('id')) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
?>
					<div class="subtitle">Edit Chat Room</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="edit_chatroom_name">Chat Room Name</label>
							</dt>
							<dd>
								<input type="text" id="edit_chatroom_name" class="selectionText" name="edit_chatroom_name" value="<?php echo $row['name']; ?>" />
								<p class="explain">
									Enter a name for this chatroom.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_chatroom_desc">Chat Room Description</label>
							</dt>
							<dd>
								<input maxlength="100" type="text" id="edit_chatroom_desc" class="selectionText" name="edit_chatroom_desc" value="<?php echo $row['description']; ?>" />
								<p class="explain">
									Enter a short description for this chat room.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_chatroom_welcome_msg">Welcome Message</label>
							</dt>
							<dd>
								<input maxlength="255" type="text" id="edit_chatroom_welcome_msg" class="selectionText" name="edit_chatroom_welcome_msg" value="<?php echo $row['welcome_message']; ?>" />
								<p class="explain">
									This message will display each time a user enters this chat room.  This also accepts links and HTML, and it can be left blank.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_chatroom_img">Chat Room Icon</label>
							</dt>
							<dd>
								<label for="edit_chatroom_img" style="position:relative">
									<input type="text" id="edit_chatroom_img" class="selectionText" name="edit_chatroom_img" value="<?php echo $row['image']; ?>" />
									<style>
										.chatroom-icon-select img{width:30px;height:30px;cursor:pointer}
										.chatroom-icon-select div{padding:5px;float:left;}
									</style>
									<script type="text/javascript">
										$(document).ready(function() {
											$(".chatroom-icon-select").bind('mousewheel DOMMouseScroll', function (e) {
												var e0 = e.originalEvent,
													delta = e0.wheelDelta || -e0.detail;
												this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
												e.preventDefault();
											});
											$("#edit_chatroom_img").focusin(function() {
												$(".chatroom-icon-select").show("fast");
											});
											var resultsSelected2 = false;
											$(".chatroom-icon-select").hover(
												function () { resultsSelected2 = true; },
												function () { resultsSelected2 = false; }
											);
											$("#edit_chatroom_img").blur(function () {
												if (!resultsSelected2 && !$("#edit_chatroom_img").is(":focus")) {  
													$(".chatroom-icon-select").hide("fast"); 
												}
											});
											$(".chatroom-icon-select img").click(function() {
												$("#edit_chatroom_img").val('chatroom_'+$(this).attr('alt')+'.png');
											});
											$('body').on('keydown', '#edit_chatroom_img', function(e) {
												if (e.which == 9) {
													$(".chatroom-icon-select").hide("fast"); 
												}
											});
										});
									</script>
									<div class="chatroom-icon-select" style="position:absolute; top:22px; left:0; background:#fff; width:300px; height:150px; overflow-y:auto; padding:10px; border:1px solid #cecece; z-index:500; -webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;display:none">
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_back.png" alt="back" title="Back" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_bag.png" alt="bag" title="Bag" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_brightness.png" alt="brightness" title="Brightness" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_camera.png" alt="camera" title="Camera" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_chat.png" alt="chat" title="Chat" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_clock.png" alt="clock" title="Clock" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_cloud.png" alt="cloud" title="Cloud" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_coffee.png" alt="coffee" title="Coffee" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_documents.png" alt="documents" title="Documents" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_folder.png" alt="folder" title="Folder" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_forward.png" alt="forward" title="Forward" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_headphones.png" alt="headphones" title="Headphones" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_home.png" alt="home" title="Home" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_infinity.png" alt="infinity" title="Infinity" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_list.png" alt="list" title="List" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_location.png" alt="location" title="Location" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_lock.png" alt="lock" title="Lock" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_mail.png" alt="mail" title="Mail" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_marker.png" alt="marker" title="Marker" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_mic.png" alt="mic" title="Mic" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_monitor.png" alt="monitor" title="Monitor" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_mouse.png" alt="mouse" title="Mouse" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_music.png" alt="music" title="Music" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_paperclip.png" alt="paperclip" title="Paperclip" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_pause.png" alt="pause" title="Pause" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_phone.png" alt="phone" title="Phone" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_play.png" alt="play" title="Play" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_power.png" alt="power" title="Power" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_printer.png" alt="printer" title="Printer" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_refresh.png" alt="refresh" title="Refresh" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_rewind.png" alt="rewind" title="Rewind" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_search.png" alt="search" title="Search" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_sound.png" alt="sound" title="Sound" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_swap.png" alt="swap" title="Swap" /></div>
										<div><img src="../themes/<?php echo $theme; ?>/images/icons/chatroom_users.png" alt="users" title="Users" /></div>
									</div>
								</label>
								<p class="explain">
									The filename of the icon in the themes's image icon folder. Ex: Enter mail.gif for themes/{theme name}/images/icons/mail.gif.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt><label>Chat Room Type</label></dt>
							<dd>
								<ul>
									<li>
										<label for="chatroom_type_1">
											<input type="radio" name="edit_chatroom_type" value="1" <?php if($row['type']==1) echo "checked=\"checked\""; ?> id="chatroom_type_1" /> Public
										</label>
									</li>
									<li>
										<label for="chatroom_type_2">
											<input type="radio" name="edit_chatroom_type" value="2" <?php if($row['type']==2) echo "checked=\"checked\""; ?> id="chatroom_type_2" /> Password Protected
										</label>
									</li>
									<li>
										<label for="chatroom_type_3">
											<input type="radio" name="edit_chatroom_type" value="3" <?php if($row['type']==3) echo "checked=\"checked\""; ?> id="chatroom_type_3" /> Administrators Only
										</label>
									</li>
								</ul>
								<p class="explain">
									Specify which type of Chatroom you would like.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_chatroom_password">Chat Room Password</label>
							</dt>
							<dd>
								<input type="text" id="edit_chatroom_password" class="selectionText" name="edit_chatroom_password" maxlength="25" value="<?php echo $row['password']; ?>" />
								<p class="explain">
									If the chatroom is password protected, enter a password.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_chatroom_group">Disallowed Group(s)</label>
							</dt>
							<dd>
								<?php
									$disallowed_groups = unserialize($row['disallowed_groups']);
								?>
								<select multiple="multiple" id="edit_chatroom_group" name="edit_chatroom_group[]" style="width: 440px; height: 125px;">
									<option value="acg" <?php if (in_array('acg', $disallowed_groups)) echo 'selected="selected"'; ?>>ArrowChat Guests (acg)</option>
								<?php	
										$groups_list = get_all_groups();

										if (!is_null($groups_list)) 
										{
											foreach ($groups_list as $val)
											{
								?>
									<option value="<?php echo $val[0]; ?>" <?php if (in_array($val[0], $disallowed_groups)) echo 'selected="selected"'; ?>><?php echo $val[1]; ?> (<?php echo $val[0]; ?>)</option>
								<?php
											}
										}
								?>
								</select>
								<p class="explain">
									Select the group(s) that should not have access to the room.  Hold Ctrl for multiple selections.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_chatroom_length">Chat Room Length</label>
							</dt>
							<dd>
								<input type="text" id="edit_chatroom_length" class="selectionText" name="edit_chatroom_length" maxlength="10" value="<?php echo $row['length']; ?>" />
								<p class="explain">
									The time, in minutes, that the chat room will last before not showing up anymore.  <b>Enter 0 for a chat room that lasts until deleted.</b>
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="chatroom_max_users">Chat Room Max Users</label>
							</dt>
							<dd>
								<input type="text" id="chatroom_max_users" class="selectionText" name="chatroom_max_users" maxlength="10" value="<?php echo $row['max_users']; ?>" />
								<p class="explain">
									The number of users that can be in a chat room before no one else is allowed in. <b>Enter 0 for an unlimited number of users.</b>
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="limit_message_num">Messaging Flood</label>
							</dt>
							<dd>
								Users can send 
								<select type="text" id="limit_message_num" class="selectionText" name="limit_message_num" style="width:60px">
									<option value="1" <?php if ($row['limit_message_num'] == 1) echo 'selected="selected"'; ?>>1</option>
									<option value="2" <?php if ($row['limit_message_num'] == 2) echo 'selected="selected"'; ?>>2</option>
									<option value="3" <?php if ($row['limit_message_num'] == 3) echo 'selected="selected"'; ?>>3</option>
									<option value="4" <?php if ($row['limit_message_num'] == 4) echo 'selected="selected"'; ?>>4</option>
									<option value="5" <?php if ($row['limit_message_num'] == 5) echo 'selected="selected"'; ?>>5</option>
									<option value="10" <?php if ($row['limit_message_num'] == 10) echo 'selected="selected"'; ?>>10</option>
									<option value="15" <?php if ($row['limit_message_num'] == 15) echo 'selected="selected"'; ?>>15</option>
									<option value="20" <?php if ($row['limit_message_num'] == 20) echo 'selected="selected"'; ?>>20</option>
								</select>
								messages every 
								<select type="text" id="limit_seconds_num" class="selectionText" name="limit_seconds_num" style="width:60px">
									<option value="1" <?php if ($row['limit_seconds_num'] == 1) echo 'selected="selected"'; ?>>1</option>
									<option value="2" <?php if ($row['limit_seconds_num'] == 2) echo 'selected="selected"'; ?>>2</option>
									<option value="3" <?php if ($row['limit_seconds_num'] == 3) echo 'selected="selected"'; ?>>3</option>
									<option value="4" <?php if ($row['limit_seconds_num'] == 4) echo 'selected="selected"'; ?>>4</option>
									<option value="5" <?php if ($row['limit_seconds_num'] == 5) echo 'selected="selected"'; ?>>5</option>
									<option value="10" <?php if ($row['limit_seconds_num'] == 10) echo 'selected="selected"'; ?>>10</option>
									<option value="15" <?php if ($row['limit_seconds_num'] == 15) echo 'selected="selected"'; ?>>15</option>
									<option value="20" <?php if ($row['limit_seconds_num'] == 20) echo 'selected="selected"'; ?>>20</option>
									<option value="25" <?php if ($row['limit_seconds_num'] == 25) echo 'selected="selected"'; ?>>25</option>
									<option value="30" <?php if ($row['limit_seconds_num'] == 30) echo 'selected="selected"'; ?>>30</option>
									<option value="40" <?php if ($row['limit_seconds_num'] == 40) echo 'selected="selected"'; ?>>40</option>
									<option value="50" <?php if ($row['limit_seconds_num'] == 50) echo 'selected="selected"'; ?>>50</option>
									<option value="60" <?php if ($row['limit_seconds_num'] == 60) echo 'selected="selected"'; ?>>60</option>
									<option value="90" <?php if ($row['limit_seconds_num'] == 90) echo 'selected="selected"'; ?>>90</option>
									<option value="120" <?php if ($row['limit_seconds_num'] == 120) echo 'selected="selected"'; ?>>120</option>
								</select>
								seconds.
								<p class="explain">
									Select how much you want to limit messages in this chat room.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="unban_username">Remove Bans</label>
							</dt>
							<dd>
								<select multiple="multiple" id="unban_username" name="unban_username[]" style="width: 440px; height: 125px;">
<?php
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_chatroom_banlist 
			WHERE chatroom_id = '" . $db->escape_string(get_var('id')) . "'
				AND ((ban_length * 60) + ban_time > " . time() . " 
					OR ban_length = 0)
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
				if (check_if_guest($row['user_id'])) {
					$banned_username = "Guest " . substr($row['user_id'], 1);
				} else {
					$result2 = $db->execute("
						SELECT " . DB_USERTABLE_NAME . " 
						FROM " . TABLE_PREFIX . DB_USERTABLE . " 
						WHERE " . DB_USERTABLE_USERID . " = '" . $row['user_id'] . "'
					");

					$row2 = $db->fetch_array($result2);
					$banned_username = $row2[DB_USERTABLE_NAME];
				}
?>
									<option value="<?php echo $row['user_id']; ?>"><?php echo $banned_username; ?></option>
<?php
			}
		} 
		else 
		{
?>
									<option value="0">There are no banned users in this chat room</option>
<?php
		}
?>
								</select>
								<p class="explain">
									Select the usernames that you would like to unban. Hold Ctrl to select multiple names.
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
								<input type="hidden" name="chatroom_id" value="<?php echo get_var('id'); ?>" />
								<input type="hidden" name="chatroom_edit_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
			} 
			else 
			{
?>
				This chatroom does not exist.
<?php
			}
		}
	}
?>

<?php
	if ($do == "notificationsettings") 
	{
?>
					<div class="subtitle">Installed Notifications</div>
					<div class="subExplain"><i>Notifications must first be installed correctly to function properly. Please visit http://www.arrowchat.com/support/ for documentation on installation.</i></div>
					<h2 class="subHeading">Notifications</h2>
					<ol class="scrollable">
<?php
		$theme_array = array();
		
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_notifications_markup 
			ORDER BY id ASC
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
?>
						<li class="listItem">
							<a href="manage.php?do=notificationsettings&delete=<?php echo $row['id']; ?>" title="Delete" class="secondaryContent delete"><span>Delete</span></a>
							<a href="manage.php?do=notificationsedit&id=<?php echo $row['id']; ?>" class="secondaryContent">Edit</a>
							<h4>
								<a href="manage.php?do=notificationsedit&id=<?php echo $row['id']; ?>">
									<?php echo $row['name']; ?>
								</a>
							</h4>
						</li>
<?php
			}
		} 
		else 
		{
?>
						<li class="listItem">
							<h4>
								<a href="#">
									No Installed Notifications
								</a>
							</h4>
						</li>
<?php
		}
?>
					</ol>
					</form>

				</div>
			</div>
			<script type="text/javascript">
				$(document).ready(function() {
					$('#author_name').click(function() {
						$('#add_notification_markup').append('{author_name}');
					});
					$('#author_id').click(function() {
						$('#add_notification_markup').append('{author_id}');
					});
					$('#longago').click(function() {
						$('#add_notification_markup').append('{longago}');
					});
					$('#message_time').click(function() {
						$('#add_notification_markup').append('{message_time}');
					});
					$('#misc1').click(function() {
						$('#add_notification_markup').append('{misc1}');
					});
					$('#misc2').click(function() {
						$('#add_notification_markup').append('{misc2}');
					});
					$('#misc3').click(function() {
						$('#add_notification_markup').append('{misc3}');
					});
				});
			</script>
			<div class="title_bg"> 
				<div class="title">Add Notification</div> 
				<div class="module_content">
					<form method="post" id="add-notification" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Add Notification</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="add_notification_name">Notification Name</label>
							</dt>
							<dd>
								<input type="text" id="add_notification_name" class="selectionText" name="add_notification_name" value="<?php if(var_check('add_notification_name')) echo get_var('add_notification_name'); ?>" />
								<p class="explain">
									Enter a name for this notification.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="add_notification_markup">Notification Markup</label>
							</dt>
							<dd>
								<textarea id="add_notification_markup" class="selectionArea" name="add_notification_markup"><?php if(var_check('add_notification_markup')) echo get_var('add_notification_markup'); ?></textarea>
								<p>
									<a id="author_name" title="Returns the author's name" href="javascript:;">{author_name}</a> <a id="author_id" title="Returns the author's ID" href="javascript:;">{author_id}</a> <a id="longago" title="Returns how long ago it was sent. Ex: 2 minutes ago" href="javascript:;">{longago}</a> <a id="message_time" title="Returns when it was sent in unix time" href="javascript:;">{message_time}</a> <a id="misc1" title="Returns the miscellaneous 1 field" href="javascript:;">{misc1}</a> <a id="misc2" title="Returns the miscellaneous 2 field" href="javascript:;">{misc2}</a> <a id="misc3" title="Returns the miscellaneous 3 field" href="javascript:;">{misc3}</a>
								</p>
								<p class="explain">
									The HTML Markup for the notification. Displayed when the user receives a notification.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['add-notification'].submit(); return false">
									<span>Add Notification</span>
								</a>
								<input type="hidden" name="add_notification_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>

<?php
	if ($do == "notificationsedit") 
	{
		if (!empty($msg)) 
		{
?>
					<a href="manage.php?do=notificationsettings">Click here to go back to notification settings</a>
<?php
		} 
		else 
		{
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_notifications_markup 
				WHERE id = '" . $db->escape_string(get_var('id')) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
?>					
					<script type="text/javascript">
						$(document).ready(function() {
							$('#author_name').click(function() {
								$('#edit_notification_markup').append('{author_name}');
							});
							$('#author_id').click(function() {
								$('#edit_notification_markup').append('{author_id}');
							});
							$('#longago').click(function() {
								$('#edit_notification_markup').append('{longago}');
							});
							$('#message_time').click(function() {
								$('#edit_notification_markup').append('{message_time}');
							});
							$('#misc1').click(function() {
								$('#edit_notification_markup').append('{misc1}');
							});
							$('#misc2').click(function() {
								$('#edit_notification_markup').append('{misc2}');
							});
							$('#misc3').click(function() {
								$('#edit_notification_markup').append('{misc3}');
							});
						});
					</script>
					<div class="subtitle">Edit Notification</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="edit_notification_name">Notification Name</label>
							</dt>
							<dd>
								<input type="text" id="edit_notification_name" class="selectionText" name="edit_notification_name" value="<?php echo $row['name']; ?>" />
								<p class="explain">
									Enter a name for this notification.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_notification_name">Notification Number</label>
							</dt>
							<dd>
								<?php echo $row['type']; ?>
								<p class="explain">
									Use this notification number when inserting calls into the database. This will ensure the proper markup is displayed.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="edit_notification_markup">Notification Markup</label>
							</dt>
							<dd>
								<textarea id="edit_notification_markup" class="selectionArea" name="edit_notification_markup"><?php echo $row['markup']; ?></textarea>
								<p>
									<a id="author_name" title="Returns the author's name" href="javascript:;">{author_name}</a> <a id="author_id" title="Returns the author's ID" href="javascript:;">{author_id}</a> <a id="longago" title="Returns how long ago it was sent. Ex: 2 minutes ago" href="javascript:;">{longago}</a> <a id="message_time" title="Returns when it was sent in unix time" href="javascript:;">{message_time}</a> <a id="misc1" title="Returns the miscellaneous 1 field" href="javascript:;">{misc1}</a> <a id="misc2" title="Returns the miscellaneous 2 field" href="javascript:;">{misc2}</a> <a id="misc3" title="Returns the miscellaneous 3 field" href="javascript:;">{misc3}</a>
								</p>
								<p class="explain">
									The HTML Markup for the notification. Displayed when the user receives a notification.
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
								<input type="hidden" name="notification_id" value="<?php echo $row['id']; ?>" />
								<input type="hidden" name="notification_edit_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
			} 
			else 
			{
?>
				This notification does not exist.
<?php
			}
		}
	}
?>

<?php
	if ($do == "traylinks") 
	{
?>
					<div class="subtitle">Installed Bar Links</div>
					<h2 class="subHeading">Bar Links</h2>
					<ol class="scrollable">
<?php
			
		$theme_array = array();
		
		$result = $db->execute("
			SELECT MAX(tray_location)
			FROM arrowchat_trayicons 
			ORDER BY tray_location ASC
		");

		if ($result AND $db->count_select() > 0) 
		{
			$row2 = $db->fetch_array($result);
			$max_number = $row2['MAX(tray_location)'];
		}
		
		$result = $db->execute("
			SELECT *
			FROM arrowchat_trayicons 
			ORDER BY tray_location ASC
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
?>
						<li class="listItem">
							<a href="manage.php?do=traylinks&delete=<?php echo $row['id']; ?>" title="Delete" class="secondaryContent delete"><span>Delete</span></a>
							<a href="manage.php?do=traylinks&move=up&id=<?php echo $row['id']; ?>" title="Move Up" class="secondaryContent moveUp"><span style="<?php if($row['tray_location'] == '1') echo 'background:none !important;' ?>">Move Up</span></a>
							<a href="manage.php?do=traylinks&move=down&id=<?php echo $row['id']; ?>" title="Move Down" class="secondaryContent moveDown"><span style="<?php if($row['tray_location'] == $max_number) echo 'background: none !important;' ?>">Move Down</span></a>
							<a href="manage.php?do=traylinksedit&id=<?php echo $row['id']; ?>" class="secondaryContent">Edit</a>
							<a href="manage.php?do=traylinks&activate=<?php echo $row['active']; ?>&id=<?php echo $row['id']; ?>" title="<?php if ($row['active']==1) echo "Deactivate"; else echo "Activate"; ?>" class="secondaryContent <?php if ($row['active']==1) echo "deactivate"; else echo "activate"; ?>"><span>Deactivate</span></a>
							<h4>
								<a href="manage.php?do=traylinksedit&id=<?php echo $row['id']; ?>">
									<img border="0" src="../themes/new_facebook_full/images/icons/<?php echo $row['icon'] ?>" style="width: 12px; height: 13px; position:relative; top: 2px" />&nbsp;&nbsp;&nbsp;<?php echo $row['name']; ?>
								</a>
							</h4>
						</li>
<?php
			}
		} 
		else 
		{
?>
						<li class="listItem">
							<h4>
								<a href="#">
									No Installed Bar Links
								</a>
							</h4>
						</li>
<?php
		}
?>
					</ol>
					</form>

				</div>
			</div>
			<script type="text/javascript">
				$(document).ready(function() {
					$('#link_tab_width').slider({
						value: <?php if(var_check('link_tab_width')) echo get_var('link_tab_width'); else echo "16"; ?>,
						min: 16,
						max: 200,
						step: 1,
						slide: function ( event, ui ) {
							$('#link_tab_width_amt').val( ui.value );
							$('#link_tab_width_amt2').html( ui.value );
						}
					});
					$('#user_id').click(function() {
						$('#link_url').val($('#link_url').val() + '{USER_ID}');
					});
					$('#user_name').click(function() {
						$('#link_url').val($('#link_url').val() + '{USER_NAME}');
					});
				});
			</script>
			<div class="title_bg"> 
				<div class="title">Add Bar Link</div> 
				<div class="module_content">
					<form method="post" id="add-bar-link" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Add Bar Link</div>
						<dl class="selectionBox">
							<dt>
								<label for="link_name">Link Name</label>
							</dt>
							<dd>
								<input type="text" id="link_name" class="selectionText" name="link_name" value="<?php if(var_check('link_name')) echo get_var('link_name'); ?>" />
								<p class="explain">
									A name for the bar link.  This will also be the tooltip for the link.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_icon">Link Icon Location</label>
							</dt>
							<dd>
								<ul>
									<li>
										<label for="link_icon">
											<input type="text" id="link_icon" class="selectionText" name="link_icon" value="<?php if(var_check('link_icon')) echo get_var('link_icon'); ?>" />
										</label>
									</li>
									<li>
										or
									</li>
									<li>
										<input type="file" name="link_icon_upload" />
									</li>
								</ul>
								<p class="explain">
									The filename of the icon in the themes's image icon folder. Ex: Enter mail.gif for themes/default/images/icons/mail.gif.  You can also upload an image.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_url">Link URL</label>
							</dt>
							<dd>
								<input type="text" id="link_url" class="selectionText" name="link_url" value="<?php if(var_check('link_url')) echo get_var('link_url'); ?>" />
								<p>
									<a id="user_id" title="Returns user's ID" href="javascript:;">{USER_ID}</a> <a id="user_name" title="Returns the user's name" href="javascript:;">{USER_NAME}</a>
								</p>
								<p class="explain">
									The URL is open when the bar link is clicked. Enter the full URL with http:// included.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_target">Link Target</label>
							</dt>
							<dd>
								<input type="text" id="link_target" class="selectionText" name="link_target" value="<?php if(var_check('link_target')) echo get_var('link_target'); ?>" />
								<p class="explain">
									Optional - Sets the target for the link.  Ex: _blank will open the link in a new window
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>
								<label for="link_tab_width">Button Width</label>
							</dt>
							<dd>
								<div id="link_tab_width" class="slider"></div><div id="link_tab_width_amt2" class="slider-number"><?php if(var_check('link_tab_width')) echo get_var('link_tab_width'); else echo "16"; ?></div>
								<input type="hidden" id="link_tab_width_amt" name="link_tab_width" value="<?php if(var_check('link_tab_width')) echo get_var('link_tab_width'); else echo "16"; ?>" />
								<p class="explain">
									Sets the width of the bar link button to allow for text instead of just an icon.  Entering 16 will display the icon only. Default: 16
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_tab_name">Button Name</label>
							</dt>
							<dd>
								<input type="text" id="link_tab_name" class="selectionText" name="link_tab_name" value="<?php if(var_check('link_tab_name')) echo get_var('link_tab_name'); ?>" />
								<p class="explain">
									Sets the text for the bar link button to display next to the icon.  This will only display if the button width is wide enough to display the text.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['add-bar-link'].submit(); return false">
									<span>Add Bar Link</span>
								</a>
								<input type="hidden" name="link_add_submit" value="1" />
							</div>
						</dd>
					</dl>
					</form>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Bar Link Settings</div> 
				<div class="module_content">
					<form method="post" id="barlink_settings_submit" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="show_bar_links_right">
											<input type="checkbox" id="show_bar_links_right" name="show_bar_links_right" <?php if($show_bar_links_right == 1) echo 'checked="checked"'; ?> value="1" />
											Show Bar Links Right of Applications
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will show the bar links to the right of the applications menu instead of on the left.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></td>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['barlink_settings_submit'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="barlink_settings_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>

<?php
	if ($do == "traylinksedit") 
	{
		if (!empty($msg)) 
		{
?>
					<a href="manage.php?do=traylinks">Click here to go back to bar links management</a>
<?php
		} 
		else 
		{
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_trayicons 
				WHERE id = '" . $db->escape_string(get_var('id')) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
				
				if (empty($row['tray_width']))
				{
					$row['tray_width'] = 16;
				}
?>
					<script type="text/javascript">
						$(document).ready(function() {
							$('#link_tab_width').slider({
								value: <?php echo $row['tray_width']; ?>,
								min: 16,
								max: 200,
								step: 1,
								slide: function ( event, ui ) {
									$('#link_tab_width_amt').val( ui.value );
									$('#link_tab_width_amt2').html( ui.value );
								}
							});
						});
					</script>
					<div class="subtitle">Edit Bar Link</div>
						<dl class="selectionBox">
							<dt>
								<label for="link_name">Link Name</label>
							</dt>
							<dd>
								<input type="text" id="link_name" class="selectionText" name="link_name" value="<?php echo $row['name']; ?>" />
								<p class="explain">
									A name for the bar link.  This will also be the tooltip for the link.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_icon">Link Icon Location</label>
							</dt>
							<dd>
								<input type="text" id="link_icon" class="selectionText" name="link_icon" value="<?php echo $row['icon']; ?>" />
								<p class="explain">
									The filename of the icon in the themes's image icon folder. Ex: Enter mail.gif for themes/default/images/icons/mail.gif.  You can also upload an image.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_url">Link URL</label>
							</dt>
							<dd>
								<input type="text" id="link_url" class="selectionText" name="link_url" value="<?php echo $row['location']; ?>" />
								<p class="explain">
									The URL is open when the bar link is clicked. Enter the full URL with http:// included.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_target">Link Target</label>
							</dt>
							<dd>
								<input type="text" id="link_target" class="selectionText" name="link_target" value="<?php echo $row['target']; ?>" />
								<p class="explain">
									Optional - Sets the target for the link.  Ex: _blank will open the link in a new window
								</p>
							</dd>
						</dl>
					</fieldset>
					<fieldset>
						<dl class="selectionBox">
							<dt>
								<label for="link_tab_width">Button Width</label>
							</dt>
							<dd>
								<div id="link_tab_width" class="slider"></div><div id="link_tab_width_amt2" class="slider-number"><?php echo $row['tray_width']; ?></div>
								<input type="hidden" id="link_tab_width_amt" name="link_tab_width" value="<?php echo $row['tray_width']; ?>" />
								<p class="explain">
									Sets the width of the bar link button to allow for text instead of just an icon.  Entering 16 will display the icon only. Default: 16
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="link_tab_name">Button Name</label>
							</dt>
							<dd>
								<input type="text" id="link_tab_name" class="selectionText" name="link_tab_name" value="<?php echo $row['tray_name']; ?>" />
								<p class="explain">
									Sets the text for the bar link button to display next to the icon.  This will only display if the button width is wide enough to display the text.
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
								<input type="hidden" name="link_id" value="<?php echo $row['id']; ?>" />
								<input type="hidden" name="link_edit_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
			} 
			else 
			{
?>
				This bar link does not exist.
<?php
			}
		}
	}
?>
					
					</form>

				</div>
			</div>