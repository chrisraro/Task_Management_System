CREATE DATABASE IF NOT EXISTS task_management;
USE task_management;

-- Users table: stores user credentials and roles (admin/employee)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','employee') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tasks table: stores tasks created by the admin and assigned to an employee
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    assigned_to INT, 
    status ENUM('pending','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- Insert sample users.
-- Replace the hash strings with ones generated from PHP’s password_hash() function.
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$uG/E3vE0lG2b7Wv0cWJZ6eXWjJz85f7o3f/Nm/2YdEg0Ehd05yHbi', 'admin'),
('employee1', '$2y$10$gRFrB38dZjJGu/kqZH26XOSF.J3R90C.rp5L7pd3nTCCaz6YrMeqq', 'employee');