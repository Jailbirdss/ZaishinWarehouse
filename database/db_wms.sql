-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2026 at 09:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_wms`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3b82f6',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `color`, `created_at`) VALUES
(1, 'Kertas', 'Bahan kertas berbagai jenis dan ukuran', '#3b82f6', '2026-06-05 13:53:40'),
(2, 'Tinta & Toner', 'Tinta cetak dan toner printer', '#8b5cf6', '2026-06-05 13:53:40'),
(3, 'Film & Plate', 'Film negatif dan plate cetak offset', '#f59e0b', '2026-06-05 13:53:40'),
(4, 'Consumables', 'Bahan habis pakai operasional percetakan', '#10b981', '2026-06-05 13:53:40'),
(5, 'Spare Part', 'Suku cadang mesin cetak', '#ef4444', '2026-06-05 13:53:40');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(20) NOT NULL DEFAULT 'pcs',
  `min_stock` int(11) NOT NULL DEFAULT 10,
  `qr_code` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `sku`, `name`, `category_id`, `description`, `unit`, `min_stock`, `qr_code`, `created_at`) VALUES
(1, 'KRT-001', 'Kertas HVS A4 70gsm', 1, NULL, 'rim', 50, 'QR-KRT001', '2026-06-05 13:53:40'),
(2, 'KRT-002', 'Kertas HVS A3 80gsm', 1, NULL, 'rim', 30, 'QR-KRT002', '2026-06-05 13:53:40'),
(3, 'KRT-003', 'Art Paper A4 150gsm', 1, NULL, 'rim', 20, 'QR-KRT003', '2026-06-05 13:53:40'),
(4, 'KRT-004', 'Art Carton A3 230gsm', 1, NULL, 'rim', 15, 'QR-KRT004', '2026-06-05 13:53:40'),
(5, 'KRT-005', 'Kertas Buram F4', 1, NULL, 'rim', 40, 'QR-KRT005', '2026-06-05 13:53:40'),
(6, 'KRT-006', 'Vinyl Sticker A3', 1, NULL, 'lembar', 10, 'QR-KRT006', '2026-06-05 13:53:40'),
(7, 'KRT-007', 'Banner Frontlite 440gsm', 1, NULL, 'roll', 5, 'QR-KRT007', '2026-06-05 13:53:40'),
(8, 'TIN-001', 'Tinta Cyan (C) 1L', 2, NULL, 'botol', 10, 'QR-TIN001', '2026-06-05 13:53:40'),
(9, 'TIN-002', 'Tinta Magenta (M) 1L', 2, NULL, 'botol', 10, 'QR-TIN002', '2026-06-05 13:53:40'),
(10, 'TIN-003', 'Tinta Yellow (Y) 1L', 2, NULL, 'botol', 10, 'QR-TIN003', '2026-06-05 13:53:40'),
(11, 'TIN-004', 'Tinta Black (K) 1L', 2, NULL, 'botol', 15, 'QR-TIN004', '2026-06-05 13:53:40'),
(12, 'TIN-005', 'Toner HP LaserJet 85A', 2, NULL, 'cartridge', 5, 'QR-TIN005', '2026-06-05 13:53:40'),
(13, 'TIN-006', 'Toner Canon 328', 2, NULL, 'cartridge', 5, 'QR-TIN006', '2026-06-05 13:53:40'),
(14, 'FLM-001', 'Film Negatif A3', 3, NULL, 'lembar', 20, 'QR-FLM001', '2026-06-05 13:53:40'),
(15, 'FLM-002', 'Plate Offset A2', 3, NULL, 'lembar', 10, 'QR-FLM002', '2026-06-05 13:53:40'),
(16, 'CNS-001', 'Selotip Bening 2inch', 4, NULL, 'roll', 20, 'QR-CNS001', '2026-06-05 13:53:40'),
(17, 'CNS-002', 'Lakban Coklat 3inch', 4, NULL, 'roll', 15, 'QR-CNS002', '2026-06-05 13:53:40'),
(18, 'CNS-003', 'Tali Rafia Gulung', 4, NULL, 'gulung', 10, 'QR-CNS003', '2026-06-05 13:53:40'),
(19, 'CNS-004', 'Bubble Wrap Roll 50m', 4, NULL, 'roll', 5, 'QR-CNS004', '2026-06-05 13:53:40'),
(20, 'SPR-001', 'Roller Karet Mesin Cetak', 5, NULL, 'pcs', 3, 'QR-SPR001', '2026-06-05 13:53:40'),
(21, 'SPR-002', 'Blade Scraper', 5, NULL, 'pcs', 5, 'QR-SPR002', '2026-06-05 13:53:40');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `role`, `type`, `title`, `message`, `related_id`, `is_read`, `created_at`) VALUES
(1, NULL, 'admin_gudang', 'low_stock', 'Alarm Stok Rendah: Kertas HVS A4 70gsm', 'Stok barang Kertas HVS A4 70gsm (KRT-001) saat ini menipis menjadi 12 rim (Batas minimum: 50 rim). Segera ajukan restock.', 1, 1, '2026-06-06 20:11:24'),
(2, NULL, 'divisi_pembelian', 'low_stock', 'Alarm Stok Rendah: Kertas HVS A4 70gsm', 'Stok barang Kertas HVS A4 70gsm (KRT-001) saat ini menipis menjadi 12 rim (Batas minimum: 50 rim). Segera ajukan restock.', 1, 1, '2026-06-06 20:11:24'),
(3, NULL, 'admin_gudang', 'restock_submitted', 'Permintaan Restock Baru', 'Dani Prasetyo mengajukan restock untuk barang Tinta Magenta (M) 1L sebanyak 10 botol.', 1, 1, '2026-06-06 20:11:24'),
(4, 4, NULL, 'restock_approved', 'Permintaan Restock Disetujui', 'Permintaan restock Anda untuk barang Art Paper A4 150gsm sebanyak 5 rim telah disetujui oleh Admin.', 2, 1, '2026-06-06 20:11:24'),
(5, NULL, 'petugas_gudang', 'opname_initiated', 'Sesi Stock Opname Dimulai', 'Sesi Stock Opname OPN-20260606-95CBB baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.', 2, 1, '2026-06-06 20:11:24'),
(6, NULL, 'admin_gudang', 'restock_submitted', 'Permintaan Restock Baru', 'Dani Prasetyo mengajukan restock untuk barang Kertas HVS A4 70gsm sebanyak 50 unit.', 1, 1, '2026-06-06 20:44:22'),
(7, NULL, 'admin_gudang', 'so_created', 'Sales Order Baru: SO-20260607-29054', 'Sales Order SO-20260607-29054 untuk pelanggan PT Sinar Grafika baru saja dibuat oleh Citra Dewi dan siap untuk diproses.', NULL, 1, '2026-06-07 03:26:13'),
(8, NULL, 'petugas_gudang', 'so_created', 'Sales Order Baru: SO-20260607-29054', 'Sales Order SO-20260607-29054 untuk pelanggan PT Sinar Grafika baru saja dibuat oleh Citra Dewi dan siap untuk diproses.', NULL, 1, '2026-06-07 03:26:13'),
(9, NULL, 'admin_gudang', 'restock_submitted', 'Permintaan Restock Baru', 'Dani Prasetyo mengajukan restock untuk barang Tinta Magenta (M) 1L sebanyak 50 unit.', 2, 1, '2026-06-07 03:43:05'),
(10, 4, NULL, 'restock_approved', 'Permintaan Restock Disetujui', 'Permintaan restock Anda untuk barang Tinta Magenta (M) 1L sebanyak 50 unit telah disetujui oleh Admin.', 2, 1, '2026-06-07 03:43:30'),
(11, NULL, 'petugas_gudang', 'opname_initiated', 'Sesi Stock Opname Dimulai', 'Sesi Stock Opname OPN-20260607-180F3 baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.', 3, 1, '2026-06-07 04:10:47'),
(12, NULL, 'admin_gudang', 'opname_completed', 'Sesi Stock Opname Selesai', 'Sesi Stock Opname OPN-20260607-180F3 telah selesai difinalisasi. Data kuantitas stok di sistem telah disinkronkan sesuai hasil perhitungan fisik lapangan.', 3, 1, '2026-06-07 04:11:16'),
(13, NULL, 'manajemen', 'opname_completed', 'Sesi Stock Opname Selesai', 'Sesi Stock Opname OPN-20260607-180F3 telah selesai difinalisasi. Data kuantitas stok di sistem telah disinkronkan sesuai hasil perhitungan fisik lapangan.', 3, 0, '2026-06-07 04:11:16'),
(14, 4, NULL, 'restock_rejected', 'Permintaan Restock Ditolak', 'Permintaan restock Anda untuk barang Kertas HVS A4 70gsm sebanyak 50 unit telah ditolak oleh Etmin Datang.', 1, 1, '2026-06-07 12:40:49'),
(15, NULL, 'petugas_gudang', 'opname_initiated', 'Sesi Stock Opname Dimulai', 'Sesi Stock Opname OPN-20260612-D4E90 baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.', 4, 1, '2026-06-12 11:52:05'),
(16, NULL, 'petugas_gudang', 'opname_initiated', 'Sesi Stock Opname Dimulai', 'Sesi Stock Opname OPN-20260627-A3C5B baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.', 5, 1, '2026-06-27 03:50:14'),
(17, NULL, 'petugas_gudang', 'opname_initiated', 'Sesi Stock Opname Dimulai', 'Sesi Stock Opname OPN-20260627-9125D baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.', 6, 1, '2026-06-27 10:45:30'),
(18, NULL, 'petugas_gudang', 'opname_initiated', 'Sesi Stock Opname Dimulai', 'Sesi Stock Opname OPN-20260627-A9211 baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.', 7, 1, '2026-06-27 11:06:13'),
(19, NULL, 'admin_gudang', 'inbound_discrepancy', 'Laporan Ketidaksesuaian Inbound: Art Paper A4 150gsm', 'Terdapat ketidaksesuaian pada penerimaan barang Art Paper A4 150gsm (KRT-003). Jumlah fisik: 0 rim. Kondisi: Rusak. Catatan: [KETIDAKSESUAIAN: Jumlah Fisik Tidak Sesuai PO] adadw. Nomor Ref: IN-20260627-9CFAC', 3, 0, '2026-06-27 12:00:56'),
(20, NULL, 'divisi_pembelian', 'inbound_discrepancy', 'Laporan Ketidaksesuaian Inbound: Art Paper A4 150gsm', 'Terdapat ketidaksesuaian pada penerimaan barang Art Paper A4 150gsm (KRT-003). Jumlah fisik: 0 rim. Kondisi: Rusak. Catatan: [KETIDAKSESUAIAN: Jumlah Fisik Tidak Sesuai PO] adadw. Nomor Ref: IN-20260627-9CFAC', 3, 1, '2026-06-27 12:00:56'),
(21, NULL, 'manajemen', 'inbound_discrepancy', 'Laporan Ketidaksesuaian Inbound: Art Paper A4 150gsm', 'Terdapat ketidaksesuaian pada penerimaan barang Art Paper A4 150gsm (KRT-003). Jumlah fisik: 0 rim. Kondisi: Rusak. Catatan: [KETIDAKSESUAIAN: Jumlah Fisik Tidak Sesuai PO] adadw. Nomor Ref: IN-20260627-9CFAC', 3, 0, '2026-06-27 12:00:56'),
(22, NULL, 'admin_gudang', 'so_created', 'Sales Order Baru: SO-20260627-9C759', 'Sales Order SO-20260627-9C759 untuk pelanggan PT BIG TRIPLE T baru saja dibuat oleh Citra Dewi dan siap untuk diproses.', NULL, 1, '2026-06-27 12:08:59'),
(23, NULL, 'petugas_gudang', 'so_created', 'Sales Order Baru: SO-20260627-9C759', 'Sales Order SO-20260627-9C759 untuk pelanggan PT BIG TRIPLE T baru saja dibuat oleh Citra Dewi dan siap untuk diproses.', NULL, 0, '2026-06-27 12:08:59'),
(24, NULL, 'admin_gudang', 'restock_submitted', 'Permintaan Restock Baru', 'Dani Prasetyo mengajukan restock untuk barang Kertas HVS A4 70gsm sebanyak 10 unit.', 3, 1, '2026-06-27 12:10:39'),
(25, 4, NULL, 'restock_approved', 'Permintaan Restock Disetujui', 'Permintaan restock Anda untuk barang Kertas HVS A4 70gsm sebanyak 10 unit telah disetujui oleh Etmin Datang.', 3, 1, '2026-06-27 12:10:47'),
(26, NULL, 'admin_gudang', 'restock_submitted', 'Permintaan Restock Baru', 'Dani Prasetyo mengajukan restock untuk barang Lakban Coklat 3inch sebanyak 10 unit.', 9, 1, '2026-06-28 08:05:26'),
(27, 4, NULL, 'restock_approved', 'Permintaan Restock Disetujui', 'Permintaan restock Anda untuk barang Lakban Coklat 3inch sebanyak 10 unit telah disetujui oleh Etmin Datang.', 9, 1, '2026-06-28 08:07:26'),
(28, NULL, 'admin_gudang', 'low_stock', 'Alarm Stok Rendah: Kertas HVS A4 70gsm', 'Stok barang Kertas HVS A4 70gsm (KRT-001) saat ini menipis menjadi 38 rim (Batas minimum: 50 rim). Segera ajukan restock.', 1, 1, '2026-06-28 08:14:46'),
(29, NULL, 'divisi_pembelian', 'low_stock', 'Alarm Stok Rendah: Kertas HVS A4 70gsm', 'Stok barang Kertas HVS A4 70gsm (KRT-001) saat ini menipis menjadi 38 rim (Batas minimum: 50 rim). Segera ajukan restock.', 1, 1, '2026-06-28 08:14:46'),
(30, 3, NULL, 'so_completed', 'Sales Order Selesai: SO-20260607-00001', 'Sales Order SO-20260607-00001 untuk pelanggan PT Sinar Grafika telah selesai diproses (picking list selesai).', NULL, 0, '2026-06-28 08:14:46'),
(31, NULL, 'admin_gudang', 'restock_submitted', 'Permintaan Restock Baru', 'Dani Prasetyo mengajukan restock untuk barang Kertas HVS A4 70gsm sebanyak 50 unit.', 10, 0, '2026-06-28 08:23:01'),
(32, 4, NULL, 'restock_approved', 'Permintaan Restock Disetujui', 'Permintaan restock Anda untuk barang Kertas HVS A4 70gsm sebanyak 50 unit telah disetujui oleh Etmin Datang.', 10, 0, '2026-06-28 08:26:47'),
(33, NULL, 'admin_gudang', 'restock_submitted', 'Permintaan Restock Baru', 'Dani Prasetyo mengajukan restock untuk barang Vinyl Sticker A3 sebanyak 5 unit.', 11, 0, '2026-06-28 08:28:06'),
(34, 4, NULL, 'restock_approved', 'Permintaan Restock Disetujui', 'Permintaan restock Anda untuk barang Vinyl Sticker A3 sebanyak 5 unit telah disetujui oleh Etmin Datang.', 11, 0, '2026-06-28 08:28:44'),
(35, NULL, 'petugas_gudang', 'opname_initiated', 'Sesi Stock Opname Dimulai', 'Sesi Stock Opname OPN-20260630-54FD4 baru saja diinisiasi. Petugas lapangan diharap segera melakukan pemeriksaan fisik menggunakan pemindai QR.', 8, 0, '2026-06-29 23:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `racks`
--

CREATE TABLE `racks` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `rack_code` varchar(20) NOT NULL,
  `row_num` int(11) NOT NULL,
  `col_num` int(11) NOT NULL,
  `total_slots` int(11) NOT NULL DEFAULT 8,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `racks`
--

INSERT INTO `racks` (`id`, `zone_id`, `rack_code`, `row_num`, `col_num`, `total_slots`, `created_at`) VALUES
(1, 1, 'ZA-R01', 1, 1, 8, '2026-06-05 13:53:40'),
(2, 1, 'ZA-R02', 1, 2, 8, '2026-06-05 13:53:40'),
(3, 1, 'ZA-R03', 2, 1, 8, '2026-06-05 13:53:40'),
(4, 1, 'ZA-R04', 2, 2, 8, '2026-06-05 13:53:40'),
(5, 2, 'ZB-R01', 1, 1, 8, '2026-06-05 13:53:40'),
(6, 2, 'ZB-R02', 1, 2, 8, '2026-06-05 13:53:40'),
(7, 2, 'ZB-R03', 2, 1, 8, '2026-06-05 13:53:40'),
(8, 3, 'ZC-R01', 1, 1, 8, '2026-06-05 13:53:40'),
(9, 3, 'ZC-R02', 1, 2, 8, '2026-06-05 13:53:40'),
(10, 4, 'ZD-R01', 1, 1, 8, '2026-06-05 13:53:40'),
(11, 4, 'ZD-R02', 1, 2, 8, '2026-06-05 13:53:40'),
(12, 4, 'ZD-R03', 2, 1, 8, '2026-06-05 13:53:40'),
(13, 5, 'ZE-R01', 1, 1, 8, '2026-06-05 13:53:40'),
(14, 5, 'ZE-R02', 1, 2, 8, '2026-06-05 13:53:40');

-- --------------------------------------------------------

--
-- Table structure for table `rack_slots`
--

CREATE TABLE `rack_slots` (
  `id` int(11) NOT NULL,
  `rack_id` int(11) NOT NULL,
  `slot_number` int(11) NOT NULL,
  `status` enum('free','loaded') DEFAULT 'free'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rack_slots`
