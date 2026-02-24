-- TaskFlow Pro - Complete Database Setup
-- Run this FIRST in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS taskflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taskflow;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    whatsapp VARCHAR(20),
    currency VARCHAR(10) DEFAULT 'INR',
    currency_symbol VARCHAR(5) DEFAULT '₹',
    role ENUM('user','admin') DEFAULT 'user',
    is_verified TINYINT(1) DEFAULT 0,
    verify_token VARCHAR(100),
    reset_token VARCHAR(100),
    reset_expires DATETIME,
    push_subscription TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low','medium','high') DEFAULT 'medium',
    status ENUM('pending','in_progress','completed') DEFAULT 'pending',
    due_date DATE,
    due_time TIME,
    cc_emails TEXT,
    reminder_sent TINYINT(1) DEFAULT 0,
    whatsapp_reminder TINYINT(1) DEFAULT 1,
    email_reminder TINYINT(1) DEFAULT 1,
    push_reminder TINYINT(1) DEFAULT 1,
    recurring_type ENUM('none','daily','weekly','monthly') DEFAULT 'none',
    recurring_parent_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    category VARCHAR(100),
    expense_date DATE NOT NULL,
    notes TEXT,
    person_name VARCHAR(100),
    person_email VARCHAR(150),
    person_phone VARCHAR(20),
    person_whatsapp VARCHAR(20),
    send_notification TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS dues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    person_name VARCHAR(100) NOT NULL,
    person_phone VARCHAR(20),
    person_whatsapp VARCHAR(20),
    person_email VARCHAR(150),
    amount DECIMAL(12,2) NOT NULL,
    paid_amount DECIMAL(12,2) DEFAULT 0,
    due_type ENUM('given','taken') NOT NULL,
    description TEXT,
    due_date DATE,
    status ENUM('pending','partial','paid') DEFAULT 'pending',
    whatsapp_reminder TINYINT(1) DEFAULT 1,
    email_reminder TINYINT(1) DEFAULT 1,
    push_reminder TINYINT(1) DEFAULT 1,
    reminder_sent_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('task','expense','due') NOT NULL,
    ref_id INT,
    channel ENUM('email','whatsapp','push') NOT NULL,
    message TEXT,
    status ENUM('sent','failed','pending') DEFAULT 'pending',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Demo admin (password: Admin@123)
INSERT IGNORE INTO users (name, email, password, role, is_verified, currency, currency_symbol)
VALUES ('Admin', 'admin@taskflow.com',
        '$2y$10$TKh8H1.PfuA2Pi7q7pcYsePh2KJGZ7gFJDf48ByT4.K5TdFxVBYjq',
        'admin', 1, 'INR', '₹');
