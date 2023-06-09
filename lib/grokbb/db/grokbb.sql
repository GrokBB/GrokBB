-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 09, 2023 at 04:05 PM
-- Server version: 8.0.33-0ubuntu0.22.04.2
-- PHP Version: 8.0.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grokbb`
--
CREATE DATABASE IF NOT EXISTS `grokbb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `grokbb`;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_board`
--

DROP TABLE IF EXISTS `gbb_board`;
CREATE TABLE `gbb_board` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `plan` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `type` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` int UNSIGNED NOT NULL,
  `updated` int UNSIGNED NOT NULL DEFAULT '0',
  `expires` int UNSIGNED NOT NULL DEFAULT '0',
  `desc_sidebar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_sidebar_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_sidebar_mods` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `desc_sidebar_whos` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `desc_tagline` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tag1` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tag2` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tag3` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `stripe_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `stripe_cancelled` int UNSIGNED NOT NULL DEFAULT '0',
  `stripe_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `counter_users` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_points` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_topics` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_replies` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gbb_board`
--

INSERT INTO `gbb_board` (`id`, `id_ur`, `plan`, `type`, `name`, `created`, `updated`, `expires`, `desc_sidebar`, `desc_sidebar_md`, `desc_sidebar_mods`, `desc_sidebar_whos`, `desc_tagline`, `tag1`, `tag2`, `tag3`, `stripe_id`, `stripe_cancelled`, `stripe_error`, `counter_users`, `counter_points`, `counter_topics`, `counter_replies`) VALUES
(1, 1, 4, 0, 'GrokBB Help', 1459545094, 1467697714, 0, '<h3>Welcome to the official GrokBB Help!</h3>\n<p>We will eventually get some HowTos and FAQs up here, but for now please feel free to post questions related to general site usage or board administration. We will answer you as soon as possible.</p>\n<p>If you have suggestions for new features, want to view our roadmap, or would like to report a bug then please do that over in <a href=\"https://www.grokbb.com/g/GrokBB_Dev\">GrokBB Dev</a>.</p>\n<p>Your user experience is our top priority, and we\'re always striving to provide you with friendly, easy to understand feedback and support. Please let us know how we can help, and thank you for visiting!</p>\n', '### Welcome to the official GrokBB Help!\n\nWe will eventually get some HowTos and FAQs up here, but for now please feel free to post questions related to general site usage or board administration. We will answer you as soon as possible.\n\nIf you have suggestions for new features, want to view our roadmap, or would like to report a bug then please do that over in [GrokBB Dev](https://www.grokbb.com/g/GrokBB_Dev).\n\nYour user experience is our top priority, and we\'re always striving to provide you with friendly, easy to understand feedback and support. Please let us know how we can help, and thank you for visiting!', 1, 1, 'The official GrokBB help documentation and support.', 'GrokBB', 'help', '', '0', 0, '', 0, 0, 0, 0),
(2, 1, 4, 0, 'GrokBB Dev', 1459554039, 1467697459, 0, '<h3>Welcome to the official GrokBB Dev!</h3>\n<p>Here you can discuss the inner workings of GrokBB, request new features, view our roadmap and submit bug reports.</p>\n<p>Please review the following topics before posting.</p>\n<ul><li><a href=\"https://www.grokbb.com/g/GrokBB_Dev/view/1\">GrokBB Roadmap</a></li>\n<li><a href=\"https://www.grokbb.com/g/GrokBB_Dev/view/2\">Bug Reporting Guidelines</a></li>\n</ul>', '### Welcome to the official GrokBB Dev!\n\nHere you can discuss the inner workings of GrokBB, request new features, view our roadmap and submit bug reports.\n\nPlease review the following topics before posting.\n\n* [GrokBB Roadmap](https://www.grokbb.com/g/GrokBB_Dev/view/1)\n* [Bug Reporting Guidelines](https://www.grokbb.com/g/GrokBB_Dev/view/2)', 1, 1, 'The official GrokBB development community. Discuss new features, report bugs, and review our roadmap.', 'GrokBB', '', '', '0', 0, '', 0, 0, 2, 0),
(3, 1, 4, 0, 'GrokBB Off Topic', 1466152726, 1467697742, 1468744726, '<h3>Welcome to the official GrokBB Off Topic!</h3>\n<p>Feel free to discuss anything you want here.</p>\n', '### Welcome to the official GrokBB Off Topic!\n\nFeel free to discuss anything you want here.', 1, 1, 'The official Off Topic board for GrokBB. Feel free to discuss anything you want here, while this place grows.', 'GrokBB', '', '', '0', 0, '', 0, 0, 0, 0),
(4, 1, 4, 0, 'Spaceship Zero', 1467333733, 1467362540, 1469925733, '<p>From the latest advances in science and technology to the weird and unexplainable. Let\'s discuss ...</p>\n<ul><li>Art</li>\n<li>Computers</li>\n<li>Medicine</li>\n<li>Paranormal</li>\n<li>Robots / Cybernetics</li>\n<li>Science From All Realms</li>\n<li>Space &amp; Exploration</li>\n<li>Technology &amp; Recent Advances</li>\n<li>The Mind / Psychology</li>\n</ul><p>Science Fiction literature representing any of these topics are welcome too!</p>\n', 'From the latest advances in science and technology to the weird and unexplainable. Let\'s discuss ...\n\n* Art\n* Computers\n* Medicine\n* Paranormal\n* Robots / Cybernetics\n* Science From All Realms\n* Space & Exploration\n* Technology & Recent Advances\n* The Mind / Psychology\n\nScience Fiction literature representing any of these topics are welcome too!\n', 1, 0, 'Exploring all realms of science and the paranormal', 'Science', 'Paranormal', '', '0', 0, '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gbb_board_announcement`
--

