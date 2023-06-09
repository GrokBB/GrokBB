<?php
$GLOBALS['includePicker'] = true;
$GLOBALS['includeEditor'] = true;
$GLOBALS['includeStripe'] = true;

require(SITE_BASE_APP . 'header.php');

$imagesPath = SITE_DATA_DIR . 'boards' . DIRECTORY_SEPARATOR . $board->id . DIRECTORY_SEPARATOR . 'images';
$imagesSize = round(GrokBB\Util::getDirSize($imagesPath) / 1024 / 1024, 2);
$images = @scandir($imagesPath); if (!$images) { $images = array(); }

$categories = $GLOBALS['db']->getAll('board_category', array('id_bd' => $board->id), 'defcat DESC, name ASC');
$settings = $GLOBALS['db']->getOne('board_settings', array('id_bd' => $board->id));
if (!$settings) { $settings = new stdClass(); }

$tags = $GLOBALS['db']->getAll('board_tag', array('id_bd' => $board->id), '');
foreach ($tags as $tag) { $_SESSION['board']->tags[] = str_replace("'", "\'", $tag->name); }

if ($settings->board_request_access_md == '') {
    $settings->board_request_access_md = 'This is a private board, and only approved users are allowed to read and post topics. Please enter your reason for requesting access below.';
}

if ($settings->topic_request_access_md == '') {
    $settings->topic_request_access_md = 'This is a moderated board, and only approved users are allowed to post topics. Please enter your reason for requesting access below.';
}
?>

