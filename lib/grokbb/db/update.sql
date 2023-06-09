-- back populate the statistics

TRUNCATE `gbb_stats`;

INSERT INTO `gbb_stats` (`counter_topics`, `counter_replies`)
    SELECT (SELECT COUNT(*) FROM `gbb_topic`) AS counter_topics, (SELECT COUNT(*) FROM `gbb_reply`) AS counter_replies;

UPDATE `gbb_board` b INNER JOIN (
    SELECT ct.`id_bd`, COUNT(ct.id) AS counter_topics FROM `gbb_topic` ct GROUP BY ct.`id_bd`
) t ON b.`id` = t.`id_bd` SET b.`counter_topics` = t.`counter_topics`;

UPDATE `gbb_board` b INNER JOIN (
    SELECT ct.`id_bd`, COUNT(cr.id) AS counter_replies FROM `gbb_reply` cr INNER JOIN `gbb_topic` ct ON cr.`id_tc` = ct.`id` GROUP BY ct.`id_bd`
) r ON b.`id` = r.`id_bd` SET b.`counter_replies` = r.`counter_replies`;

TRUNCATE `gbb_user_board_stats`;

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

-- clean up the expired sessions

CREATE EVENT cleanup_sessions
    ON SCHEDULE EVERY 1 DAY
    DO DELETE FROM grokbb.`gbb_session` WHERE `sess_time` + 86400 < UNIX_TIMESTAMP();