DROP TABLE IF EXISTS `gbb_board_announcement`;
CREATE TABLE `gbb_board_announcement` (
  `id` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `subject` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` int UNSIGNED NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  `sent` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_board_badge`
--

DROP TABLE IF EXISTS `gbb_board_badge`;
CREATE TABLE `gbb_board_badge` (
  `id` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `width` smallint UNSIGNED NOT NULL DEFAULT '0',
  `height` smallint UNSIGNED NOT NULL DEFAULT '0',
  `desc` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_board_category`
--

DROP TABLE IF EXISTS `gbb_board_category`;
CREATE TABLE `gbb_board_category` (
  `id` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#23395B',
  `image` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `defcat` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `private` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gbb_board_category`
--

INSERT INTO `gbb_board_category` (`id`, `id_bd`, `name`, `color`, `image`, `defcat`, `private`) VALUES
(1, 2, 'General', '#23395B', 0, 1, 0),
(2, 2, 'GrokBB Roadmap', '#23395B', 0, 0, 1),
(3, 2, 'Bug Report', '#23395B', 0, 0, 0),
(4, 2, 'Feature Request', '#23395B', 0, 0, 0),
(5, 1, 'General', '#23395B', 0, 1, 0),
(22, 3, 'General', '#23395B', 0, 1, 0),
(24, 2, 'Feedback / Suggestion', '#23395B', 0, 0, 0),
(39, 4, 'General', '#267C4E', 0, 1, 0),
(40, 4, 'Art', '#267C4E', 0, 0, 0),
(41, 4, 'Science From All Realms', '#267C4E', 0, 0, 0),
(42, 4, 'Technology', '#267C4E', 0, 0, 0),
(43, 4, 'Science Fiction', '#267C4E', 0, 0, 0),
(44, 4, 'Computers', '#267C4E', 0, 0, 0),
(45, 4, 'Robots / Cybernetics', '#267C4E', 0, 0, 0),
(46, 4, 'Paranormal', '#267C4E', 0, 0, 0),
(47, 4, 'Medicine', '#267C4E', 0, 0, 0),
(48, 4, 'Space & Exploration', '#267C4E', 0, 0, 0),
(49, 4, 'The Mind / Psychology', '#267C4E', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gbb_board_settings`
--

DROP TABLE IF EXISTS `gbb_board_settings`;
CREATE TABLE `gbb_board_settings` (
  `id` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `header_back_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#18181B',
  `header_back_repeat` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `header_menu_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#23395B',
  `header_name_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `header_name_font` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''Helvetica Neue'', Helvetica, Arial, sans-serif',
  `button_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#267C4E',
  `button_hover` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#267C4E',
  `button_font` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''Helvetica Neue'', Helvetica, Arial, sans-serif',
  `button_text_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `button_text_hover` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `tag_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#23395B',
  `stylesheet` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic_content_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Your Content',
  `topic_content_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic_content_desc_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic_request_access` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic_request_access_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic_allowpolls` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `board_request_access` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `board_request_access_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gbb_board_settings`
--

INSERT INTO `gbb_board_settings` (`id`, `id_bd`, `header_back_color`, `header_back_repeat`, `header_menu_color`, `header_name_color`, `header_name_font`, `button_color`, `button_hover`, `button_font`, `button_text_color`, `button_text_hover`, `tag_color`, `stylesheet`, `topic_content_name`, `topic_content_desc`, `topic_content_desc_md`, `topic_request_access`, `topic_request_access_md`, `topic_allowpolls`, `board_request_access`, `board_request_access_md`) VALUES
(1, 1, '#18181B', 1, '#23395B', '#FFFFFF', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#267C4E', '#267C4E', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#FFFFFF', '#FFFFFF', '#23395B', '', 'Your Content', '', '', '', '', 1, '', ''),
(2, 2, '#18181B', 1, '#23395B', '#FFFFFF', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#267C4E', '#267C4E', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#FFFFFF', '#FFFFFF', '#23395B', '', 'Your Content', '', '', '', '', 1, '', ''),
(5, 3, '#18181B', 1, '#23395B', '#FFFFFF', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#267C4E', '#267C4E', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#FFFFFF', '#FFFFFF', '#23395B', '', 'Your Content', '', '', '', '', 1, '', ''),
(8, 4, '#18181B', 0, '#18181B', '#FFFFFF', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#267C4E', '#000000', '\'Helvetica Neue\', Helvetica, Arial, sans-serif', '#FFFFFF', '#FFFFFF', '#23395B', '.gbb-header {\n	background-color: #18181B;\n}\n\n.gbb-header-text {\n	position: relative;\n    bottom: 3px;\n}', 'Your Content', '', '', '', '', 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `gbb_board_tag`
--

DROP TABLE IF EXISTS `gbb_board_tag`;
CREATE TABLE `gbb_board_tag` (
  `id` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gbb_board_tag`
--

INSERT INTO `gbb_board_tag` (`id`, `id_bd`, `name`) VALUES
(1, 2, '2023'),
(2, 2, '2024'),
(3, 2, '2025'),
(4, 2, 'Q1'),
(5, 2, 'Q2'),
(6, 2, 'Q3'),
(7, 2, 'Q4'),
(8, 2, 'Open'),
(9, 2, 'Fixed'),
(10, 2, 'Won\'t Fix'),
(11, 2, 'Not a Bug'),
(12, 2, 'Needs Feedback'),
(13, 2, 'Duplicate'),
(14, 2, 'Critical'),
(15, 2, 'High'),
(16, 2, 'Low');

-- --------------------------------------------------------

--
-- Table structure for table `gbb_error`
--

DROP TABLE IF EXISTS `gbb_error`;
CREATE TABLE `gbb_error` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL DEFAULT '0',
  `level` int UNSIGNED NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `line` int UNSIGNED NOT NULL,
  `time` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_message`
--

DROP TABLE IF EXISTS `gbb_message`;
CREATE TABLE `gbb_message` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_to` int UNSIGNED NOT NULL,
  `id_ry` int UNSIGNED NOT NULL DEFAULT '0',
  `id_tc` int UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` int NOT NULL DEFAULT '0',
  `deleted` int NOT NULL DEFAULT '0',
  `sent` int NOT NULL DEFAULT '0',
  `rcvd` int NOT NULL DEFAULT '0',
  `read` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_reply`
--

DROP TABLE IF EXISTS `gbb_reply`;
CREATE TABLE `gbb_reply` (
  `id` int UNSIGNED NOT NULL,
  `id_tc` int UNSIGNED NOT NULL,
  `id_ry` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` int UNSIGNED NOT NULL,
  `created_ipaddress` int NOT NULL,
  `updated` int UNSIGNED NOT NULL DEFAULT '0',
  `updated_ipaddress` int NOT NULL,
  `updated_id_ur` int UNSIGNED NOT NULL DEFAULT '0',
  `deleted` int UNSIGNED NOT NULL DEFAULT '0',
  `deleted_id_ur` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_replies` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_saves` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_reply_points`
--

DROP TABLE IF EXISTS `gbb_reply_points`;
CREATE TABLE `gbb_reply_points` (
  `id` int UNSIGNED NOT NULL,
  `id_ry` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `points` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_session`
--

DROP TABLE IF EXISTS `gbb_session`;
CREATE TABLE `gbb_session` (
  `sess_id` varbinary(128) NOT NULL,
  `sess_data` mediumblob NOT NULL,
  `sess_time` int UNSIGNED NOT NULL,
  `sess_lifetime` mediumint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_stats`
--

DROP TABLE IF EXISTS `gbb_stats`;
CREATE TABLE `gbb_stats` (
  `counter_topics` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_replies` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_topic`
--

DROP TABLE IF EXISTS `gbb_topic`;
CREATE TABLE `gbb_topic` (
  `id` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `id_bc` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_ry` int UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` int UNSIGNED NOT NULL,
  `created_ipaddress` int NOT NULL,
  `updated` int UNSIGNED NOT NULL DEFAULT '0',
  `updated_ipaddress` int NOT NULL,
  `updated_id_ur` int UNSIGNED NOT NULL DEFAULT '0',
  `deleted` int UNSIGNED NOT NULL DEFAULT '0',
  `deleted_id_ur` int UNSIGNED NOT NULL DEFAULT '0',
  `sticky` int UNSIGNED NOT NULL DEFAULT '0',
  `locked` int UNSIGNED NOT NULL DEFAULT '0',
  `private` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `counter_views` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_replies` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gbb_topic`
--

INSERT INTO `gbb_topic` (`id`, `id_bd`, `id_bc`, `id_ur`, `id_ry`, `title`, `content`, `content_md`, `created`, `created_ipaddress`, `updated`, `updated_ipaddress`, `updated_id_ur`, `deleted`, `deleted_id_ur`, `sticky`, `locked`, `private`, `notes`, `counter_views`, `counter_replies`) VALUES
(1, 2, 2, 1, 0, 'GrokBB Roadmap', '<p>The following is the high-level roadmap for GrokBB development. This may change over time, depending on your feedback and how long things actually take, but for the most part it represents the core features we plan to implement in the foreseeable future. Our top priority here is to maintain a high quality user experience and a bug-free codebase, and so those items will always take priority over this roadmap.</p>\n<p>A topic will be created for each feature, so that they can be discussed in further detail with anyone who is interested. This topic should only be used to discuss additions to this list and possible timeline adjustments. Please keep feature specific questions in their respective topics.</p>\n<p>TODO</p>\n', 'The following is the high-level roadmap for GrokBB development. This may change over time, depending on your feedback and how long things actually take, but for the most part it represents the core features we plan to implement in the foreseeable future. Our top priority here is to maintain a high quality user experience and a bug-free codebase, and so those items will always take priority over this roadmap.\n\nA topic will be created for each feature, so that they can be discussed in further detail with anyone who is interested. This topic should only be used to discuss additions to this list and possible timeline adjustments. Please keep feature specific questions in their respective topics.\n\nTODO', 1686324914, 0, 1686324914, 0, 1, 0, 0, 1686324914, 0, 0, '', 1, 0),
(2, 2, 3, 1, 0, 'Bug Reporting Guidelines', '<p>When reporting a bug please provide the following information whenever possible.</p>\n<ul>\n<li>a summary of the issue and what you expected to happen</li>\n<li>step-by-step instructions to reproduce the issue</li>\n<li>a screenshot with the URL visible and the issue highlighted in some way</li>\n</ul>\n<p>We will be tagging <strong> Bug Report </strong> topics with the following statuses. These may change over time, as our development process evolves here.</p>\n<p><strong> Open </strong></p>\n<p>This is acknowledged as a valid bug, and it is currently being worked on. Please see the <strong> Bug Priorities </strong> below to get an idea of when it will be fixed.</p>\n<p><strong> Fixed </strong></p>\n<p>This is acknowledged as a valid bug, and it is currently fixed.</p>\n<p><strong> Won\'t Fix </strong></p>\n<p>There are technical or philosophical reasons why the requested update won\'t be made.</p>\n<p><strong> Not a Bug </strong></p>\n<p>This is not a bug. The feature is working as designed, and there was just a misunderstanding on how it was supposed to work.</p>\n<p><strong> Needs Feedback </strong></p>\n<p>We require further feedback from the user who reported the bug (or anyone else who can reproduce) to continue working on this issue.</p>\n<p><strong> Duplicate </strong></p>\n<p>This bug has already been reported.</p>\n<h3><strong> Bug Priorities </strong></h3>\n<hr />\n<p>The <strong> Open </strong> issues will be tagged with a priority, and in general, they will be fixed in the following order. There can be exceptions. For example, when a low priority issue is easy to fix or if there are no other <strong> Critical </strong> / <strong> High </strong> ones open, then they will get bumped up in priority and fixed as soon as possible.</p>\n<p><strong> Critical </strong></p>\n<p>It will be fixed as soon as possible.</p>\n<p><strong> High </strong></p>\n<p>It will be fixed in the next major release.</p>\n<p><strong> Low </strong></p>\n<p>It will be fixed some time in the future.</p>\n', 'When reporting a bug please provide the following information whenever possible.\n\n* a summary of the issue and what you expected to happen\n* step-by-step instructions to reproduce the issue\n* a screenshot with the URL visible and the issue highlighted in some way\n\n\nWe will be tagging ** Bug Report ** topics with the following statuses. These may change over time, as our development process evolves here.\n\n** Open **\n\nThis is acknowledged as a valid bug, and it is currently being worked on. Please see the ** Bug Priorities ** below to get an idea of when it will be fixed.\n\n** Fixed **\n\nThis is acknowledged as a valid bug, and it is currently fixed.\n\n** Won\'t Fix **\n\nThere are technical or philosophical reasons why the requested update won\'t be made.\n\n** Not a Bug **\n\nThis is not a bug. The feature is working as designed, and there was just a misunderstanding on how it was supposed to work.\n\n** Needs Feedback **\n\nWe require further feedback from the user who reported the bug (or anyone else who can reproduce) to continue working on this issue.\n\n** Duplicate **\n\nThis bug has already been reported.\n\n### ** Bug Priorities **\n\n<hr>\n\nThe ** Open ** issues will be tagged with a priority, and in general, they will be fixed in the following order. There can be exceptions. For example, when a low priority issue is easy to fix or if there are no other ** Critical ** / ** High ** ones open, then they will get bumped up in priority and fixed as soon as possible.\n\n** Critical **\n\nIt will be fixed as soon as possible.\n\n** High **\n\nIt will be fixed in the next major release.\n\n** Low **\n\nIt will be fixed some time in the future.', 1686324944, 1269576253, 1686324944, 0, 1, 0, 0, 1686324914, 0, 0, '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gbb_topic_media`
--

DROP TABLE IF EXISTS `gbb_topic_media`;
CREATE TABLE `gbb_topic_media` (
  `id` int UNSIGNED NOT NULL,
  `id_tc` int UNSIGNED NOT NULL,
  `url` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `txt` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_topic_points`
--

DROP TABLE IF EXISTS `gbb_topic_points`;
CREATE TABLE `gbb_topic_points` (
  `id` int UNSIGNED NOT NULL,
  `id_tc` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `points` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_topic_tag`
--

DROP TABLE IF EXISTS `gbb_topic_tag`;
CREATE TABLE `gbb_topic_tag` (
  `id` int UNSIGNED NOT NULL,
  `id_tc` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL DEFAULT '0',
  `id_bd` int UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_topic_view`
--

DROP TABLE IF EXISTS `gbb_topic_view`;
CREATE TABLE `gbb_topic_view` (
  `id` int UNSIGNED NOT NULL,
  `id_tc` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED DEFAULT NULL,
  `id_sn` varbinary(128) NOT NULL,
  `viewed` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user`
--

DROP TABLE IF EXISTS `gbb_user`;
CREATE TABLE `gbb_user` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `joined` int UNSIGNED NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `updated` int NOT NULL DEFAULT '0',
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio_md` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` int UNSIGNED NOT NULL DEFAULT '0',
  `login_attempts` int UNSIGNED NOT NULL DEFAULT '0',
  `login_ipaddress` int NOT NULL,
  `counter_topics` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_replies` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gbb_user`
--

INSERT INTO `gbb_user` (`id`, `username`, `password`, `remember`, `joined`, `email`, `updated`, `bio`, `bio_md`, `login`, `login_attempts`, `login_ipaddress`, `counter_topics`, `counter_replies`) VALUES
(1, 'Admin', '$2y$10$RIqsAj0xDfcsu1tWOk0G..DnLDTtnDtpeVlGLV4k2RC.7FL5As/Ny', '$2y$10$AHXnEm90MihKZNLSdom4l.NJb8gUOzbPwnLJeXRUJAI/gv8kOirU6', 1459544850, 'brian.otto@zoho.com', 0, '', '', 1686294628, 0, 0, 17, 38),
(2, 'GBB Test 1', '$2y$10$4gVIUKRncTa/bNUfBlaUP.FQy/OZB8Q5Tdl4hziUAQ/PdCz3o4j82', '', 1461831073, 'brian.otto@zoho.com', 0, '', '', 1468487111, 0, 1269576253, 0, 0),
(3, 'GBB Test 2', '$2y$10$OhzPEGGXyDJP8k9TydCPr.2G6ScsHE7jDJnyjDgy4vLOH3Crj8UDu', '', 1465320365, '', 0, '', '', 1467357680, 0, 1269576253, 0, 0),
(4, 'GBB Test 3', '$2y$10$qsjwz6ATxFh5ZMByIvUlHu3BFCC3Id8dVES6dMh1BtiOPGeyA8MXu', '', 1465895295, '', 0, '', '', 1467362671, 0, 1269576253, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_board`
--

DROP TABLE IF EXISTS `gbb_user_board`;
CREATE TABLE `gbb_user_board` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `added` int UNSIGNED NOT NULL,
  `deleted` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_board_announcement`
--

DROP TABLE IF EXISTS `gbb_user_board_announcement`;
CREATE TABLE `gbb_user_board_announcement` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_ba` int UNSIGNED NOT NULL,
  `read` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_board_approved`
--

DROP TABLE IF EXISTS `gbb_user_board_approved`;
CREATE TABLE `gbb_user_board_approved` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `approved` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_board_badge`
--

DROP TABLE IF EXISTS `gbb_user_board_badge`;
CREATE TABLE `gbb_user_board_badge` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_bb` int UNSIGNED NOT NULL,
  `awarded` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_board_banned`
--

DROP TABLE IF EXISTS `gbb_user_board_banned`;
CREATE TABLE `gbb_user_board_banned` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `banned` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_board_moderator`
--

DROP TABLE IF EXISTS `gbb_user_board_moderator`;
CREATE TABLE `gbb_user_board_moderator` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `added` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_board_stats`
--

DROP TABLE IF EXISTS `gbb_user_board_stats`;
CREATE TABLE `gbb_user_board_stats` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_bd` int UNSIGNED NOT NULL,
  `counter_points` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_topics` int UNSIGNED NOT NULL DEFAULT '0',
  `counter_replies` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_friend`
--

DROP TABLE IF EXISTS `gbb_user_friend`;
CREATE TABLE `gbb_user_friend` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_fd` int UNSIGNED NOT NULL,
  `added` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_reply`
--

DROP TABLE IF EXISTS `gbb_user_reply`;
CREATE TABLE `gbb_user_reply` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_ry` int UNSIGNED NOT NULL,
  `saved` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_reset`
--

DROP TABLE IF EXISTS `gbb_user_reset`;
CREATE TABLE `gbb_user_reset` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent` int UNSIGNED NOT NULL,
  `used` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gbb_user_topic`
--

DROP TABLE IF EXISTS `gbb_user_topic`;
CREATE TABLE `gbb_user_topic` (
  `id` int UNSIGNED NOT NULL,
  `id_ur` int UNSIGNED NOT NULL,
  `id_tc` int UNSIGNED NOT NULL,
  `saved` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gbb_board`
--
ALTER TABLE `gbb_board`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `id_ur` (`id_ur`),
  ADD KEY `tag1` (`tag1`),
  ADD KEY `tag2` (`tag2`),
  ADD KEY `tag3` (`tag3`),
  ADD KEY `desc_tagline` (`desc_tagline`);

--
-- Indexes for table `gbb_board_announcement`
--
ALTER TABLE `gbb_board_announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bd` (`id_bd`) USING BTREE;

--
-- Indexes for table `gbb_board_badge`
--
ALTER TABLE `gbb_board_badge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_board_category`
--
ALTER TABLE `gbb_board_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_board_settings`
--
ALTER TABLE `gbb_board_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_bd` (`id_bd`) USING BTREE;

--
-- Indexes for table `gbb_board_tag`
--
ALTER TABLE `gbb_board_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `id_bd` (`id_bd`) USING BTREE;

--
-- Indexes for table `gbb_error`
--
ALTER TABLE `gbb_error`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ur` (`id_ur`);

--
-- Indexes for table `gbb_message`
--
ALTER TABLE `gbb_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ur` (`id_ur`),
  ADD KEY `id_to` (`id_to`),
  ADD KEY `id_ry` (`id_ry`),
  ADD KEY `id_tc` (`id_tc`);

--
-- Indexes for table `gbb_reply`
--
ALTER TABLE `gbb_reply`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tc` (`id_tc`),
  ADD KEY `id_ry` (`id_ry`),
  ADD KEY `deleted_id_ur` (`deleted_id_ur`),
  ADD KEY `updated_id_ur` (`updated_id_ur`);

--
-- Indexes for table `gbb_reply_points`
--
ALTER TABLE `gbb_reply_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ry_ur` (`id_ry`,`id_ur`) USING BTREE,
  ADD KEY `id_ur` (`id_ur`),
  ADD KEY `id_ry` (`id_ry`) USING BTREE;

--
-- Indexes for table `gbb_session`
--
ALTER TABLE `gbb_session`
  ADD PRIMARY KEY (`sess_id`);

--
-- Indexes for table `gbb_topic`
--
ALTER TABLE `gbb_topic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bd` (`id_bd`),
  ADD KEY `id_bc` (`id_bc`),
  ADD KEY `id_ur` (`id_ur`),
  ADD KEY `id_ry` (`id_ry`),
  ADD KEY `deleted_id_ur` (`deleted_id_ur`),
  ADD KEY `updated_id_ur` (`updated_id_ur`);
ALTER TABLE `gbb_topic` ADD FULLTEXT KEY `title` (`title`);
ALTER TABLE `gbb_topic` ADD FULLTEXT KEY `content_md` (`content_md`);

--
-- Indexes for table `gbb_topic_media`
--
ALTER TABLE `gbb_topic_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tc` (`id_tc`);

--
-- Indexes for table `gbb_topic_points`
--
ALTER TABLE `gbb_topic_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_tc_ur` (`id_tc`,`id_ur`) USING BTREE,
  ADD KEY `id_tc` (`id_tc`),
  ADD KEY `id_ur` (`id_ur`);

--
-- Indexes for table `gbb_topic_tag`
--
ALTER TABLE `gbb_topic_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tc` (`id_tc`),
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `name` (`name`),
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_topic_view`
--
ALTER TABLE `gbb_topic_view`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tc` (`id_tc`),
  ADD KEY `id_ur` (`id_ur`),
  ADD KEY `id_sn` (`id_sn`);

--
-- Indexes for table `gbb_user`
--
ALTER TABLE `gbb_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `gbb_user_board`
--
ALTER TABLE `gbb_user_board`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_user_board_announcement`
--
ALTER TABLE `gbb_user_board_announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_ba` (`id_ba`) USING BTREE;

--
-- Indexes for table `gbb_user_board_approved`
--
ALTER TABLE `gbb_user_board_approved`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_bd` (`id_ur`,`id_bd`) USING BTREE,
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_user_board_badge`
--
ALTER TABLE `gbb_user_board_badge`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_bb` (`id_ur`,`id_bb`),
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_bb` (`id_bb`);

--
-- Indexes for table `gbb_user_board_banned`
--
ALTER TABLE `gbb_user_board_banned`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_bd` (`id_ur`,`id_bd`) USING BTREE,
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_user_board_moderator`
--
ALTER TABLE `gbb_user_board_moderator`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_bd` (`id_ur`,`id_bd`) USING BTREE,
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_user_board_stats`
--
ALTER TABLE `gbb_user_board_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_bd` (`id_ur`,`id_bd`) USING BTREE,
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_bd` (`id_bd`);

--
-- Indexes for table `gbb_user_friend`
--
ALTER TABLE `gbb_user_friend`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_fd` (`id_ur`,`id_fd`),
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_fd` (`id_fd`);

--
-- Indexes for table `gbb_user_reply`
--
ALTER TABLE `gbb_user_reply`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_ry` (`id_ur`,`id_ry`) USING BTREE,
  ADD KEY `id_ur` (`id_ur`) USING BTREE,
  ADD KEY `id_ry` (`id_ry`) USING BTREE;

--
-- Indexes for table `gbb_user_reset`
--
ALTER TABLE `gbb_user_reset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ur` (`id_ur`);

--
-- Indexes for table `gbb_user_topic`
--
ALTER TABLE `gbb_user_topic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_ur_tc` (`id_ur`,`id_tc`) USING BTREE,
  ADD KEY `id_tc` (`id_tc`),
  ADD KEY `id_ur` (`id_ur`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gbb_board`
--
ALTER TABLE `gbb_board`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gbb_board_announcement`
--
ALTER TABLE `gbb_board_announcement`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_board_badge`
--
ALTER TABLE `gbb_board_badge`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_board_category`
--
ALTER TABLE `gbb_board_category`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `gbb_board_settings`
--
ALTER TABLE `gbb_board_settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `gbb_board_tag`
--
ALTER TABLE `gbb_board_tag`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `gbb_error`
--
ALTER TABLE `gbb_error`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_message`
--
ALTER TABLE `gbb_message`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_reply`
--
ALTER TABLE `gbb_reply`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_reply_points`
--
ALTER TABLE `gbb_reply_points`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_topic`
--
ALTER TABLE `gbb_topic`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gbb_topic_media`
--
ALTER TABLE `gbb_topic_media`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_topic_points`
--
ALTER TABLE `gbb_topic_points`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_topic_tag`
--
ALTER TABLE `gbb_topic_tag`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_topic_view`
--
ALTER TABLE `gbb_topic_view`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user`
--
ALTER TABLE `gbb_user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gbb_user_board`
--
ALTER TABLE `gbb_user_board`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_board_announcement`
--
ALTER TABLE `gbb_user_board_announcement`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_board_approved`
--
ALTER TABLE `gbb_user_board_approved`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_board_badge`
--
ALTER TABLE `gbb_user_board_badge`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_board_banned`
--
ALTER TABLE `gbb_user_board_banned`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_board_moderator`
--
ALTER TABLE `gbb_user_board_moderator`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_board_stats`
--
ALTER TABLE `gbb_user_board_stats`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_friend`
--
ALTER TABLE `gbb_user_friend`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_reply`
--
ALTER TABLE `gbb_user_reply`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_reset`
--
ALTER TABLE `gbb_user_reset`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gbb_user_topic`
--
ALTER TABLE `gbb_user_topic`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `cleanup_sessions`$$
CREATE DEFINER=`root`@`localhost` EVENT `cleanup_sessions` ON SCHEDULE EVERY 1 DAY STARTS '2016-06-13 04:00:00' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM grokbb.`gbb_session` WHERE `sess_time` + 86400 < UNIX_TIMESTAMP()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
