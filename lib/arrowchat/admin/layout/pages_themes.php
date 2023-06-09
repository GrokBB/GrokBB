			<div class="title_bg"> 
				<div class="title">Themes</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
<?php
	if ($do == "edit") 
	{
		if (!empty($msg)) 
		{
?>
					<a href="themes.php?do=managethemes">Click here to go back to themes management</a>
<?php
		} 
		else 
		{
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_themes 
				WHERE id = '" . $db->escape_string(get_var('id')) . "'
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
?>
					<div class="subtitle">Edit Theme</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="theme_name">Theme Name</label>
							</dt>
							<dd>
								<input type="text" id="theme_name" class="selectionText" name="theme_name" value="<?php echo $row['name']; ?>" />
								<p class="explain">
									Provide a name for the theme.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="theme_folder">Theme Folder</label>
							</dt>
							<dd>
								<input type="text" id="theme_folder" class="selectionText" name="theme_folder" value="<?php echo $row['folder']; ?>" />
								<p class="explain">
									The folder location of the theme. You should not have to change this.
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
										<label for="theme_active">
											<input type="checkbox" id="theme_active" name="theme_active" <?php if ($row['default']==1) { echo 'checked="checked"'; echo " disabled='disabled'"; } else if ($row['active']==1) echo 'checked="checked"'; ?> value="1" />
											Make Active
										</label>
									</li>
								</ul>
								<p class="explain">
									Check this if you would like for users to be able to use this theme.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="theme_default">
											<input type="checkbox" id="theme_default" name="theme_default" <?php if ($row['default']==1) { echo 'checked="checked"'; echo " disabled='disabled'"; } ?> value="1" />
											Make Default
										</label>
									</li>
								</ul>
								<p class="explain">
									Check this to make this the new default theme.
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
								<input type="hidden" name="theme_edit_submit" value="1" />
								<input type="hidden" name="theme_id" value="<?php echo $row['id']; ?>" />
								<?php if ($row['default']==1) { ?>
								<input type="hidden" name="theme_active" value="1" />
								<input type="hidden" name="theme_default" value="1" />
								<?php } ?>
							</div>
						</dd>
					</dl>
<?php
			} 
			else 
			{
?>
				This theme does not exist.
<?php
			}
		}
	}
?>
<?php
	if ($do == "install") 
	{
		if (!empty($msg)) 
		{
?>
					<a href="themes.php?do=managethemes">Click here to go back to themes management</a>
<?php
		} 
		else 
		{
?>
					<div class="subtitle">Install Theme</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="theme_name">Theme Name</label>
							</dt>
							<dd>
								<input type="text" id="theme_name" class="selectionText" name="theme_name" value="<?php if (!empty($_REQUEST['theme_name'])) { echo get_var('theme_name'); } ?>" />
								<p class="explain">
									Provide a name for the theme.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="theme_folder">Theme Folder</label>
							</dt>
							<dd>
								<input type="text" id="theme_folder" class="selectionText" name="theme_folder" value="<?php if (!empty($_REQUEST['theme_folder'])) { echo get_var('theme_folder'); } else { echo get_var('f'); } ?>" />
								<p class="explain">
									The folder location of the theme. You should not have to change this.
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
										<label for="theme_active">
											<input type="checkbox" id="theme_active" name="theme_active" <?php if (get_var('theme_active') == 1) echo 'checked="checked"'; ?> value="1" />
											Make Active
										</label>
									</li>
								</ul>
								<p class="explain">
									Check this if you would like for users to be able to use this theme.
								</p>
							</dd>
						</dl>
						<!--
						TODO: Not Working for some reason.
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="theme_default">
											<input type="checkbox" id="theme_default" name="theme_default" <?php if (get_var('theme_default') == 1) echo 'checked="checked"'; ?> value="1" />
											Make Default
										</label>
									</li>
								</ul>
								<p class="explain">
									Check this to make this the new default theme.
								</p>
							</dd>
						</dl> 
						-->
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Install Theme</span>
								</a>
								<input type="hidden" name="theme_install_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
		}
	}