--

INSERT INTO `rack_slots` (`id`, `rack_id`, `slot_number`, `status`) VALUES
(1, 1, 1, 'free'),
(2, 1, 2, 'loaded'),
(3, 1, 3, 'loaded'),
(4, 1, 4, 'loaded'),
(5, 1, 5, 'free'),
(6, 1, 6, 'loaded'),
(7, 1, 7, 'loaded'),
(8, 1, 8, 'free'),
(9, 2, 1, 'loaded'),
(10, 2, 2, 'free'),
(11, 2, 3, 'free'),
(12, 2, 4, 'loaded'),
(13, 2, 5, 'loaded'),
(14, 2, 6, 'loaded'),
(15, 2, 7, 'loaded'),
(16, 2, 8, 'loaded'),
(17, 3, 1, 'free'),
(18, 3, 2, 'free'),
(19, 3, 3, 'loaded'),
(20, 3, 4, 'loaded'),
(21, 3, 5, 'free'),
(22, 3, 6, 'loaded'),
(23, 3, 7, 'free'),
(24, 3, 8, 'free'),
(25, 4, 1, 'loaded'),
(26, 4, 2, 'loaded'),
(27, 4, 3, 'loaded'),
(28, 4, 4, 'free'),
(29, 4, 5, 'free'),
(30, 4, 6, 'free'),
(31, 4, 7, 'loaded'),
(32, 4, 8, 'loaded'),
(33, 5, 1, 'loaded'),
(34, 5, 2, 'loaded'),
(35, 5, 3, 'loaded'),
(36, 5, 4, 'free'),
(37, 5, 5, 'loaded'),
(38, 5, 6, 'free'),
(39, 5, 7, 'free'),
(40, 5, 8, 'loaded'),
(41, 6, 1, 'free'),
(42, 6, 2, 'loaded'),
(43, 6, 3, 'free'),
(44, 6, 4, 'loaded'),
(45, 6, 5, 'free'),
(46, 6, 6, 'loaded'),
(47, 6, 7, 'free'),
(48, 6, 8, 'loaded'),
(49, 7, 1, 'free'),
(50, 7, 2, 'free'),
(51, 7, 3, 'loaded'),
(52, 7, 4, 'loaded'),
(53, 7, 5, 'free'),
(54, 7, 6, 'loaded'),
(55, 7, 7, 'loaded'),
(56, 7, 8, 'loaded'),
(57, 8, 1, 'loaded'),
(58, 8, 2, 'loaded'),
(59, 8, 3, 'loaded'),
(60, 8, 4, 'loaded'),
(61, 8, 5, 'loaded'),
(62, 8, 6, 'free'),
(63, 8, 7, 'free'),
(64, 8, 8, 'loaded'),
(65, 9, 1, 'loaded'),
(66, 9, 2, 'loaded'),
(67, 9, 3, 'free'),
(68, 9, 4, 'free'),
(69, 9, 5, 'free'),
(70, 9, 6, 'loaded'),
(71, 9, 7, 'loaded'),
(72, 9, 8, 'loaded'),
(73, 10, 1, 'loaded'),
(74, 10, 2, 'loaded'),
(75, 10, 3, 'loaded'),
(76, 10, 4, 'loaded'),
(77, 10, 5, 'loaded'),
(78, 10, 6, 'free'),
(79, 10, 7, 'loaded'),
(80, 10, 8, 'loaded'),
(81, 11, 1, 'free'),
(82, 11, 2, 'free'),
(83, 11, 3, 'free'),
(84, 11, 4, 'loaded'),
(85, 11, 5, 'free'),
(86, 11, 6, 'free'),
(87, 11, 7, 'loaded'),
(88, 11, 8, 'free'),
(89, 12, 1, 'loaded'),
(90, 12, 2, 'loaded'),
(91, 12, 3, 'loaded'),
(92, 12, 4, 'loaded'),
(93, 12, 5, 'loaded'),
(94, 12, 6, 'loaded'),
(95, 12, 7, 'free'),
(96, 12, 8, 'loaded'),
(97, 13, 1, 'free'),
(98, 13, 2, 'free'),
(99, 13, 3, 'loaded'),
(100, 13, 4, 'free'),
(101, 13, 5, 'loaded'),
(102, 13, 6, 'loaded'),
(103, 13, 7, 'free'),
(104, 13, 8, 'loaded'),
(105, 14, 1, 'free'),
(106, 14, 2, 'loaded'),
(107, 14, 3, 'free'),
(108, 14, 4, 'free'),
(109, 14, 5, 'free'),
(110, 14, 6, 'free'),
(111, 14, 7, 'loaded'),
(112, 14, 8, 'free');

