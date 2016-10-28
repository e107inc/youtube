CREATE TABLE `youtube` (
  `youtube_id` int(11) NOT NULL AUTO_INCREMENT,
  `youtube_ref` varchar(50) NOT NULL,
  `youtube_title` varchar(255) NOT NULL,
  `youtube_type` varchar(5) NOT NULL,
  `youtube_sef` varchar(255) NOT NULL,
  `youtube_subscribe` varchar(50) NOT NULL,
  PRIMARY KEY (`youtube_id`),
  UNIQUE KEY `youtube_sef` (`youtube_sef`),
  KEY `youtube_type` (`youtube_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;