<?php header('HTTP/1.1 404 Not Found'); ?>
<?php require(SITE_BASE_APP . 'header.php'); ?>

<div class="uk-width-1-1">
    <div class="uk-alert uk-alert-large uk-alert-danger" data-uk-alert>
        <h2>404 Error</h2><p>The page you requested does not exist. You can try <a href="<?php echo SITE_BASE_URL; ?>/home/search" class="uk-button uk-button-primary">Searching</a> for what you were looking for or just go back to the <a href="<?php echo SITE_BASE_URL; ?>" class="uk-button uk-button-primary">Home Page</a></span>&nbsp;.</p>
    </div>
</div>

<?php require(SITE_BASE_APP . 'footer.php'); ?>