-- --------------------------------------------------------

--
-- Table structure for table `restock_requests`
--

CREATE TABLE `restock_requests` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `requested_qty` int(11) NOT NULL,
  `current_stock` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `requested_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `po_number` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `supplier_address` varchar(255) DEFAULT NULL,
  `item_price` int(11) DEFAULT 120000,
  `received_qty` int(11) DEFAULT 0,
  `arrival_status` enum('pending','arrived') DEFAULT 'pending',
  `arrival_date` datetime DEFAULT NULL,
  `expedition` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restock_requests`
--

INSERT INTO `restock_requests` (`id`, `item_id`, `requested_qty`, `current_stock`, `status`, `requested_by`, `approved_by`, `notes`, `created_at`, `updated_at`, `po_number`, `supplier_name`, `supplier_address`, `item_price`, `received_qty`, `arrival_status`, `arrival_date`, `expedition`) VALUES
(3, 1, 10, 53, 'approved', 4, 1, 'Restock kertas HVS A4', '2026-06-27 12:10:00', '2026-06-27 12:10:00', 'PO-RESTOCK-3', 'CV Mitra Kertas Indo', 'Jl. Industri No. 45, Cikupa, Tangerang', 120000, 0, 'arrived', '2026-06-27 22:45:00', 'JNE Cargo'),
(4, 2, 10, 91, 'approved', 4, 1, 'Restock kertas HVS A3', '2026-06-27 12:10:00', '2026-06-27 12:10:00', 'PO-RESTOCK-3', 'CV Mitra Kertas Indo', 'Jl. Industri No. 45, Cikupa, Tangerang', 120000, 0, 'arrived', '2026-06-27 22:45:00', 'JNE Cargo'),
(5, 8, 24, 49, 'approved', 4, 1, 'Restock tinta cyan', '2026-06-26 07:30:00', '2026-06-26 07:30:00', 'PO-RESTOCK-4', 'PT Sumber Alat Tulis', 'Kawasan Industri Jatake, Tangerang', 45000, 0, 'arrived', '2026-06-27 09:20:00', 'Sicepat Cargo'),
(6, 9, 12, 10, 'approved', 4, 1, 'Restock tinta magenta', '2026-06-26 07:30:00', '2026-06-26 07:30:00', 'PO-RESTOCK-4', 'PT Sumber Alat Tulis', 'Kawasan Industri Jatake, Tangerang', 45000, 0, 'arrived', '2026-06-27 09:20:00', 'Sicepat Cargo'),
(7, 10, 5, 63, 'approved', 4, 1, 'Restock tinta yellow', '2026-06-26 07:30:00', '2026-06-26 07:30:00', 'PO-RESTOCK-4', 'PT Sumber Alat Tulis', 'Kawasan Industri Jatake, Tangerang', 45000, 0, 'arrived', '2026-06-27 09:20:00', 'Sicepat Cargo'),
(8, 11, 8, 80, 'completed', 4, 1, 'Restock tinta black', '2026-06-25 03:00:00', '2026-06-28 08:04:00', 'PO-RESTOCK-5', 'UD Tinta Prima', 'Ruko Ink Mas, Jakarta Pusat', 120000, 8, 'arrived', '2026-06-27 10:00:00', 'J&T Cargo'),
(9, 17, 10, 303, 'approved', 4, 1, '', '2026-06-28 08:05:26', '2026-06-28 08:12:08', NULL, NULL, NULL, 120000, 0, 'arrived', '2026-06-28 18:45:00', 'JNE Cargo'),
(10, 1, 50, 38, 'completed', 4, 1, 'Pengadaan otomatis via alarm stok di Pusat Notifikasi', '2026-06-28 08:23:01', '2026-06-28 08:32:05', NULL, NULL, NULL, 120000, 50, 'arrived', '2026-06-28 15:29:00', 'JNE Cargo'),
(11, 6, 5, 296, 'approved', 4, 1, '', '2026-06-28 08:28:06', '2026-06-28 08:28:44', NULL, NULL, NULL, 120000, 0, 'pending', NULL, NULL);

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_key` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`role_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_key`, `display_name`) VALUES
('admin_gudang', 'Admin Gudang'),
('petugas_gudang', 'Petugas Gudang'),
('divisi_penjualan', 'Divisi Penjualan'),
('divisi_pembelian', 'Divisi Pembelian'),
('manajemen', 'Manajemen');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `permission_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role`, `permission_name`) VALUES
(1, 'admin_gudang', 'dashboard.view'),
(12, 'admin_gudang', 'inbound.create'),
(11, 'admin_gudang', 'inbound.view'),
(6, 'admin_gudang', 'items.create'),
(8, 'admin_gudang', 'items.delete'),
(7, 'admin_gudang', 'items.edit'),
(5, 'admin_gudang', 'items.view'),
(3, 'admin_gudang', 'notifications.view'),
(20, 'admin_gudang', 'opname.finalize'),
(18, 'admin_gudang', 'opname.initiate'),
(19, 'admin_gudang', 'opname.scan'),
(17, 'admin_gudang', 'opname.view'),
(14, 'admin_gudang', 'outbound.create'),
(13, 'admin_gudang', 'outbound.view'),
(10, 'admin_gudang', 'racks.create'),
(16, 'admin_gudang', 'relocation.create'),
(15, 'admin_gudang', 'relocation.view'),
(4, 'admin_gudang', 'reports.view'),
(23, 'admin_gudang', 'restock.approve'),
(22, 'admin_gudang', 'restock.create'),
(21, 'admin_gudang', 'restock.view'),
(25, 'admin_gudang', 'sales_orders.create'),
(24, 'admin_gudang', 'sales_orders.view'),
(2, 'admin_gudang', 'stock.view'),
(27, 'admin_gudang', 'users.create'),
(29, 'admin_gudang', 'users.delete'),
(28, 'admin_gudang', 'users.edit'),
(30, 'admin_gudang', 'users.toggle'),
(26, 'admin_gudang', 'users.view'),
(9, 'admin_gudang', 'zones.view'),
(47, 'divisi_pembelian', 'dashboard.view'),
(49, 'divisi_pembelian', 'notifications.view'),
(50, 'divisi_pembelian', 'reports.view'),
(52, 'divisi_pembelian', 'restock.create'),
(51, 'divisi_pembelian', 'restock.view'),
(48, 'divisi_pembelian', 'stock.view'),
(43, 'divisi_penjualan', 'dashboard.view'),
(46, 'divisi_penjualan', 'sales_orders.create'),
(45, 'divisi_penjualan', 'sales_orders.view'),
(44, 'divisi_penjualan', 'stock.view'),
(53, 'manajemen', 'dashboard.view'),
(55, 'manajemen', 'notifications.view'),
(60, 'manajemen', 'opname.finalize'),
(59, 'manajemen', 'opname.initiate'),
(58, 'manajemen', 'opname.view'),
(56, 'manajemen', 'reports.view'),
(61, 'manajemen', 'sales_orders.view'),
(54, 'manajemen', 'stock.view'),
(57, 'manajemen', 'zones.view'),
(31, 'petugas_gudang', 'dashboard.view'),
(36, 'petugas_gudang', 'inbound.create'),
(35, 'petugas_gudang', 'inbound.view'),
(33, 'petugas_gudang', 'notifications.view'),
(42, 'petugas_gudang', 'opname.scan'),
(41, 'petugas_gudang', 'opname.view'),
(38, 'petugas_gudang', 'outbound.create'),
(37, 'petugas_gudang', 'outbound.view'),
(40, 'petugas_gudang', 'relocation.create'),
(39, 'petugas_gudang', 'relocation.view'),
(32, 'petugas_gudang', 'stock.view'),
(34, 'petugas_gudang', 'zones.view'),
(62, 'admin_gudang', 'zones.create'),
(63, 'admin_gudang', 'zones.delete'),
(64, 'admin_gudang', 'racks.delete'),
(65, 'admin_gudang', 'roles.manage');

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` int(11) NOT NULL,
  `so_number` varchar(50) NOT NULL,
  `customer` varchar(100) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `so_number`, `customer`, `status`, `created_by`, `created_at`) VALUES
