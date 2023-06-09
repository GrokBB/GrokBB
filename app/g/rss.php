<?php
if (empty($_SESSION['gbbboard'])) {
    header('HTTP/1.1 404 Not Found'); exit();
}

$nameCleaned = GrokBB\Util::sanitizeBoard($_SESSION['gbbboard']);
$board = $GLOBALS['db']->getOne('board', array('name' => $nameCleaned));

if ($board === false || $board->type == 1) {
    header('HTTP/1.1 404 Not Found'); exit();
}

$filter = array('t.id_bd' => $board->id, 't.deleted' => 0, 't.private' => 0);
$topics = $GLOBALS['db']->getAll('topic t INNER JOIN ' . DB_PREFIX . 'user u ON t.id_ur = u.id INNER JOIN ' . DB_PREFIX . 'board_category bc ON t.id_bc = bc.id', 
    $filter, 't.created DESC', array('t.*', 'u.username', 'bc.name AS category'), 20);

if ($topics == false) { $topics = array(); }

header('Content-Type: application/rss+xml; charset=utf-8');
?>
<?xml version="1.0" encoding="utf-8"?>

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><![CDATA[<?php echo html_entity_decode($board->name); ?>]]></title>
        <link><?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard']; ?></link>
        <atom:link href="<?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard']; ?>/rss" rel="self" type="application/rss+xml" />
        <description><![CDATA[<?php echo $board->desc_tagline; ?>]]></description>
        <lastBuildDate><?php echo gmdate(DATE_RSS); ?></lastBuildDate>
        <language>en-us</language>
        <ttl>60</ttl>
        <?php foreach ($topics as $topic) { ?>
        <item>
            <title><![CDATA[<?php echo html_entity_decode($topic->title); ?>]]></title>
            <dc:creator><![CDATA[<?php echo $topic->username; ?>]]></dc:creator>
            <link><?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $topic->id; ?></link>
            <guid><?php echo SITE_BASE_URL . '/g/' . $_SESSION['gbbboard'] . '/view/' . $topic->id; ?></guid>
            <pubDate><?php echo gmdate(DATE_RSS, $topic->created); ?></pubDate>
            <category><![CDATA[<?php echo $topic->category; ?>]]></category>
            <description><![CDATA[<?php echo $topic->content; ?>]]></description>
        </item>
        <?php } ?>
    </channel>
</rss>