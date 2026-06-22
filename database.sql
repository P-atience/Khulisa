CREATE DATABASE IF NOT EXISTS khulisa;
USE khulisa;

DROP TABLE IF EXISTS gaps;
DROP TABLE IF EXISTS scores;
DROP TABLE IF EXISTS assessments;
DROP TABLE IF EXISTS funders;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    business_name VARCHAR(150),
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('owner','mentor','admin') DEFAULT 'owner',
    readiness_score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE assessments (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    revenue DECIMAL(12,2) DEFAULT 0,
    separate_account ENUM('yes','no') DEFAULT 'no',
    record_keeping ENUM('none','manual','spreadsheet','software') DEFAULT 'none',
    years_operating INT DEFAULT 0,
    business_plan ENUM('yes','no') DEFAULT 'no',
    cashflow_forecast ENUM('yes','no') DEFAULT 'no',
    financial_statements ENUM('yes','no') DEFAULT 'no',
    credit_score INT DEFAULT 0,
    tax_compliance ENUM('yes','no') DEFAULT 'no',
    profitability ENUM('yes','no') DEFAULT 'no',
    total_score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE scores (
    score_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    assessment_id INT NOT NULL,
    financial_score INT DEFAULT 0,
    documentation_score INT DEFAULT 0,
    credit_score INT DEFAULT 0,
    governance_score INT DEFAULT 0,
    viability_score INT DEFAULT 0,
    total_score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE
);

CREATE TABLE gaps (
    gap_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    assessment_id INT NOT NULL,
    category VARCHAR(100),
    description TEXT,
    severity ENUM('High','Medium','Low') DEFAULT 'Medium',
    status ENUM('Open','Closed') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE
);

CREATE TABLE funders (
    funder_id INT AUTO_INCREMENT PRIMARY KEY,
    funder_name VARCHAR(150) NOT NULL,
    funder_type VARCHAR(100),
    min_loan DECIMAL(12,2),
    max_loan DECIMAL(12,2),
    min_years INT DEFAULT 0,
    min_revenue DECIMAL(12,2) DEFAULT 0,
    min_score INT DEFAULT 0,
    description TEXT,
    website VARCHAR(255)
);

INSERT INTO funders 
(funder_name, funder_type, min_loan, max_loan, min_years, min_revenue, min_score, description, website)
VALUES
('Small Business Fund','Government',0,50000,0,0,40,'Entry-level funding for small businesses.','#'),
('MicroEnterprise Loan','Alternative',10000,100000,1,10000,55,'Micro enterprise growth finance.','#'),
('Women in Business Fund','DFI',50000,500000,2,50000,70,'Funding for women-owned businesses.','#'),
('Youth Enterprise Fund','Government',20000,200000,1,10000,60,'Youth entrepreneur support funding.','#'),
('Green Business Finance','Impact',50000,1000000,2,100000,75,'Finance for environmentally focused businesses.','#');

INSERT INTO users 
(full_name, business_name, email, password, user_type)
VALUES
('System Administrator', 'KHULISA', 'admin@khulisa.co.za', '$2y$10$S1lWZJ78Y0MpwlunF8NxFeNhhkYlEKD3M4wHDb9tEDVhya9na0V9.', 'admin'),
('Business Mentor', 'KHULISA Mentors', 'mentor@khulisa.co.za', '$2y$10$S1lWZJ78Y0MpwlunF8NxFeNhhkYlEKD3M4wHDb9tEDVhya9na0V9.', 'mentor');