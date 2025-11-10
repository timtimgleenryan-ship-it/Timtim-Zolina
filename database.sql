-- Simple Payroll System Database Structure
-- Create database
CREATE DATABASE IF NOT EXISTS payroll_system;
USE payroll_system;

-- Users table (for both admin and employees)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Payslips table
CREATE TABLE payslips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    daily_wage DECIMAL(10,2) NOT NULL,
    days_worked INT NOT NULL,
    gross_income DECIMAL(10,2) NOT NULL,
    tax_deduction DECIMAL(10,2) NOT NULL,
    sss_deduction DECIMAL(10,2) NOT NULL,
    philhealth_deduction DECIMAL(10,2) NOT NULL DEFAULT 250.00,
    pagibig_deduction DECIMAL(10,2) NOT NULL DEFAULT 200.00,
    total_deductions DECIMAL(10,2) NOT NULL,
    net_income DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee_month_year (employee_id, month, year)
);

-- Insert default admin user
-- Password: admin123 (hashed)
INSERT INTO users (name, email, password, role) VALUES 
('Administrator', 'admin@payroll.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample employee for testing
-- Password: employee123 (hashed)
INSERT INTO users (name, email, password, role) VALUES 
('John Doe', 'john@payroll.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee');
