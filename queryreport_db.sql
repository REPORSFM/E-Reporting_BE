-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for queryreport_db
DROP DATABASE IF EXISTS `queryreport_db`;
CREATE DATABASE IF NOT EXISTS `queryreport_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `queryreport_db`;

-- Dumping structure for table queryreport_db.tqueryreport
DROP TABLE IF EXISTS `tqueryreport`;
CREATE TABLE IF NOT EXISTS `tqueryreport` (
  `Kode` varchar(50) NOT NULL COMMENT 'Kode unik report (PK)',
  `NamaReport` varchar(200) NOT NULL COMMENT 'Nama report',
  `QuerySql` text NOT NULL COMMENT 'Query SQL untuk report (hanya SELECT)',
  `Parameter` text DEFAULT NULL COMMENT 'Definisi parameter dalam format JSON',
  `Catatan` text DEFAULT NULL COMMENT 'Catatan atau deskripsi report',
  `IdOrganisasi` varchar(50) DEFAULT NULL COMMENT 'ID Organisasi pemilik report',
  `StAktif` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif (1=Aktif, 0=Tidak Aktif)',
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Tanggal dibuat',
  `CreatedBy` varchar(50) NOT NULL COMMENT 'User yang membuat',
  `UpdatedAt` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Tanggal terakhir diupdate',
  `UpdatedBy` varchar(50) DEFAULT NULL COMMENT 'User yang terakhir mengupdate',
  PRIMARY KEY (`Kode`),
  KEY `idx_organisasi` (`IdOrganisasi`),
  KEY `idx_staktif` (`StAktif`),
  KEY `idx_nama` (`NamaReport`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk menyimpan definisi query report';

-- Dumping data for table queryreport_db.tqueryreport: ~4 rows (approximately)
INSERT IGNORE INTO `tqueryreport` (`Kode`, `NamaReport`, `QuerySql`, `Parameter`, `Catatan`, `IdOrganisasi`, `StAktif`, `CreatedAt`, `CreatedBy`, `UpdatedAt`, `UpdatedBy`) VALUES
	('REP20251127001001', 'Laporan Semua Report', 'SELECT Kode, NamaReport, IdOrganisasi, StAktif, CreatedAt FROM TQueryReport WHERE StAktif = 1', NULL, 'Laporan untuk melihat semua report yang aktif', 'ORG001', 1, '2025-11-27 08:42:52', 'SYSTEM', NULL, NULL),
	('REP20251127001002', 'Laporan Report by Organisasi', 'SELECT Kode, NamaReport, CreatedAt, CreatedBy FROM TQueryReport WHERE IdOrganisasi = :organisasi AND StAktif = :status', '[{"name":"organisasi","type":"string","label":"ID Organisasi"},{"name":"status","type":"integer","label":"Status Aktif (1/0)"}]', 'Laporan report berdasarkan organisasi dan status', 'ORG001', 1, '2025-11-27 08:42:52', 'SYSTEM', NULL, NULL),
	('REP20251127001003', 'Laporan Penjualan per Tanggal', 'SELECT tanggal, produk, jumlah, harga, total, customer FROM TSales WHERE tanggal = :tanggal AND organisasi = :organisasi', '[{"name":"tanggal","type":"date","label":"Tanggal"},{"name":"organisasi","type":"string","label":"ID Organisasi"}]', 'Laporan penjualan berdasarkan tanggal dan organisasi', 'ORG001', 1, '2025-11-27 08:42:52', 'SYSTEM', NULL, NULL),
	('REP20251127001004', 'Total Penjualan per Organisasi', 'SELECT organisasi, COUNT(*) as total_transaksi, SUM(total) as total_nilai FROM TSales WHERE organisasi = :organisasi GROUP BY organisasi', '[{"name":"organisasi","type":"string","label":"ID Organisasi"}]', 'Laporan total penjualan berdasarkan organisasi', 'ORG001', 1, '2025-11-27 08:42:52', 'SYSTEM', NULL, NULL);

-- Dumping structure for table queryreport_db.tsales
DROP TABLE IF EXISTS `tsales`;
CREATE TABLE IF NOT EXISTS `tsales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `produk` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `customer` varchar(100) DEFAULT NULL,
  `organisasi` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_organisasi` (`organisasi`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel demo untuk data penjualan';

-- Dumping data for table queryreport_db.tsales: ~5 rows (approximately)
INSERT IGNORE INTO `tsales` (`id`, `tanggal`, `produk`, `jumlah`, `harga`, `total`, `customer`, `organisasi`, `created_at`) VALUES
	(1, '2025-11-01', 'Laptop Dell', 2, 15000000.00, 30000000.00, 'PT ABC', 'ORG001', '2025-11-27 08:42:52'),
	(2, '2025-11-02', 'Mouse Logitech', 10, 250000.00, 2500000.00, 'PT XYZ', 'ORG001', '2025-11-27 08:42:52'),
	(3, '2025-11-03', 'Keyboard Mechanical', 5, 1500000.00, 7500000.00, 'PT ABC', 'ORG001', '2025-11-27 08:42:52'),
	(4, '2025-11-04', 'Monitor LG', 3, 3000000.00, 9000000.00, 'CV Maju', 'ORG002', '2025-11-27 08:42:52'),
	(5, '2025-11-05', 'Laptop Asus', 1, 12000000.00, 12000000.00, 'PT XYZ', 'ORG001', '2025-11-27 08:42:52');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
