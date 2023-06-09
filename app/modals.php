<!-- Signup / Login -->

<div id="modal-login" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-nbfc gbb-padding">
            <div class="uk-grid uk-grid-divider uk-grid-match" data-uk-grid-match="{ target: '.uk-panel' }" data-uk-grid-margin>
                <div id="modal-login-signup" class="uk-visible-large uk-width-large-1-2">
                    <div class="uk-panel uk-panel-box uk-panel-header gbb-login">
                        <div class="uk-panel-title uk-text-bold uk-text-primary">Create a New Account</div>
                        <form class="uk-form uk-form-stacked">
                            <input class="gbb-email" type="email" id="email-signup" name="email">
                            <div class="uk-form-row">
                                <p class="uk-hidden-small uk-hidden-xsmall uk-form-help-block">
                                Only a few fields and you're signed up!<span class="uk-margin-small-left">No email is required.</span>
                                </p>
                                <p class="uk-hidden-large uk-form-help-block">
                                Or you can <a id="login-mobile" href="#" class="uk-button uk-button-primary">Login</a><span class="uk-hidden-xsmall"> if you have an existing account.</span><span class="uk-visible-xsmall"> instead.</span>
                                </p>
                            </div>
                            <div class="uk-form-row">
                                <label class="uk-form-label" for="username-signup">Username</label>
                                <div class="uk-hidden-xsmall uk-hidden-small uk-text-small gbb-form-help">Enter up to 15 characters, using only letters, numbers, and a few <span class="uk-text-danger" data-uk-tooltip="{ pos: 'bottom-left' }" title="Allowed Special Characters&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ! @ # $ % ^ & * ( ) [ ] { } - + = | ' : . ; ~ ` ? and single spaces, have fun!">special characters</span>.</div>
                                <div class="uk-form-controls">
                                    <input class="gbb-username" type="text" placeholder="John Smith 123" id="username-signup" maxlength="15" tabindex="5" autocomplete="off">
                                    &nbsp;<span id="username-signup-msg" class="uk-alert uk-text-small" style="display: none"></span>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label class="uk-form-label" for="password-signup">Password</label>
                                <div class="uk-text-small gbb-form-help">You must enter at least 15 characters.<span class="uk-hidden-small"> I know that sounds like a lot, but just enter an easy to remember password and pad it with some repeating characters or symbols.</span></div>
                                <div class="uk-form-controls">
                                    <input class="uk-form-width-large" type="password" placeholder="Password 123 ++++" id="password-signup" maxlength="255" tabindex="6">
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label class="uk-form-label" for="password-signup-verify">Verify Password</label>
                                <div class="uk-form-controls">
                                    <input class="uk-form-width-large" type="password" placeholder="Password 123 ++++" id="password-signup-verify" maxlength="255" tabindex="7">
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label><input id="remember-signup" type="checkbox" tabindex="8"> Login automatically<span class="uk-hidden-xsmall"> on every visit</span></label>
                            </div>
                            <div class="uk-form-row">
                                <a id="signup" class="uk-button uk-button-primary" tabindex="9">Sign Up Now</a>
                                &nbsp;<span class="uk-visible-xsmall"><br /><br /></span><span id="signup-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="modal-login-login" class="uk-width-large-1-2">
                    <div class="uk-panel uk-panel-box uk-panel-header gbb-login">
                        <div class="uk-panel-title uk-text-bold uk-text-primary">Login</div>
                        <form class="uk-form uk-form-stacked">
                            <div class="uk-form-row">
                                <p class="uk-hidden-small uk-hidden-xsmall uk-form-help-block">
                                Welcome back, we've missed you! 
                                </p>
                                <p class="uk-hidden-large uk-form-help-block">
                                New Here? <span class="uk-visible-xsmall">&nbsp;&nbsp;</span><span class="uk-hidden-xsmall">You can </span><a id="signup-mobile" href="#" class="uk-button uk-button-primary">Create <span class="uk-hidden-xsmall">a New </span>Account</a><span class="uk-hidden-xsmall"> .</span>
                                </p>
                            </div>
                            <div class="uk-form-row">
                                <label class="uk-form-label" for="username-login">Username</label>
                                <div class="uk-hidden-small uk-hidden-xsmall uk-text-small gbb-form-help">Relax, you're almost there, just a few more keystrokes ...</div>
                                <div class="uk-form-controls">
                                    <input class="gbb-username" type="text" placeholder="John Smith 123" id="username-login" name="username" maxlength="15" tabindex="1" autocomplete="off">
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label class="uk-form-label" for="password-login">Password</label>
                                <div class="uk-text-small gbb-form-help"><span class="uk-hidden-small uk-hidden-xsmall">You probably padded your password with some repeating characters.<br />Make sure your Caps Lock is OFF too. </span>You can also <a href="<?php echo SITE_BASE_URL; ?>/user/reset">reset</a> your password.</div>
                                <div class="uk-form-controls">
                                    <input class="uk-form-width-large" type="password" placeholder="Password 123 ++++" id="password-login" name="password" maxlength="255" tabindex="2">
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label><input id="remember-login" type="checkbox" tabindex="3"> Login automatically<span class="uk-hidden-xsmall"> on every visit</span></label>
                            </div>
                            <div class="uk-form-row">
                                <a id="login" class="uk-button uk-button-primary" tabindex="4">Login</a>
                                &nbsp;<span class="uk-visible-xsmall"><br /><br /></span><span id="login-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($_SESSION['user']) { ?>

