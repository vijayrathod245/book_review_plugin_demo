--
-- Table structure for table `wp_t_book_catalog`
--

CREATE TABLE IF NOT EXISTS `wp_t_book_catalog` (
  `bcat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bcat_country_code` char(8) NOT NULL,
  `bcat_asin` varchar(255) NOT NULL,
  `bcat_author_wpuid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK (Wordpress user table user id)',
  `bcat_title` varchar(255) NOT NULL,
  `bcat_book_format` tinyint(2) UNSIGNED NOT NULL DEFAULT '1',
  `bcat_isbn_13` varchar(255) NOT NULL,
  `bcat_amazon_permalink` text NOT NULL,
  `bcat_cover_image` varchar(255) NOT NULL,
  `bcat_sample_book_file` varchar(255) NOT NULL,
  `bcat_full_length_book_file` varchar(255) NOT NULL,
  `bcat_is_active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `bcat_adt` datetime NOT NULL,
  PRIMARY KEY (`bcat_id`),
  UNIQUE KEY `bcat_asin_2` (`bcat_asin`),
  KEY `bcat_country_code` (`bcat_country_code`),
  KEY `bcat_asin` (`bcat_asin`),
  KEY `bcat_author_wpuid` (`bcat_author_wpuid`),
  KEY `bcat_title` (`bcat_title`),
  KEY `bcat_book_format` (`bcat_book_format`),
  KEY `bcat_isbn_13` (`bcat_isbn_13`),
  KEY `bcat_is_active` (`bcat_is_active`),
  KEY `bcat_adt` (`bcat_adt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



--
-- Table structure for table `wp_t_book_request_cycle`
--

CREATE TABLE IF NOT EXISTS `wp_t_book_request_cycle` (
  `brc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `brc_bcat_id` int(10) UNSIGNED NOT NULL COMMENT 'FK (Wordpress user table user id)',
  `brc_bcat_author_wpuid` int(10) UNSIGNED NOT NULL COMMENT 'FK (Wordpress user table user id)',
  `brc_reviewer_wpuid` int(10) UNSIGNED NOT NULL,
  `brc_request_adt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `brc_request_status` tinyint(2) UNSIGNED NOT NULL DEFAULT '1',
  `brc_pending_adt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `brc_approved_adt` datetime DEFAULT NULL,
  `brc_received_adt` datetime DEFAULT NULL,
  `brc_completed_adt` datetime DEFAULT NULL,
  `brc_denied_adt` datetime DEFAULT NULL,
  `brc_denied_reason` varchar(255) DEFAULT NULL,
  `brc_turnaround_time` tinyint(2) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`brc_id`),
  UNIQUE KEY `brc_bcat_id_2` (`brc_bcat_id`,`brc_bcat_author_wpuid`,`brc_reviewer_wpuid`),
  KEY `brc_turnaround_time` (`brc_turnaround_time`),
  KEY `brc_bcat_id` (`brc_bcat_id`),
  KEY `brc_bcat_author_wpuid` (`brc_bcat_author_wpuid`),
  KEY `brc_reviewer_wpuid` (`brc_reviewer_wpuid`),
  KEY `brc_request_status` (`brc_request_status`),
  KEY `brc_pending_adt` (`brc_pending_adt`),
  KEY `brc_approved_adt` (`brc_approved_adt`),
  KEY `brc_received_adt` (`brc_received_adt`),
  KEY `brc_completed_adt` (`brc_completed_adt`),
  KEY `brc_denied_adt` (`brc_denied_adt`),
  KEY `brc_denied_reason` (`brc_denied_reason`),
  KEY `brc_request_adt` (`brc_request_adt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `wp_t_book_request_cycle` ADD FOREIGN KEY (`brc_bcat_id`) REFERENCES `wp_t_book_catalog`(`bcat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `wp_t_book_request_cycle` ADD `brc_review_url` VARCHAR(255) NOT NULL DEFAULT 'NULL' AFTER `brc_reviewer_wpuid`;