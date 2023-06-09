	<script type="text/javascript" src="includes/js/jquery.jfeed.pack.js"></script>
	<script type="text/javascript" src="includes/js/highcharts.js"></script>
	<script type="text/javascript" src="includes/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
		var chart1;
		tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,image,code,|,fontselect,fontsizeselect,|,undo,redo,|,forecolor,backcolor",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_resizing : true,
		});
		$(document).ready(function() {
			chart1 = new Highcharts.Chart({
				chart: {
					renderTo: 'container',
					zoomType: 'x',
				},
				credits: {
					enabled: false
				},
				title: {
					text: ' '
				},
				xAxis: {
					type: 'datetime',
					maxZoom: 10 * 24 * 3600000, // fourteen days
					title: {
						text: null
					},
					startOnTick: false,
					showFirstLabel: false
				},
				yAxis: {
					min: '0',
					title: {
					   text: 'Number of messages'
					}
				},
				series: [{
					type: 'line',
					name: 'User Messages',
					pointInterval: 24 * 3600 * 1000,
					pointStart: <?php echo $install_time->config_value*1000; ?>,
					data: [ 	
					<?php
						// Get the graph data
						$result = $db->execute("
							SELECT user_messages
							FROM arrowchat_graph_log
							ORDER BY id ASC
						");
						
						while ($row = $db->fetch_array($result))
						{
							echo $row['user_messages'] . ",";
						}
					?> 
						]
				}, {
					type: 'line',
					name: 'Chat Room Messages',
					pointInterval: 24 * 3600 * 1000,
					pointStart: <?php echo $install_time->config_value*1000; ?>,
					data: [ 	
					<?php
						// Get the graph data
						$result = $db->execute("
							SELECT chat_room_messages
							FROM arrowchat_graph_log
							ORDER BY id ASC
						");
						
						while ($row = $db->fetch_array($result))
						{
							echo $row['chat_room_messages'] . ",";
						}
					?> 
					]
				}]
			});
			chart1.xAxis[0].setExtremes(<?php echo time()*1000 - 2592000000; ?>, <?php echo time()*1000; ?>);
			jQuery.getFeed({
				url: './includes/functions/functions_proxy_rss.php?url=http://www.arrowchat.com/blog/wp-rss2.php',
				success: function(feed) {	
					var html = '';
					
					for(var i = 0; i < feed.items.length && i < 5; i++) {
					
						var item = feed.items[i];
						var date = item.updated.split(" ");
						
						html += '<span style="font-size:13px;"><a target="_blank" class="bluelink" href="'
						+ item.link
						+ '">'
						+ item.title
						+ '</a></span>';
						
						html += '<div style="display: inline; margin-left: 5px; color: #666666;">'
						+ date[2] + ' ' + date[1] + ', ' + date[3]
						+ '</div>';
						
						var test = false;
						var matches = item.description.match(/img/gi);
						if (matches != null) {
							test = true;
						}
						
						item.description = item.description.replace('<img','<img style="display: none;"');
						item.description = item.description.replace('<span style="color: #585858; font-family: arial; font-size:13px; font-weight: bold;">', '<span style="color: #585858; font-family: arial; font-size:13px; font-weight: bold; display: none;">');
						
						if (test) {
							html += '<div style="line-height: 1.4em; margin-top: -30px; margin-bottom: 30px;">'
							+ item.description
							+ '</div>';
						} else {
							html += '<div style="line-height: 1.4em; margin-top: 10px; margin-bottom: 30px;">'
							+ item.description
							+ '</div>';
						}
					}
					
					html = html.substr(0,html.length-6);
					
					$('#rssLoading').hide();
					$('#rssOutput').fadeIn("normal");
					$('#rssOutput').html(html);
				},
				error: function(x,e) {
					var html = 'There was an error loading the blog.  Please try refershing.';
					$('#rssLoading').hide();
					$('#rssOutput').fadeIn("normal");
					$('#rssOutput').html(html);
				}
			});
		});
		function confirmSubmit()
		{
		var agree=confirm("Are you sure you want to delete messages older than 3 hours and notifications older than 5 days?");
		if (agree)
			return true ;
		else
			return false ;
		}
	</script>
	<?php
		$announcement = str_replace("\\'", "'", $announcement);
		
		$announcement_read = $db->count_rows("
			SELECT announcement 
			FROM arrowchat_status 
			WHERE announcement = '1'
		");
		
		$announcement_unread = $db->count_rows("
			SELECT announcement 
			FROM arrowchat_status 
			WHERE announcement = '0'
		");
	?>
			<div class="title_bg" id="content-overview"> 
				<div class="title">Overview</div> 
				<div class="module_content">
					<b>Chat Statistics</b> [<a href="./index.php?do=delete_history" onClick="return confirmSubmit()">Delete Chat History</a>]<br /><br />
					<table cellspacing="0" cellpadding="0">
						<tr style="height: 25px;">
							<td style="width: 175px;" class="row1"><div style="margin-bottom: 5px;">Number of messages:</div></td>
							<td style="width: 175px;" class="row2" valign="top"><?php echo number_format($num_messages); ?> <?php if ($num_messages>100000) { ?><img src="images/warning.png" border="0" title="Your database is getting full.  It is recommended that you delete your chat history immediately." /><?php } ?></td>
							<td style="width: 175px;" class="row1" valign="top">Messages per day:</td>
							<td style="width: 175px;" class="row2" valign="top"><?php echo number_format($messages_day, 2); ?></td>
						</tr>
						<tr style="height: 25px;">
							<td class="row1">Number of users:</td>
							<td class="row2"><?php echo number_format($num_users); ?></td>
							<td class="row1">Users per day:</td>
							<td class="row2"><?php echo number_format($users_day, 2); ?></td>
						</tr>
						<tr style="height: 25px;">
							<td class="row1">ArrowChat Installed:</td>
							<td class="row2"><?php echo date('M j, Y g:i a',$install_time->config_value); ?></td>
							<td class="row1"></td>
							<td class="row2"></td>
						</tr>
					</table>
					<?php
						if (ARROWCHAT_EDITION != "lite")
						{
					?>
					<br />
					<form method="post" action="./" enctype="multipart/form-data">
					<b>Post an Announcement</b> [<?php echo $announcement_unread; ?> Not Read, <?php echo $announcement_read; ?> Read]<br /><br />
					<textarea name="announcement" style="width: 100%; height: 100px;"><?php echo $announcement; ?></textarea>
					<div style="margin-top: 10px; float: right;">
						<input type="submit" name="announcement_show" class="button-primary" value="Show" /> <input type="submit" name="announcement_hide" class="button-primary" value="Hide" />
					</div>
					<div style="clear: both;"></div>
					</form>
					<?php
						}
					?>
				</div>
			</div>
			<div class="title_bg" id="content-analytics"> 
				<div class="title">Analytics</div> 
				<div class="module_content">
					<div id="container" style="width: 100%; height: 300px; margin-bottom: 15px;"></div>
				</div>
			</div>
			
      <div class="title_bg" id="content-recent"> 
            <div class="title">Recent Chat</div> 
            <div class="module_content">          
		<?php
			$numrows = $db->count_all("
				arrowchat
			");

			$pagenum = 1;
			$self = htmlentities($_SERVER['PHP_SELF']);
			$nav  = 'Page ';
			$maxpage = 10;
			$total_pages = ceil($numrows / $maxpage);
			
			if(var_check('page')) 
			{
				$pagenum = get_var('page');
			}
			
			for ($i = 1; $i <= $total_pages; $i++) 
			{ 
				if ($i == $pagenum) 
				{
					$nav .= " $i ";
				} 
				else if ($i <= 5) 
				{
					$nav .= " <a href=\"$self?page=$i\">" . floor($i) . "</a> ";
				}     
			}
			
			$offset = ($pagenum - 1) * $maxpage;
			
			$result = $db->execute("
				SELECT * 
				FROM arrowchat 
				ORDER BY id DESC 
				LIMIT " . $db->escape_string($offset) . ", " . $db->escape_string($maxpage) . "
			");
			
			if ($result AND $db->count_select() > 0) 
			{
		?>
				<table cellspacing="0" cellpadding="0">
				<tr style="height: 25px;">
					<td style="width: 125px;" class="row2">From</td>
					<td style="width: 125px;" class="row2">To</td>
					<td style="width: 305px;" class="row2">Message</td>
					<td style="width: 50px;" class="row2">Read</td>
					<td style="width: 125px;" class="row2">Sent</td>
				</tr>
			<?php
				while ($row = $db->fetch_array($result)) 
				{
					if (check_if_guest($row['from']))
					{
						$from_username = $language[83] . " " . substr($row['from'], 1);
					}
					else
					{
						$from_result = $db->execute("
							SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
							FROM " . TABLE_PREFIX . DB_USERTABLE . " 
							WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row['from']) . "'
						");
						
						$from_username = $db->fetch_array($from_result);
						$from_username = $from_username[DB_USERTABLE_NAME];
					}
				  
					if (check_if_guest($row['to']))
					{
						$to_username = $language[83] . " " . substr($row['to'], 1);
					}
					else
					{
						$to_result = $db->execute("
							SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
							FROM " . TABLE_PREFIX . DB_USERTABLE . " 
							WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($row['to']) . "'
						");
						
						$to_username = $db->fetch_array($to_result);
						$to_username = $to_username[DB_USERTABLE_NAME];
					}
					
			?>
				<tr style="height: 25px;">
					<td class="row1"><a href="users.php?do=logs&id=<?php echo $row['from']; ?>"><?php echo $from_username; ?></a></td>
					<td class="row1"><a href="users.php?do=logs&id=<?php echo $row['to']; ?>"><?php echo $to_username; ?></a></td>
					<td class="row1"><div style="overflow: hidden; width: 300px;"><?php echo $row['message']; ?></div></td>
				<?php
					if($row['user_read'] == 1) 
					{
						echo '<td class="row1">Yes</td>';
					} 
					else 
					{
						echo '<td class="row1">No</td>';
					}
				?>
					<td class="row1"><?php echo date("M j, Y", $row['sent']); ?><br><?php echo date("g:i a", $row['sent']); ?></td>
				</tr>
			<?php
				}
			?>
				<tr>
					<td colspan="5">
						<div style="margin-top: 10px; float: left;">
							<?php echo $nav; ?>
						</div>
					</td>
				</tr>
			</table>
		<?php
			} 
			else 
			{
		?>
				No one has ever chatted!
		<?php
			}
		?>
			</div>
		 </div>
			
			<div class="title_bg" id="content-blog" style="width:500px"> 
				<div class="title">ArrowChat Facebook Page</div> 
				<div class="module_content" style="padding: 0px;">
					<iframe src="//www.facebook.com/connect/connect.php?name=ArrowChat&connections=6&stream=1&show_border=false&show_posts=true&width=500&height=400" width="500" height="400" scrolling="no" frameborder="0"></iframe>
				</div>
			</div>
			<div style="clear: both;"></div>