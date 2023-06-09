<?php
	if ($do == "logs") 
	{
?>
<?php
		if (!empty($_REQUEST['id'])) 
		{
			if (check_if_guest(get_var('id')))
			{
				$username = $language[83] . " " . substr(get_var('id'), 1);
			}
			else
			{
				$result = $db->execute("
					SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
					FROM " . TABLE_PREFIX . DB_USERTABLE . " 
					WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string(get_var('id')) . "'
				");
				$row = $db->fetch_array($result);
				$username = $row[DB_USERTABLE_NAME];
				$username2 = $username;
			}
?>
			<div class="title_bg"> 
				<div class="title">Logs<div style="float:right"><a href="users.php?do=view&id=<?php echo get_var('id'); ?>">[Edit this user]</a>&nbsp;&nbsp;&nbsp;</div></div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
<?php
			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
?>
					<div class="subtitle"><a href="users.php?do=view&id=<?php echo get_var('id'); ?>"><?php echo $username; ?>'s</a> Chat Logs</div>
					<table cellspacing="0" cellpadding="0" style="margin-bottom: 15px;" class="module_table">
						<tr>
<?php
				$result2 = $db->execute("
					SELECT arrowchat.to 
					FROM arrowchat 
					WHERE arrowchat.from = '" . $db->escape_string(get_var('id')) . "' 
					GROUP BY arrowchat.to
				");

				$i = 1;
				
				if ($result2 AND $db->count_select() > 0) 
				{
					while ($row2 = $db->fetch_array($result2)) 
					{
						if (check_if_guest($row2['to']))
						{
							$username = $language[83] . " " . substr($row2['to'], 1);
						}
						else
						{
							$result3 = $db->execute("
								SELECT " . DB_USERTABLE_NAME . " 
								FROM " . TABLE_PREFIX . DB_USERTABLE . " 
								WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row2['to']) . "'
							");
							
							$row3 = $db->fetch_array($result3);
							
							$username = $row3[DB_USERTABLE_NAME];
						}
?>
							<td style="width:175px; text-align: center;"><?php if (get_var('w') == $row2['to']) { ?><strong><?php } ?><a href="users.php?do=logs&id=<?php echo get_var('id'); ?>&w=<?php echo $row2['to']; ?>"><?php echo $username; ?></a><?php if (get_var('w') == $row2['to']) { ?></strong><?php } ?></td>
<?php
						if ($i%4 == 0) 
						{
?>
						</tr>
						<tr>
<?php
						}
?>
<?php
						$i++;
					}
				} 
				else 
				{
?>
						<td>This user has never sent any chats.</td>
<?php
				}
			}
			else 
			{
?>
						There is no user with that ID.
<?php
			}
?>
						</tr>
					</table>
					</form>
				</div>
			</div>
<?php
		if (!empty($_REQUEST['w'])) 
		{
?>
			<div class="title_bg"> 
				<div class="title">Chat Log<div style="float:right"><a href="users.php?do=view&id=<?php echo get_var('id'); ?>">[Edit this user]</a>&nbsp;&nbsp;&nbsp;</div></div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div style="height: 300px; padding: 10px; overflow: auto;" id="chatboxes">
<?php
			$result2 = $db->execute("
				SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
				FROM " . TABLE_PREFIX . DB_USERTABLE . " 
				WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string(get_var('w')) . "'
			");

			$result3 = $db->execute("
				SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
				FROM " . TABLE_PREFIX . DB_USERTABLE . " 
				WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string(get_var('id')) . "'
			");
			
			$result = $db->execute("
				SELECT * 
				FROM arrowchat 
				WHERE (arrowchat.to = '" . $db->escape_string(get_var('w')) . "' 
						AND arrowchat.from = '" . $db->escape_string(get_var('id')) . "') 
					OR (arrowchat.to = '" . $db->escape_string(get_var('id')) . "' 
						AND arrowchat.from = '" . $db->escape_string(get_var('w')) . "') 
				ORDER BY id ASC
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row2 = $db->fetch_array($result2);
				$row3 = $db->fetch_array($result3);
				
				while ($row = $db->fetch_array($result)) 
				{
					if ($row['from'] == get_var('w')) 
					{
						if (check_if_guest(get_var('w')))
						{
							$msg_username = $language[83] . " " . substr(get_var('w'), 1);
						}
						else
						{
							$msg_username = $row2[DB_USERTABLE_NAME];
						}
?>					
						<div style="padding:0px 10px 5px 0px; float: left; background-color: #fff; width: 470px;"><a href="users.php?do=logs&id=<?php echo get_var('w'); ?>"><b><?php echo $msg_username; ?></b></a>: <?php echo $row['message']; ?></div><div style="padding:0px 10px 5px; float: right; background-color: #fff; width: 150px;"><?php echo date('M j, Y g:i a', $row['sent']); ?></div><div class="clear"></div>
<?php
					} 
					else 
					{
						if (check_if_guest(get_var('id')))
						{
							$msg_username = $language[83] . " " . substr(get_var('id'), 1);
						}
						else
						{
							$msg_username = $row3[DB_USERTABLE_NAME];
						}
?>
						<div style="padding:0px 10px 5px 0px; float: left; width: 470px;"><a href="users.php?do=logs&id=<?php echo get_var('id'); ?>"><b><?php echo $msg_username; ?></b></a>: <?php echo $row['message']; ?></div><div style="padding: 0px 10px 5px; float: right; width: 150px;"><?php echo date('M j, Y g:i a', $row['sent']); ?></div><div class="clear"></div>
<?php
					}
?>
<?php
				}
			} 
			else 
			{
?>
				Oops, we found no chats to or from these users.
<?php
			}
?>
					</div>
					</form>
				</div>
			</div>
			<script type="text/javascript">
				var objDiv = document.getElementById("chatboxes");
				objDiv.scrollTop = objDiv.scrollHeight;
			</script>
<?php
		}
?>
			<div class="title_bg"> 
				<div class="title">Last 20 Messages Sent By <?php echo $username2; ?><div style="float:right"><a href="users.php?do=view&id=<?php echo get_var('id'); ?>">[Edit this user]</a>&nbsp;&nbsp;&nbsp;</div></div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<table cellspacing="0" cellpadding="0" class="module_table">
<?php
			$result = $db->execute("
				SELECT * 
				FROM arrowchat 
				WHERE arrowchat.from = '" . $db->escape_string(get_var('id')) . "' 
				ORDER BY arrowchat.id DESC 
				LIMIT 20
			");

			if ($result AND $db->count_select() > 0) 
			{
?>
						<tr>
							<td style="width: 175px;" class="row2">Sent To</td>
							<td style="width: 525px;" class="row2">Message</td>
						</tr>
<?php
				while ($row = $db->fetch_array($result)) 
				{
					if (check_if_guest($row['to']))
					{
						$usertable_name = $language[83] . " " . substr($row['to'], 1);
					}
					else
					{
						$result3 = $db->execute("
							SELECT " . DB_USERTABLE_NAME . " 
							FROM " . TABLE_PREFIX . DB_USERTABLE . " 
							WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row['to']) . "'
						");
						
						$row3 = $db->fetch_array($result3);
						$usertable_name = $row3[DB_USERTABLE_NAME];
					}
?>
						<tr>
							<td class="row1" style="padding-top: 3px; padding-bottom: 3px;"><a href="users.php?do=logs&id=<?php echo $row['to']; ?>"><?php echo $usertable_name; ?></a></td>
							<td class="row1" style="padding-top: 3px; padding-bottom: 3px;"><?php echo $row['message']; ?></td>
						</tr>
<?php
				}
			} 
			else 
			{
?>
						This user has no messages in the database.
<?php
			}
?>
					</table>
<?php
		}
	}
?>

<?php
	if ($do == "view") 
	{
		if (!empty($_REQUEST['id'])) 
		{
			$request_id = get_var('id');
			
			if (check_if_guest(get_var('id')))
			{
				$username = $language[83] . " " . substr(get_var('id'), 1);
			}
			else
			{
				$result = $db->execute("
					SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
					FROM " . TABLE_PREFIX . DB_USERTABLE . " 
					WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($request_id) . "'
				");

				$row = $db->fetch_array($result);
				$username = $row[DB_USERTABLE_NAME];
			}
			
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_status 
				WHERE userid = '" . $db->escape_string($request_id) . "'
			");

			$row = $db->fetch_array($result);
			
			$status = $row['status'];
			$is_admin = $row['is_admin'];
			$is_mod = $row['is_mod'];
			$hide_bar = $row['hide_bar'];
			$play_sound = $row['play_sound'];
			$window_open = $row['window_open'];
			$only_names = $row['only_names'];
			$announcement = $row['announcement'];
			
			if ($status == "available" OR empty($status)) 
			{
				if ((time() - $row['session_time']) < $online_timeout)
				{
					$status = "Available";
				}
				else
				{
					$status = "Offline";
				}
			}
?>
			<div class="title_bg"> 
				<div class="title">Edit <?php echo $username; ?> ( <?php echo $status; ?> )<div style="float:right"><a href="users.php?do=logs&id=<?php echo get_var('id'); ?>">[View chat logs]</a>&nbsp;&nbsp;&nbsp;</div></div> 
				<div class="module_content">
					<form method="post" id="edit-user" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>&id=<?php echo get_var('id'); ?>" enctype="multipart/form-data">
					<div class="subtitle">Edit <?php echo $username; ?></div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="is_admin">
											<input type="checkbox" id="is_admin" name="is_admin" <?php if($row['is_admin'] == 1) echo 'checked="checked"';  ?> value="1" />
											Make Administrator
										</label>
									</li>
								</ul>
								<p class="explain">
									Makes this user have administrator privileges.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="is_mod">
											<input type="checkbox" id="is_mod" name="is_mod" <?php if($row['is_mod'] == 1) echo 'checked="checked"';  ?> value="1" />
											Make Moderator
										</label>
									</li>
								</ul>
								<p class="explain">
									Makes this user have moderator privileges.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="hide_bar">
											<input type="checkbox" id="hide_bar" name="hide_bar" <?php if($row['hide_bar'] == 1) echo 'checked="checked"';  ?> value="1" />
											Hide Bar
										</label>
									</li>
								</ul>
								<p class="explain">
									Hide or show the ArrowChat bar for this user.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="play_sound">
											<input type="checkbox" id="play_sound" name="play_sound" <?php if($row['play_sound'] == 1) echo 'checked="checked"';  ?> value="1" />
											Play Chat Sounds
										</label>
									</li>
								</ul>
								<p class="explain">
									Choose whether this user has sounds enabled or disabled.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="window_open">
											<input type="checkbox" id="window_open" name="window_open" <?php if($row['window_open'] == 1) echo 'checked="checked"';  ?> value="1" />
											Buddy List Window Opened
										</label>
									</li>
								</ul>
								<p class="explain">
									If checked, the user's buddy list will stay open.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="only_names">
											<input type="checkbox" id="only_names" name="only_names" <?php if($row['only_names'] == 1) echo 'checked="checked"';  ?> value="1" />
											Show Only Names
										</label>
									</li>
								</ul>
								<p class="explain">
									If checked, the user's buddy list, chat, and chat rooms will not show avatars.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="announcement">
											<input type="checkbox" id="announcement" name="announcement" <?php if($row['announcement'] == 1) echo 'checked="checked"';  ?> value="1" />
											Hide Announcement Message
										</label>
									</li>
								</ul>
								<p class="explain">
									If checked, this will mark the user's announcement as read and it will not display.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="username">View Logs</label>
							</dt>
							<dd style="margin-top: 7px;">
								<a href="users.php?do=logs&id=<?php echo get_var('id'); ?>">Click Here</a>
								<p class="explain">
									
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="username">IP Address</label>
							</dt>
							<dd style="margin-top: 7px;">
								<span><?php echo $row['ip_address']; ?></span>
								<p class="explain">
									
								</p>
							</dd>
						</dl>
					<?php
						$group_id = get_group_id(get_var('id'));
						if (!is_null($group_id)) {
					?>
						<dl class="selectionBox">
							<dt>
								<label for="username">Group IDs</label>
							</dt>
							<dd style="margin-top: 7px;">
								<span>
								<?php 
									$i = 1;
									foreach ($group_id as $val) {
										if ($i == 1)
											echo $val;
										else
											echo ', ' . $val;
											
										$i++;
									}
								?>
								</span>
								<p class="explain">
									
								</p>
							</dd>
						</dl>
					<?php
						}
					?>
					<?php
						if (check_if_guest(get_var('id'))) {
					?>
						<dl class="selectionBox">
							<dt>
								<label for="username">Guest Name</label>
							</dt>
							<dd style="margin-top: 7px;">
								<a href="users.php?do=view&id=<?php echo get_var('id'); ?>&guest_name=1">Delete User's Guest Name</a>
								<p class="explain">
									Click the link above will remove the user's guest name and they will have the option to change it again.
								</p>
							</dd>
						</dl>
					<?php
						}
					?>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['edit-user'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="user_id" value="<?php echo get_var('id'); ?>" />
								<input type="hidden" name="user_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
		}
	}
?>

<?php
	if ($do == "manageusers") 
	{
?>
			<div class="title_bg"> 
				<div class="title">Search</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Manage Users</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="username">Username</label>
							</dt>
							<dd>
								<input type="text" id="username" class="selectionText" name="username" value="" />
								<p class="explain">
									Enter the username or part of the username that you'd like to search for.  You may also enter the user ID if known.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="guest_id">Guest ID/Name</label>
							</dt>
							<dd>
								<input type="text" id="guest_id" class="selectionText" name="guest_id" value="" />
								<p class="explain">
									Enter the guest ID or guest name that you would like to search for.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Search</span>
								</a>
								<input type="hidden" name="user_search" value="1" />
							</div>
						</dd>
					</dl>
<?php
		if (!empty($_POST['user_search'])) 
		{
			$result = $db->execute("
				SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
				FROM " . TABLE_PREFIX . DB_USERTABLE . " 
				WHERE LOWER(" . DB_USERTABLE_NAME . ") 
				LIKE '%" . $db->escape_string(strtolower(get_var('username'))) . "%'
					OR " . DB_USERTABLE_USERID . " = '" . $db->escape_string(strtolower(get_var('username'))) . "'
				ORDER BY " . DB_USERTABLE_NAME . " ASC 
				LIMIT 50
			");
?>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Results</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Search Results</div>
					<h2 class="subHeading">Results</h2>
					<ol class="scrollable">
<?php
			if ($result AND $db->count_select() > 0 AND empty($_POST['guest_id'])) 
			{
				while ($row = $db->fetch_array($result)) 
				{
?>
						<li class="listItem">
							<a href="users.php?do=logs&id=<?php echo $row[DB_USERTABLE_USERID]; ?>" class="secondaryContent">Logs</a>
							<a href="users.php?do=view&id=<?php echo $row[DB_USERTABLE_USERID]; ?>" class="secondaryContent">Edit</a>
							<h4>
								<a href="users.php?do=view&id=<?php echo $row[DB_USERTABLE_USERID]; ?>">
									<?php echo $row[DB_USERTABLE_USERID]; ?>&nbsp;&nbsp;&nbsp;<?php echo $row[DB_USERTABLE_NAME]; ?>
								</a>
							</h4>
						</li>
<?php
				}
			}
			else if (!empty($_POST['guest_id']))
			{
				$result = $db->execute("
					SELECT userid, guest_name
					FROM arrowchat_status 
					WHERE userid 
							LIKE 'g" . $db->escape_string(strtolower(get_var('guest_id'))) . "%'
						OR LOWER(guest_name)
							LIKE '%" . $db->escape_string(strtolower(get_var('guest_id'))) . "%'
					ORDER BY userid ASC 
					LIMIT 50
				");
				
				if ($result AND $db->count_select() > 0) 
				{
					while ($row = $db->fetch_array($result)) 
					{
?>
						<li class="listItem">
							<a href="users.php?do=logs&id=<?php echo $row['userid']; ?>" class="secondaryContent">Logs</a>
							<a href="users.php?do=view&id=<?php echo $row['userid']; ?>" class="secondaryContent">Edit</a>
							<h4>
								<a href="users.php?do=view&id=<?php echo $row['userid']; ?>">
									<?php echo $row['userid']; ?>&nbsp;&nbsp;&nbsp;<?php if (!empty($row['guest_name'])) echo $row['guest_name']; else echo "Guest " . substr($row['userid'], 1); ?>
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
									We didn't find any guests with that ID.
								</a>
							</h4>
						</li>
<?php
				}
?>
<?php
			}
			else
			{
?>
						<li class="listItem">
							<h4>
								<a href="#">
									We didn't find any usernames matching that search.
								</a>
							</h4>
						</li>
<?php
			}
?>
					</ol>
					</form>
<?php
		}
	}
?>
<?php
		if (!empty($_REQUEST['aid']) AND $do == "actions") 
		{
			if (check_if_guest(get_var('aid')))
			{
				$username = $language[83] . " " . substr(get_var('aid'), 1);
			}
			else
			{
				$result3 = $db->execute("
					SELECT " . DB_USERTABLE_NAME . " 
					FROM " . TABLE_PREFIX . DB_USERTABLE . " 
					WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string(get_var('aid')) . "'
				");
				
				$row3 = $db->fetch_array($result3);
				
				$mod_username = $row3[DB_USERTABLE_NAME];
			}
?>
			<div class="title_bg"> 
				<div class="title">Last 100 Warnings Given by <?php echo $mod_username; ?><div style="float:right"><a href="users.php?do=view&id=<?php echo get_var('aid'); ?>">[Edit this user]</a>&nbsp;&nbsp;&nbsp;</div></div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div style="height: 300px; padding: 10px; overflow: auto;" id="chatboxes">
<?php		
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_warnings
				WHERE warned_by = '" . $db->escape_string(get_var('aid')) . "' 
				ORDER BY warning_time ASC
				LIMIT 100
			");

			if ($result AND $db->count_select() > 0) 
			{	
				while ($row = $db->fetch_array($result)) 
				{
						if (check_if_guest($row['user_id']))
						{
							$username = $language[83] . " " . substr($row['user_id'], 1);
						}
						else
						{
							$result3 = $db->execute("
								SELECT " . DB_USERTABLE_NAME . " 
								FROM " . TABLE_PREFIX . DB_USERTABLE . " 
								WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row['user_id']) . "'
							");
							
							$row3 = $db->fetch_array($result3);
							
							$username = $row3[DB_USERTABLE_NAME];
						}
						
						if ($row['user_read'] == 1)
							$is_read = 'Yes';
						else
							$is_read = 'No';

?>					
						
						<div style="padding:0px 10px 10px 0px; float: left; background-color: #fff; width: 470px;"><span style="font-size:13px;font-weight:bold">Warned <a href="users.php?do=logs&id=<?php echo $row['user_id']; ?>"><?php echo $username; ?></a></span><br /><span style="font-size:13px">User Read: <?php echo $is_read; ?><br />Warn Reason: <?php echo $row['warn_reason']; ?></span></div><div style="padding:0px 10px 5px; float: right; background-color: #fff; width: 150px;"><?php echo date('M j, Y g:i a', $row['warning_time']); ?></div><div class="clear"></div>
<?php
				}
			} 
			else 
			{
?>
				This user hasn't warned anyone yet.
<?php
			}
?>
					</div>
					</form>
				</div>
			</div>
			<script type="text/javascript">
				var objDiv = document.getElementById("chatboxes");
				objDiv.scrollTop = objDiv.scrollHeight;
			</script>
			<div class="title_bg"> 
				<div class="title">Last 100 Bans Given by <?php echo $mod_username; ?><div style="float:right"><a href="users.php?do=view&id=<?php echo get_var('aid'); ?>">[Edit this user]</a>&nbsp;&nbsp;&nbsp;</div></div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div style="height: 300px; padding: 10px; overflow: auto;border:1px solid #C0C0C0;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;-khtml-border-radius: 4px;" id="chatboxes2">
<?php		
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_banlist
				WHERE banned_by = '" . $db->escape_string(get_var('aid')) . "' 
				ORDER BY banned_time ASC
				LIMIT 100
			");

			if ($result AND $db->count_select() > 0) 
			{	
				while ($row = $db->fetch_array($result)) 
				{
					if (!empty($row['ban_userid']))
					{
						if (check_if_guest($row['ban_userid']))
						{
							$username = $language[83] . " " . substr($row['ban_userid'], 1);
						}
						else
						{
							$result3 = $db->execute("
								SELECT " . DB_USERTABLE_NAME . " 
								FROM " . TABLE_PREFIX . DB_USERTABLE . " 
								WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row['ban_userid']) . "'
							");
							
							$row3 = $db->fetch_array($result3);
							
							$username = $row3[DB_USERTABLE_NAME];
						}
					} else {
						$username = "N/A";
					}
?>					
						
						<div style="padding:0px 10px 10px 0px; float: left; background-color: #fff; width: 470px;"><span style="font-size:13px;font-weight:bold">Banned <?php if (!empty($row['ban_userid'])) { ?><a href="users.php?do=logs&id=<?php echo $row['ban_userid']; ?>"><?php echo $username; ?></a><?php } ?></span><br /><span style="font-size:13px">IP Address: <?php if (!empty($row['ban_ip'])) echo $row['ban_ip']; else echo "N/A"; ?></span></div><div style="padding:0px 10px 5px; float: right; background-color: #fff; width: 150px;"><?php echo date('M j, Y g:i a', $row['banned_time']); ?></div><div class="clear"></div>
<?php
				}
			} 
			else 
			{
?>
				This user hasn't banned anyone yet.
<?php
			}
?>
					</div>
					</form>
				</div>
			</div>
			<script type="text/javascript">
				var objDiv = document.getElementById("chatboxes2");
				objDiv.scrollTop = objDiv.scrollHeight;
			</script>
			<div class="title_bg"> 
				<div class="title">Last 100 Reports Completed by <?php echo $mod_username; ?><div style="float:right"><a href="users.php?do=view&id=<?php echo get_var('aid'); ?>">[Edit this user]</a>&nbsp;&nbsp;&nbsp;</div></div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div style="height: 300px; padding: 10px; overflow: auto;border:1px solid #C0C0C0;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;-khtml-border-radius: 4px;" id="chatboxes3">
<?php		
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_reports
				WHERE completed_by = '" . $db->escape_string(get_var('aid')) . "' 
				ORDER BY completed_time ASC
				LIMIT 100
			");

			if ($result AND $db->count_select() > 0) 
			{	
				while ($row = $db->fetch_array($result)) 
				{
					if (!empty($row['report_about']))
					{
						if (check_if_guest($row['report_about']))
						{
							$username = $language[83] . " " . substr($row['report_about'], 1);
						}
						else
						{
							$result3 = $db->execute("
								SELECT " . DB_USERTABLE_NAME . " 
								FROM " . TABLE_PREFIX . DB_USERTABLE . " 
								WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row['report_about']) . "'
							");
							
							$row3 = $db->fetch_array($result3);
							
							$username = $row3[DB_USERTABLE_NAME];
						}
					} else {
						$username = "N/A";
					}
?>					
						
						<div style="padding:0px 10px 10px 0px; float: left; background-color: #fff; width: 470px;"><span style="font-size:13px;font-weight:bold">Report about <a href="users.php?do=logs&id=<?php echo $row['report_about']; ?>"><?php echo $username; ?></a> closed</span></div><div style="padding:0px 10px 5px; float: right; background-color: #fff; width: 150px;"><?php echo date('M j, Y g:i a', $row['completed_time']); ?></div><div class="clear"></div>
<?php
				}
			} 
			else 
			{
?>
				This user hasn't banned anyone yet.
<?php
			}
?>
					</div>
					</form>
				</div>
			</div>
			<script type="text/javascript">
				var objDiv = document.getElementById("chatboxes3");
				objDiv.scrollTop = objDiv.scrollHeight;
			</script>
<?php
		}
?>
<?php
	if ($do == "manageadmins") 
	{
?>
			<div class="title_bg"> 
				<div class="title">Moderators</div> 
				<div class="module_content">
					<div class="subtitle">Current Moderators</div>
					<div class="subExplain"><i>Moderators have access to moderation options in chat rooms and are able to ban/warn users in the main chat.</i></div>
					<h2 class="subHeading">Moderators</h2>
					<ol class="scrollable">
<?php	
		$result = $db->execute("
			SELECT arrowchat_status.userid userid, " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_NAME . " name
			FROM " . TABLE_PREFIX . DB_USERTABLE . " 
			LEFT JOIN arrowchat_status
				ON " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = arrowchat_status.userid
			WHERE arrowchat_status.is_mod = 1
			ORDER BY arrowchat_status.userid ASC
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
?>
						<li class="listItem">
							<a href="users.php?do=view&id=<?php echo $row['userid']; ?>" class="secondaryContent">Edit</a>
							<a href="users.php?do=logs&id=<?php echo $row['userid']; ?>" class="secondaryContent">Logs</a>
							<a href="users.php?do=actions&aid=<?php echo $row['userid']; ?>" class="secondaryContent">View Actions</a>
							<h4>
								<a href="users.php?do=actions&aid=<?php echo $row['userid']; ?>">
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
									There are currently no moderators
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
				<div class="title">Administrators</div> 
				<div class="module_content">
					<div class="subtitle">Current Administrators</div>
					<div class="subExplain"><i>Administrators have the same access that moderators have as well as additional options available.</i></div>
					<h2 class="subHeading">Administrators</h2>
					<ol class="scrollable">
<?php
		
		$result = $db->execute("
			SELECT arrowchat_status.userid userid, " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_NAME . " name
			FROM " . TABLE_PREFIX . DB_USERTABLE . " 
			LEFT JOIN arrowchat_status
				ON " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = arrowchat_status.userid
			WHERE arrowchat_status.is_admin = 1
			ORDER BY arrowchat_status.userid ASC
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
?>
						<li class="listItem">
							<a href="users.php?do=view&id=<?php echo $row['userid']; ?>" class="secondaryContent">Edit</a>
							<a href="users.php?do=logs&id=<?php echo $row['userid']; ?>" class="secondaryContent">Logs</a>
							<a href="users.php?do=actions&aid=<?php echo $row['userid']; ?>" class="secondaryContent">View Actions</a>
							<h4>
								<a href="users.php?do=actions&aid=<?php echo $row['userid']; ?>">
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
									There are currently no administrators
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
				<div class="title">Change Permissions</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=manageusers" enctype="multipart/form-data">
					<div class="subtitle">Add Moderator/Admin</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="username">Username</label>
							</dt>
							<dd>
								<input type="text" id="username" class="selectionText" name="username" value="" />
								<p class="explain">
									Enter the username or part of the username that you'd like to make an admin or mod.  You may also enter the user ID if known.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="guest_id">Guest ID/Name</label>
							</dt>
							<dd>
								<input type="text" id="guest_id" class="selectionText" name="guest_id" value="" />
								<p class="explain">
									Enter the guest ID or guest name that you would like to make an admin or mod.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Search</span>
								</a>
								<input type="hidden" name="user_search" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>
<?php
	if ($do == "groups") 
	{
?>
			<div class="title_bg"> 
				<div class="title">Groups</div> 
				<div class="module_content">
					<div class="subtitle">Group Permissions</div>
					<div class="subExplain"><i>Users that are set to groups from your main site can have their ArrowChat permissions set here.</i></div>
					<h2 class="subHeading">Groups</h2>
					<ol class="scrollable">
<?php	
		$groups_list = get_all_groups();

		if (!is_null($groups_list)) 
		{
			foreach ($groups_list as $val)
			{
?>
						<li class="listItem">
							<a href="users.php?do=groupsedit&gid=<?php echo $val[0]; ?>" class="secondaryContent">Change Permissions</a>
							<h4>
								<a href="users.php?do=groupsedit&gid=<?php echo $val[0]; ?>">
									<?php echo $val[1]; ?> (<?php echo $val[0]; ?>)
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
									The groups feature is either not setup or your site does not have groups.  You can setup groups in the includes/integration.php file.
								</a>
							</h4>
						</li>
<?php
		}
?>
					</ol>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">ArrowChat Groups</div> 
				<div class="module_content">
					<div class="subtitle">ArrowChat Permissions</div>
					<div class="subExplain"><i>Any guest permissions above will not work. You must control guest permissions via the ArrowChat guest group.</i></div>
					<h2 class="subHeading">Groups</h2>
					<ol class="scrollable">
						<li class="listItem">
							<a href="users.php?do=groupsedit&gid=acg" class="secondaryContent">Change Permissions</a>
							<h4>
								<a href="users.php?do=groupsedit&gid=acg">
									ArrowChat Guests (acg)
								</a>
							</h4>
						</li>
					</ol>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Group Permission Settings</div> 
				<div class="module_content">
					<form method="post" id="group_settings_submit" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="group_enable_mode">
											<input type="checkbox" id="group_enable_mode" name="group_enable_mode" <?php if($group_enable_mode == 1) echo 'checked="checked"'; ?> value="1" />
											Switch from 'Disable' to 'Enable'
										</label>
									</li>
								</ul>
								<p class="explain">
									Checking this will switch all group permissions from 'Disable {Feature}' to 'Enable {Feature}'.<br /><br />WARNING: This will reset all group permissions. If checked, everyone will have ArrowChat disabled until permissions are enabled.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></td>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['group_settings_submit'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="group_settings_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>

<?php
	if ($do == "groupsedit") 
	{
		if (!empty($_REQUEST['gid'])) 
		{
			$request_id = get_var('gid');
			
			$groups_list = get_all_groups();
			$group_exists = false;
			
			foreach ($groups_list as $group)
			{
				if ($group[0] == $request_id)
				{
					$groups_name = $group[1];
					$group_exists = true;
					break;
				}
			}
			
			if ($request_id == "acg")
			{
				$group_exists = true;
				$groups_name = "ArrowChat Guests";
			}
			
			$group_disable_arrowchat_ex = explode(",", $group_disable_arrowchat);
			$group_disable_video_ex = explode(",", $group_disable_video);
			$group_disable_apps_ex = explode(",", $group_disable_apps);
			$group_disable_rooms_ex = explode(",", $group_disable_rooms);
			$group_disable_uploads_ex = explode(",", $group_disable_uploads);
			$group_disable_sending_private_ex = explode(",", $group_disable_sending_private);
			$group_disable_sending_rooms_ex = explode(",", $group_disable_sending_rooms);
?>
<?php
			if ($group_exists) 
			{
?>
			<div class="title_bg"> 
				<div class="title">Edit <?php echo $groups_name; ?></div> 
				<div class="module_content">
					<form method="post" id="edit-group" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>&gid=<?php echo get_var('gid'); ?>" enctype="multipart/form-data">
					<div class="subtitle">Edit <?php echo $groups_name; ?></div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="group_disable_arrowchat">
											<input type="checkbox" id="group_disable_arrowchat" name="group_disable_arrowchat" <?php if(in_array($request_id, $group_disable_arrowchat_ex)) echo 'checked="checked"';  ?> value="1" />
											<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> ArrowChat
										</label>
									</li>
								</ul>
								<p class="explain">
									<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?>s ArrowChat for this group.
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
										<label for="group_disable_video">
											<input type="checkbox" id="group_disable_video" name="group_disable_video" <?php if(in_array($request_id, $group_disable_video_ex)) echo 'checked="checked"';  ?> value="1" />
											<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> Video Chat
										</label>
									</li>
								</ul>
								<p class="explain">
									<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> video chat for this group.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="group_disable_apps">
											<input type="checkbox" id="group_disable_apps" name="group_disable_apps" <?php if(in_array($request_id, $group_disable_apps_ex)) echo 'checked="checked"';  ?> value="1" />
											<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> Applications
										</label>
									</li>
								</ul>
								<p class="explain">
									<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?>s all applications for this group.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="group_disable_rooms">
											<input type="checkbox" id="group_disable_rooms" name="group_disable_rooms" <?php if(in_array($request_id, $group_disable_rooms_ex)) echo 'checked="checked"';  ?> value="1" />
											<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> Chat Rooms
										</label>
									</li>
								</ul>
								<p class="explain">
									<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?>s using chat rooms for this group.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="group_disable_uploads">
											<input type="checkbox" id="group_disable_uploads" name="group_disable_uploads" <?php if(in_array($request_id, $group_disable_uploads_ex)) echo 'checked="checked"';  ?> value="1" />
											<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> Uploading Files
										</label>
									</li>
								</ul>
								<p class="explain">
									<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?>s uploading files in private chat and chat rooms for this group.
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
										<label for="group_disable_sending_private">
											<input type="checkbox" id="group_disable_sending_private" name="group_disable_sending_private" <?php if(in_array($request_id, $group_disable_sending_private_ex)) echo 'checked="checked"';  ?> value="1" />
											<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> Sending Private Messages
										</label>
									</li>
								</ul>
								<p class="explain">
								<?php if ($group_enable_mode == 1) { ?>
									Only users in this group will be able to send private messages. All others will be shown an error message that can be changed in the language file.
								<?php } else { ?>
									Users in this group will be unable to send private messages and will be shown an error message that can be changed in the language file.
								<?php } ?>
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="group_disable_sending_rooms">
											<input type="checkbox" id="group_disable_sending_rooms" name="group_disable_sending_rooms" <?php if(in_array($request_id, $group_disable_sending_rooms_ex)) echo 'checked="checked"';  ?> value="1" />
											<?php if ($group_enable_mode == 1) echo 'Enable'; else echo 'Disable'; ?> Sending Chat Room Messages
										</label>
									</li>
								</ul>
								<p class="explain">
								<?php if ($group_enable_mode == 1) { ?>
									Only users in this group will be able to send chat room messages. All others will be shown an error message that can be changed in the language file.
								<?php } else { ?>
									Users in this group will be unable to send chat room messages and will be shown an error message that can be changed in the language file.
								<?php } ?>
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['edit-group'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="group_id" value="<?php echo get_var('gid'); ?>" />
								<input type="hidden" name="group_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
			} else {
?>
			<div class="title_bg"> 
				<div class="title">Group Permissions</div> 
				<div class="module_content">
					We could not find a group with that ID.  There is most likely an error with the groups integration.  Please check the includes/integration.php file.
<?php
			}
?>
<?php
		}
	}
?>

<?php
	if ($do == "banusernames") 
	{
?>
			<div class="title_bg"> 
				<div class="title">Ban Usernames</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Ban Usernames</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="ban_username">Username(s)</label>
							</dt>
							<dd>
								<textarea id="ban_username" class="selectionArea" name="ban_username"></textarea>
								<p class="explain">
									Enter each username you would like to ban <b>separated by a new line</b>.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Ban Usernames</span>
								</a>
								<input type="hidden" name="ban_username_submit" value="1" />
							</div>
						</dd>
					</dl>
					</form>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Remove Bans</div> 
				<div class="module_content">
					<form method="post" id="remove-ban" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Remove Bans</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="unban_username">Username(s)</label>
							</dt>
							<dd>
								<select multiple="multiple" id="unban_username" name="unban_username[]" style="width: 440px; height: 125px;">
<?php
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_banlist 
			WHERE ban_userid IS NOT NULL
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
				$result2 = $db->execute("
					SELECT " . DB_USERTABLE_NAME . " 
					FROM " . TABLE_PREFIX . DB_USERTABLE . " 
					WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row['ban_userid']) . "'
				");

				$row2 = $db->fetch_array($result2);
?>
									<option value="<?php echo $row['ban_id']; ?>"><?php echo $row2[DB_USERTABLE_NAME]; ?></option>
<?php
			}
		} 
		else 
		{
?>
									<option value="0">There are no banned usernames</option>
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
								<a class="fwdbutton" onclick="document.forms['remove-ban'].submit(); return false">
									<span>Remove Bans</span>
								</a>
								<input type="hidden" name="unban_username_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>
<?php
	if ($do == "banip") 
	{
?>
			<div class="title_bg"> 
				<div class="title">Ban IP Addresses</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Ban IP Addresses</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="ban_ip">IP Address(es)</label>
							</dt>
							<dd>
								<textarea id="ban_ip" class="selectionArea" name="ban_ip"></textarea>
								<p class="explain">
									Enter each IP address you would like to ban <b>separated by a new line</b>.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Ban IP Addresses</span>
								</a>
								<input type="hidden" name="ban_ip_submit" value="1" />
							</div>
						</dd>
					</dl>
					</form>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Remove Bans</div> 
				<div class="module_content">
					<form method="post" id="remove-ban" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Remove Bans</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="unban_ip">IP Address(es)</label>
							</dt>
							<dd>
								<select multiple="multiple" id="unban_ip" name="unban_ip[]" style="width: 440px; height: 125px;">
<?php
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_banlist 
			WHERE ban_ip IS NOT NULL
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
?>
									<option value="<?php echo $row['ban_id']; ?>"><?php echo $row['ban_ip']; ?></option>
<?php
			}
		} 
		else 
		{
?>
									<option value="0">There are no banned IP addresses</option>
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
								<a class="fwdbutton" onclick="document.forms['remove-ban'].submit(); return false">
									<span>Remove Bans</span>
								</a>
								<input type="hidden" name="unban_ip_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>
					
					</form>

				</div>
			</div>