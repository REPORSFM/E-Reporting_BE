-- ====================================================================
-- Database Schema for Query Report API
-- ====================================================================
-- Description: Database schema untuk Query Report Builder API
-- Database: queryreport_db
-- Author: System
-- Date: 2025-11-27
-- ====================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS `queryreport_db` 
DEFAULT CHARACTER SET utf8mb4 
DEFAULT COLLATE utf8mb4_general_ci;

USE `queryreport_db`;

-- ====================================================================
-- Table: TQueryReport
-- Description: Menyimpan definisi query report yang bisa dieksekusi
-- ====================================================================
CREATE TABLE IF NOT EXISTS `TQueryReport` (
  `Kode` VARCHAR(50) NOT NULL PRIMARY KEY COMMENT 'Kode unik report (PK)',
  `NamaReport` VARCHAR(200) NOT NULL COMMENT 'Nama report',
  `QuerySql` TEXT NOT NULL COMMENT 'Query SQL untuk report (hanya SELECT)',
  `Parameter` TEXT DEFAULT NULL COMMENT 'Definisi parameter dalam format JSON',
  `Catatan` TEXT DEFAULT NULL COMMENT 'Catatan atau deskripsi report',
  `IdOrganisasi` VARCHAR(50) DEFAULT NULL COMMENT 'ID Organisasi pemilik report',
  `StAktif` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif (1=Aktif, 0=Tidak Aktif)',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal dibuat',
  `CreatedBy` VARCHAR(50) NOT NULL COMMENT 'User yang membuat',
  `UpdatedAt` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Tanggal terakhir diupdate',
  `UpdatedBy` VARCHAR(50) DEFAULT NULL COMMENT 'User yang terakhir mengupdate',
  
  INDEX `idx_organisasi` (`IdOrganisasi`),
  INDEX `idx_staktif` (`StAktif`),
  INDEX `idx_nama` (`NamaReport`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Tabel untuk menyimpan definisi query report';

-- ====================================================================
-- Sample Data for Testing
-- ====================================================================

-- Insert sample report 1: Simple report without parameters
INSERT INTO `TQueryReport` 
(`Kode`, `NamaReport`, `QuerySql`, `Parameter`, `Catatan`, `IdOrganisasi`, `StAktif`, `CreatedAt`, `CreatedBy`) 
VALUES 
('REP20251127001001', 
 'Laporan Semua Report', 
 'SELECT Kode, NamaReport, IdOrganisasi, StAktif, CreatedAt FROM TQueryReport WHERE StAktif = 1', 
 NULL,
 'Laporan untuk melihat semua report yang aktif', 
 'ORG001', 
 1, 
 NOW(), 
 'SYSTEM');

-- Insert sample report 2: Report with parameters (example for sales data)
-- Note: Ini contoh jika ada tabel sales, sesuaikan dengan kebutuhan
INSERT INTO `TQueryReport` 
(`Kode`, `NamaReport`, `QuerySql`, `Parameter`, `Catatan`, `IdOrganisasi`, `StAktif`, `CreatedAt`, `CreatedBy`) 
VALUES 
('REP20251127001002', 
 'Laporan Report by Organisasi', 
 'SELECT Kode, NamaReport, CreatedAt, CreatedBy FROM TQueryReport WHERE IdOrganisasi = :organisasi AND StAktif = :status', 
 '[{"name":"organisasi","type":"string","label":"ID Organisasi"},{"name":"status","type":"integer","label":"Status Aktif (1/0)"}]',
 'Laporan report berdasarkan organisasi dan status', 
 'ORG001', 
 1, 
 NOW(), 
 'SYSTEM');

-- ====================================================================
-- Optional: Create table for demo sales data if needed
-- ====================================================================
CREATE TABLE IF NOT EXISTS `TSales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tanggal` DATE NOT NULL,
  `produk` VARCHAR(100) NOT NULL,
  `jumlah` INT NOT NULL,
  `harga` DECIMAL(15,2) NOT NULL,
  `total` DECIMAL(15,2) NOT NULL,
  `customer` VARCHAR(100),
  `organisasi` VARCHAR(50),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_tanggal` (`tanggal`),
  INDEX `idx_organisasi` (`organisasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Tabel demo untuk data penjualan';

-- Insert sample sales data
INSERT INTO `TSales` (`tanggal`, `produk`, `jumlah`, `harga`, `total`, `customer`, `organisasi`) VALUES
('2025-11-01', 'Laptop Dell', 2, 15000000, 30000000, 'PT ABC', 'ORG001'),
('2025-11-02', 'Mouse Logitech', 10, 250000, 2500000, 'PT XYZ', 'ORG001'),
('2025-11-03', 'Keyboard Mechanical', 5, 1500000, 7500000, 'PT ABC', 'ORG001'),
('2025-11-04', 'Monitor LG', 3, 3000000, 9000000, 'CV Maju', 'ORG002'),
('2025-11-05', 'Laptop Asus', 1, 12000000, 12000000, 'PT XYZ', 'ORG001');

-- Insert sample report for sales
INSERT INTO `TQueryReport` 
(`Kode`, `NamaReport`, `QuerySql`, `Parameter`, `Catatan`, `IdOrganisasi`, `StAktif`, `CreatedAt`, `CreatedBy`) 
VALUES 
('REP20251127001003', 
 'Laporan Penjualan per Tanggal', 
 'SELECT tanggal, produk, jumlah, harga, total, customer FROM TSales WHERE tanggal = :tanggal AND organisasi = :organisasi', 
 '[{"name":"tanggal","type":"date","label":"Tanggal"},{"name":"organisasi","type":"string","label":"ID Organisasi"}]',
 'Laporan penjualan berdasarkan tanggal dan organisasi', 
 'ORG001', 
 1, 
 NOW(), 
 'SYSTEM');

INSERT INTO `TQueryReport` 
(`Kode`, `NamaReport`, `QuerySql`, `Parameter`, `Catatan`, `IdOrganisasi`, `StAktif`, `CreatedAt`, `CreatedBy`) 
VALUES 
('REP20251127001004', 
 'Total Penjualan per Organisasi', 
 'SELECT organisasi, COUNT(*) as total_transaksi, SUM(total) as total_nilai FROM TSales WHERE organisasi = :organisasi GROUP BY organisasi', 
 '[{"name":"organisasi","type":"string","label":"ID Organisasi"}]',
 'Laporan total penjualan berdasarkan organisasi', 
 'ORG001', 
 1, 
 NOW(), 
 'SYSTEM');

-- ====================================================================
-- End of Schema
-- ====================================================================

-- Show created tables
SHOW TABLES;

-- Show TQueryReport structure
DESCRIBE TQueryReport;

-- Show sample data
SELECT * FROM TQueryReport;
