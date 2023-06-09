<?php
/* Smarty version 3.1.29, created on 2016-07-22 06:04:57
  from "C:\Users\Brian\OneDrive\Projects\GrokBB\src\lib\arrowchat\admin\layout\pages_login.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5791a979a6a935_06860040',
  'file_dependency' => 
  array (
    '37420bed64cc9384e0b730bd9991ad8a65b51e4d' => 
    array (
      0 => 'C:\\Users\\Brian\\OneDrive\\Projects\\GrokBB\\src\\lib\\arrowchat\\admin\\layout\\pages_login.tpl',
      1 => 1365933342,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5791a979a6a935_06860040 ($_smarty_tpl) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr"> 
<head profile="http://gmpg.org/xfn/11"> 
 
	<title>ArrowChat - Administrator Panel Login</title> 
	
	<link rel="stylesheet" type="text/css" href="includes/css/login-style.css"> 
	
	<?php echo '<script'; ?>
 type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"><?php echo '</script'; ?>
> 
	<?php echo '<script'; ?>
 type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/jquery-ui.min.js"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="includes/js/scripts.js"><?php echo '</script'; ?>
>
	
	<?php echo '<script'; ?>
 type="text/javascript">
		$(document).ready(function() {
			var emitter;
			$('#logo').animate({ 'marginLeft':'0%'}, 500, function () {
				emitter = new particle_emitter({
					image: ['./images/particle.gif'],
					center: ['50%', '140px'], offset: [-250, 0], radius: 0,
					size: 2, velocity: 100, decay: 1000, rate: 20
				}).start();
			});
			$('.fwdbutton').click(function() {
				emitter.stop();
				$('#logo').animate({ 'marginLeft':'-200%'}, 500, function () {
					document.forms['login'].submit();
				});
				
			});
			$(document).keypress(function(e) {
				if(e.keyCode == 13) {
					emitter.stop();
					$('#logo').animate({ 'marginLeft':'-200%'}, 500, function () {
						document.forms['login'].submit();
					});
				}
			});
			$('.login-form').illuminate({ 'intensity':'0.3','outGlow':'true','outerGlowSize':'30px','outerGlowColor':'#ffffff','blink':'false','color':'#ffffff'});
		});
	<?php echo '</script'; ?>
>
	
</head>
<body>
	<div style="margin: 0 auto; width: 550px; text-align: center; padding-top: 100px;">
		<div id="logo" style="margin-left: -200%; width: 521px; height: 69px;">
			<img id="logo2" src="./images/img-logo.png" alt="ArrowChat Logo" border="0" />
		</div>
		<div class="login-form">
			<form autocomplete="off" action="./" id="login" method="post"> 
				<div class="admin-panel-text">ArrowChat Admin Panel Login</div>
				<div style="clear: both;"></div>
				<div class="input-text">Username</div>
				<div class="input-box">
					<input class="text" id="username" name="username" value="<?php if (!empty($_smarty_tpl->tpl_vars['username_post']->value)) {
echo $_smarty_tpl->tpl_vars['username_post']->value;
}?>" type="text" />
				</div>
				<div style="clear: both;"></div>
				<div class="input-text">Password</div>
				<div class="input-box">
					<input class="text" name="password" value="<?php if (!empty($_smarty_tpl->tpl_vars['password_post']->value)) {
echo $_smarty_tpl->tpl_vars['password_post']->value;
}?>"  type="password" />
					<input type="hidden" name="login" value="1" />
				</div>
				<div style="clear: both;"></div>
				<div class="button_container float">
					<div class="login-error">
						<?php echo $_smarty_tpl->tpl_vars['error']->value;?>

					</div>
					<div class="floatr">
						<a class="fwdbutton">
							<span>Login</span>
						</a>
					</div>
					<div class="forgot">
						<a href="javascript:0;" class="vtip" title="The password and username can be changed in the arrowchat_admin table in your database.  The password must be converted to MD5 first.">Forgot Password</a><span class="forgot-big">&nbsp;&nbsp;&nbsp;|</span> 
					</div>
				</div>
				<div style="clear: both;"></div>
			</form> 
		</div>
	</div>
	<?php echo '<script'; ?>
 type="text/javascript">
		document.getElementById("username").focus();
	<?php echo '</script'; ?>
>
</body>
</html><?php }
}