?>
<?php
	if ($do == "smilies") 
	{
?>
					<script type="text/javascript">
						$(document).ready(function() {
							$("#add_smiley_name").keyup(function () {
								$("#add_smiley_path").html("themes/{theme name}/images/smilies/" + $(this).val() + ".png");
							});
						});
					</script>
					<div class="subtitle">Smilies</div>
					<div class="subExplain"><i>The name is also the location of the smiley in the theme's smiley folder.  For example, "smiley-sad" would be found in themes/{theme name}/images/smilies/smiley-sad.png. Do not enter the extension, all smilies must be .png.</i></div>
					<table cellspacing="0" cellpadding="0" class="table_table">
						<tr>
							<th>Smiley Name</th>
							<th>Smiley Code</th>
							<th></th>
						</tr>
<?php
	$result = $db->execute("
		SELECT * 
		FROM arrowchat_smilies
	");

	$i = 1;
	while ($row = $db->fetch_array($result)) 
	{
?>
						<tr>
							<td>
								<input type="hidden" name="smiley_id_<?php echo $i; ?>" value="<?php echo $row['id']; ?>" />
								<input type="text" class="selectionText" style="width: 300px;" name="smiley_name_<?php echo $i; ?>" value="<?php echo $row['name']; ?>" />
							</td>
							<td><input type="text" class="selectionText" style="width: 300px;" name="smiley_pattern_<?php echo $i; ?>" value="<?php echo $row['code']; ?>" /></td>
							<td><a href="themes.php?do=smilies&deletesmiley=<?php echo $row['id']; ?>"><img style="position:relative; top: 4px;" src="./images/img-red-no.png" title="Delete This Smiley" alt="" border="0" /></a></td>
						</tr>
<?php
		$i++;
	}
?>
					</table>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr" style="float: right;">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="smiley_count" value="<?php echo $i; ?>" />
								<input type="hidden" name="smiley_submit" value="1" />
							</div>
						</dd>
					</dl>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Smilies</div> 
				<div class="module_content">
					<form method="post" id="smiley_add" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Add Smiley</div>
					<fieldset class="firstFieldset">
					<dl class="selectionBox">
						<dt>
							<label for="add_smiley_name">Smiley Name</label>
						</dt>
						<dd>
							<input type="text" id="add_smiley_name" class="selectionText" name="add_smiley_name" value="" />
							<p class="explain">
								The name is also the location of the smiley in the theme's smiley folder.  Do not enter the extension, all smilies must be .png.
							</p>
						</dd>
					</dl>
					<dl class="selectionBox">
						<dt>
							<label for="add_smiley_path">Smiley Path</label>
						</dt>
						<dd>
							<div id="add_smiley_path" style="min-height: 20px; margin-top: 5px;">themes/{theme name}/images/smilies/.png</div>
							<p class="explain">
								This path is based on the smiley name and cannot be changed. Your smiley must be located here.
							</p>
						</dd>
					</dl>
					<dl class="selectionBox">
						<dt>
							<label for="add_smiley_pattern">Smiley Code</label>
						</dt>
						<dd>
							<input type="text" id="add_smiley_pattern" class="selectionText" name="add_smiley_pattern" value="" />
							<p class="explain">
								This is the code that will be matched in text to convert to the smiley image.
							</p>
						</dd>
					</dl>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['smiley_add'].submit(); return false">
									<span>Add Smiley</span>
								</a>
								<input type="hidden" name="add_smiley_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>
<?php
	if ($do == "managethemes") 
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
													document.location = 'themes.php?do=managethemes&update=1&id='+c;
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
					<div class="subtitle">Installed Themes</div>
					<h2 class="subHeading">Themes</h2>
					<ol class="scrollable">
<?php
		$theme_array = array();
		
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_themes
		");

		while ($row = $db->fetch_array($result)) 
		{
			if ($row['default'] == 1) 
			{
				$used_by = $db->count_rows("
					SELECT theme 
					FROM arrowchat_status 
					WHERE theme = '" . $row['id'] . "' 
						OR theme IS NULL
				");
			} 
			else 
			{
				$used_by = $db->count_rows("
					SELECT theme 
					FROM arrowchat_status 
					WHERE theme = '" . $row['id'] . "'
				");
			}
			
			$theme_array[] = $row['folder'];
			
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
					$current_version = @stream_get_contents($fp);
					fclose ($fp);
				} 
				else 
				{
					$current_version = $row['version'];
				}
			}
?>
						<li class="listItem">
							<a href="themes.php?do=managethemes&delete=<?php echo $row['id']; ?>" title="Delete" class="secondaryContent delete"><span>Delete</span></a>
							<a href="themes.php?do=edit&id=<?php echo $row['id']; ?>" class="secondaryContent">Edit</a>
							<a href="javascript:;" <?php if ($row['version']!=$current_version) echo "id='itip_".$row['id']."'"; ?> class="secondaryContent version_link <?php if ($row['version']!=$current_version) echo "red"; ?>">v<?php echo $row['version']; ?></a>
							<a href="themes.php?do=managethemes&activate=<?php echo $row['active']; ?>&id=<?php echo $row['id']; ?>" title="<?php if ($row['active']==1) echo "Deactivate"; else echo "Activate"; ?>" class="secondaryContent <?php if ($row['active']==1) echo "deactivate"; else echo "activate"; ?>"><span>Deactivate</span></a>
<label title="Make this theme the default" class="secondaryContent"><input name="theme_default" onclick="document.forms[0].submit(); return false" type="radio" <?php if ($row['default']==1) echo "checked='checked'"; else echo ""; ?> value="<?php echo $row['id']; ?>" /></label>
							<h4>
								<a href="themes.php?do=edit&id=<?php echo $row['id']; ?>">
									<?php echo $row['name']; ?>
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
				<div class="title">Themes</div> 
				<div class="module_content">
					<div class="subtitle">Uninstalled Themes</div>
					<table cellspacing="0" cellpadding="0" class="table_table">
						<thead>
							<tr>
								<th>Name</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
<?php
		$folders = get_folders(AC_FOLDER_THEMES);
		$no_installed = true;
		
		foreach ($folders as $folder) 
		{
			if (!in_array($folder['name'], $theme_array)) 
			{
				$no_installed = false;
?>
							<tr>
								<td><?php echo $folder['name']; ?></td>
								<td><a href="themes.php?do=install&f=<?php echo $folder['name']; ?>">Install</a></td>
							</tr>
<?php
			}
		}
		
		if ($no_installed) 
		{
?>
							<tr>
								<td colspan="2">No Uninstalled Themes</td>
							</tr>
<?php
		}
?>
						</tbody>
					</table>
<?php
	}
?>
<?php
	if ($do == "templates") 
	{
?>
					<script type="text/javascript">
						$(document).ready(function() {
							$("#template_data").tabby();
						});
					</script>
					<dl class="selectionBox">
						<dt>
							<label for="theme_edit">Theme to Edit</label>
						</dt>
						<dd>
							<select name="edit_theme" onChange="this.form.submit();">
<?php
	if (!var_check('edit_theme'))
	{
		$_POST['edit_theme'] = "";
	}
	
	if (!var_check('edit_template'))
	{
		$_POST['edit_template'] = "";
	}
		
	$result = $db->execute("
		SELECT * 
		FROM arrowchat_themes
	");

	while ($row = $db->fetch_array($result)) 
	{
?>
									<option value="<?php echo $row['folder']; ?>" <?php if((get_var('edit_theme') == $row['folder'] AND !empty($_POST['edit_theme'])) OR (empty($_POST['edit_theme']) AND $row['default'] == 1)) echo "selected"; ?>><?php echo $row['name'];?></option>
<?php
	}
?>
								</select>
								<input type="hidden" name="theme_select" value="Go" />
							<p class="explain">
								
							</p>
						</dd>
					</dl>
					<dl class="selectionBox">
						<dt>
							<label for="template_edit">Template to Edit</label>
						</dt>
						<dd>
								<select name="edit_template" onChange="this.form.submit();">
									<option value="css">Stylesheet</option>
									<option value="announcements_display" <?php if(get_var('edit_template') == "announcements_display") echo "selected"; ?>>Announcements Display</option>
									<option value="applications_bookmarks_list" <?php if(get_var('edit_template') == "applications_bookmarks_list") echo "selected"; ?>>Applications Bookmarks List</option>
									<option value="applications_bookmarks_tab" <?php if (get_var('edit_template') == "applications_bookmarks_tab") echo "selected"; ?>>Applications Bookmarks Tab</option>
									<option value="applications_bookmarks_window" <?php if (get_var('edit_template') == "applications_bookmarks_window") echo "selected"; ?>>Applications Bookmarks Window</option>
									<option value="applications_tab" <?php if (get_var('edit_template') == "applications_tab") echo "selected"; ?>>Applications Tab</option>
									<option value="applications_window" <?php if (get_var('edit_template') == "applications_window") echo "selected"; ?>>Applications Window</option>
									<option value="bar_hide_tab" <?php if (get_var('edit_template') == "bar_hide_tab") echo "selected"; ?>>Bar Hide Tab</option>
									<option value="bar_show_tab" <?php if (get_var('edit_template') == "bar_show_tab") echo "selected"; ?>>Bar Show Tab</option>
									<option value="buddylist_tab" <?php if (get_var('edit_template') == "buddylist_tab") echo "selected"; ?>>Buddy List Tab</option>
									<option value="buddylist_window" <?php if (get_var('edit_template') == "buddylist_window") echo "selected"; ?>>Buddy List Window</option>
									<option value="chat_tab" <?php if (get_var('edit_template') == "chat_tab") echo "selected"; ?>>Chat Tab</option>
									<option value="chat_window" <?php if (get_var('edit_template') == "chat_window") echo "selected"; ?>>Chat Window</option>
									<option value="chatrooms_room" <?php if (get_var('edit_template') == "chatrooms_room") echo "selected"; ?>>Chatrooms Room</option>
									<option value="chatrooms_tab" <?php if (get_var('edit_template') == "chatrooms_tab") echo "selected"; ?>>Chatrooms Tab</option>
									<option value="chatrooms_window" <?php if (get_var('edit_template') == "chatrooms_window") echo "selected"; ?>>Chatrooms Window</option>
									<option value="maintenance_tab" <?php if (get_var('edit_template') == "maintenance_tab") echo "selected"; ?>>Maintenance Tab</option>
									<option value="notifications_tab" <?php if (get_var('edit_template') == "notifications_tab") echo "selected"; ?>>Notifications Tab</option>
									<option value="notifications_window" <?php if (get_var('edit_template') == "notifications_window") echo "selected"; ?>>Notifications Window</option>
								</select>
								<input type="hidden" name="template_select" value="Go" />
							<p class="explain">
								
							</p>
						</dd>
					</dl>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Edit Template</div> 
				<div class="module_content">
					<form method="post" id="template-data" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
<?php
	if (empty($_POST['edit_template']) OR get_var('edit_template') == "css") 
	{
		if (empty($_POST['edit_theme']))
		{
			$themefile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/css/style.css";
		}
		else
		{
			$themefile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR  . get_var('edit_theme') . "/css/style.css";
		}
	} 
	else 
	{
		if (empty($_POST['edit_theme']))
		{
			$themefile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . $theme . "/template/" . get_var('edit_template') . ".php";
		}
		else
		{
			$themefile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_THEMES . DIRECTORY_SEPARATOR . get_var('edit_theme') . "/template/" . $_POST['edit_template'] . ".php";
		}
	}
	
	$textarea = get_include_contents($themefile);
	$textarea = stripslashes($textarea);
	$textarea = htmlspecialchars($textarea);
?>
					<textarea id="template_data" name="template_data"><?php echo $textarea; ?></textarea>
<?php
		if (is_writable($themefile)) 
		{
?>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr" style="float:right;">
								<a class="fwdbutton" onclick="document.forms['template-data'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="save_template_submit" value="1" />
								<input type="hidden" name="template_file" value="<?php echo $themefile; ?>" />
								<input type="hidden" name="edit_template" value="<?php echo get_var('edit_template'); ?>" />
								<input type="hidden" name="edit_theme" value="<?php echo get_var('edit_theme'); ?>" />
							</div>
						</dd>
					</dl>
<?php
		} 
		else 
		{
?>
					<br /><br /><b>You need to CHMOD this file to be writable before you can save edits.  You can find it in the directory:<br /><?php echo $themefile; ?></b>
<?php
		}
?>
<?php
	}
?>
					
					</form>

				</div>
			</div>