-- Town Halls table
CREATE TABLE IF NOT EXISTS `town_halls` (
  `townhall_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 0,
  `status` enum('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `meeting_link` varchar(255) DEFAULT NULL,
  `agenda` text,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`townhall_id`),
  KEY `status` (`status`),
  KEY `date` (`date`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Town Hall Registrations table
CREATE TABLE IF NOT EXISTS `townhall_registrations` (
  `registration_id` int(11) NOT NULL AUTO_INCREMENT,
  `townhall_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `attendance_status` enum('registered','attended','absent') NOT NULL DEFAULT 'registered',
  `registration_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `questions` text,
  `feedback` text,
  `rating` int(1) DEFAULT NULL,
  PRIMARY KEY (`registration_id`),
  UNIQUE KEY `townhall_user` (`townhall_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `townhall_id` (`townhall_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Town Hall Updates table
CREATE TABLE IF NOT EXISTS `townhall_updates` (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `townhall_id` int(11) NOT NULL,
  `update_text` text NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`update_id`),
  KEY `townhall_id` (`townhall_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Town Hall Documents table
CREATE TABLE IF NOT EXISTS `townhall_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `townhall_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_by` varchar(50) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `townhall_id` (`townhall_id`),
  KEY `uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
