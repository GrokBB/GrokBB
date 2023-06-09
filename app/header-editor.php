<link rel="stylesheet" href="<?php echo $resCM . ((CDNJS) ? '' : '/lib'); ?>/codemirror.css" />
<link rel="stylesheet" href="<?php echo $resUK; ?>/css/components/htmleditor.almost-flat.min.css" />

<script src="<?php echo $resCM . ((CDNJS) ? '' : '/lib'); ?>/codemirror.min.js"></script>
<script src="<?php echo $resCM; ?>/mode/markdown/markdown.js"></script>
<script src="<?php echo $resCM; ?>/addon/mode/overlay.js"></script>
<script src="<?php echo $resCM; ?>/mode/xml/xml.js"></script>
<script src="<?php echo $resCM; ?>/mode/gfm/gfm.js"></script>

<!-- the original file has been updated to fix a bug with parsing URLs containing () -->
<script src="<?php echo SITE_BASE_URL; ?>/lib/marked-0.3.5/lib/marked.js"></script>

<!-- the original file has been updated to support a code button, GFM newlines and spell checking -->
<script src="<?php echo SITE_BASE_URL; ?>/lib/uikit-2.26.3/js/components/htmleditor.js"></script>