(1, 'SO-20260607-00001', 'PT Sinar Grafika', 'completed', 3, '2026-06-07 03:14:02'),
(2, 'SO-20260607-00002', 'CV Utama Printing', 'pending', 3, '2026-06-07 03:14:02'),
(3, 'SO-20260607-29054', 'PT Sinar Grafika', 'pending', 3, '2026-06-07 03:26:13'),
(4, 'SO-20260627-9C759', 'PT BIG TRIPLE T', 'pending', 3, '2026-06-27 12:08:59');

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` int(11) NOT NULL,
  `sales_order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `sales_order_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 15),
(2, 1, 8, 2),
(3, 2, 3, 5),
(4, 2, 16, 4),
(5, 3, 9, 20),
(6, 3, 4, 15),
(7, 4, 4, 26),
(8, 4, 3, 20);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `rack_slot_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `received_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `item_id`, `rack_slot_id`, `quantity`, `updated_at`, `received_at`) VALUES
(1, 6, 2, 115, '2026-06-06 19:55:46', '2026-06-12 11:35:00'),
(2, 1, 3, 38, '2026-06-28 08:14:46', '2026-06-12 11:35:00'),
(3, 5, 4, 52, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(4, 5, 6, 20, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(5, 2, 9, 91, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(6, 7, 12, 41, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(7, 3, 13, 73, '2026-06-06 04:59:40', '2026-06-12 11:35:00'),
(8, 6, 14, 90, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(9, 2, 15, 97, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(10, 5, 16, 17, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(11, 4, 19, 18, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(12, 2, 20, 98, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(13, 4, 22, 51, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(14, 7, 25, 19, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(15, 4, 26, 29, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(16, 4, 27, 42, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(17, 6, 31, 91, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(18, 3, 32, 18, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(19, 8, 37, 47, '2026-06-28 08:14:46', '2026-06-12 11:35:00'),
(20, 10, 40, 63, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(21, 11, 42, 80, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(22, 13, 44, 91, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(23, 8, 46, 96, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(24, 8, 48, 87, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(25, 8, 51, 83, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(26, 12, 52, 42, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(27, 10, 54, 33, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(28, 11, 55, 46, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(29, 8, 56, 76, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(30, 15, 57, 17, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(31, 14, 58, 84, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(32, 14, 59, 42, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(33, 15, 60, 52, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(34, 14, 61, 24, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(35, 14, 64, 88, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(36, 14, 65, 17, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(37, 15, 66, 26, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(38, 14, 70, 84, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(39, 14, 71, 11, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(40, 14, 72, 80, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(41, 17, 73, 12, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(42, 17, 74, 53, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(43, 19, 75, 31, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(44, 19, 76, 68, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(45, 16, 77, 85, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(46, 16, 79, 67, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(47, 18, 80, 63, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(48, 17, 84, 26, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(49, 18, 87, 29, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(50, 17, 89, 60, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(51, 16, 90, 74, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(52, 19, 91, 40, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(53, 19, 92, 81, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(54, 17, 93, 58, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(55, 16, 94, 76, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(56, 17, 96, 94, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(57, 21, 99, 68, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(58, 20, 101, 77, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(59, 20, 102, 17, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(60, 21, 104, 70, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(61, 21, 106, 50, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(62, 20, 111, 66, '2026-06-05 13:53:40', '2026-06-12 11:35:00'),
(64, 9, 33, 10, '2026-06-05 18:44:12', '2026-06-12 11:35:00'),
(67, 9, 34, 50, '2026-06-07 03:56:20', '2026-06-12 11:35:00'),
(68, 11, 35, 8, '2026-06-28 07:59:36', '2026-06-28 07:59:36'),
(69, 1, 7, 50, '2026-06-28 08:32:05', '2026-06-28 08:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `stock_opnames`
--

CREATE TABLE `stock_opnames` (
  `id` int(11) NOT NULL,
  `opname_no` varchar(50) NOT NULL,
  `status` enum('initiated','completed') DEFAULT 'initiated',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_opnames`