<div class="uk-grid uk-grid-small" data-uk-grid-margin>
    <input type="hidden" id="board-id" value="<?php echo $board->id; ?>">
    
    <div class="uk-width-large-3-4">
        <ul class="uk-tab uk-tab-grid" data-uk-tab="{ connect: '#settings-tabs' }">
            <li class="uk-width-1-6" id="settings-general"><a href="#" class="uk-text-truncate"><span class="uk-hidden-xsmall">General<span class="uk-hidden-small uk-hidden-medium"> Settings</span></span><span class="uk-visible-xsmall">Gen</span></a></li>
            <li class="uk-width-1-6" id="settings-sidebar"><a href="#" class="uk-text-truncate"><span class="uk-hidden-xsmall">Sidebar<span class="uk-hidden-small uk-hidden-medium"> Description</span></span><span class="uk-visible-xsmall">Side</span></a></li>
            <li class="uk-width-1-6" id="settings-stylesheet"><a href="#"><span class="uk-hidden-xsmall">Stylesheet</span><span class="uk-visible-xsmall">Style</span></a></li>
            <li class="uk-width-1-6" id="settings-images"><a href="#" class="uk-text-truncate"><span class="uk-hidden-xsmall"><span class="uk-hidden-small uk-hidden-medium">Upload </span>Images</span><span class="uk-visible-xsmall">Img</span></a></li>
            <li class="uk-width-1-6" id="settings-categories"><a href="#" class="uk-text-truncate"><span class="uk-hidden-xsmall"><span class="uk-hidden-small uk-hidden-medium">Topic </span>Categories</span><span class="uk-visible-xsmall">Cat</span></a></li>
            <li class="uk-width-1-6" id="settings-topics"><a href="#"><span class="uk-hidden-xsmall"><span class="uk-hidden-small uk-hidden-medium">Topic Settings</span><span class="uk-hidden-large">Topics</span></span><span class="uk-visible-xsmall">Top</span></a></li>
        </ul>
        
        <ul id="settings-tabs" class="uk-switcher">
            <li class="uk-panel uk-panel-box">
                <div class="uk-grid uk-grid-match" data-uk-grid-margin>
                    <div class="uk-width-medium-1-2">
                        <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                            <div class="uk-panel-title">
                                <span class="uk-text-bold uk-text-primary">Board Name</span>
                            </div>
                            
                            <p>Enter up to 30 characters, using only letters, numbers, dashes and spaces.</p>
                            
                            <input class="uk-width-2-3 uk-form-large" type="text" id="settings-name" maxlength="30" value="<?php echo $board->name; ?>">
                        </div>
                    </div>
                    
                    <div class="uk-width-medium-1-2">
                        <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                            <div class="uk-panel-title">
                                <span class="uk-text-bold uk-text-primary">Subscription<span class="uk-hidden-xsmall"> Plan</span></span>
                                
                                <?php if ($board->plan != 4) { ?>
                                <br />
                                
                                <span class="uk-text-small settings-expires">
                                    <?php
                                    if ($board->expires) {
                                        if ($board->stripe_cancelled > 0) {
                                            echo '<span class="uk-text-danger">Your board subscription has been cancelled.<br />You must add a credit card or you will lose ownership in <strong>' . GrokBB\Util::getTimespan(strtotime('+30 days', $board->expires), 2) . '</strong></span>';
                                        } else if (time() < $board->expires) {
                                            echo 'Your trial period will end in <strong>' . GrokBB\Util::getTimespan($board->expires, 2) . '</strong>';
                                        } else if ($board->stripe_id === '0') {
                                            echo '<span class="uk-text-danger">Your trial period has ended.<br />You must add a credit card or you will lose ownership in <strong>' . GrokBB\Util::getTimespan(strtotime('+30 days', $board->expires), 2) . '</strong></span>';
                                        } else {
                                            $stripe = \GrokBB\Board::subscribeInfo($board->id);
                                            
                                            if (is_object($stripe)) {
                                                $renews = $stripe->subscriptions->data[0]->current_period_end;
                                                echo 'Your credit card will be charged in <strong>' . GrokBB\Util::getTimespan($renews, 2) . '</strong>';
                                            } else {
                                                echo '<span class="uk-text-danger">' . $stripe . '</span>';
                                            }
                                        }
                                    }
                                    ?>
                                </span>
                                
                                <?php if ($board->stripe_id === '0' || $board->stripe_cancelled > 0) { ?>
                                <div class="uk-panel-badge">
                                    <button id="settings-plan-stripe" class="uk-button uk-button-primary">Add Credit Card</button>
                                </div>
                                <?php } ?>
                                
                                <?php } ?>
                            </div>
                            
                            <?php if ($board->plan == 4) { ?>
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1 uk-text-center">
                                    <h4><strong><i class="uk-icon uk-icon-heart"></i>&nbsp;&nbsp;FREE</strong> Subscription</h4>
                                    <div class="uk-alert uk-alert-info">
                                        Thank you for supporting GrokBB !
                                    </div>
                                </div>
                            </div>
                            <?php } else if ($board->stripe_id === '0' || $board->stripe_cancelled > 0) { ?>
                            <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                                <div class="uk-width-small-1-2 uk-container-center">
                                    <div id="settings-plan-monthly" class="uk-panel uk-panel-box uk-panel-header <?php if ($board->plan == 1) { echo 'uk-panel-box-primary'; } ?>">
                                        <h3>Monthly</h3>
                                        <div>
                                            <input type="radio" id="settings-plan-monthly-radio" name="settings-plan" value="1" <?php if ($board->plan == 1) { echo 'checked="checked"'; } ?>>
                                            &nbsp;$3 per month
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-width-small-1-2 uk-container-center">
                                    <div id="settings-plan-yearly" class="uk-panel uk-panel-box uk-panel-header <?php if ($board->plan == 0) { echo 'uk-panel-box-primary'; } ?>">
                                        <h3>Yearly</h3>
                                        <div>
                                            <input type="radio" id="settings-plan-yearly-radio" name="settings-plan" value="0" <?php if ($board->plan == 0) { echo 'checked="checked"'; } ?>>
                                            &nbsp;$30 per year
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1 uk-text-center">
                                    <?php if ($board->plan == 1) { ?>
                                    <h4>Monthly Subscription ($3 per month)</h4>
                                    <?php } else { ?>
                                    <h4>Yearly Subscription ($30 per year)</h4>
                                    <?php } ?>
                                    
                                    <button class="uk-button uk-button-danger" data-uk-modal="{ target: '#modal-cancel', bgclose: false, center: true }">Cancel Subscription</button>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <br />
                
                <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                    <div class="uk-panel-title">
                        <span class="uk-text-bold uk-text-primary">Board Type</span>
                    </div>
                    
                    <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                        <div class="uk-width-xlarge-1-3 uk-container-center">
                            <div id="settings-type-public" class="uk-panel uk-panel-box uk-panel-header <?php if ($board->type == 0) { echo 'uk-panel-box-primary'; } ?>">
                                <h3>Public</h3>
                                
                                <div class="uk-grid uk-grid-small">
                                    <div class="uk-width-1-10">
                                        <input type="radio" id="settings-type-public-radio" name="settings-type" value="0" <?php if ($board->type == 0) { echo 'checked="checked"'; } ?>>
                                    </div>
                                    <div class="uk-width-9-10 settings-allowed">
                                        Anyone is allowed to READ topics<br />
                                        Anyone is allowed to comment<br />
                                        Anyone is allowed to POST topics
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-xlarge-1-3 uk-container-center">
                            <div id="settings-type-moderated" class="uk-panel uk-panel-box uk-panel-header <?php if ($board->type == 2) { echo 'uk-panel-box-primary'; } ?>">
                                <h3>Moderated</h3>
                                
                                <div class="uk-grid uk-grid-small">
                                    <div class="uk-width-1-10">
                                        <input type="radio" id="settings-type-moderated-radio" name="settings-type" value="2" <?php if ($board->type == 2) { echo 'checked="checked"'; } ?>>
                                    </div>
                                    <div class="uk-width-9-10 settings-allowed">
                                        Anyone is allowed to READ topics<br />
                                        Anyone is allowed to comment<br />
                                        <span class="uk-text-danger">Only approved users can POST topics</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-xlarge-1-3 uk-container-center">
                            <div id="settings-type-private" class="uk-panel uk-panel-box uk-panel-header <?php if ($board->type == 1) { echo 'uk-panel-box-primary'; } ?>">
                                <h3>Private</h3>
                                
                                <div class="uk-grid uk-grid-small">
                                    <div class="uk-width-1-10">
                                        <input type="radio" id="settings-type-private-radio" name="settings-type" value="1" <?php if ($board->type == 1) { echo 'checked="checked"'; } ?>>
                                    </div>
                                    <div class="uk-width-9-10 settings-allowed">
                                        <span class="uk-text-danger">Only approved users can READ topics</span><br />
                                        <span class="uk-text-danger">Only approved users can comment</span><br />
                                        <span class="uk-text-danger">Only approved users can POST topics</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <br />
                
                <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                    <div class="uk-panel-title">
                        <span class="uk-text-bold uk-text-primary">Board Header</span>
                    </div>
                    
                    <p>This will replace the default background image that is displayed behind your board's name.</p>
                    
                    <p class="uk-alert uk-alert-info uk-text-small">
                        We only accept PNG images. The maximum width / height your image should be is 1920x80px. Click on &nbsp;<i class="uk-icon uk-icon-image"></i>&nbsp; to upload a new one.
                    </p>
                    
                    <div class="uk-thumbnail" style="width: 100%">
                        <div id="settings-header-progressbar" class="uk-progress uk-progress-striped uk-active uk-width-1-1" style="display: none">
                            <div class="uk-progress-bar" style="width: 0%;"></div>
                        </div>
                        
                        <div id="settings-header-image">
                            <img src="<?php echo SITE_BASE_URL; ?>/img.php?bid=<?php echo $_SESSION['board']->id; ?>">
                        </div>
                        
                        <div class="uk-thumbnail-caption uk-text-left uk-text-small">
                            <div id="settings-header-link">
                                <span class="uk-text-bold">Image URL:</span>&nbsp;<?php echo SITE_BASE_URL; ?>/img.php?bid=<?php echo $_SESSION['board']->id; ?>
                            </div>
                            <div id="settings-header-menu" class="uk-align-right">
                                <!-- TODO: this has been disabled because it causes the background color to flash momentarily, right before the image is loaded
                                           we need a new option to enable / disable the background color option, instead of automatically including it with the image
                                <span class="uk-text-bold">Background Color:</span>&nbsp;&nbsp;<input type="text" id="settings-header-back-color" value="<?php echo $board->header_back_color; ?>" />
                                &nbsp;&nbsp;&nbsp;
                                -->
                                <span class="uk-text-bold">Repeat Image:</span>&nbsp;&nbsp;<input type="checkbox" id="settings-header-back-repeat" value="1" <?php echo ($board->header_back_repeat) ? 'checked="checked"' : ''; ?>/>
                                &nbsp;&nbsp;&nbsp;
                                <span class="uk-form-file"><i id="settings-header-upload" class="uk-icon uk-icon-image"></i><input id="settings-header-select" type="file" data-uk-tooltip="{ pos: 'bottom-right' }" title="Upload New Image"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <br />
                
                <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                    <div class="uk-panel-title">
                        <span class="uk-text-bold uk-text-primary">Board Tagline</span>
                    </div>
                    
                    <p>This short description will display alongside your board's name when users browse or search.</p>
                    
                    <input class="uk-width-1-1 uk-form-large" type="text" id="settings-tagline" maxlength="120" value="<?php echo $board->desc_tagline; ?>">
                </div>
                
                <br />
                
                <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                    <div class="uk-panel-title">
                        <span class="uk-text-bold uk-text-primary">Board Tags</span>
                    </div>
                    
                    <p>Enter up to 3 words or phrases that describe your board's content. Users can use tags to search for boards with similar content.</p>
                    
                    <input class="uk-width-1-4 uk-form-large uk-text-center" type="text" id="settings-tag1" maxlength="15" value="<?php echo $board->tag1; ?>">
                    &nbsp;&nbsp;
                    <input class="uk-width-1-4 uk-form-large uk-text-center" type="text" id="settings-tag2" maxlength="15" value="<?php echo $board->tag2; ?>">
                    &nbsp;&nbsp;
                    <input class="uk-width-1-4 uk-form-large uk-text-center" type="text" id="settings-tag3" maxlength="15" value="<?php echo $board->tag3; ?>">
                </div>
                
                <br />
                
                <?php if ($board->type == 1) { ?>
                <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                    <div class="uk-panel-title">
                        <span class="uk-text-bold uk-text-primary">Request For Access</span>
                    </div>
                    
                    <p>This text will be displayed to users who are requesting access to your board.</p>
                    
                    <textarea id="board-request-access"><?php echo $settings->board_request_access_md; ?></textarea>
                </div>
                
                <br />
                <?php } ?>
                
                <button id="settings-update" class="uk-button uk-button-primary uk-align-right">Update Settings</button>
                &nbsp;<span id="settings-msg" class="uk-alert uk-alert-danger uk-text-small uk-align-right" style="display: none"></span>
            </li>
            
            <li class="uk-panel uk-panel-box">
                <div class="uk-panel-title uk-clearfix uk-margin-remove">
                    <span class="uk-text-bold uk-text-primary">Sidebar Description</span>
                    <button id="editor-save-sidebar" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Save Description</button>
                    <div class="uk-hidden-xsmall uk-text-small uk-align-right gbb-editor-characters"><span id="editor-char-sidebar">0</span> / 15,000 characters</div>
                </div>
                
                <div class="gbb-line-height">This description will appear on the right, in your board's sidebar. You can use this area to describe your board's content, list its rules and moderators, provide links or say just about anything you want. The HTML editor uses <a href="https://help.github.com/articles/github-flavored-markdown" target="_blank">GHF Markdown</a> to format your content, and you can <button id="settings-upload" class="uk-button uk-button-primary">Upload Images</button> too.</div><br />
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row">
                        <textarea id="editor-text-sidebar"><?php echo $board->desc_sidebar_md; ?></textarea>
                    </div>
                </form>
                
                <div class="uk-form-row gbb-spacing-large">
                    <input type="checkbox" id="desc_sidebar_mods" value="1" <?php echo ($board->desc_sidebar_mods) ? 'checked="checked"' : ''; ?>/>
                    <label class="uk-form-label uk-text-bold" for="desc_sidebar_mods" style="display: inline">Display Moderators</label>
                    
                    <div class="uk-text-small gbb-form-help gbb-spacing">Display your username and a list of all your moderators at the bottom of your sidebar</span>
                </div>
                
                <div class="uk-form-row">
                    <input type="checkbox" id="desc_sidebar_whos" value="1" <?php echo ($board->desc_sidebar_whos) ? 'checked="checked"' : ''; ?>/>
                    <label class="uk-form-label uk-text-bold" for="desc_sidebar_whos" style="display: inline">Display Who is Online</label>
                    
                    <div class="uk-text-small gbb-form-help gbb-spacing">Display a list of all the users currently browsing your board at the bottom of your sidebar</span>
                </div>
            </li>
            
            <li id="settings-stylesheet-panel" class="uk-panel uk-panel-box">
                <div class="uk-panel-title">
                    <span class="uk-text-bold uk-text-primary">Your Stylesheet</span>
                    <button id="editor-save-stylesheet" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Save Stylesheet</button>
                    <div class="uk-text-small uk-align-right gbb-editor-characters uk-hidden-xsmall"><span id="editor-char-stylesheet">0</span> / 15,000 characters</div>
                </div>
                
                <div class="gbb-line-height">
                    <div class="gbb-spacing-small uk-text-nowrap uk-hidden-xsmall">
                        These settings allow you to update your board's branding without having to write custom CSS.
                    </div>
                    
                    <table cellspacing="15" cellpadding="0" width="100%" class="uk-text-small">
                        <tr>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Header Menu Color</span><span class="uk-visible-xsmall">HMC</span>:</td>
                            <td><input type="text" id="settings-header-menu-color" value="<?php echo $board->header_menu_color; ?>" /></td>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Board Name Color</span><span class="uk-visible-xsmall">BNC</span>:</td>
                            <td><input type="text" id="settings-header-name-color" value="<?php echo $board->header_name_color; ?>" /></td>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Board Name Font</span><span class="uk-visible-xsmall">BNF</span>:</td>
                            <td width="100%"><input type="text" id="settings-header-name-font" value="<?php echo $board->header_name_font; ?>"  class="uk-width-xlarge-1-2" /></td>
                        </tr>
                        <tr>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Button Color</span><span class="uk-visible-xsmall">BC</span>:</td>
                            <td><input type="text" id="settings-button-color" value="<?php echo $board->button_color; ?>" /></td>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Button Hover</span><span class="uk-visible-xsmall">BH</span>:</td>
                            <td><input type="text" id="settings-button-hover" value="<?php echo $board->button_hover; ?>" /></td>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Button Font</span><span class="uk-visible-xsmall">BF</span>:</td>
                            <td width="100%"><input type="text" id="settings-button-font" value="<?php echo $board->button_font; ?>"  class="uk-width-xlarge-1-2" /></td>
                        </tr>
                        <tr>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Button Text Color</span><span class="uk-visible-xsmall">BTC</span>:</td>
                            <td><input type="text" id="settings-button-text-color" value="<?php echo $board->button_text_color; ?>" /></td>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Button Text Hover</span><span class="uk-visible-xsmall">BTH</span>:</td>
                            <td><input type="text" id="settings-button-text-hover" value="<?php echo $board->button_text_hover; ?>" /></td>
                            <td class="uk-text-bold uk-text-right uk-text-nowrap"><span class="uk-hidden-xsmall">Tag Color</span><span class="uk-visible-xsmall">TC</span>:</td>
                            <td width="100%"><input type="text" id="settings-tag-color" value="<?php echo $board->tag_color; ?>" /></td>
                        </tr>
                    </table>
                </div>
                
                <div class="gbb-spacing-large gbb-line-height">
                    <h3 class="uk-text-primary uk-text-bold uk-margin-remove">Write Custom CSS</h3>
                    
                    <div class="gbb-spacing-small">
                        If you want to do more advanced stuff then you can write custom CSS below to override the GrokBB styles.<span class="uk-hidden-xsmall uk-hidden-small"><br /></span>
                        This will allow your board to have a unique layout and branding.<span class="uk-hidden-xsmall"> You can also <button id="settings-upload" class="uk-button uk-button-primary">Upload Images</button> and use the generated URLs here too.</span>
                    </div>
                </div>
                
                <div class="uk-alert uk-alert-danger uk-text-small">
                    <i class="uk-icon uk-icon-warning"></i>&nbsp;&nbsp;The site is currently in Beta testing mode and class names may change while the site is evolving, and so this feature is considered experimental right now.
                </div>
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row">
                        <textarea id="editor-text-stylesheet"><?php echo $board->stylesheet; ?></textarea>
                    </div>
                </form>
            </li>
            
            <li class="uk-panel uk-panel-box">
                <div class="uk-grid">
                    <div class="uk-width-1-1">You can upload images here that can be used in your board's sidebar or stylesheet.</div>
                    
                    <div class="uk-width-1-1 gbb-spacing">You are currently using <strong><?php echo $imagesSize; ?></strong> / 50 MB.<br /><br /></div>
                    
                    <div class="uk-width-small-4-6 uk-width-medium-3-5 uk-width-large-3-5 uk-width-xlarge-2-5 uk-container-center gbb-spacing-large">
                        <div>
                            <span class="uk-form-file">
                                <button class="uk-button uk-button-primary">Upload New Image ...</button>
                                <input id="image-select" type="file">
                            </span>
                            
                            &nbsp;&nbsp;
                            
                            <span class="uk-alert uk-alert-info uk-text-small uk-hidden-xsmall">
                                We only accept PNG, JPG and GIF images.
                            </span>
                        </div>
                        
                        <div id="image-progressbar" class="uk-progress uk-progress-striped uk-active uk-width-1-1" style="display: none">
                            <div class="uk-progress-bar" style="width: 0%;"></div>
                        </div>
                        
                        <div id="upload-msg" class="uk-alert uk-alert-danger uk-text-small uk-align-left" style="display: none"></div>
                    </div>
                </div>
                
                <div class="uk-grid gbb-padding-large" data-uk-grid-margin>
                    <?php
                    $imgNum = 0;
                    
                    foreach ($images as $image) {
                        if (in_array($image, array('.', '..'))) {
                            continue;
                        }
                        
                        $imgNum++;
                        
                        $imgURL = SITE_BASE_URL . '/img.php?bid=' . $board->id . '&img=' . $image;
                    ?>
                    <div class="uk-width-medium-1-2">
                        <div class="uk-grid">
                            <div class="uk-width-2-10">
                                <img class="image-window" src="<?php echo $imgURL; ?>">
                            </div>
                            <div class="uk-width-7-10">
                                <a href="javascript: void(0)" onclick="copyToClipboard('image<?php echo $imgNum; ?>')">Copy URL to Clipboard</a><br />
                                <span class="uk-hidden-xsmall"><span id="image<?php echo $imgNum; ?>"><?php echo $imgURL; ?></span><br /></span>
                                File Size: <strong><?php echo round(filesize($imagesPath . DIRECTORY_SEPARATOR . $image) / 1024 / 1024, 2); ?> MB</strong>
                            </div>
                            <div class="uk-width-1-10">
                                <i id="image-delete" class="uk-icon uk-icon-remove" onclick="deleteImage('<?php echo $image; ?>')" data-uk-tooltip="{ pos: 'top-right' }" title="Delete Image"></i>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </li>
            
            <li class="uk-panel uk-panel-box">
                <div class="uk-grid">
                    <div class="uk-width-1-1">
                        You can organize your board's content by creating categories that users assign to their topics.
                    </div>
                    
                    <br />
                    
                    <div class="uk-width-xlarge-3-5 uk-container-center gbb-spacing-large">
                        <div class="uk-form uk-form-stacked">
                            <div class="uk-form-row">
                                <label class="uk-form-label uk-text-bold" for="category-name">Category Name & Color</label>
                                <div class="uk-text-small gbb-form-help">Enter up to 30 characters, using only letters, numbers, and a few <span class="uk-text-danger" data-uk-tooltip="{ pos: 'bottom-left' }" title="Allowed Special Characters&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ! @ # $ % ^ & * ( ) [ ] { } - + = | ' : . ; ~ ` , ? / and spaces.">special characters</span>.</div>
                                
                                <div class="uk-form-controls">
                                    <input class="uk-width-2-4" type="text" id="category-name" maxlength="30">
                                    &nbsp;
                                    <input type="text" id="category-color" value="#23395B" />
                                    &nbsp;
                                    <input type="checkbox" id="category-private" /><span id="category-private-text" data-uk-tooltip="{ pos: 'top' }" title="Only allow moderators access to this category when creating a topic">Private</span>
                                    &nbsp;&nbsp;<span class="uk-visible-xsmall"><br /><br /></span>
                                    <button id="category-create" class="uk-button uk-button-primary">Add New Category</button>
                                </div>
                            </div>
                            
                            <div class="uk-form-row">
                                <label class="uk-form-label uk-text-bold">Category Image</label>
                                <div class="uk-text-small gbb-form-help">This is optional. You must still enter a name, and the image will be cropped if it doesn't meet the 5:1 ratio.</div>
                                
                                <span class="uk-form-file">
                                    <button class="uk-button uk-button-primary">Upload Category Image ...</button>
                                    <input id="category-select" type="file">
                                </span>
                                
                                &nbsp;&nbsp;
                                
                                <span class="uk-alert uk-alert-info uk-text-small uk-hidden-xsmall">
                                    We only accept PNG images.<span class="uk-hidden-small"> They will automatically be resized to 200x40 pixels.</span>
                                </span>
                                
                                <div id="category-progressbar" class="uk-progress uk-progress-striped uk-active uk-width-1-1" style="display: none">
                                    <div class="uk-progress-bar" style="width: 0%;"></div>
                                </div>
                                
                                <div id="category-msg" class="uk-alert uk-alert-danger uk-text-small uk-align-left" style="display: none"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <br /><br />
                
                <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                    <?php foreach ($categories as $category) { ?>
                    <div class="uk-width-small-4-10 uk-width-medium-1-3 uk-width-xlarge-1-4">
                        <?php if ($category->image) { ?>
                        <img src="<?php echo SITE_BASE_URL . '/img.php?bid=' . $board->id . '&cat=' . $category->id; ?>" width="200" height="40" data-uk-tooltip="{ pos: 'top-right' }" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                        <?php } else { ?>
                        <figure class="uk-overlay" style="background-color: <?php echo $category->color; ?>">
                            <img src="<?php echo SITE_BASE_URL . '/img/category.png'; ?>" width="200" height="40" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>">
                            <figcaption class="uk-overlay-panel uk-flex uk-flex-middle"><?php echo $category->name; ?></figcaption>
                        </figure>
                        <?php } ?>
                        
                        <div class="gbb-spacing-small">
                            <span class="uk-form-file"><i id="category-upload-<?php echo $category->id; ?>" class="uk-icon uk-icon-image"></i><input id="category-upload-select-<?php echo $category->id; ?>" type="file" data-uk-tooltip="{ pos: 'top-left' }" title="Upload New Image"></span>
                            <?php if ($category->image) { ?>
                            <i id="category-remove" class="uk-icon uk-icon-font" onclick="removeCategoryImage('<?php echo $category->id; ?>')" data-uk-tooltip="{ pos: 'top-left' }" title="Remove Image"></i>&nbsp;
                            <?php } ?>
                            <i id="category-update" class="uk-icon uk-icon-cog" onclick="updateCategory('<?php echo $category->id; ?>', '<?php echo str_replace("'", "\\'", $category->name); ?>', '<?php echo $category->color; ?>', <?php echo (int) $category->private; ?>)" data-uk-tooltip="{ pos: 'top-left' }" title="Edit Category"></i>&nbsp;
                            <?php if ($category->defcat == 0) { ?>
                            <i id="category-toggle" class="uk-icon uk-icon-toggle-off" onclick="updateCategoryDefault('<?php echo $category->id; ?>')" data-uk-tooltip="{ pos: 'top-left' }" title="Set as Default Category"></i>&nbsp;
                            <i id="category-delete" class="uk-icon uk-icon-remove" onclick="deleteCategory('<?php echo $category->id; ?>', '<?php echo str_replace("'", "\\'", $category->name); ?>')" data-uk-tooltip="{ pos: 'top-left' }" title="Delete Category"></i>&nbsp;
                            <?php } else { ?>
                            <i id="category-toggle" class="uk-icon uk-icon-toggle-on" data-uk-tooltip="{ pos: 'top-left' }" title="Default Category" style="cursor: help"></i>&nbsp;
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </li>
            
            <li class="uk-panel uk-panel-box">
                You can create a tagging system for your board by entering a list of allowed tags here. Any time a moderator adds one of these tags to a topic it becomes available for users to filter on.
                This allows your users to search for related content at a more granular level, instead of just filtering by topic category.
                
                <div class="uk-form uk-form-stacked gbb-spacing">
                    <div class="uk-form-row">
                        <ul class="uk-width-1" id="board-tags"></ul>
                    </div>
                </div>
                
                <br />
                
                <span class="uk-hidden-small uk-hidden-xsmall">These settings affect how your &nbsp;<button href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/topic/0'; ?>" class="uk-button uk-button-primary sidebar-button">Create Topic&nbsp;&nbsp;<i class="uk-icon-chevron-circle-right"></i></button>&nbsp; page looks and what options are available to users.</span>
                
                <span class="uk-hidden-small uk-hidden-xsmall"><br /><br /></span>
                
                <div class="uk-form uk-form-stacked">
                    <div class="uk-form-row">
                        <label class="uk-form-label uk-text-bold" for="topic-content-name">Content Name</label>
                        <div class="uk-text-small gbb-form-help">This name is displayed above the content editor for a new topic. Enter up to 60 characters, using text only.</div>
                        
                        <input class="uk-width-1-2" type="text" id="topic-content-name" maxlength="60" value="<?php echo $settings->topic_content_name; ?>">
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label uk-text-bold" for="topic-content-desc">Content Description</label>
                        <div class="uk-text-small gbb-form-help">This description is displayed below the name and can be used to remind users of your posting rules and / or content guidelines.</div>
                        
                        <textarea id="topic-content-desc"><?php echo $settings->topic_content_desc_md; ?></textarea>
                    </div>
                    
                    <?php if ($board->type == 2) { ?>
                    <div class="uk-form-row">
                        <label class="uk-form-label uk-text-bold" for="topic-request-access">Request For Access</label>
                        <div class="uk-text-small gbb-form-help">This text will be displayed to users who are requesting access to post topics on your board.</div>
                        
                        <textarea id="topic-request-access"><?php echo $settings->topic_request_access_md; ?></textarea>
                    </div>
                    <?php } ?>
                    
                    <!--
                    <div class="uk-form-row">
                        <input type="checkbox" id="topic-content-allowpolls" value="1" <?php echo ($board->topic_allowpolls) ? 'checked="checked"' : ''; ?>/>
                        <label class="uk-form-label uk-text-bold" for="topic-content-name" style="display: inline">Allow Polls</label>
                        
                        <div class="uk-text-small gbb-form-help gbb-spacing">Allow users to include polls in their topics</span>
                    </div>
                    -->
                    
                    <br />
                
                    <button id="settings-update-topics" class="uk-button uk-button-primary uk-align-right">Update Settings</button>
                    &nbsp;<span id="settings-topics-msg" class="uk-alert uk-alert-danger uk-text-small uk-align-right" style="display: none"></span>
                </div>
            </li>
        </ul>
    </div>
    <?php require('sidebar.php'); ?>
</div>

<div id="modal-category-update" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Category Name & Color</div>
                
                <div class="uk-form gbb-spacing">
                    <div class="uk-text-small gbb-form-help">Enter up to 30 characters, using only letters, numbers, and a few <span class="uk-text-danger" data-uk-tooltip="{ pos: 'bottom-left' }" title="Allowed Special Characters&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ! @ # $ % ^ & * ( ) [ ] { } - + = | ' : . ; ~ ` ? and spaces.">special characters</span>.</div>
                    
                    <div class="uk-form-controls">
                        <input class="uk-width-2-4" type="text" id="category-update-name" maxlength="30">
                        &nbsp;
                        <input type="text" id="category-update-color" value="" />
                        &nbsp;
                        <input type="checkbox" id="category-update-private" /><span id="category-private-text" data-uk-tooltip="{ pos: 'top' }" title="Only allow moderators access to this category when creating a topic">Private</span>
                    </div>
                </div>
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row uk-align-right gbb-spacing-large">
                        <span id="category-update-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                        &nbsp;
                        <a id="category-update-submit" class="uk-button uk-button-primary">Update</a>
                        &nbsp;
                        <a id="category-update-cancel" class="uk-button uk-button-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal-category-delete" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Confirm Category Delete</div>
                
                <div class="gbb-spacing">
                Are you sure you want to delete the <span id="category-delete-name" class="uk-text-bold"></span> category?<br />
                All topics currently associated to this category will be reset to the default one.
                </div>
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row uk-align-right gbb-spacing-large">
                        <a id="category-delete-confirm" class="uk-button uk-button-danger">Confirm Delete</a>
                        &nbsp;
                        <a id="category-delete-cancel" class="uk-button uk-button-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal-cancel" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <div class="uk-width-1-1">
            <div class="uk-panel uk-panel-box uk-panel-header gbb-spacing-large">
                <div class="uk-panel-title uk-text-bold uk-text-primary">Cancel Subscription</div>
                
                <div class="gbb-spacing">
                Are you sure you want to cancel your subscription?<br />
                </div>
                
                <form class="uk-form uk-form-stacked">
                    <div class="uk-form-row uk-align-right gbb-spacing-large">
                        <a id="settings-cancel" class="uk-button uk-button-danger">Yes, Cancel My Subscription</a>
                        &nbsp;
                        <a id="settings-return" class="uk-button uk-button-primary">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>