<!-- Settings -->

<div id="modal-settings" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-settings">
                <div class="uk-panel-title uk-text-bold uk-text-primary">User Settings</div>
                <form class="uk-form uk-form-stacked">
                    <div class="uk-grid uk-grid-collapse">
                        <div class="uk-width-4-10">
                            <label class="uk-form-label">Username</label>
                            <div class="uk-text-small gbb-form-help">You can not change your username.</div>
                        </div>
                        <div class="uk-width-6-10 uk-vertical-align uk-text-center">
                            <div class="uk-vertical-align-middle uk-container-center">
                                <a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $_SESSION['user']->id; ?>" class="uk-text-bold"><?php echo $_SESSION['user']->username; ?></a><a href="<?php echo SITE_BASE_URL; ?>/user/profile" class="uk-button uk-button-primary gbb-view-profile">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                    <div class="uk-form-row gbb-spacing-small">
                        <label class="uk-form-label" for="password-current">Current Password</label>
                        <div class="uk-text-small gbb-form-help">You must enter your current password before you can update any settings.</div>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" type="password" placeholder="" id="password-current" maxlength="255" tabindex="11" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="password">Email Address</label>
                        <div class="uk-text-small gbb-form-help">This is optional, but it is the only way you can reset a forgotten password.</div>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" type="email" placeholder="username@domain.com" id="email" maxlength="255" value="<?php echo $_SESSION['user']->email; ?>" tabindex="10">
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="password-new">New Password</label>
                        <div class="uk-text-small gbb-form-help">You must enter at least 15 characters. I know that sounds like a lot, but just enter an easy to remember password and pad it with some repeating characters or symbols.</div>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" type="password" placeholder="Password 123 ++++" id="password-new" maxlength="255" tabindex="12">
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="password-new-verify">Verify New Password</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" type="password" placeholder="Password 123 ++++" id="password-new-verify" maxlength="255" tabindex="13">
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label><input id="remember-new" type="checkbox" tabindex="14"<?php echo (isset($_COOKIE['uid'])) ? ' checked="checked"' : ''; ?>> Login automatically on every visit</label>
                    </div>
                    <div class="uk-form-row">
                        <a id="settings" class="uk-button uk-button-primary" tabindex="15">Update Settings</a>
                        &nbsp;<span id="settings-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php } ?>

<div id="modal-search-guide" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Search Guide<a class="uk-button uk-button-primary uk-align-right uk-modal-close gbb-editor-button">Close</a></div>
                
                By default, searching will return results that match at least one of the words you provide. 
                If you are sorting by Relevance, then the more words that match, the higher the item will be in the results. 
                You can alter this behavior by using the following symbols in your search text.
                
                <br />
                
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th class="uk-text-center">Symbol</th>
                            <th>Description</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <td class="gbb-search-col uk-text-center"></td>
                            <td class="gbb-search-col">At least one word must exist</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dogs run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to eat
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">+</td>
                            <td class="gbb-search-col">The word must exist</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dogs +run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-danger">Will NOT Match</span><br />
                                        dogs like to eat
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">-</td>
                            <td class="gbb-search-col">The word must NOT exist</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dogs -run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-danger">Will NOT Match</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to eat
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">&gt;</td>
                            <td class="gbb-search-col">The word is more important</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dogs &gt;run eat
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match Higher</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to eat
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">&lt;</td>
                            <td class="gbb-search-col">The word is less important</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dogs &lt;run eat
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match Higher</span><br />
                                        dogs like to eat
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">()</td>
                            <td class="gbb-search-col">Used for grouping words</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dogs +(run eat)
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-danger">Will NOT Match</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-danger">Will NOT Match</span><br />
                                        dogs like to eat
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to run and eat
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">~</td>
                            <td class="gbb-search-col">The word is not important at all</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dogs run ~eat
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to run and eat
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match Higher</span><br />
                                        dogs like to run and be around other dogs
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">*</td>
                            <td class="gbb-search-col">Used for wildcard searches</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        dog*
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogdeball is fun
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="gbb-search-col uk-text-center">" "</td>
                            <td class="gbb-search-col">Used for phrases</td>
                            <td>
                                <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td class="gbb-search-example">
                                        "dogs like to run"
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-success">Will Match</span><br />
                                        dogs like to run
                                    </td>
                                    <td class="gbb-search-example">
                                        <span class="uk-text-danger">Will NOT Match</span><br />
                                        dogs and cats like to run
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>