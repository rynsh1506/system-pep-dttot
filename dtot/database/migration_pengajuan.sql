-- Create Table pengajuan_dtot
CREATE TABLE IF NOT EXISTS pengajuan_dtot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    nama_cadeb VARCHAR(255) NOT NULL,
    nik VARCHAR(50) NOT NULL,
    hasil_pengecekan ENUM('Belum Dicek', 'Terindikasi', 'Tidak Terindikasi') DEFAULT 'Belum Dicek',
    keterangan TEXT,
    checked_by INT NULL,
    checked_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (checked_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Dummy Data for Testing
INSERT INTO pengajuan_dtot (tanggal, nama_cadeb, nik, hasil_pengecekan) VALUES 
(CURDATE(), 'MIRA ARIANI', '640201205820003', 'Belum Dicek'),
(CURDATE(), 'BUDI SANTOSO', '327501234567890', 'Belum Dicek'),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'ANI WIJAYA', '317109876543210', 'Belum Dicek');
