<?php header('Content-Type: text/css'); ?>
<?php require_once('../../../cfg.php'); ?>

/* GrokBB */

a, a:hover {
    color: #3454D1;
}

/* Note: I am duplicating these topic link styles from home.css.php because
 *       Chrome is not applying them when using the back button from a topic */

.topic-link,
.topic-link:hover {
    color: #000000;
}

.topic-link:visited {
    color: #A0A0A0;
}

input[type="checkbox"] {
    margin-right: 5px;
    position: relative;
    top: 2px;
}

input[type="radio"] {
    position: relative;
    top: 1px;
}

::-webkit-file-upload-button {
    cursor: pointer;
}

.gbb-icon-small {
    font-size: 75%;
}

.gbb-icon-large {
    font-size: 125%;
}

.gbb-icon-flip-h {
    -moz-transform: scale(-1, 1);
    -webkit-transform: scale(-1, 1);
    -o-transform: scale(-1, 1);
    -ms-transform: scale(-1, 1);
    transform: scale(-1, 1);
}

.gbb-icon-flip-v {
    -moz-transform: scale(1, -1);
    -webkit-transform: scale(1, -1);
    -o-transform: scale(1, -1);
    -ms-transform: scale(1, -1);
    transform: scale(1, -1);
}

.gbb-icon-rotate {
    -moz-transform: rotate(90deg);
    -webkit-transform: rotate(90deg);
    -o-transform: rotate(90deg);
    -ms-transform: rotate(90deg);
    transform: rotate(90deg);
}

.gbb-line-height {
    line-height: 200%;
}

.gbb-padding-small {
    padding: 5px;
}

.gbb-padding {
    padding: 10px;
}

.gbb-padding-large {
    padding: 15px;
}

.gbb-spacing-small {
    margin-top: 5px !important;
}

.gbb-spacing {
    margin-top: 10px !important;
}

.gbb-spacing-large {
    margin-top: 15px !important;
}

.gbb-backdrop {
    padding: 10px;
    background-color: #E4E4E7 !important;
}

.gbb-header {
    /* background: url('/img/header.png') repeat; */
    background-color: #18181B;
    cursor: pointer;
    height: 80px;
}

.gbb-header-text {
    padding-top: 20px;
    margin-left: 184px;
    <?php if (!$_SESSION['pagerqst']) { ?>
    /* text-shadow: 3px 3px #26547C; */
    <?php } ?>
    color: #FFFFFF;
}

.gbb-menu-icon-left {
    padding-left: 2px;
}

.gbb-menu-icon {
    width: 20px;
}

.gbb-help-icon {
    font-size: 75%;
    position: relative;
    bottom: 2px;
}

.gbb-form-help {
    padding-bottom: 10px;
}

.gbb-editor-button {
    margin-left: 15px;
    position: relative;
    bottom: 4px;
}

.gbb-editor-characters {
    position: relative;
    top: 4px;
}

.gbb-footer {
    color: #FFFFFF;
}

figure img {
    max-width: none;
}

@media /* (min-width: 375px) and */ (max-width: 479px) {
    .gbb-header-text {
        font-size: 150%;
    }
}

@media (min-width: 960px) {
    #modal-login-login,
    #modal-login-signup {
        display: block !important;
    }
}

/* UIkit Overrides */

em {
    color: #000000;
}

blockquote {
    font-size: 95%;
}

.uk-navbar {
    background-color: #23395B !important;
    color: #FFFFFF;
}

.uk-nav-sub li {
    margin-left: 0px;
}

.uk-button-primary {
    background-color: #267C4E !important;
    color: #FFFFFF !important;
}

.uk-button-primary:hover {
    background-color: #23395B !important;
    color: #FFFFFF !important;
}

.uk-button-link, .uk-button-link:hover {
    color: #3454D1;
}

.uk-button:disabled,
.uk-button:disabled:hover {
    background-color: #fafafa !important;
    color: #999999 !important;
    border-color: rgba(0, 0, 0, 0.06) !important;
    box-shadow: none !important;
    text-shadow: 0 1px 0 #ffffff !important;
}

.uk-panel {
    background-color: #E4E4E7 !important;
}

.uk-panel-box-primary {
    background-color: #EBF7FD !important;
}

