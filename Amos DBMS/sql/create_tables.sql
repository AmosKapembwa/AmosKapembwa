-- Town Halls table
CREATE TABLE IF NOT EXISTS `town_halls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `platform` enum('Zoom','Google Meet','MS Teams') NOT NULL,
  `meeting_link` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 100,
  `agenda` text,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Town Hall Registrations table
CREATE TABLE IF NOT EXISTS `town_hall_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `town_hall_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `registration_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attendance_status` enum('registered','attended','cancelled') NOT NULL DEFAULT 'registered',
  PRIMARY KEY (`id`),
  UNIQUE KEY `town_hall_user` (`town_hall_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initiatives table
CREATE TABLE IF NOT EXISTS `initiatives` (
  `initiative_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` enum('infrastructure','education','healthcare','environment','social') NOT NULL,
  `location` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(15,2) NOT NULL,
  `impact_area` text NOT NULL,
  `status` enum('proposed','in_progress','completed') NOT NULL DEFAULT 'proposed',
  `progress_percentage` int(11) NOT NULL DEFAULT 0,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`initiative_id`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initiative Documents table
CREATE TABLE IF NOT EXISTS `initiative_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `initiative_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `initiative_id` (`initiative_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initiative Updates table
CREATE TABLE IF NOT EXISTS `initiative_updates` (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `initiative_id` int(11) NOT NULL,
  `progress_percentage` int(11) NOT NULL,
  `status` enum('proposed','in_progress','completed') NOT NULL,
  `notes` text NOT NULL,
  `updated_by` varchar(50) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`update_id`),
  KEY `initiative_id` (`initiative_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
