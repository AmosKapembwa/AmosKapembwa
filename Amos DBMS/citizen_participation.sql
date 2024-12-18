-- Citizen Participation System Database Schema

CREATE DATABASE IF NOT EXISTS citizen_participation;
USE citizen_participation;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    address TEXT,
    phone VARCHAR(20),
    role ENUM('citizen', 'admin', 'moderator') DEFAULT 'citizen',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP
);

-- Initiatives table
CREATE TABLE IF NOT EXISTS initiatives (
    initiative_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('proposed', 'under_review', 'approved', 'in_progress', 'completed', 'rejected') DEFAULT 'proposed',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- Feedback table
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    initiative_id INT,
    content TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (initiative_id) REFERENCES initiatives(initiative_id)
);

-- Polls table
CREATE TABLE IF NOT EXISTS polls (
    poll_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    start_date TIMESTAMP,
    end_date TIMESTAMP,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- Poll Options table
CREATE TABLE IF NOT EXISTS poll_options (
    option_id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT,
    option_text TEXT NOT NULL,
    FOREIGN KEY (poll_id) REFERENCES polls(poll_id)
);

-- Poll Votes table
CREATE TABLE IF NOT EXISTS poll_votes (
    vote_id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT,
    user_id INT,
    option_id INT,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poll_id) REFERENCES polls(poll_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (option_id) REFERENCES poll_options(option_id),
    UNIQUE KEY unique_vote (poll_id, user_id)
);

-- Town Halls table
CREATE TABLE IF NOT EXISTS town_halls (
    town_hall_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    scheduled_date TIMESTAMP NOT NULL,
    duration_minutes INT DEFAULT 60,
    meeting_link TEXT,
    created_by INT,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- Town Hall Registrations table
CREATE TABLE IF NOT EXISTS town_hall_registrations (
    registration_id INT PRIMARY KEY AUTO_INCREMENT,
    town_hall_id INT,
    user_id INT,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attendance_status ENUM('registered', 'attended', 'absent') DEFAULT 'registered',
    FOREIGN KEY (town_hall_id) REFERENCES town_halls(town_hall_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    UNIQUE KEY unique_registration (town_hall_id, user_id)
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    initiative_id INT,
    content TEXT NOT NULL,
    parent_comment_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (initiative_id) REFERENCES initiatives(initiative_id),
    FOREIGN KEY (parent_comment_id) REFERENCES comments(comment_id)
);

-- Create indexes for better performance
CREATE INDEX idx_initiatives_status ON initiatives(status);
CREATE INDEX idx_polls_status ON polls(status);
CREATE INDEX idx_town_halls_scheduled_date ON town_halls(scheduled_date);
