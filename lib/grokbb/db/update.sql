# back populate the statistics

INSERT INTO `gbb_stats` (`counter_topics`, `counter_replies`)
    SELECT (SELECT COUNT(*) FROM `gbb_topic`) AS counter_topics, (SELECT COUNT(*) FROM `gbb_reply`) AS counter_replies;

UPDATE `gbb_board` b INNER JOIN (
    SELECT ct.`id_bd`, COUNT(ct.id) AS counter_topics FROM `gbb_topic` ct GROUP BY ct.`id_bd`
) t ON b.`id` = t.`id_bd` SET b.`counter_topics` = t.`counter_topics`;

UPDATE `gbb_board` b INNER JOIN (
    SELECT ct.`id_bd`, COUNT(cr.id) AS counter_replies FROM `gbb_reply` cr INNER JOIN `gbb_topic` ct ON cr.`id_tc` = ct.`id` GROUP BY ct.`id_bd`
) r ON b.`id` = r.`id_bd` SET b.`counter_replies` = r.`counter_replies`;

INSERT INTO `gbb_user_board_stats` (`id_ur`, `id_bd`, `counter_topics`, `counter_replies`)
    SELECT uid, bid, SUM(IF(type = 't', counter, 0)) AS counter_topics, SUM(IF(type = 'r', counter, 0)) AS counter_replies FROM ( 
        SELECT u.`id` AS uid, b.`id` AS bid, 't' AS type, count(t.id) AS counter FROM `gbb_user` u 
        INNER JOIN `gbb_topic` t ON u.`id` = t.`id_ur`
        INNER JOIN `gbb_board` b ON t.`id_bd` = b.`id`
        GROUP BY u.`id`, b.`id`
        
        UNION
        
        SELECT u.`id` AS uid, b.`id` AS bid, 'r' AS type, count(r.id) AS counter FROM `gbb_user` u 
        INNER JOIN `gbb_reply` r ON u.`id` = r.`id_ur`
        INNER JOIN `gbb_topic` t ON r.`id_tc` = t.`id`
        INNER JOIN `gbb_board` b ON t.`id_bd` = b.`id`
        GROUP BY u.`id`, b.`id`
    ) AS stats GROUP BY uid, bid;

UPDATE `gbb_topic` t INNER JOIN (
    SELECT cr.`id_tc`, COUNT(cr.id) AS counter_replies FROM `gbb_reply` cr GROUP BY cr.`id_tc`
) r ON t.`id` = r.`id_tc` SET t.`counter_replies` = r.`counter_replies`;

UPDATE `gbb_reply` r INNER JOIN (
    SELECT cr.`id_ry`, COUNT(cr.id) AS counter_replies FROM `gbb_reply` cr WHERE id_ry <> 0 GROUP BY cr.`id_ry`
) p ON r.`id` = p.`id_ry` SET r.`counter_replies` = p.`counter_replies`;

# clean up the expired sessions

CREATE EVENT cleanup_sessions
    ON SCHEDULE EVERY 1 DAY
    DO DELETE FROM grokbb.`gbb_session` WHERE `sess_time` + 86400 < UNIX_TIMESTAMP();

# added reply tracking to messages

ALTER TABLE `gbb_message` ADD `id_ry` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `id_to`, ADD INDEX (`id_ry`);
ALTER TABLE `gbb_message` ADD `id_tc` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `id_ry`, ADD INDEX (`id_tc`);

# we now track who updated a topic or reply

ALTER TABLE `gbb_reply` ADD `updated_id_ur` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `updated_ipaddress`, ADD INDEX (`updated_id_ur`);
ALTER TABLE `gbb_topic` ADD `updated_id_ur` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `updated_ipaddress`, ADD INDEX (`updated_id_ur`);

# added a new setting to automatically display a board's moderators

ALTER TABLE `gbb_board` ADD `desc_sidebar_mods` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `desc_sidebar_md`;

# added a new setting to automatically display who is online

ALTER TABLE `gbb_board` ADD `desc_sidebar_whos` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `desc_sidebar_mods`;

# doubled the topic title length

ALTER TABLE `gbb_topic` CHANGE `title` `title` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

# added the ability to add media to topics

CREATE TABLE `gbb_topic_media` (
  `id` int(10) unsigned NOT NULL,
  `id_tc` int(10) unsigned NOT NULL,
  `url` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `gbb_topic_media` ADD PRIMARY KEY (`id`), ADD KEY `id_tc` (`id_tc`);

ALTER TABLE `gbb_topic_media` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `gbb_topic_media` ADD `txt` VARCHAR(120) NOT NULL AFTER `url`;