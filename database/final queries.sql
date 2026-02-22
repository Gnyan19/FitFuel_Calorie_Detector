-- ============================================================
-- FitFuel Calorie Detector — Database Setup
-- Run this file once to initialize the database schema.
-- ============================================================

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    age           TINYINT UNSIGNED NOT NULL,
    weight        SMALLINT UNSIGNED NOT NULL,
    height        SMALLINT UNSIGNED NOT NULL,
    gender        ENUM('m', 'f') NOT NULL,
    bmi           FLOAT DEFAULT NULL,
    bmi_category  VARCHAR(100) DEFAULT NULL,
    bfp           FLOAT(5, 2)  DEFAULT NULL,
    bmr           FLOAT(8, 2)  DEFAULT NULL,
    breakfast     INT DEFAULT 0,
    lunch         INT DEFAULT 0,
    dinner        INT DEFAULT 0,
    snacks        INT DEFAULT 0,
    daily_goal    INT DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);