<?php require(SITE_BASE_APP . 'header.php'); ?>

<div class="gbb-backdrop">
    <div class="uk-grid">
        <div class="uk-width-large-1 uk-width-xlarge-3-4 uk-container-center">
            <div class="uk-grid uk-grid-match uk-grid-divider" data-uk-grid-margin>
                <div class="uk-width-large-1-2 uk-hidden-small">
                    <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                        <h2>Board Features</h2>
                        
                        <div class="uk-grid">
                            <div class="uk-width-1 uk-container-center">
                                <ul class="uk-list uk-list-line">
                                    <li class="uk-text-danger"><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;No Ads, Ever&nbsp;!</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Topics Never Get Locked (unless you want them too)</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Create Sticky Topics, Private Topics and Media Slideshows</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Send Board Announcements To All Subscribed Users</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Built-In Category System for Organizing Topics</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Use Tags To Create Your Own Filtering System</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Extensive Moderation Tools (including banning users)</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Users Can Subscribe, Search, Sort and Filter Topics</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Create Badges and Customize Your Own Branding</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Access to User / Board / Topic Analytics</li>
                                    <li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;All Boards & Content is Optimized for Mobile Devices</li>
                                    <!--<li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;File / Photo Gallery - Coming Soon!</li>-->
                                    <!--<li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;Customizable Chat Room - Coming Soon ! *</li>-->
                                    <!--<li><i class="uk-icon uk-icon-dot-circle-o"></i>&nbsp;&nbsp;New Features Added All The Time *</li>-->
                                    <li class="uk-text-small">
                                        * Please join the discussions or read more about our roadmap at <a href="<?php echo SITE_BASE_URL; ?>/g/GrokBB_Dev">GrokBB Dev</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="uk-width-large-1-2">
                    <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                        <h2>Subscription Plan</h2>
                        
                        <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                            <!--
                            <div class="uk-width-small-1-2 uk-container-center">
                                <div id="create-plan-monthly" class="uk-panel uk-panel-box uk-panel-header">
                                    <h3>Monthly</h3>
                                    <div>
                                        <input type="radio" id="create-plan-monthly-radio" name="create-plan" value="1">
                                        &nbsp;$3 per month
                                    </div>
                                </div>
                            </div>
                            <div class="uk-width-small-1-2 uk-container-center">
                                <div id="create-plan-yearly" class="uk-panel uk-panel-box uk-panel-header uk-panel-box-primary">
                                    <h3>Yearly</h3>
                                    <div>
                                        <input type="radio" id="create-plan-yearly-radio" name="create-plan" value="0" checked="checked">
                                        &nbsp;$30 per year
                                    </div>
                                </div>
                            </div>
                            -->
                            <div class="uk-width-1 uk-text-center">
                                <div class="uk-panel uk-panel-box uk-panel-header uk-panel-box-primary">
                                    <h4><strong><i class="uk-icon uk-icon-heart"></i>&nbsp;&nbsp;FREE</strong> Subscription</i></h4>
                                </div>
                            </div>
                            <div class="uk-width-1 uk-container-center" style="margin-top: 18px">
                                <div class="uk-alert uk-text-small">
                                    <h3>The Fine Print</h3>
                                    <p>All boards are free and require NO subscription.</p>
                                    <p>Eventually, we will monetize the site by creating premium, add-on features and these will require a paid subscription. The premium features will not handicap the free boards in any way. These will be features that require additional resources to support, like chat or more storage space for images.</p>
                                    <p>Please feel free to request new features over at <a href="<?php echo SITE_BASE_URL; ?>/g/GrokBB_Dev">GrokBB Dev</a>. We want you to feel at home here. We want you to have the tools you need to manage your boards, to be able to create unique, high quality content and discussions, and to give your audience an unrivaled user experience.</p>
                                    <h4 class="uk-text-center uk-margin-top-remove">Thank you for supporting GrokBB !</h4>
                                    <!--
                                    <p>This subscription is for ONE board. A new subscription is required for every board you want to create / manage. You will have a 30 day trial period before a credit card is required to keep your board open.</p>
                                    <p>Your subscription will automatically be renewed each period, but you can cancel at any time. 
                                    If you cancel, you will lose ownership at the end of your renewal period, and your board will become available for purchase by the general public.</p>
                                    <p>If a payment can not be processed then you will be given 30 days to resolve the issue. If that time passes without a payment then you will lose ownership of your board.</p>
                                    -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="uk-grid">
        <div class="uk-width-large-1 uk-width-xlarge-3-4 uk-container-center">
            <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                <h2>Board Type</h2>
                
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3 uk-container-center">
                        <div id="create-type-public" class="uk-panel uk-panel-box uk-panel-header uk-panel-box-primary">
                            <h3>Public</h3>
                            
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-10">
                                    <input type="radio" id="create-type-public-radio" name="create-type" value="0" checked="checked">
                                </div>
                                <div class="uk-width-9-10">
                                    Anyone is allowed to READ topics<br />
                                    Anyone is allowed to comment<br />
                                    Anyone is allowed to POST topics
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3 uk-container-center">
                        <div id="create-type-moderated" class="uk-panel uk-panel-box uk-panel-header">
                            <h3>Moderated</h3>
                            
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-10">
                                    <input type="radio" id="create-type-moderated-radio" name="create-type" value="2">
                                </div>
                                <div class="uk-width-9-10">
                                    Anyone is allowed to READ topics<br />
                                    Anyone is allowed to comment<br />
                                    <span class="uk-text-danger">Only approved users can POST topics</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3 uk-container-center">
                        <div id="create-type-private" class="uk-panel uk-panel-box uk-panel-header">
                            <h3>Private</h3>
                            
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-10">
                                    <input type="radio" id="create-type-private-radio" name="create-type" value="1">
                                </div>
                                <div class="uk-width-9-10">
                                    <span class="uk-text-danger">Only approved users can READ topics</span><br />
                                    <span class="uk-text-danger">Only approved users can comment</span><br />
                                    <span class="uk-text-danger">Only approved users can POST topics</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="uk-alert uk-alert-primary uk-text-small">
                    <i class="uk-icon uk-icon-coffee"></i>&nbsp;&nbsp;Don't worry too much about this. If you need to, this CAN be changed after you create your board.
                </div>
            </div>
        </div>
    </div>
    
    <div class="uk-grid">
        <div class="uk-width-large-1 uk-width-xlarge-3-4 uk-container-center">
            <div class="uk-alert uk-alert-danger uk-text-small">
                <h4><i class="uk-icon uk-icon-warning"></i>&nbsp;&nbsp;IMPORTANT - Please be aware ...</h4>
                Hate groups and sexual material of ANY kind will not be tolerated here. These boards will be taken down without notice. You have been warned.<br />
            </div>
        </div>
    </div>
    
    <div class="uk-grid gbb-spacing-large">
        <div class="uk-width-large-1 uk-width-xlarge-3-4 uk-container-center">
            <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                <h2>Board Name</h2>
                <p>Enter up to 30 characters, using only letters, numbers, dashes and spaces.</p>
                
                <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                    <div class="uk-width-small-1-3">
                        <input class="uk-form-large uk-width-1" type="text" id="create-name" maxlength="30">
                    </div>
                    <div class="uk-width-small-2-3">
                        <button <?php echo (isset($_SESSION['user'])) ? 'id="create-new"' : 'data-uk-modal="{ target: \'#modal-login\', bgclose: false, center: true }"'; ?> class="uk-button uk-button-primary gbb-spacing-small">Create New Board</button>
                        &nbsp;<span id="create-msg" class="uk-alert uk-alert-danger uk-text-small" style="display: none"></span>
                    </div>
                </div>
                
                <div class="uk-alert uk-alert-primary uk-text-small">
                    <i class="uk-icon uk-icon-coffee"></i>&nbsp;&nbsp;This CAN be changed after you create your board too, as long as the name you want is available.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>