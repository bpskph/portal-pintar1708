-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 29, 2024 at 12:24 PM
-- Server version: 10.3.28-MariaDB
-- PHP Version: 8.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bps1700_portalpintar2.0`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

CREATE TABLE `access_logs` (
  `id` bigint(11) NOT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `user_ip` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

CREATE TABLE `agenda` (
  `id_agenda` bigint(20) NOT NULL,
  `kegiatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_kategori` int(11) NOT NULL,
  `waktumulai` timestamp NULL DEFAULT current_timestamp(),
  `waktuselesai` timestamp NOT NULL DEFAULT current_timestamp(),
  `waktumulai_tunda` timestamp NULL DEFAULT NULL,
  `waktuselesai_tunda` timestamp NULL DEFAULT NULL,
  `metode` tinyint(4) NOT NULL,
  `pelaksana` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `progress` tinyint(4) NOT NULL,
  `peserta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `peserta_lain` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemimpin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_lanjutan` bigint(20) DEFAULT NULL,
  `reporter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agendapimpinan`
--

CREATE TABLE `agendapimpinan` (
  `id_agendapimpinan` bigint(20) NOT NULL,
  `waktumulai` timestamp NULL DEFAULT current_timestamp(),
  `waktuselesai` timestamp NOT NULL DEFAULT current_timestamp(),
  `tempat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kegiatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pendamping` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendamping_lain` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_agendapimpinan_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apel`
--

CREATE TABLE `apel` (
  `id_apel` bigint(20) NOT NULL,
  `jenis_apel` tinyint(4) NOT NULL DEFAULT 0,
  `tanggal_apel` date NOT NULL,
  `pembina_inspektur` varchar(50) NOT NULL,
  `pemimpin_komandan` varchar(50) NOT NULL,
  `perwira` varchar(50) DEFAULT NULL,
  `mc` varchar(50) NOT NULL,
  `uud` varchar(50) NOT NULL,
  `korpri` varchar(50) NOT NULL,
  `doa` varchar(50) NOT NULL,
  `ajudan` varchar(50) NOT NULL,
  `operator` varchar(50) NOT NULL,
  `bendera` text DEFAULT NULL,
  `tambahsatu_text` varchar(255) DEFAULT NULL,
  `tambahsatu_petugas` varchar(50) DEFAULT NULL,
  `tambahdua_text` varchar(255) DEFAULT NULL,
  `tambahdua_petugas` varchar(50) DEFAULT NULL,
  `reporter` varchar(50) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_apel_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `beritarilis`
--

CREATE TABLE `beritarilis` (
  `id_beritarilis` bigint(20) NOT NULL,
  `waktumulai` timestamp NOT NULL DEFAULT current_timestamp(),
  `waktuselesai` timestamp NOT NULL DEFAULT current_timestamp(),
  `materi_rilis` text NOT NULL,
  `narasumber` varchar(50) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `reporter` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `dl`
--

CREATE TABLE `dl` (
  `id_dl` bigint(20) NOT NULL,
  `pegawai` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `fk_tujuan` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tugas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tim` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dltujuan`
--

CREATE TABLE `dltujuan` (
  `id_dltujuan` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_tujuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_prov` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dltujuanprov`
--

CREATE TABLE `dltujuanprov` (
  `id_dltujuanprov` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_tujuanprov` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` bigint(20) NOT NULL,
  `laporan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dokumentasi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval` int(11) NOT NULL DEFAULT 0,
  `timestamp_laporan` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_laporan_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `linkapp`
--

CREATE TABLE `linkapp` (
  `id_linkapp` bigint(20) NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `views` bigint(20) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `owner` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `linkmat`
--

CREATE TABLE `linkmat` (
  `id_linkmat` bigint(20) NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `views` bigint(20) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `owner` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobildinas`
--