.uk-panel-box-secondary {
    background-color: #FFFFFF !important;
}

.uk-badge-danger {
    background-color: #D85030 !important;
}

.uk-badge-success {
    background-color: #267C4E !important;
}

.uk-block-secondary a {
    color: #FFFFFF;
}

.uk-tab > li > a {
    color: #000000 !important;
}

.uk-tab > li.uk-active > a {
    background-color: #E4E4E7 !important;
    font-weight: bold;
}

.uk-text-primary {
    color: #23395B !important;
}

.uk-text-success {
    color: #267C4E !important;
}

.uk-alert a, .uk-alert a:hover {
    color: #000000;
}

.uk-alert-danger {
    font-weight: bold !important;
    border-width: 2px !important;
}

.uk-alert-success {
    font-weight: bold !important;
    border-width: 2px !important;
}

.uk-notify-message {
    border-color: #FFFFFF !important;
    padding: 5px !important;
    text-align: center !important;
    font-size: 95% !important;
}

.uk-notify-message-success {
    background-color: #267C4E !important;
    color: #FFFFFF !important;
}

.uk-pagination > .uk-active > a,
.uk-pagination > .uk-active > a:link,
.uk-pagination > .uk-active > a:visited,
.uk-pagination > .uk-active > a:active {
    background: #00A8E6 !important;
    color: #FFFFFF !important;
}

.uk-pagination > .uk-disabled i {
    background-color: #F5F5F5 !important;
    color: #999999 !important;
}

@media (min-width: 960px) {
  .uk-visible-xsmall {
    display: none !important;
  }
  .uk-visible-small {
    display: none !important;
  }
  .uk-visible-medium {
    display: none !important;
  }
  .uk-hidden-large {
    display: none !important;
  }
}

@media (min-width: 768px) and (max-width: 959px) {
  .uk-visible-xsmall {
    display: none !important;
  }
  .uk-visible-small {
    display: none !important;
  }
  .uk-visible-large {
    display: none !important ;
  }
  .uk-hidden-medium {
    display: none !important;
  }
}

@media (min-width: 480px) and (max-width: 767px) {
  .uk-visible-xsmall {
    display: none !important;
  }
  .uk-visible-medium {
    display: none !important;
  }
  .uk-visible-large {
    display: none !important;
  }
  .uk-hidden-small {
    display: none !important;
  }
}

@media (max-width: 479px) {
  .uk-visible-small {
    display: none !important;
  }
  .uk-visible-medium {
    display: none !important;
  }
  .uk-visible-large {
    display: none !important;
  }
  .uk-hidden-xsmall {
    display: none !important;
  }
}

@media /* (min-width: 375px) and */ (max-width: 479px) {
  .uk-grid-width-xsmall-1-1 > * {
    width: 100%;
  }
  .uk-grid-width-xsmall-1-2 > * {
    width: 50%;
  }
  .uk-grid-width-xsmall-1-3 > * {
    width: 33.333%;
  }
  .uk-grid-width-xsmall-1-4 > * {
    width: 25%;
  }
  .uk-grid-width-xsmall-1-5 > * {
    width: 20%;
  }
  .uk-grid-width-xsmall-1-6 > * {
    width: 16.666%;
  }
  .uk-grid-width-xsmall-1-10 > * {
    width: 10%;
  }
}

@media /* (min-width: 375px) and */ (max-width: 479px) {
  /* Whole */
  .uk-width-xsmall-1-1 {
    width: 100%;
  }
  /* Halves */
  .uk-width-xsmall-1-2,
  .uk-width-xsmall-2-4,
  .uk-width-xsmall-3-6,
  .uk-width-xsmall-5-10 {
    width: 50%;
  }
  /* Thirds */
  .uk-width-xsmall-1-3,
  .uk-width-xsmall-2-6 {
    width: 33.333%;
  }
  .uk-width-xsmall-2-3,
  .uk-width-xsmall-4-6 {
    width: 66.666%;
  }
  /* Quarters */
  .uk-width-xsmall-1-4 {
    width: 25%;
  }
  .uk-width-xsmall-3-4 {
    width: 75%;
  }
  /* Fifths */
  .uk-width-xsmall-1-5,
  .uk-width-xsmall-2-10 {
    width: 20%;
  }
  .uk-width-xsmall-2-5,
  .uk-width-xsmall-4-10 {
    width: 40%;
  }
  .uk-width-xsmall-3-5,
  .uk-width-xsmall-6-10 {
    width: 60%;
  }
  .uk-width-xsmall-4-5,
  .uk-width-xsmall-8-10 {
    width: 80%;
  }
  /* Sixths */
  .uk-width-xsmall-1-6 {
    width: 16.666%;
  }
  .uk-width-xsmall-5-6 {
    width: 83.333%;
  }
  /* Tenths */
  .uk-width-xsmall-1-10 {
    width: 10%;
  }
  .uk-width-xsmall-3-10 {
    width: 30%;
  }
  .uk-width-xsmall-7-10 {
    width: 70%;
  }
  .uk-width-xsmall-9-10 {
    width: 90%;
  }
}

