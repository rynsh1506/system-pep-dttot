-- Create Database
CREATE DATABASE IF NOT EXISTS db_dtot;
USE db_dtot;

-- Create Table Terduga
CREATE TABLE IF NOT EXISTS terduga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    terduga_type ENUM('Orang', 'Korporasi') NOT NULL,
    kode_densus VARCHAR(50),
    tempat_lahir VARCHAR(255),
    tanggal_lahir DATE,
    wn_asal_negara VARCHAR(100),
    deskripsi TEXT,
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data
INSERT INTO terduga (nama, terduga_type, kode_densus, tempat_lahir, tanggal_lahir, wn_asal_negara, deskripsi, alamat) 
VALUES 
('MIRA ARIANI alias UMM ZAHRA', 'Orang', 'IDD-032', 'Tenggarong', '1982-05-02', 'Indonesia', 'NIK 640201205820003, Istri dari Edi Siswanto (I.D.D.022)', 'Jl. H. Usman, Meruyung, Kec. Limo, Kota Depok, Jawa Barat, 16515; Jalan Stadion Gg. Bahagia, Panji, Tenggarong, Kab. Kutai Kartanegara, Kalimantan Timur'),
('BAITUL MAAL MUHZATUL UMMAH alias MUHZATUL UMMAH', 'Korporasi', 'EDD-026', '-', NULL, 'Indonesia', 'Pengurus Muhzatul Ummah antara lain Mira Ariani, Edi Siswanto, dan Afrizal', 'N/A');

-- Add soft delete and pending status to terduga
ALTER TABLE terduga ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;
ALTER TABLE terduga ADD COLUMN is_pending TINYINT(1) DEFAULT 0;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role_level INT NOT NULL COMMENT '1: Staf, 2: Supervisor, 3: Manager, 4: Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Change Requests Table (For Approval Workflow)
CREATE TABLE IF NOT EXISTS change_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target_id INT NULL COMMENT 'ID of the record in terduga table (null for new data)',
    request_type ENUM('ADD', 'EDIT', 'DELETE') NOT NULL,
    data_json TEXT NOT NULL COMMENT 'Serialized new data',
    requester_id INT NOT NULL,
    status ENUM('PENDING_SPV', 'PENDING_MANAGER', 'APPROVED', 'REJECTED') DEFAULT 'PENDING_SPV',
    approver_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME NULL,
    FOREIGN KEY (requester_id) REFERENCES users(id)
);

-- Sample Users (Password is 'password123' for all)
INSERT INTO users (username, password, full_name, role_level) VALUES 
('staf', '$2y$10$tx6TIvc8btshlMJmhy3AKeTeZ5dNpJoiqFNjh2.qiXEls3lzkY2He', 'Andi Staf', 1),
('spv', '$2y$10$tx6TIvc8btshlMJmhy3AKeTeZ5dNpJoiqFNjh2.qiXEls3lzkY2He', 'Budi Supervisor', 2),
('manager', '$2y$10$tx6TIvc8btshlMJmhy3AKeTeZ5dNpJoiqFNjh2.qiXEls3lzkY2He', 'Citra Manager', 3),
('admin', '$2y$10$tx6TIvc8btshlMJmhy3AKeTeZ5dNpJoiqFNjh2.qiXEls3lzkY2He', 'Super Admin', 4);

