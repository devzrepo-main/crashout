-- Create database (run as a user with privileges)
CREATE DATABASE IF NOT EXISTS crashout_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crashout_db;

-- Table to log every crashout
CREATE TABLE IF NOT EXISTS crashout_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category ENUM('sports','gaming','delivery','minorities','other') NOT NULL,
  detail VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (category),
  INDEX (created_at)
);

-- Optional: create a limited app user (recommended)
-- CREATE USER 'crashout'@'localhost' IDENTIFIED BY 'strongpassword';
-- GRANT INSERT, SELECT ON crashout_db.* TO 'crashout'@'localhost';
-- FLUSH PRIVILEGES;