/* Note: allows the audio player to position correctly on Chrome */
audio {
    height: 35px !important;
}

@media (max-width: 1219px) {
    /* Note: allows the audio player to display on iOS */
    audio {
        height: 40px !important;
    }
   
    /* Note: allows the audio player to vertically align on iOS */
    .uk-lightbox-content audio {
        position: absolute;
        top: 50%; left: 15%;
        margin-top: -18px;
        width: 70% !important;
    }
    
    /* Note: allows the lightbox nav to display on iOS */
    .uk-slidenav-position .uk-slidenav {
        display: block !important;
    }
}

/* Spectrum Overrides */

.sp-replacer {
    width: 32px !important;
    height: 12px !important;
    border-color: #000000 !important;
}

.sp-replacer:hover {
    border-color: #000000 !important;
}

.sp-preview {
    width: 15px !important;
    height: 10px !important;
}

.sp-dd {
    line-height: 4px !important;
}

.sp-container {
    border-color: #000000 !important;
}

.sp-input:focus  {
    border-color: #D85030;
}

.sp-cancel {
    padding-top: 5px !important;
}

.sp-choose {
    background-color: #267C4E !important;
    background-image: none !important;
    color: #FFFFFF !important;
    border-color: #267C4E !important;
    text-shadow: none !important;
}

/* Tag Handler Overrides */

.tagItem {
    background-color: #23395B !important;
}

.tagHandler ul.tagHandlerContainer li.tagItem {
    cursor: pointer;
}

.tagHandlerContainer {
    padding-right: 0px !important;
}

.tagInputField {
    height: 20px !important;
    /* width: 111px !important; */
    width: 102px !important;
    padding: 3px !important;
    font-family: inherit !important;
    background-color: transparent !important;
    /* border: 1px solid #000000 !important; */
    position: relative !important;
    bottom: 3px !important;
}

.ui-autocomplete {
    width: 102px !important;
    color: #FFFFFF !important;
    font-size: 12px; !important;
    font-family: inherit !important;
    background-color: #23395B !important;
}

.ui-menu-item {
    border-bottom: 1px solid #FFFFFF !important;
}

.ui-autocomplete .ui-state-focus {
    color: #FFFFFF !important;
    background-color: #23395B !important;
    border: 0px solid !important;
    border-bottom: 1px solid #FFFFFF !important;
    font-style: oblique !important;
    margin: 0px !important;
}

<?php
require('grokbb.modals.css.php');

$templateCSS = str_replace('.php', '.css.php', $_SESSION['template']);
$templateCSS = str_replace(SITE_BASE_APP, SITE_BASE_GBB . 'css' . DIRECTORY_SEPARATOR, $templateCSS);

$sepPos = strrpos($templateCSS, DIRECTORY_SEPARATOR) + 1;
$dotPos = strpos($templateCSS, '.');

// include the CSS for the common header
$headerCSS = substr($templateCSS, 0, $sepPos) . 'header' . substr($templateCSS, $dotPos);
if (is_readable($headerCSS)) { require($headerCSS); }

// include the CSS for the template
if (is_readable($templateCSS)) { require($templateCSS); }

// include the CSS for the common sidebar
$sidebarCSS = substr($templateCSS, 0, $sepPos) . 'sidebar' . substr($templateCSS, $dotPos);
if (is_readable($sidebarCSS)) { require($sidebarCSS); }
?>