CREATE TABLE `mobildinas` (
  `id_mobildinas` bigint(20) NOT NULL,
  `mulai` timestamp NOT NULL DEFAULT current_timestamp(),
  `selesai` timestamp NOT NULL DEFAULT current_timestamp(),
  `keperluan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keperluan_lainnya` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `borrower` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval` tinyint(4) NOT NULL DEFAULT 0,
  `alasan_tolak_batal` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobildinaskeperluan`
--

CREATE TABLE `mobildinaskeperluan` (
  `id_mobildinaskeperluan` int(11) NOT NULL,
  `nama_mobildinaskeperluan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  `link_id` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patches`
--

CREATE TABLE `patches` (
  `id_patches` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` text DEFAULT NULL,
  `is_notification` tinyint(4) NOT NULL DEFAULT 0,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `nipbaru` bigint(20) NOT NULL,
  `nip` bigint(20) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nomor_hp` varchar(14) NOT NULL,
  `tgl_daftar` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `level` tinyint(4) NOT NULL DEFAULT 1,
  `approver_mobildinas` tinyint(4) NOT NULL DEFAULT 0,
  `sk_maker` tinyint(4) NOT NULL DEFAULT 0,
  `theme` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `popups`
--

CREATE TABLE `popups` (
  `id_popups` int(11) NOT NULL,
  `judul_popups` varchar(255) NOT NULL,
  `rincian_popups` text NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id_project` bigint(20) NOT NULL,
  `tahun` year(4) NOT NULL,
  `nama_project` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_team` bigint(20) NOT NULL,
  `panggilan_project` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktif` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectmember`
--

CREATE TABLE `projectmember` (
  `id_projectmember` bigint(20) NOT NULL,
  `fk_project` bigint(20) NOT NULL,
  `pegawai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_status` tinyint(4) NOT NULL DEFAULT 1,
  `timetstamp_projectmember_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id_rooms` bigint(20) NOT NULL,
  `nama_ruangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp_rooms` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sk`
--

CREATE TABLE `sk` (
  `id_sk` bigint(20) NOT NULL,
  `nomor_sk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_sk` date NOT NULL,
  `tentang_sk` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dalam_sk` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratkode`
--

CREATE TABLE `suratkode` (
  `id_suratkode` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` tinyint(4) NOT NULL,
  `rincian_suratkode` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratrepo`
--

CREATE TABLE `suratrepo` (
  `id_suratrepo` bigint(20) NOT NULL,
  `fk_agenda` bigint(20) DEFAULT NULL,
  `penerima_suratrepo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_suratrepo` date NOT NULL,
  `perihal_suratrepo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '-',
  `fk_suratsubkode` int(11) NOT NULL,
  `jenis` tinyint(4) NOT NULL DEFAULT 0,
  `nomor_suratrepo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi_suratrepo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isi_lampiran` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isi_lampiran_orientation` tinyint(4) NOT NULL DEFAULT 0,
  `pihak_pertama` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pihak_kedua` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ttd_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ttd_by_jabatan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tembusan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_suratrepo_lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratrepoeks`
--

CREATE TABLE `suratrepoeks` (
  `id_suratrepoeks` bigint(20) NOT NULL,
  `fk_agenda` bigint(20) DEFAULT NULL,
  `penerima_suratrepoeks` text NOT NULL,
  `tanggal_suratrepoeks` date NOT NULL,
  `perihal_suratrepoeks` text NOT NULL,
  `lampiran` varchar(255) DEFAULT '-',
  `fk_suratsubkode` int(11) NOT NULL,
  `sifat` tinyint(4) NOT NULL DEFAULT 0,
  `jenis` tinyint(4) NOT NULL DEFAULT 0,
  `nomor_suratrepoeks` varchar(255) NOT NULL,
  `isi_suratrepoeks` text DEFAULT NULL,
  `isi_lampiran` text DEFAULT NULL,
  `isi_lampiran_orientation` tinyint(4) NOT NULL DEFAULT 0,
  `ttd_by` varchar(50) DEFAULT NULL,
  `tembusan` text DEFAULT NULL,
  `owner` varchar(50) NOT NULL,
  `invisibility` tinyint(4) NOT NULL DEFAULT 0,
  `shared_to` tinyint(4) DEFAULT NULL,
  `approver` varchar(50) NOT NULL,
  `komentar` text DEFAULT NULL,
  `jumlah_revisi` tinyint(4) NOT NULL DEFAULT 0,
  `approval` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_suratrepoeks_lastupdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `suratrepoeksttd`
--

CREATE TABLE `suratrepoeksttd` (
  `id_suratrepoeksttd` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `suratsubkode`
--

CREATE TABLE `suratsubkode` (
  `id_suratsubkode` int(11) NOT NULL,
  `fk_suratkode` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_suratsubkode` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rincian_suratsubkode` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `id_team` bigint(20) NOT NULL,
  `nama_team` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `panggilan_team` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teamleader`
--

CREATE TABLE `teamleader` (
  `id_teamleader` bigint(20) NOT NULL,
  `nama_teamleader` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_team` bigint(20) NOT NULL,
  `leader_status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zooms`
--

CREATE TABLE `zooms` (
  `id_zooms` bigint(20) NOT NULL,
  `fk_agenda` bigint(20) NOT NULL,
  `jenis_zoom` tinyint(4) NOT NULL,
  `jenis_surat` tinyint(4) NOT NULL,
  `fk_surat` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proposer` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zoomstype`
--

CREATE TABLE `zoomstype` (
  `id_zoomstype` int(11) NOT NULL,
  `nama_zoomstype` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kuota` int(11) NOT NULL DEFAULT 100,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id_agenda`);

--
-- Indexes for table `agendapimpinan`
--
ALTER TABLE `agendapimpinan`
  ADD PRIMARY KEY (`id_agendapimpinan`);

--
-- Indexes for table `apel`
--
ALTER TABLE `apel`
  ADD PRIMARY KEY (`id_apel`);

--
-- Indexes for table `beritarilis`
--
ALTER TABLE `beritarilis`
  ADD PRIMARY KEY (`id_beritarilis`);

--
-- Indexes for table `dl`
--
ALTER TABLE `dl`
  ADD PRIMARY KEY (`id_dl`);

--
-- Indexes for table `dltujuan`
--
ALTER TABLE `dltujuan`
  ADD PRIMARY KEY (`id_dltujuan`);

--
-- Indexes for table `dltujuanprov`
--
ALTER TABLE `dltujuanprov`
  ADD PRIMARY KEY (`id_dltujuanprov`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `linkapp`
--
ALTER TABLE `linkapp`
  ADD PRIMARY KEY (`id_linkapp`);

--
-- Indexes for table `linkmat`
--
ALTER TABLE `linkmat`
  ADD PRIMARY KEY (`id_linkmat`);

--
-- Indexes for table `mobildinas`
--
ALTER TABLE `mobildinas`
  ADD PRIMARY KEY (`id_mobildinas`);

--
-- Indexes for table `mobildinaskeperluan`
--
ALTER TABLE `mobildinaskeperluan`
  ADD PRIMARY KEY (`id_mobildinaskeperluan`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patches`
--
ALTER TABLE `patches`
  ADD PRIMARY KEY (`id_patches`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `nipbaru` (`nipbaru`);

--
-- Indexes for table `popups`
--
ALTER TABLE `popups`
  ADD PRIMARY KEY (`id_popups`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id_project`);

--
-- Indexes for table `projectmember`
--
ALTER TABLE `projectmember`
  ADD PRIMARY KEY (`id_projectmember`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id_rooms`);

--
-- Indexes for table `sk`
--
ALTER TABLE `sk`
  ADD PRIMARY KEY (`id_sk`);

--
-- Indexes for table `suratkode`
--
ALTER TABLE `suratkode`
  ADD PRIMARY KEY (`id_suratkode`);

--
-- Indexes for table `suratrepo`
--
ALTER TABLE `suratrepo`
  ADD PRIMARY KEY (`id_suratrepo`);

--
-- Indexes for table `suratrepoeks`
--
ALTER TABLE `suratrepoeks`
  ADD PRIMARY KEY (`id_suratrepoeks`);

--
-- Indexes for table `suratrepoeksttd`
--
ALTER TABLE `suratrepoeksttd`
  ADD PRIMARY KEY (`id_suratrepoeksttd`);

--
-- Indexes for table `suratsubkode`
--
ALTER TABLE `suratsubkode`
  ADD PRIMARY KEY (`id_suratsubkode`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id_team`);

--
-- Indexes for table `teamleader`
--
ALTER TABLE `teamleader`
  ADD PRIMARY KEY (`id_teamleader`);

--
-- Indexes for table `zooms`
--
ALTER TABLE `zooms`
  ADD PRIMARY KEY (`id_zooms`);

--
-- Indexes for table `zoomstype`
--
ALTER TABLE `zoomstype`
  ADD PRIMARY KEY (`id_zoomstype`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id_agenda` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agendapimpinan`
--
ALTER TABLE `agendapimpinan`
  MODIFY `id_agendapimpinan` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apel`
--
ALTER TABLE `apel`
  MODIFY `id_apel` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beritarilis`
--
ALTER TABLE `beritarilis`
  MODIFY `id_beritarilis` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dl`
--
ALTER TABLE `dl`
  MODIFY `id_dl` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `linkapp`
--
ALTER TABLE `linkapp`
  MODIFY `id_linkapp` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `linkmat`
--
ALTER TABLE `linkmat`
  MODIFY `id_linkmat` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mobildinas`
--
ALTER TABLE `mobildinas`
  MODIFY `id_mobildinas` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mobildinaskeperluan`
--
ALTER TABLE `mobildinaskeperluan`
  MODIFY `id_mobildinaskeperluan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patches`
--
ALTER TABLE `patches`
  MODIFY `id_patches` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `popups`
--
ALTER TABLE `popups`
  MODIFY `id_popups` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id_project` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projectmember`
--
ALTER TABLE `projectmember`
  MODIFY `id_projectmember` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id_rooms` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sk`
--
ALTER TABLE `sk`
  MODIFY `id_sk` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suratrepo`
--
ALTER TABLE `suratrepo`
  MODIFY `id_suratrepo` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suratrepoeks`
--
ALTER TABLE `suratrepoeks`
  MODIFY `id_suratrepoeks` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suratrepoeksttd`
--
ALTER TABLE `suratrepoeksttd`
  MODIFY `id_suratrepoeksttd` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suratsubkode`
--
ALTER TABLE `suratsubkode`
  MODIFY `id_suratsubkode` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id_team` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teamleader`
--
ALTER TABLE `teamleader`
  MODIFY `id_teamleader` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zooms`
--
ALTER TABLE `zooms`
  MODIFY `id_zooms` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zoomstype`
--
ALTER TABLE `zoomstype`
  MODIFY `id_zoomstype` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
