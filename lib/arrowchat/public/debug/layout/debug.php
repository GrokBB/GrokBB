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

?>
			If you are having trouble with getting ArrowChat running, this debug mode will help you find the problem.
			<br /><br />

			<table class="form-table"> 
				<tr> 
					<td width="20"><img src="<?php echo $base_url; ?>public/debug/images/reserve_tab_<?php echo $test_userid_img; ?>.png" alt="" /></td> 
					<th scope="row"><label for="phpver"><a href="javascript:;" class="vtip" title="This makes sure that you are currently logged in. If a user is not being registered as logged in, the ArrowChat bar may not show.">User ID</a></label></th> 
					<td><?php echo $test_userid; ?></td>
				</tr> 
				<tr> 
					<td width="20"><img src="<?php echo $base_url; ?>public/debug/images/reserve_tab_<?php echo $test_buddylist_img; ?>.png" alt="" /></td> 
					<th scope="row"><label for="mysql"><a href="javascript:;" class="vtip" title="Checks to make sure that your buddy list function is a valid MySQL statement. This DOES NOT check whether it is successfully getting friends.">Buddy List</a></label></th> 
					<td><?php echo $test_buddylist; ?></td>
				</tr> 
				<tr> 
					<td width="20"><img src="<?php echo $base_url; ?>public/debug/images/reserve_tab_<?php echo $test_banned_img; ?>.png" alt="" /></td> 
					<th scope="row"><label for="configwrite"><a href="javascript:;" class="vtip" title="This checks whether your username or IP address is currently banned.">Banned</a></label></th> 
					<td><?php echo $test_banned; ?></td>
				</tr> 
				<tr> 
					<td width="20"><img src="<?php echo $base_url; ?>public/debug/images/reserve_tab_<?php echo $test_browser_img; ?>.png" alt="" /></td> 
					<th scope="row"><label for="configwrite"><a href="javascript:;" class="vtip" title="ArrowChat will not load in certain browsers. This is a check to make sure you are currently not using one of them.">Browser</a></label></th> 
					<td><?php echo $test_browser; ?></td>
				</tr> 
				<tr class="no-border"> 
					<td width="20"><img src="<?php echo $base_url; ?>public/debug/images/reserve_tab_<?php echo $integration_img; ?>.png" alt="" /></td> 
					<th scope="row"><label for="cachewrite"><a href="javascript:;" class="vtip" title="An integration file must exist for ArrowChat to function.">Integration File</a></label></th> 
					<td><?php echo $integration_test; ?></td>
				</tr>   
			</table>