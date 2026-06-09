CREATE DATABASE IF NOT EXISTS cadeb_db;
USE cadeb_db;

CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_cadeb VARCHAR(255) NOT NULL,
    no_identitas VARCHAR(50) NOT NULL,
    nama_pasangan VARCHAR(255),
    no_identitas_pasangan VARCHAR(50),
    kategori VARCHAR(50) NOT NULL DEFAULT 'Cadeb',
    keterangan_pep VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed data dari gambar yang diupload
INSERT INTO candidates (nama_cadeb, no_identitas, nama_pasangan, no_identitas_pasangan, keterangan_pep) VALUES
('SEPTA AGUNG KURNIAWAN', '1971042509940004', 'ADE ARYANA RESTU SAPUTRA', '1371116305920012', 'Pasangan Cadeb'),
('SHINTA TIARA PUTRI', '3674076002940002', NULL, NULL, 'Cadeb'),
('UMAR BAWI', '6311060708650002', NULL, NULL, 'Cadeb'),
('AGUS MUJIWANTO', '1303100808750001', NULL, NULL, 'Cadeb'),
('Karlina Sofyarto', '1371115602920008', NULL, NULL, 'Cadeb'),
('ADELIN ZAIN FAHIRZA', '1607075106000002', NULL, NULL, 'Cadeb'),
('DHIMAS WIDHIARTO NUGROHO', '6271032506870009', 'PURI ANGGRIA LESTARI', '6271035901880004', 'Pasangan Cadeb'),
('SUKISNO', '1871090201680000', NULL, NULL, 'Cadeb'),
('SONNY KURNIAWAN', '1606011903760000', NULL, NULL, 'Cadeb'),
('JARWOKO', '1812061106880002', NULL, NULL, 'Cadeb'),
('Hj LINA MARLINA', '1807025505770010', NULL, NULL, 'Cadeb'),
('RELLYVA VENNY OCTALINA', '3302266810810002', NULL, NULL, 'Cadeb'),
('NENA ANALIA', '3603176005880009', NULL, NULL, 'Cadeb'),
('YUDHI DAMAI ATH THORIQ', '1471080502890001', NULL, NULL, 'Cadeb'),
('Andri', '1807021910740001', NULL, NULL, 'Cadeb'),
('SAWALUDIN WAHID', '1802052511730004', NULL, NULL, 'Cadeb'),
('FATURRACHMAN', '1904023003830001', 'AGUSTIN RACHMATRIYANI', '1971055908820004', 'Cadeb & Pasangan'),
('CITRA PRASETIA TRIMAHESA', '3217034205020006', 'MUHAMMAD RACHA AUDIANS', '3217061607020013', 'Pasangan Cadeb'),
('SUPARMAN', '1901042707700003', 'SITI NORMINA', '1901045502700001', 'Cadeb & Pasangan'),
('Budi Oktavian', '1671082810820005', 'Eka Susilawati', '1971082810820005', 'Cadeb & Pasangan'),
('YOGA SILIWA PANJAITAN', '1209311204010001', NULL, NULL, 'Cadeb');

-- Tabel Users untuk Login
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100),
    level INT NOT NULL DEFAULT 1, -- 1: Staff, 2: Supervisor, 3: Manager, 4: Super Admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reset users with levels (password: admin123)
TRUNCATE TABLE users;
INSERT INTO users (username, password, nama_lengkap, level) VALUES 
('staff', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Staff Input (L1)', 1),
('supervisor', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Supervisor (L2)', 2),
('manager', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Manager (L3)', 3),
('admin', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Super Admin (L4)', 4);

-- Tabel Approval Requests
CREATE TABLE IF NOT EXISTS approval_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT,
    type ENUM('EDIT', 'DELETE') NOT NULL,
    old_data JSON, -- Current data for comparison
    new_data JSON, -- Proposed data for EDIT
    requester_id INT,
    l2_status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    l2_approver_id INT,
    l2_notes TEXT,
    l3_status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    l3_approver_id INT,
    l3_notes TEXT,
    final_status ENUM('PENDING', 'COMPLETED', 'REJECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
    FOREIGN KEY (requester_id) REFERENCES users(id)
);