--

INSERT INTO `stock_opnames` (`id`, `opname_no`, `status`, `created_by`, `created_at`, `completed_at`) VALUES
(1, 'OPN-20260606-8B2E1', 'completed', 1, '2026-06-06 19:55:40', '2026-06-06 19:55:40'),
(2, 'OPN-20260606-95CBB', 'completed', 1, '2026-06-06 19:55:46', '2026-06-06 19:55:46'),
(3, 'OPN-20260607-180F3', 'completed', 1, '2026-06-07 04:10:47', '2026-06-07 04:11:16');

-- --------------------------------------------------------

--
-- Table structure for table `stock_opname_details`
--

CREATE TABLE `stock_opname_details` (
  `id` int(11) NOT NULL,
  `stock_opname_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `rack_slot_id` int(11) NOT NULL,
  `system_quantity` int(11) NOT NULL DEFAULT 0,
  `physical_quantity` int(11) DEFAULT NULL,
  `discrepancy` int(11) DEFAULT NULL,
  `status` enum('pending','verified') DEFAULT 'pending',
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_opname_details`
--

INSERT INTO `stock_opname_details` (`id`, `stock_opname_id`, `item_id`, `rack_slot_id`, `system_quantity`, `physical_quantity`, `discrepancy`, `status`, `verified_at`) VALUES
(1, 1, 6, 2, 95, 105, 10, 'verified', '2026-06-06 19:55:40'),
(2, 1, 1, 3, 53, NULL, NULL, 'pending', NULL),
(3, 1, 5, 4, 52, NULL, NULL, 'pending', NULL),
(4, 1, 5, 6, 20, NULL, NULL, 'pending', NULL),
(5, 1, 2, 9, 91, NULL, NULL, 'pending', NULL),
(8, 2, 6, 2, 105, 115, 10, 'verified', '2026-06-06 19:55:46'),
(9, 2, 1, 3, 53, NULL, NULL, 'pending', NULL),
(10, 2, 5, 4, 52, NULL, NULL, 'pending', NULL),
(11, 2, 5, 6, 20, NULL, NULL, 'pending', NULL),
(12, 2, 2, 9, 91, NULL, NULL, 'pending', NULL),
(13, 3, 15, 57, 17, NULL, NULL, 'pending', NULL),
(14, 3, 14, 58, 84, NULL, NULL, 'pending', NULL),
(15, 3, 14, 59, 42, NULL, NULL, 'pending', NULL),
(16, 3, 15, 60, 52, NULL, NULL, 'pending', NULL),
(17, 3, 14, 61, 24, NULL, NULL, 'pending', NULL),
(18, 3, 14, 64, 88, NULL, NULL, 'pending', NULL),
(19, 3, 14, 65, 17, NULL, NULL, 'pending', NULL),
(20, 3, 15, 66, 26, NULL, NULL, 'pending', NULL),
(21, 3, 14, 70, 84, NULL, NULL, 'pending', NULL),
(22, 3, 14, 71, 11, NULL, NULL, 'pending', NULL),
(23, 3, 14, 72, 80, NULL, NULL, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `po_number` varchar(50) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `type` enum('inbound','outbound') NOT NULL,
  `condition` enum('baik','rusak','sebagian_rusak') DEFAULT 'baik',
  `quantity` int(11) NOT NULL,
  `rack_slot_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `reference_no`, `po_number`, `item_id`, `type`, `condition`, `quantity`, `rack_slot_id`, `user_id`, `notes`, `created_at`) VALUES
(1, 'TRX-00001', NULL, 11, 'outbound', 'baik', 6, 35, 2, NULL, '2026-05-22 13:53:40'),
(2, 'TRX-00002', NULL, 10, 'outbound', 'baik', 41, 74, 1, NULL, '2026-05-31 13:53:40'),
(3, 'TRX-00003', NULL, 4, 'inbound', 'baik', 14, 14, 2, NULL, '2026-06-05 13:53:40'),
(4, 'TRX-00004', NULL, 3, 'outbound', 'baik', 8, 96, 1, NULL, '2026-05-08 13:53:40'),
(5, 'TRX-00005', NULL, 12, 'outbound', 'baik', 28, 107, 2, NULL, '2026-05-22 13:53:40'),
(6, 'TRX-00006', NULL, 7, 'inbound', 'baik', 45, 28, 2, NULL, '2026-05-21 13:53:40'),
(7, 'TRX-00007', NULL, 20, 'inbound', 'baik', 21, 103, 1, NULL, '2026-05-20 13:53:40'),
(8, 'TRX-00008', NULL, 3, 'inbound', 'baik', 8, 96, 1, NULL, '2026-05-08 13:53:40'),
(9, 'TRX-00009', NULL, 9, 'outbound', 'baik', 33, 35, 2, NULL, '2026-05-28 13:53:40'),
(10, 'TRX-00010', NULL, 10, 'inbound', 'baik', 30, 111, 1, NULL, '2026-05-23 13:53:40'),
(11, 'TRX-00011', NULL, 16, 'outbound', 'baik', 9, 53, 2, NULL, '2026-05-22 13:53:40'),
(12, 'TRX-00012', NULL, 19, 'inbound', 'baik', 40, 98, 1, NULL, '2026-05-08 13:53:40'),
(13, 'TRX-00013', NULL, 18, 'outbound', 'baik', 36, 29, 1, NULL, '2026-05-23 13:53:40'),
(14, 'TRX-00014', NULL, 5, 'outbound', 'baik', 10, 78, 1, NULL, '2026-05-25 13:53:40'),
(15, 'TRX-00015', NULL, 8, 'outbound', 'baik', 17, 100, 2, NULL, '2026-05-23 13:53:40'),
(16, 'TRX-00016', NULL, 18, 'outbound', 'baik', 20, 51, 1, NULL, '2026-05-15 13:53:40'),
(17, 'TRX-00017', NULL, 2, 'outbound', 'baik', 33, 105, 2, NULL, '2026-05-14 13:53:40'),
(18, 'TRX-00018', NULL, 18, 'inbound', 'baik', 25, 82, 2, NULL, '2026-06-03 13:53:40'),
(19, 'TRX-00019', NULL, 7, 'inbound', 'baik', 40, 89, 2, NULL, '2026-05-10 13:53:40'),
(20, 'TRX-00020', NULL, 14, 'outbound', 'baik', 29, 7, 2, NULL, '2026-05-12 13:53:40'),
(21, 'TRX-00021', NULL, 10, 'outbound', 'baik', 34, 7, 1, NULL, '2026-05-23 13:53:40'),
(22, 'TRX-00022', NULL, 18, 'outbound', 'baik', 29, 58, 2, NULL, '2026-05-13 13:53:40'),
(23, 'TRX-00023', NULL, 20, 'outbound', 'baik', 36, 95, 2, NULL, '2026-05-31 13:53:40'),
(24, 'TRX-00024', NULL, 15, 'inbound', 'baik', 2, 99, 2, NULL, '2026-06-01 13:53:40'),
(25, 'TRX-00025', NULL, 1, 'inbound', 'baik', 28, 12, 2, NULL, '2026-05-17 13:53:40'),
(26, 'TRX-00026', NULL, 20, 'inbound', 'baik', 12, 103, 1, NULL, '2026-05-14 13:53:40'),
(27, 'TRX-00027', NULL, 7, 'outbound', 'baik', 2, 25, 2, NULL, '2026-05-14 13:53:40'),
(28, 'TRX-00028', NULL, 5, 'inbound', 'baik', 28, 99, 1, NULL, '2026-05-30 13:53:40'),
(29, 'TRX-00029', NULL, 10, 'outbound', 'baik', 5, 12, 2, NULL, '2026-05-31 13:53:40'),
(30, 'TRX-00030', NULL, 1, 'inbound', 'baik', 47, 109, 1, NULL, '2026-05-26 13:53:40'),
(31, 'TRX-00031', NULL, 20, 'inbound', 'baik', 4, 66, 2, NULL, '2026-05-11 13:53:40'),
(32, 'TRX-00032', NULL, 18, 'outbound', 'baik', 47, 99, 1, NULL, '2026-05-10 13:53:40'),
(33, 'TRX-00033', NULL, 18, 'inbound', 'baik', 10, 10, 1, NULL, '2026-05-27 13:53:40'),
(34, 'TRX-00034', NULL, 2, 'outbound', 'baik', 15, 50, 1, NULL, '2026-05-16 13:53:40'),
(35, 'TRX-00035', NULL, 10, 'inbound', 'baik', 14, 81, 2, NULL, '2026-05-22 13:53:40'),
(36, 'TRX-00036', NULL, 18, 'inbound', 'baik', 44, 29, 1, NULL, '2026-05-30 13:53:40'),
(37, 'TRX-00037', NULL, 7, 'outbound', 'baik', 48, 38, 1, NULL, '2026-05-26 13:53:40'),
(38, 'TRX-00038', NULL, 9, 'inbound', 'baik', 2, 42, 1, NULL, '2026-05-30 13:53:40'),
(39, 'TRX-00039', NULL, 17, 'outbound', 'baik', 25, 73, 1, NULL, '2026-05-12 13:53:40'),
(40, 'TRX-00040', NULL, 7, 'inbound', 'baik', 23, 109, 1, NULL, '2026-05-17 13:53:40'),
(41, 'TRX-00041', NULL, 5, 'inbound', 'baik', 10, 75, 1, NULL, '2026-06-05 13:53:40'),
(42, 'TRX-00042', NULL, 15, 'inbound', 'baik', 46, 94, 2, NULL, '2026-05-28 13:53:40'),
(43, 'TRX-00043', NULL, 8, 'inbound', 'baik', 4, 105, 1, NULL, '2026-05-18 13:53:40'),
(44, 'TRX-00044', NULL, 7, 'inbound', 'baik', 36, 19, 1, NULL, '2026-05-07 13:53:40'),
(45, 'TRX-00045', NULL, 6, 'inbound', 'baik', 28, 84, 1, NULL, '2026-05-11 13:53:40'),
(46, 'TRX-00046', NULL, 6, 'inbound', 'baik', 48, 83, 1, NULL, '2026-05-30 13:53:40'),
(47, 'TRX-00047', NULL, 13, 'outbound', 'baik', 41, 30, 1, NULL, '2026-05-21 13:53:40'),
(48, 'TRX-00048', NULL, 1, 'outbound', 'baik', 35, 93, 2, NULL, '2026-05-27 13:53:40'),
(49, 'TRX-00049', NULL, 21, 'inbound', 'baik', 3, 21, 1, NULL, '2026-05-21 13:53:40'),
(50, 'TRX-00050', NULL, 10, 'outbound', 'baik', 22, 12, 2, NULL, '2026-05-16 13:53:40'),
(51, 'TRX-00051', NULL, 11, 'outbound', 'baik', 19, 20, 2, NULL, '2026-05-15 13:53:40'),
(52, 'TRX-00052', NULL, 3, 'inbound', 'baik', 45, 36, 2, NULL, '2026-05-07 13:53:40'),
(53, 'TRX-00053', NULL, 21, 'outbound', 'baik', 24, 48, 2, NULL, '2026-05-09 13:53:40'),
(54, 'TRX-00054', NULL, 18, 'outbound', 'baik', 18, 103, 2, NULL, '2026-05-16 13:53:40'),
(55, 'TRX-00055', NULL, 16, 'outbound', 'baik', 23, 81, 2, NULL, '2026-05-27 13:53:40'),
(56, 'TRX-00056', NULL, 4, 'outbound', 'baik', 30, 28, 1, NULL, '2026-05-09 13:53:40'),
(57, 'TRX-00057', NULL, 14, 'inbound', 'baik', 38, 70, 2, NULL, '2026-05-25 13:53:40'),
(58, 'TRX-00058', NULL, 11, 'inbound', 'baik', 45, 21, 2, NULL, '2026-05-07 13:53:40'),
(59, 'TRX-00059', NULL, 6, 'outbound', 'baik', 13, 56, 1, NULL, '2026-06-05 13:53:40'),
(60, 'TRX-00060', NULL, 9, 'outbound', 'baik', 31, 19, 1, NULL, '2026-05-31 13:53:40'),
(61, 'TRX-00061', NULL, 20, 'outbound', 'baik', 49, 29, 1, NULL, '2026-05-31 13:53:40'),
(62, 'TRX-00062', NULL, 19, 'outbound', 'baik', 49, 11, 1, NULL, '2026-05-27 13:53:40'),
(63, 'TRX-00063', NULL, 7, 'inbound', 'baik', 7, 71, 2, NULL, '2026-06-05 13:53:40'),
(64, 'TRX-00064', NULL, 15, 'inbound', 'baik', 35, 27, 2, NULL, '2026-05-31 13:53:40'),
(65, 'TRX-00065', NULL, 17, 'inbound', 'baik', 39, 33, 2, NULL, '2026-05-23 13:53:40'),
(66, 'TRX-00066', NULL, 13, 'inbound', 'baik', 3, 101, 1, NULL, '2026-06-05 13:53:40'),
(67, 'TRX-00067', NULL, 18, 'inbound', 'baik', 42, 28, 1, NULL, '2026-05-07 13:53:40'),
(68, 'TRX-00068', NULL, 7, 'inbound', 'baik', 40, 23, 2, NULL, '2026-05-16 13:53:40'),
(69, 'TRX-00069', NULL, 9, 'outbound', 'baik', 38, 75, 2, NULL, '2026-05-27 13:53:40'),
(70, 'TRX-00070', NULL, 20, 'outbound', 'baik', 28, 53, 1, NULL, '2026-05-08 13:53:40'),
(71, 'TRX-00071', NULL, 14, 'outbound', 'baik', 12, 101, 1, NULL, '2026-05-25 13:53:40'),
(72, 'TRX-00072', NULL, 7, 'outbound', 'baik', 1, 76, 2, NULL, '2026-05-11 13:53:40'),
(73, 'TRX-00073', NULL, 8, 'outbound', 'baik', 9, 35, 2, NULL, '2026-05-17 13:53:40'),
(74, 'TRX-00074', NULL, 7, 'inbound', 'baik', 7, 59, 1, NULL, '2026-05-22 13:53:40'),
(75, 'TRX-00075', NULL, 5, 'inbound', 'baik', 34, 42, 2, NULL, '2026-05-09 13:53:40'),
(76, 'TRX-00076', NULL, 3, 'inbound', 'baik', 20, 92, 1, NULL, '2026-05-16 13:53:40'),
(77, 'TRX-00077', NULL, 21, 'outbound', 'baik', 14, 25, 2, NULL, '2026-05-12 13:53:40'),
(78, 'TRX-00078', NULL, 13, 'outbound', 'baik', 25, 50, 2, NULL, '2026-05-25 13:53:40'),
(79, 'TRX-00079', NULL, 4, 'inbound', 'baik', 13, 44, 2, NULL, '2026-05-13 13:53:40'),
(80, 'TRX-00080', NULL, 16, 'outbound', 'baik', 14, 8, 2, NULL, '2026-06-04 13:53:40'),
(81, 'TRX-00081', NULL, 5, 'inbound', 'baik', 43, 72, 1, NULL, '2026-05-18 13:53:40'),
(82, 'TRX-00082', NULL, 13, 'inbound', 'baik', 19, 70, 2, NULL, '2026-06-02 13:53:40'),
(83, 'TRX-00083', NULL, 19, 'outbound', 'baik', 41, 11, 1, NULL, '2026-05-19 13:53:40'),
(84, 'TRX-00084', NULL, 16, 'inbound', 'baik', 20, 23, 1, NULL, '2026-05-21 13:53:40'),
(85, 'TRX-00085', NULL, 16, 'inbound', 'baik', 18, 39, 2, NULL, '2026-05-26 13:53:40'),
(86, 'TRX-00086', NULL, 10, 'inbound', 'baik', 38, 7, 1, NULL, '2026-05-13 13:53:40'),
(87, 'TRX-00087', NULL, 19, 'inbound', 'baik', 47, 64, 2, NULL, '2026-05-27 13:53:40'),
(88, 'TRX-00088', NULL, 15, 'inbound', 'baik', 32, 43, 1, NULL, '2026-05-10 13:53:40'),
(89, 'TRX-00089', NULL, 20, 'inbound', 'baik', 41, 3, 2, NULL, '2026-05-09 13:53:40'),
(90, 'TRX-00090', NULL, 7, 'outbound', 'baik', 25, 98, 2, NULL, '2026-05-31 13:53:40'),
(91, 'TRX-00091', NULL, 18, 'inbound', 'baik', 18, 87, 2, NULL, '2026-05-13 13:53:40'),
(92, 'TRX-00092', NULL, 17, 'inbound', 'baik', 47, 56, 2, NULL, '2026-05-24 13:53:40'),
(93, 'TRX-00093', NULL, 8, 'outbound', 'baik', 39, 81, 1, NULL, '2026-06-03 13:53:40'),
(94, 'TRX-00094', NULL, 11, 'outbound', 'baik', 2, 15, 2, NULL, '2026-05-08 13:53:40'),
(95, 'TRX-00095', NULL, 2, 'inbound', 'baik', 46, 60, 1, NULL, '2026-06-04 13:53:40'),
(96, 'TRX-00096', NULL, 15, 'inbound', 'baik', 33, 88, 1, NULL, '2026-05-10 13:53:40'),
(97, 'TRX-00097', NULL, 17, 'inbound', 'baik', 28, 48, 1, NULL, '2026-05-22 13:53:40'),
(98, 'TRX-00098', NULL, 7, 'inbound', 'baik', 5, 23, 2, NULL, '2026-06-04 13:53:40'),
(99, 'TRX-00099', NULL, 18, 'outbound', 'baik', 18, 26, 1, NULL, '2026-05-08 13:53:40'),
(100, 'TRX-00100', NULL, 11, 'inbound', 'baik', 20, 50, 1, NULL, '2026-05-14 13:53:40'),
(101, 'TRX-00101', NULL, 14, 'inbound', 'baik', 41, 42, 1, NULL, '2026-06-02 13:53:40'),
(102, 'TRX-00102', NULL, 5, 'outbound', 'baik', 49, 89, 2, NULL, '2026-06-04 13:53:40'),
(103, 'TRX-00103', NULL, 18, 'outbound', 'baik', 4, 99, 1, NULL, '2026-05-19 13:53:40'),
(104, 'TRX-00104', NULL, 7, 'inbound', 'baik', 38, 72, 2, NULL, '2026-05-14 13:53:40'),
(105, 'TRX-00105', NULL, 4, 'inbound', 'baik', 27, 53, 1, NULL, '2026-05-27 13:53:40'),
(106, 'TRX-00106', NULL, 14, 'inbound', 'baik', 18, 51, 2, NULL, '2026-05-15 13:53:40'),
(107, 'TRX-00107', NULL, 18, 'inbound', 'baik', 36, 37, 2, NULL, '2026-06-02 13:53:40'),
(108, 'TRX-00108', NULL, 6, 'inbound', 'baik', 31, 91, 2, NULL, '2026-05-23 13:53:40'),
(109, 'TRX-00109', NULL, 10, 'outbound', 'baik', 43, 30, 1, NULL, '2026-05-24 13:53:40'),
(110, 'TRX-00110', NULL, 13, 'inbound', 'baik', 40, 42, 1, NULL, '2026-05-07 13:53:40'),
(111, 'TRX-00111', NULL, 20, 'inbound', 'baik', 26, 93, 2, NULL, '2026-05-25 13:53:40'),
(112, 'TRX-00112', NULL, 14, 'outbound', 'baik', 1, 20, 1, NULL, '2026-05-26 13:53:40'),
(113, 'TRX-00113', NULL, 15, 'inbound', 'baik', 6, 11, 2, NULL, '2026-06-02 13:53:40'),
(114, 'TRX-00114', NULL, 17, 'outbound', 'baik', 14, 26, 1, NULL, '2026-05-07 13:53:40'),
(115, 'TRX-00115', NULL, 14, 'outbound', 'baik', 6, 102, 1, NULL, '2026-05-11 13:53:40'),
(116, 'TRX-00116', NULL, 21, 'inbound', 'baik', 50, 13, 1, NULL, '2026-05-07 13:53:40'),
(117, 'TRX-00117', NULL, 14, 'inbound', 'baik', 23, 46, 2, NULL, '2026-05-11 13:53:40'),
(118, 'TRX-00118', NULL, 16, 'inbound', 'baik', 14, 12, 2, NULL, '2026-05-24 13:53:40'),
(119, 'TRX-00119', NULL, 21, 'outbound', 'baik', 16, 15, 1, NULL, '2026-05-26 13:53:40'),
(120, 'TRX-00120', NULL, 6, 'inbound', 'baik', 46, 88, 2, NULL, '2026-05-24 13:53:40'),
(121, 'IN-20260605-7C76D', 'PO-67676767', 9, 'inbound', 'baik', 10, 33, 1, '', '2026-06-05 18:44:12'),
(122, 'OUT-20260606-7E0D4', NULL, 3, 'outbound', 'baik', 5, 13, 1, '', '2026-06-06 04:59:40'),
(124, 'ADJ-20260606-8EE24', NULL, 6, 'inbound', 'baik', 10, 2, 1, '[STOCK OPNAME ADJ] Penyesuaian dari Opname OPN-20260606-8B2E1. Sistem: 95, Fisik: 105.', '2026-06-06 19:55:40'),
(125, 'ADJ-20260606-97DFE', NULL, 6, 'inbound', 'baik', 10, 2, 1, '[STOCK OPNAME ADJ] Penyesuaian dari Opname OPN-20260606-95CBB. Sistem: 105, Fisik: 115.', '2026-06-06 19:55:46'),
(126, 'IN-20260607-005E8', 'PO-RESTOCK-2', 9, 'inbound', 'baik', 50, 34, 1, '', '2026-06-07 03:56:20'),
(127, 'IN-20260627-6A172', NULL, 4, 'inbound', 'rusak', 0, NULL, 1, '[KETIDAKSESUAIAN: Jumlah Fisik Tidak Sesuai PO] testing', '2026-06-27 11:47:24'),
(128, 'IN-20260627-9CFAC', NULL, 3, 'inbound', 'rusak', 0, NULL, 1, '[KETIDAKSESUAIAN: Jumlah Fisik Tidak Sesuai PO] adadw', '2026-06-27 12:00:56'),
(129, 'IN-20260628-AECB1', 'PO-RESTOCK-8', 11, 'inbound', 'baik', 8, 35, 1, '', '2026-06-28 07:59:36'),
(130, 'OUT-20260628-336AF-1', 'SO-20260607-00001', 1, 'outbound', 'baik', 15, 3, 1, 'testing', '2026-06-28 08:14:46'),
(131, 'OUT-20260628-336AF-2', 'SO-20260607-00001', 8, 'outbound', 'baik', 2, 37, 1, 'testing', '2026-06-28 08:14:46'),
(132, 'IN-20260628-16DFB', 'PO-RESTOCK-10', 1, 'inbound', 'baik', 50, 7, 1, '', '2026-06-28 08:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin_gudang','petugas_gudang','divisi_penjualan','divisi_pembelian','manajemen') NOT NULL,
  `avatar` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `avatar`, `is_active`, `created_at`) VALUES
(1, 'Etmin Datang', 'admin', '$2y$10$hIal8RJvqFEjeptzob0LZ.IEaMYBvpq7sTf/U4SadGIFYqMsQqnxq', 'admin_gudang', 'ED', 1, '2026-06-05 13:53:40'),
(2, 'Budi Santoso', 'budi', '$2y$10$0XggsfDVYcehOqmkQbE./uMLoGQytVbpSeW7lfwMpEq2VtUbQHWz.', 'petugas_gudang', 'BS', 1, '2026-06-05 13:53:40'),
(3, 'Citra Dewi', 'citra', '$2y$10$sHfe3c7UiIX5UiX6dTGRyOxTyQ6SvFuO5p0ATzAjRkwarGRFxk8qS', 'divisi_penjualan', 'CD', 1, '2026-06-05 13:53:40'),
(4, 'Dani Prasetyo', 'dani', '$2y$10$8cRHhSI2BWmhhxiIkAwG4OMdCVPF.JOnSQPdyiNJ1BaNSuij8D9ya', 'divisi_pembelian', 'DP', 1, '2026-06-05 13:53:40'),
(5, 'Eko Manajer', 'eko', '$2y$10$wyKVkmisfhCpIOgPCgYpoOf2U6TGqOVjzNmY2cNmcZXSP3F/eN0ze', 'manajemen', 'EM', 1, '2026-06-05 13:53:40'),
(6, 'Fikri Anwar', 'fikri', '$2y$10$1S3Fvsc.z4LaecFJe./ksOHc75TsWClGE22QsT8r0yQuVEDBhamOe', 'admin_gudang', 'FA', 1, '2026-06-05 13:53:40');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_stock_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_stock_summary` (
`item_id` int(11)
,`sku` varchar(50)
,`item_name` varchar(150)
,`category_name` varchar(100)
,`category_color` varchar(7)
,`unit` varchar(20)
,`min_stock` int(11)
,`qr_code` varchar(100)
,`total_stock` decimal(32,0)
,`stock_status` varchar(6)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_zone_capacity`
-- (See below for the actual view)
--
CREATE TABLE `v_zone_capacity` (
`zone_id` int(11)
,`zone_name` varchar(100)
,`code` varchar(10)
,`description` varchar(255)
,`total_slots` bigint(21)
,`loaded_slots` decimal(22,0)
,`free_slots` decimal(22,0)
,`usage_pct` decimal(27,1)
);

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `total_racks` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`, `code`, `description`, `total_racks`, `created_at`) VALUES
(1, 'Zona A - Kertas', 'ZA', 'Semua jenis kertas cetak', 4, '2026-06-05 13:53:40'),
(2, 'Zona B - Tinta', 'ZB', 'Penyimpanan tinta dan toner', 3, '2026-06-05 13:53:40'),
(3, 'Zona C - Film & Plate', 'ZC', 'Penyimpanan film negatif dan plate', 2, '2026-06-05 13:53:40'),
(4, 'Zona D - Consumables', 'ZD', 'Bahan habis pakai dan packaging', 3, '2026-06-05 13:53:40'),
(5, 'Zona E - Spare Part', 'ZE', 'Suku cadang mesin cetak', 2, '2026-06-05 13:53:40');

-- --------------------------------------------------------

--
-- Structure for view `v_stock_summary`
--
DROP TABLE IF EXISTS `v_stock_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_stock_summary`  AS SELECT `i`.`id` AS `item_id`, `i`.`sku` AS `sku`, `i`.`name` AS `item_name`, `c`.`name` AS `category_name`, `c`.`color` AS `category_color`, `i`.`unit` AS `unit`, `i`.`min_stock` AS `min_stock`, `i`.`qr_code` AS `qr_code`, coalesce(sum(`s`.`quantity`),0) AS `total_stock`, CASE WHEN coalesce(sum(`s`.`quantity`),0) = 0 THEN 'empty' WHEN coalesce(sum(`s`.`quantity`),0) <= `i`.`min_stock` THEN 'low' ELSE 'normal' END AS `stock_status` FROM ((`items` `i` join `categories` `c` on(`i`.`category_id` = `c`.`id`)) left join `stock` `s` on(`i`.`id` = `s`.`item_id`)) GROUP BY `i`.`id`, `i`.`sku`, `i`.`name`, `c`.`name`, `c`.`color`, `i`.`unit`, `i`.`min_stock`, `i`.`qr_code` ;

-- --------------------------------------------------------

--
-- Structure for view `v_zone_capacity`
--
DROP TABLE IF EXISTS `v_zone_capacity`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_zone_capacity`  AS SELECT `z`.`id` AS `zone_id`, `z`.`name` AS `zone_name`, `z`.`code` AS `code`, `z`.`description` AS `description`, count(`rs`.`id`) AS `total_slots`, sum(case when `rs`.`status` = 'loaded' then 1 else 0 end) AS `loaded_slots`, sum(case when `rs`.`status` = 'free' then 1 else 0 end) AS `free_slots`, round(sum(case when `rs`.`status` = 'loaded' then 1 else 0 end) / count(`rs`.`id`) * 100,1) AS `usage_pct` FROM ((`zones` `z` join `racks` `r` on(`r`.`zone_id` = `z`.`id`)) join `rack_slots` `rs` on(`rs`.`rack_id` = `r`.`id`)) GROUP BY `z`.`id`, `z`.`name`, `z`.`code`, `z`.`description` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `qr_code` (`qr_code`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `racks`
--
ALTER TABLE `racks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rack_code` (`rack_code`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `rack_slots`
--
ALTER TABLE `rack_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rack_slot` (`rack_id`,`slot_number`);

--
-- Indexes for table `restock_requests`
--
ALTER TABLE `restock_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_perm` (`role`,`permission_name`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `so_number` (`so_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_order_id` (`sales_order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_stock` (`item_id`,`rack_slot_id`),
  ADD KEY `rack_slot_id` (`rack_slot_id`);

--
-- Indexes for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `opname_no` (`opname_no`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `stock_opname_details`
--
ALTER TABLE `stock_opname_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_opname_detail` (`stock_opname_id`,`item_id`,`rack_slot_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `rack_slot_id` (`rack_slot_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `rack_slot_id` (`rack_slot_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `racks`
--
ALTER TABLE `racks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rack_slots`
--
ALTER TABLE `rack_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `restock_requests`
--
ALTER TABLE `restock_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock_opname_details`
--
ALTER TABLE `stock_opname_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `racks`
--
ALTER TABLE `racks`
  ADD CONSTRAINT `racks_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`);

--
-- Constraints for table `rack_slots`
--
ALTER TABLE `rack_slots`
  ADD CONSTRAINT `rack_slots_ibfk_1` FOREIGN KEY (`rack_id`) REFERENCES `racks` (`id`);

--
-- Constraints for table `restock_requests`
--
ALTER TABLE `restock_requests`
  ADD CONSTRAINT `restock_requests_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `restock_requests_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `restock_requests_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`rack_slot_id`) REFERENCES `rack_slots` (`id`);

--
-- Constraints for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD CONSTRAINT `stock_opnames_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_opname_details`
--
ALTER TABLE `stock_opname_details`
  ADD CONSTRAINT `stock_opname_details_ibfk_1` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_opname_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `stock_opname_details_ibfk_3` FOREIGN KEY (`rack_slot_id`) REFERENCES `rack_slots` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`rack_slot_id`) REFERENCES `rack_slots` (`id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
