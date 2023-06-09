<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . SITE_BASE_URL);
}

$GLOBALS['includeEditor'] = true;

require(SITE_BASE_APP . 'header.php');

if ($_SESSION['user']->updated == 0) {
    $bio = "# My Biography\n\nWelcome to your biography page! You can enter anything you want here using [GHF Markdown](https://help.github.com/articles/github-flavored-markdown/), but keep in mind that most HTML tags are not supported. They will be removed or sanitized when you save.\n\nDon't worry, you can still link to **XKCD** images (if that's your thing) ...\n\n![](http://imgs.xkcd.com/comics/exploits_of_a_mom.png)";
} else {
    $userProfile = $GLOBALS['db']->getOne('user', array('id' => $_SESSION['user']->id));
    $bio = $userProfile->bio_md;
}

$updated = GrokBB\Util::getTimespan($_SESSION['user']->updated);
?>

<h2>
    <img id="avatar-image" class="uk-thumbnail" width="60" height="60" src="<?php echo SITE_BASE_URL; ?>/img.php?uid=<?php echo $_SESSION['user']->id; ?>" />
    &nbsp;<a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $_SESSION['user']->id; ?>"><?php echo $_SESSION['user']->username; ?></a>
    <span class="uk-form-file gbb-user-profile-upload">
        <span class="uk-visible-xsmall"><br /></span>
        <button class="uk-button uk-button-primary">Upload New Avatar ...</button>
        <input id="avatar-select" type="file">
    </span>
    <div id="avatar-progressbar" class="uk-progress uk-progress-striped uk-active gbb-user-profile-upload">
        <div class="uk-progress-bar" style="width: 0%;"></div>
    </div>
    <span class="uk-hidden-xsmall uk-hidden-small uk-hidden-medium uk-alert uk-alert-info uk-text-small gbb-editor-button">
        We only accept PNG images, and they will automatically be resized to 60x60 pixels.
    </span>
</h2>

<br />

<div class="uk-grid">
    <div class="uk-width-1-1">
        <div class="uk-panel uk-panel-box uk-panel-header">
            <div class="uk-panel-title">
                <span class="uk-text-bold uk-text-primary">My Biography</span>
                <?php if ($updated > 0) { ?>
                <span class="uk-text-small gbb-user-profile-editor-last">--&nbsp;&nbsp;&nbsp;last updated <span id="editor-last" class="uk-text-bold"><?php echo $updated; ?></span> ago</span>
                <?php } ?>
                <button id="editor-save" class="uk-button uk-button-primary uk-align-right gbb-editor-button">Save Updates</button>
                <a href="<?php echo SITE_BASE_URL; ?>/user/view/<?php echo $_SESSION['user']->id; ?>" target="_blank" class="uk-hidden-xsmall uk-button uk-button-primary uk-align-right gbb-editor-button">View Live Version</a>
                <div class="uk-hidden-xsmall uk-text-small uk-align-right gbb-editor-characters"><span id="editor-char">0</span> / 15,000 characters</div>
            </div>
            <textarea id="editor-text" autofocus="autofocus"><?php echo $bio; ?></textarea>
        </div>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>