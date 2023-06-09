<?php
$GLOBALS['includeSlides'] = true;
require(SITE_BASE_APP . 'header.php');
?>

<div data-uk-slideshow="{ duration: 750 }">
    <ul class="uk-slideshow">
        <li>
            <div class="uk-panel uk-panel-box">
                <div class="uk-grid uk-grid-small">
                    <div class="uk-hidden-small uk-width-medium-1-3">
                        <img src="<?php echo SITE_BASE_URL; ?>/img/tour1.png">
                    </div>
                    <div class="uk-width-medium-2-3">
                        <h3 class="uk-panel-title uk-text-bold">What is GrokBB ?<span class="uk-align-right uk-text-small gbb-spacing-small"><a href="<?php echo SITE_BASE_URL; ?>">Back to Homepage</a></span></h3>
                        
                        <p>
                           GrokBB is a hosting platform for online bulletin boards, otherwise known as forums, message boards, or discussion groups. 
                           Each board has it's own unique content, style and rules that govern their users and the discussions that take place there. 
                           We merely provide a way to locate or create a community that interests you, and we give you some tools to keep track of your favorite boards, friends and discussions. 
                        </p>
                        
                        <p>
                           Unlike other popular, bulletin board websites, GrokBB is not a news aggregator. 
                           Our focus is on supporting and promoting niche communities, by giving them proper tools to manage their users and organize / produce high quality, original content.
                        </p>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <div class="uk-panel uk-panel-box">
                <div class="uk-grid uk-grid-small">
                    <div class="uk-hidden-small uk-width-medium-1-3">
                        <img src="<?php echo SITE_BASE_URL; ?>/img/tour2.png">
                    </div>
                    <div class="uk-width-medium-2-3">
                        <h3 class="uk-panel-title uk-text-bold">How Do I Participate ?<span class="uk-align-right uk-text-small gbb-spacing-small"><a href="<?php echo SITE_BASE_URL; ?>">Back to Homepage</a></span></h3>
                        
                        <p>
                           The GrokBB homepage allows you to search or browse for a board that interests you.
                           After finding one, click on its name, and you will be taken to that board's homepage.
                           This page will list all the discussions currently taking place, and it will provide you some tools to search or filter for specific content.
                        </p>
                        
                        <p>
                            Also, on the right, will be a sidebar that lists the board's guidelines and any other information you should know.
                            It is important to read this, as it will allow you to get a better understanding of the community you are in.
                            At this point, you can now post new topics, join in an active discussion, or just browse around.
                        </p>
                        
                        <p>
                            If you want to receive news / announcements from this board then make sure you add it as a Favorite.
                        </p>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <div class="uk-panel uk-panel-box">
                <div class="uk-grid">
                    <div class="uk-hidden-small uk-width-medium-1-3">
                        <img src="<?php echo SITE_BASE_URL; ?>/img/tour3.png">
                    </div>
                    <div class="uk-width-medium-2-3">
                        <h3 class="uk-panel-title uk-text-bold">Should I Create My Own Board ?<span class="uk-align-right uk-text-small gbb-spacing-small"><a href="<?php echo SITE_BASE_URL; ?>">Back to Homepage</a></span></h3>
                        
                        <p>
                           Creating a board gives you complete control over that community.
                           You can define it's logo, branding and color scheme. 
                           You can make it public or require users get approval before posting a topic.
                           You can even make it completely private, if you want, so that only you and a select few friends could use it.
                           You can moderate user's posts and create your own category / filtering system.
                           There is many configurable options.
                        </p>
                        
                        <p>
                           We give you access to daily / monthly / yearly statistics, so that you can track how your board is doing and who the contributing users are.
                           You can also award users with animated badges and reputation points for providing quality content.
                        </p>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    
    <ul class="uk-pagination uk-margin-top-remove">
        <li id="tour-prev" data-uk-slideshow-item="previous"><a href="#"><i class="uk-icon-angle-double-left"></i></a></li>
        <li id="tour-step1" data-uk-slideshow-item="0" class="uk-active"><a href="#">1</a></li>
        <li id="tour-step2" data-uk-slideshow-item="1"><a href="#">2</a></li>
        <li id="tour-step3" data-uk-slideshow-item="2"><a href="#">3</a></li>
        <li id="tour-next" data-uk-slideshow-item="next"><a href="#"><i class="uk-icon-angle-double-right"></i></a></li>
    </ul>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>