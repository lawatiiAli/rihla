-- Create Database
CREATE DATABASE IF NOT EXISTS rihla_db;
USE rihla_db;

-- Tours Table (for booking.html)
CREATE TABLE tours (
    tour_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(6,2) NOT NULL,
    location VARCHAR(50) NOT NULL,
    image_url VARCHAR(255)
);

-- Transportation Table (transport.html)
CREATE TABLE transportation (
    transport_id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    city VARCHAR(50) NOT NULL,
    cost DECIMAL(6,2) NOT NULL,
    UNIQUE KEY unique_transport (type, city)
);

-- Markets Table (markets.html)
CREATE TABLE markets (
    market_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    items TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Activities Table (add_activity.html)
CREATE TABLE user_activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(6,2) NOT NULL,
    location VARCHAR(50) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    image_link VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);