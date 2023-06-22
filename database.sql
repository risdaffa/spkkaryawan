-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2023 at 10:58 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_karyawan`
--

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(10) NOT NULL,
  `nama_karyawan` varchar(20) NOT NULL,
  `tanggal_tes` date NOT NULL,
  `tanggal_input` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nama_karyawan`, `tanggal_tes`, `tanggal_input`) VALUES
(1, 'CalonKaryawan1', '2022-12-08', '2023-06-12'),
(2, 'CalonKaryawan2', '2022-12-08', '2023-06-12'),
(3, 'CalonKaryawan3', '2022-12-08', '2023-06-12'),
(4, 'CalonKaryawan4', '2022-12-08', '2023-06-12'),
(5, 'CalonKaryawan5', '2022-12-08', '2023-06-12'),
(6, 'CalonKaryawan6', '2022-12-09', '2023-06-13'),
(7, 'CalonKaryawan7', '2022-12-09', '2023-06-13'),
(8, 'CalonKaryawan8', '2022-12-09', '2023-06-13'),
(9, 'CalonKaryawan9', '2022-12-09', '2023-06-13'),
(10, 'CalonKaryawan10', '2022-12-09', '2023-06-13');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `type` enum('benefit','cost') NOT NULL,
  `bobot` float NOT NULL,
  `ada_pilihan` tinyint(1) DEFAULT NULL,
  `urutan_order` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `nama`, `type`, `bobot`, `ada_pilihan`, `urutan_order`) VALUES
(101, 'C1 - Manner Of Speaking', 'benefit', 0.075, 0, 0),
(102, 'C2 - Self Confidence', 'benefit', 0.025, 0, 1),
(103, 'C3 - Ability to Present Ideas', 'benefit', 0.175, 0, 2),
(104, 'C4 - Communication Skill', 'benefit', 0.125, 0, 3),
(105, 'C5 - Desired Salary', 'cost', 0.2, 0, 4),
(106, 'C6 - Age', 'benefit', 0.1, 0, 5),
(107, 'C7 - Years of Experience', 'benefit', 0.3, 0, 6);

-- --------------------------------------------------------

--
-- Table structure for table `nilai_karyawan`
--

CREATE TABLE `nilai_karyawan` (
  `id_nilai_karyawan` int(11) NOT NULL,
  `id_karyawan` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `nilai_karyawan`
-- 

INSERT INTO `nilai_karyawan` (`id_nilai_karyawan`, `id_karyawan`, `id_kriteria`, `nilai`) VALUES
(1, 1, 101, 5),
(2, 1, 102, 5),
(3, 1, 103, 5),
(4, 1, 104, 5),
(5, 1, 105, 4500000),
(6, 1, 106, 26),
(7, 1, 107, 3),
(8, 2, 101, 4),
(9, 2, 102, 4),
(10, 2, 103, 4),
(11, 2, 104, 3),
(12, 2, 105, 3500000),
(13, 2, 106, 25),
(14, 2, 107, 2),
(15, 3, 101, 3),
(16, 3, 102, 3),
(17, 3, 103, 3),
(18, 3, 104, 2),
(19, 3, 105, 1500000),
(20, 3, 106, 21),
(21, 3, 107, 1),
(22, 4, 101, 3),
(23, 4, 102, 3),
(24, 4, 103, 3),
(25, 4, 104, 3),
(26, 4, 105, 1500000),
(27, 4, 106, 22),
(28, 4, 107, 2),
(29, 5, 101, 4),
(30, 5, 102, 4),
(31, 5, 103, 4),
(32, 5, 104, 3),
(33, 5, 105, 3250000),
(34, 5, 106, 24),
(35, 5, 107, 2),
(36, 6, 101, 4),
(37, 6, 102, 3),
(38, 6, 103, 3),
(39, 6, 104, 3),
(40, 6, 105, 2750000),
(41, 6, 106, 19),
(42, 6, 107, 1),
(43, 7, 101, 4),
(44, 7, 102, 3),
(45, 7, 103, 3),
(46, 7, 104, 3),
(47, 7, 105, 2000000),
(48, 7, 106, 18),
(49, 7, 107, 1),
(50, 8, 101, 3),
(51, 8, 102, 3),
(52, 8, 103, 3),
(53, 8, 104, 3),
(54, 8, 105, 2500000),
(55, 8, 106, 23),
(56, 8, 107, 1),
(57, 9, 101, 4),
(58, 9, 102, 4),
(59, 9, 103, 4),
(60, 9, 104, 4),
(61, 9, 105, 2750000),
(62, 9, 106, 22),
(63, 9, 107, 2),
(64, 10, 101, 4),
(65, 10, 102, 3),
(66, 10, 103, 4),
(67, 10, 104, 4),
(68, 10, 105, 2900000),
(69, 10, 106, 21),
(70, 10, 107, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pilihan_kriteria`
--

CREATE TABLE `pilihan_kriteria` (
  `id_pil_kriteria` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(70) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `role` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `email`, `alamat`, `role`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Faris Daffa', 'fid@gmail.com', 'Tegalgede, Karanganyar', '1'),
(2, 'petugas', '670489f94b6997a870b148f74744ee5676304925', 'Adkha wildan', 'awr@gmail.com', 'Kos, Surakarta', '2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indexes for table `nilai_karyawan`
--
ALTER TABLE `nilai_karyawan`
  ADD PRIMARY KEY (`id_nilai_karyawan`),
  ADD UNIQUE KEY `id_karyawan_2` (`id_karyawan`,`id_kriteria`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  ADD PRIMARY KEY (`id_pil_kriteria`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `nilai_karyawan`
--
ALTER TABLE `nilai_karyawan`
  MODIFY `id_nilai_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  MODIFY `id_pil_kriteria` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nilai_karyawan`
--
ALTER TABLE `nilai_karyawan`
  ADD CONSTRAINT `nilai_karyawan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`),
  ADD CONSTRAINT `nilai_karyawan_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`);

--
-- Constraints for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  ADD CONSTRAINT `pilihan_kriteria_ibfk_1` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
