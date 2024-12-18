-- Suggestions table
CREATE TABLE IF NOT EXISTS `suggestions` (
  `suggestion_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` enum('infrastructure','education','healthcare','environment','social','other') NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','implemented') NOT NULL DEFAULT 'pending',
  `votes` int(11) NOT NULL DEFAULT 0,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`suggestion_id`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `category` (`category`),
  KEY `votes` (`votes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Suggestion Votes table
CREATE TABLE IF NOT EXISTS `suggestion_votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `suggestion_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `vote_type` enum('upvote','downvote') NOT NULL,
  `voted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `suggestion_user` (`suggestion_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Suggestion Comments table
CREATE TABLE IF NOT EXISTS `suggestion_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `suggestion_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `suggestion_id` (`suggestion_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
