<div class="arrowchat_chatrooms_title">
	<div class="arrowchat_tab_name">'+lang[19]+'</div>
	<div class="arrowchat_more_button" id="arrowchat_chatroom_options">
		<a href="javascript:void(0);" class="arrowchat_more_anchor arrowchat_chatroom_item2"></a>
		<div class="arrowchat_more_wrapper">
			<div id="arrowchat_chatroom_options_flyout">
				<ul class="arrowchat_inner_menu">
					<li class="arrowchat_menu_item">
						<a id="arrowchat_chatroom_window" class="arrowchat_menu_anchor">
							<span>'+lang[36]+'</span>
							<input type="checkbox" checked="" />
						</a>
					</li>
					<!-- <li class="arrowchat_menu_item">
						<a id="arrowchat_chatroom_stay" class="arrowchat_menu_anchor">
							<span>'+lang[47]+'</span>
							<input type="checkbox" checked="" />
						</a>
					</li> -->
					<li class="arrowchat_menu_item">
						<a id="arrowchat_chatroom_sound" class="arrowchat_menu_anchor">
							<span>'+lang[101]+'</span>
							<input type="checkbox" checked="" />
						</a>
					</li>
					<li class="arrowchat_menu_item">
						<a id="arrowchat_chatroom_show_names" class="arrowchat_menu_anchor">
							<span>'+lang[152]+'</span>
							<input type="checkbox" checked="" />
						</a>
					</li>
					<li class="arrowchat_menu_separator"></li>
					<li class="arrowchat_menu_item">
						<a id="arrowchat_chatroom_block" class="arrowchat_menu_anchor">
							<span>'+lang[38]+'</span>
							<input type="checkbox" checked="" />
						</a>
					</li>
				</ul>
				<div class="arrowchat_flood_menu">
					<div class="arrowchat_flood_menu_text">'+lang[172]+'</div>
					<div style="float:left">
						<select id="arrowchat_flood_select_messages">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="15">15</option>
							<option value="20">20</option>
						</select>
					</div>
					<div class="arrowchat_flood_menu_text2" style="float:left">
						'+lang[174]+'
					</div>
					<div class="arrowchat_clearfix"></div>
					<div style="float:left">
						<select id="arrowchat_flood_select_seconds">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="15">15</option>
							<option value="20">20</option>
							<option value="25">25</option>
							<option value="30">30</option>
							<option value="40">40</option>
							<option value="50">50</option>
							<option value="60">60</option>
							<option value="90">90</option>
							<option value="120">120</option>
						</select>
					</div>
					<div class="arrowchat_flood_menu_text2" style="float:left">
						'+lang[175]+'
					</div>
					<div class="arrowchat_clearfix"></div>
					<div class="arrowchat_ui_button" id="arrowchat_flood_button" style="float:right">
						<div style="width:30px;height:18px;position:relative;top:2px;left:-1px;">'+lang[173]+'</div>
					</div>
					<div class="arrowchat_clearfix"></div>
				</div>
				<i class="arrowchat_more_tip"></i>
			</div>
		</div>
	</div>
	<div class="arrowchat_chatroom_create" id="arrowchat_chatroom_create">
		<a href="javascript:void(0);" class="arrowchat_more_anchor arrowchat_chatroom_item"></a>
	</div>
	<div class="arrowchat_chatroom_leave" id="arrowchat_chatroom_leave">
		<a href="javascript:void(0);" class="arrowchat_more_anchor arrowchat_chatroom_item3"></a>
	</div>
	<div class="arrowchat_chatroom_popout"></div>
</div>
<div class="arrowchat_clearfix"></div>
<div class="arrowchat_chatroom_content">
	<div id="arrowchat_chatroom_create_flyout" class="arrowchat_create_box">
		<div class="arrowchat_create_box_wrapper">
			<div style="float: right; position:relative; top:3px">
				<div class="arrowchat_ui_button" id="arrowchat_create_button">
					<div style="width:42px;height:18px;position:relative;top:2px;left:-1px;">'+lang[31]+'</div>
				</div>
			</div>
			<div style="text-align: left; float: left; position: relative; width:170px">
				<span>'+lang[91]+'</span>
			</div>
			<div style="float:left;position:relative;width:125px;">
				<input placeholder="'+lang[98]+'" type="text" id="arrowchat_chatroom_create_input" maxlength="50" />
			</div>
			<div style="float:left;position:relative;width:110px;">
				<input placeholder="'+lang[99]+'" type="text" id="arrowchat_chatroom_create_password_input" maxlength="50" />
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div id="arrowchat_chatroom_password_flyout" class="arrowchat_password_box">
		<div class="arrowchat_password_box_wrapper">
			<div style="float: right;position:relative; top:3px">
				<div class="arrowchat_ui_button" id="arrowchat_password_button">
					<div style="width:42px;height:18px;position:relative;top:2px;left:-1px;">'+lang[100]+'</div>
				</div>
			</div>
			<div style="text-align: left; float: left; position: relative; width:170px">
				<span>'+lang[50]+'</span>
			</div>
			<div style="float:left;position:relative;width:125px;">
				<input type="text" id="arrowchat_chatroom_password_input" maxlength="50" />
				<input type="hidden" id="arrowchat_chatroom_password_id" value="" />
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div id="arrowchat_chatroom_message_flyout" class="arrowchat_message_box">
		<div class="arrowchat_message_box_wrapper">
			<div>
				<span class="arrowchat_message_text">'+lang[49]+'</span>
			</div>
		</div>
	</div>
	<div class="arrowchat_chatroom_full_content">
	</div>
</div>