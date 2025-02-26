-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 19, 2025 at 08:00 AM
-- Server version: 8.3.0
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portalpintar3.0`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

DROP TABLE IF EXISTS `access_logs`;
CREATE TABLE IF NOT EXISTS `access_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `controller` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `user_ip` varchar(45) NOT NULL,
  `user_agent` text,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=119510 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

DROP TABLE IF EXISTS `agenda`;
CREATE TABLE IF NOT EXISTS `agenda` (
  `id_agenda` bigint NOT NULL AUTO_INCREMENT,
  `kegiatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_kategori` int NOT NULL,
  `waktumulai` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `waktuselesai` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktumulai_tunda` timestamp NULL DEFAULT NULL,
  `waktuselesai_tunda` timestamp NULL DEFAULT NULL,
  `metode` tinyint NOT NULL,
  `pelaksana` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `progress` tinyint NOT NULL,
  `presensi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `peserta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `peserta_lain` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pemimpin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_lanjutan` bigint DEFAULT NULL,
  `surat_lanjutan` int NOT NULL,
  `by_event_team` tinyint NOT NULL DEFAULT '0',
  `event_team_leader` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_agenda`)
) ENGINE=MyISAM AUTO_INCREMENT=695 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agendapimpinan`
--

DROP TABLE IF EXISTS `agendapimpinan`;
CREATE TABLE IF NOT EXISTS `agendapimpinan` (
  `id_agendapimpinan` bigint NOT NULL AUTO_INCREMENT,
  `waktumulai` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `waktuselesai` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tempat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kegiatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pendamping` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pendamping_lain` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reporter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_agendapimpinan_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_agendapimpinan`)
) ENGINE=MyISAM AUTO_INCREMENT=470 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apel`
--

DROP TABLE IF EXISTS `apel`;
CREATE TABLE IF NOT EXISTS `apel` (
  `id_apel` bigint NOT NULL AUTO_INCREMENT,
  `jenis_apel` tinyint NOT NULL DEFAULT '0',
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
  `bendera` text,
  `tambahsatu_text` varchar(255) DEFAULT NULL,
  `tambahsatu_petugas` varchar(50) DEFAULT NULL,
  `tambahdua_text` varchar(255) DEFAULT NULL,
  `tambahdua_petugas` varchar(50) DEFAULT NULL,
  `reporter` varchar(50) NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_apel_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_apel`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beritarilis`
--

DROP TABLE IF EXISTS `beritarilis`;
CREATE TABLE IF NOT EXISTS `beritarilis` (
  `id_beritarilis` bigint NOT NULL AUTO_INCREMENT,
  `waktumulai` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktuselesai` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `materi_rilis` text NOT NULL,
  `narasumber` varchar(50) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `reporter` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_beritarilis`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dl`
--

DROP TABLE IF EXISTS `dl`;
CREATE TABLE IF NOT EXISTS `dl` (
  `id_dl` bigint NOT NULL AUTO_INCREMENT,
  `pegawai` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `fk_tujuan` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tugas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dl`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dltujuan`
--

DROP TABLE IF EXISTS `dltujuan`;
CREATE TABLE IF NOT EXISTS `dltujuan` (
  `id_dltujuan` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_tujuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_prov` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_dltujuan`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dltujuan`
--

INSERT INTO `dltujuan` (`id_dltujuan`, `nama_tujuan`, `fk_prov`) VALUES
('1101', 'Simeulue', '11'),
('1102', 'Aceh Singkil', '11'),
('1103', 'Aceh Selatan', '11'),
('1104', 'Aceh Tenggara', '11'),
('1105', 'Aceh Timur', '11'),
('1106', 'Aceh Tengah', '11'),
('1107', 'Aceh Barat', '11'),
('1108', 'Aceh Besar', '11'),
('1109', 'Pidie', '11'),
('1110', 'Bireuen', '11'),
('1111', 'Aceh Utara', '11'),
('1112', 'Aceh Barat Daya', '11'),
('1113', 'Gayo Lues', '11'),
('1114', 'Aceh Tamiang', '11'),
('1115', 'Nagan Raya', '11'),
('1116', 'Aceh Jaya', '11'),
('1117', 'Bener Meriah', '11'),
('1118', 'Pidie Jaya', '11'),
('1171', 'Banda Aceh', '11'),
('1172', 'Sabang', '11'),
('1173', 'Langsa', '11'),
('1174', 'Lhokseumawe', '11'),
('1175', 'Subulussalam', '11'),
('1201', 'Nias', '12'),
('1202', 'Mandailing Natal', '12'),
('1203', 'Tapanuli Selatan', '12'),
('1204', 'Tapanuli Tengah', '12'),
('1205', 'Tapanuli Utara', '12'),
('1206', 'Toba', '12'),
('1207', 'Labuhanbatu', '12'),
('1208', 'Asahan', '12'),
('1209', 'Simalungun', '12'),
('1210', 'Dairi', '12'),
('1211', 'Karo', '12'),
('1212', 'Deli Serdang', '12'),
('1213', 'Langkat', '12'),
('1214', 'Nias Selatan', '12'),
('1215', 'Humbang Hasundutan', '12'),
('1216', 'Pakpak Bharat', '12'),
('1217', 'Samosir', '12'),
('1218', 'Serdang Bedagai', '12'),
('1219', 'Batu Bara', '12'),
('1220', 'Padang Lawas Utara', '12'),
('1221', 'Padang Lawas', '12'),
('1222', 'Labuhanbatu Selatan', '12'),
('1223', 'Labuhanbatu Utara', '12'),
('1224', 'Nias Utara', '12'),
('1225', 'Nias Barat', '12'),
('1271', 'Sibolga', '12'),
('1272', 'Tanjungbalai', '12'),
('1273', 'Pematang Siantar', '12'),
('1274', 'Tebing Tinggi', '12'),
('1275', 'Medan', '12'),
('1276', 'Binjai', '12'),
('1277', 'Padangsidimpuan', '12'),
('1278', 'Gunungsitoli', '12'),
('1301', 'Kepulauan Mentawai', '13'),
('1302', 'Pesisir Selatan', '13'),
('1303', 'Solok', '13'),
('1304', 'Sijunjung', '13'),
('1305', 'Tanah Datar', '13'),
('1306', 'Padang Pariaman', '13'),
('1307', 'Agam', '13'),
('1308', 'Lima Puluh Kota', '13'),
('1309', 'Pasaman', '13'),
('1310', 'Solok Selatan', '13'),
('1311', 'Dharmasraya', '13'),
('1312', 'Pasaman Barat', '13'),
('1371', 'Padang', '13'),
('1372', 'Solok', '13'),
('1373', 'Sawahlunto', '13'),
('1374', 'Padang Panjang', '13'),
('1375', 'Bukittinggi', '13'),
('1376', 'Payakumbuh', '13'),
('1377', 'Pariaman', '13'),
('1401', 'Kuantan Singingi', '14'),
('1402', 'Indragiri Hulu', '14'),
('1403', 'Indragiri Hilir', '14'),
('1404', 'Pelalawan', '14'),
('1405', 'S I A K', '14'),
('1406', 'Kampar', '14'),
('1407', 'Rokan Hulu', '14'),
('1408', 'Bengkalis', '14'),
('1409', 'Rokan Hilir', '14'),
('1410', 'Kepulauan Meranti', '14'),
('1471', 'Pekanbaru', '14'),
('1473', 'D U M A I', '14'),
('1501', 'Kerinci', '15'),
('1502', 'Merangin', '15'),
('1503', 'Sarolangun', '15'),
('1504', 'Batang Hari', '15'),
('1505', 'Muaro Jambi', '15'),
('1506', 'Tanjung Jabung Timur', '15'),
('1507', 'Tanjung Jabung Barat', '15'),
('1508', 'Tebo', '15'),
('1509', 'Bungo', '15'),
('1571', 'Jambi', '15'),
('1572', 'Sungai Penuh', '15'),
('1601', 'Ogan Komering Ulu', '16'),
('1602', 'Ogan Komering Ilir', '16'),
('1603', 'Muara Enim', '16'),
('1604', 'Lahat', '16'),
('1605', 'Musi Rawas', '16'),
('1606', 'Musi Banyuasin', '16'),
('1607', 'Banyu Asin', '16'),
('1608', 'Ogan Komering Ulu Selatan', '16'),
('1609', 'Ogan Komering Ulu Timur', '16'),
('1610', 'Ogan Ilir', '16'),
('1611', 'Empat Lawang', '16'),
('1612', 'Penukal Abab Lematang Ilir', '16'),
('1613', 'Musi Rawas Utara', '16'),
('1671', 'Palembang', '16'),
('1672', 'Prabumulih', '16'),
('1673', 'Pagar Alam', '16'),
('1674', 'Lubuklinggau', '16'),
('1701', 'Bengkulu Selatan', '17'),
('1702', 'Rejang Lebong', '17'),
('1703', 'Bengkulu Utara', '17'),
('1704', 'Kaur', '17'),
('1705', 'Seluma', '17'),
('1706', 'Mukomuko', '17'),
('1707', 'Lebong', '17'),
('1708', 'Kepahiang', '17'),
('1709', 'Bengkulu Tengah', '17'),
('1771', 'Bengkulu', '17'),
('1801', 'Lampung Barat', '18'),
('1802', 'Tanggamus', '18'),
('1803', 'Lampung Selatan', '18'),
('1804', 'Lampung Timur', '18'),
('1805', 'Lampung Tengah', '18'),
('1806', 'Lampung Utara', '18'),
('1807', 'Way Kanan', '18'),
('1808', 'Tulangbawang', '18'),
('1809', 'Pesawaran', '18'),
('1810', 'Pringsewu', '18'),
('1811', 'Mesuji', '18'),
('1812', 'Tulang Bawang Barat', '18'),
('1813', 'Pesisir Barat', '18'),
('1871', 'Bandar Lampung', '18'),
('1872', 'Metro', '18'),
('1901', 'Bangka', '19'),
('1902', 'Belitung', '19'),
('1903', 'Bangka Barat', '19'),
('1904', 'Bangka Tengah', '19'),
('1905', 'Bangka Selatan', '19'),
('1906', 'Belitung Timur', '19'),
('1971', 'Pangkalpinang', '19'),
('2101', 'Karimun', '21'),
('2102', 'Bintan', '21'),
('2103', 'Natuna', '21'),
('2104', 'Lingga', '21'),
('2105', 'Kepulauan Anambas', '21'),
('2171', 'B A T A M', '21'),
('2172', 'Tanjung Pinang', '21'),
('3101', 'Kepulauan Seribu', '31'),
('3171', 'Jakarta Selatan', '31'),
('3172', 'Jakarta Timur', '31'),
('3173', 'Jakarta Pusat', '31'),
('3174', 'Jakarta Barat', '31'),
('3175', 'Jakarta Utara', '31'),
('3201', 'Bogor', '32'),
('3202', 'Sukabumi', '32'),
('3203', 'Cianjur', '32'),
('3204', 'Bandung', '32'),
('3205', 'Garut', '32'),
('3206', 'Tasikmalaya', '32'),
('3207', 'Ciamis', '32'),
('3208', 'Kuningan', '32'),
('3209', 'Cirebon', '32'),
('3210', 'Majalengka', '32'),
('3211', 'Sumedang', '32'),
('3212', 'Indramayu', '32'),
('3213', 'Subang', '32'),
('3214', 'Purwakarta', '32'),
('3215', 'Karawang', '32'),
('3216', 'Bekasi', '32'),
('3217', 'Bandung Barat', '32'),
('3218', 'Pangandaran', '32'),
('3271', 'Bogor', '32'),
('3272', 'Sukabumi', '32'),
('3273', 'Bandung', '32'),
('3274', 'Cirebon', '32'),
('3275', 'Bekasi', '32'),
('3276', 'Depok', '32'),
('3277', 'Cimahi', '32'),
('3278', 'Tasikmalaya', '32'),
('3279', 'Banjar', '32'),
('3301', 'Cilacap', '33'),
('3302', 'Banyumas', '33'),
('3303', 'Purbalingga', '33'),
('3304', 'Banjarnegara', '33'),
('3305', 'Kebumen', '33'),
('3306', 'Purworejo', '33'),
('3307', 'Wonosobo', '33'),
('3308', 'Magelang', '33'),
('3309', 'Boyolali', '33'),
('3310', 'Klaten', '33'),
('3311', 'Sukoharjo', '33'),
('3312', 'Wonogiri', '33'),
('3313', 'Karanganyar', '33'),
('3314', 'Sragen', '33'),
('3315', 'Grobogan', '33'),
('3316', 'Blora', '33'),
('3317', 'Rembang', '33'),
('3318', 'Pati', '33'),
('3319', 'Kudus', '33'),
('3320', 'Jepara', '33'),
('3321', 'Demak', '33'),
('3322', 'Semarang', '33'),
('3323', 'Temanggung', '33'),
('3324', 'Kendal', '33'),
('3325', 'Batang', '33'),
('3326', 'Pekalongan', '33'),
('3327', 'Pemalang', '33'),
('3328', 'Tegal', '33'),
('3329', 'Brebes', '33'),
('3371', 'Magelang', '33'),
('3372', 'Surakarta', '33'),
('3373', 'Salatiga', '33'),
('3374', 'Semarang', '33'),
('3375', 'Pekalongan', '33'),
('3376', 'Tegal', '33'),
('3401', 'Kulon Progo', '34'),
('3402', 'Bantul', '34'),
('3403', 'Gunungkidul', '34'),
('3404', 'Sleman', '34'),
('3471', 'Yogyakarta', '34'),
('3501', 'Pacitan', '35'),
('3502', 'Ponorogo', '35'),
('3503', 'Trenggalek', '35'),
('3504', 'Tulungagung', '35'),
('3505', 'Blitar', '35'),
('3506', 'Kediri', '35'),
('3507', 'Malang', '35'),
('3508', 'Lumajang', '35'),
('3509', 'Jember', '35'),
('3510', 'Banyuwangi', '35'),
('3511', 'Bondowoso', '35'),
('3512', 'Situbondo', '35'),
('3513', 'Probolinggo', '35'),
('3514', 'Pasuruan', '35'),
('3515', 'Sidoarjo', '35'),
('3516', 'Mojokerto', '35'),
('3517', 'Jombang', '35'),
('3518', 'Nganjuk', '35'),
('3519', 'Madiun', '35'),
('3520', 'Magetan', '35'),
('3521', 'Ngawi', '35'),
('3522', 'Bojonegoro', '35'),
('3523', 'Tuban', '35'),
('3524', 'Lamongan', '35'),
('3525', 'Gresik', '35'),
('3526', 'Bangkalan', '35'),
('3527', 'Sampang', '35'),
('3528', 'Pamekasan', '35'),
('3529', 'Sumenep', '35'),
('3571', 'Kediri', '35'),
('3572', 'Blitar', '35'),
('3573', 'Malang', '35'),
('3574', 'Probolinggo', '35'),
('3575', 'Pasuruan', '35'),
('3576', 'Mojokerto', '35'),
('3577', 'Madiun', '35'),
('3578', 'Surabaya', '35'),
('3579', 'Batu', '35'),
('3601', 'Pandeglang', '36'),
('3602', 'Lebak', '36'),
('3603', 'Tangerang', '36'),
('3604', 'Serang', '36'),
('3671', 'Tangerang', '36'),
('3672', 'Cilegon', '36'),
('3673', 'Serang', '36'),
('3674', 'Tangerang Selatan', '36'),
('5101', 'Jembrana', '51'),
('5102', 'Tabanan', '51'),
('5103', 'Badung', '51'),
('5104', 'Gianyar', '51'),
('5105', 'Klungkung', '51'),
('5106', 'Bangli', '51'),
('5107', 'Karangasem', '51'),
('5108', 'Buleleng', '51'),
('5171', 'Denpasar', '51'),
('5201', 'Lombok Barat', '52'),
('5202', 'Lombok Tengah', '52'),
('5203', 'Lombok Timur', '52'),
('5204', 'Sumbawa', '52'),
('5205', 'Dompu', '52'),
('5206', 'Bima', '52'),
('5207', 'Sumbawa Barat', '52'),
('5208', 'Lombok Utara', '52'),
('5271', 'Mataram', '52'),
('5272', 'Bima', '52'),
('5301', 'Sumba Barat', '53'),
('5302', 'Sumba Timur', '53'),
('5303', 'Kupang', '53'),
('5304', 'Timor Tengah Selatan', '53'),
('5305', 'Timor Tengah Utara', '53'),
('5306', 'Belu', '53'),
('5307', 'Alor', '53'),
('5308', 'Lembata', '53'),
('5309', 'Flores Timur', '53'),
('5310', 'Sikka', '53'),
('5311', 'Ende', '53'),
('5312', 'Ngada', '53'),
('5313', 'Manggarai', '53'),
('5314', 'Rote Ndao', '53'),
('5315', 'Manggarai Barat', '53'),
('5316', 'Sumba Tengah', '53'),
('5317', 'Sumba Barat Daya', '53'),
('5318', 'Nagekeo', '53'),
('5319', 'Manggarai Timur', '53'),
('5320', 'Sabu Raijua', '53'),
('5321', 'Malaka', '53'),
('5371', 'Kupang', '53'),
('6101', 'Sambas', '61'),
('6102', 'Bengkayang', '61'),
('6103', 'Landak', '61'),
('6104', 'Mempawah', '61'),
('6105', 'Sanggau', '61'),
('6106', 'Ketapang', '61'),
('6107', 'Sintang', '61'),
('6108', 'Kapuas Hulu', '61'),
('6109', 'Sekadau', '61'),
('6110', 'Melawi', '61'),
('6111', 'Kayong Utara', '61'),
('6112', 'Kubu Raya', '61'),
('6171', 'Pontianak', '61'),
('6172', 'Singkawang', '61'),
('6201', 'Kotawaringin Barat', '62'),
('6202', 'Kotawaringin Timur', '62'),
('6203', 'Kapuas', '62'),
('6204', 'Barito Selatan', '62'),
('6205', 'Barito Utara', '62'),
('6206', 'Sukamara', '62'),
('6207', 'Lamandau', '62'),
('6208', 'Seruyan', '62'),
('6209', 'Katingan', '62'),
('6210', 'Pulang Pisau', '62'),
('6211', 'Gunung Mas', '62'),
('6212', 'Barito Timur', '62'),
('6213', 'Murung Raya', '62'),
('6271', 'Palangka Raya', '62'),
('6301', 'Tanah Laut', '63'),
('6302', 'Kotabaru', '63'),
('6303', 'Banjar', '63'),
('6304', 'Barito Kuala', '63'),
('6305', 'Tapin', '63'),
('6306', 'Hulu Sungai Selatan', '63'),
('6307', 'Hulu Sungai Tengah', '63'),
('6308', 'Hulu Sungai Utara', '63'),
('6309', 'Tabalong', '63'),
('6310', 'Tanah Bumbu', '63'),
('6311', 'Balangan', '63'),
('6371', 'Banjarmasin', '63'),
('6372', 'Banjarbaru', '63'),
('6401', 'Paser', '64'),
('6402', 'Kutai Barat', '64'),
('6403', 'Kutai Kartanegara', '64'),
('6404', 'Kutai Timur', '64'),
('6405', 'Berau', '64'),
('6409', 'Penajam Paser Utara', '64'),
('6411', 'Mahakam Ulu', '64'),
('6471', 'Balikpapan', '64'),
('6472', 'Samarinda', '64'),
('6474', 'Bontang', '64'),
('6501', 'Malinau', '65'),
('6502', 'Bulungan', '65'),
('6503', 'Tana Tidung', '65'),
('6504', 'Nunukan', '65'),
('6571', 'Tarakan', '65'),
('7101', 'Bolaang Mongondow', '71'),
('7102', 'Minahasa', '71'),
('7103', 'Kepulauan Sangihe', '71'),
('7104', 'Kepulauan Talaud', '71'),
('7105', 'Minahasa Selatan', '71'),
('7106', 'Minahasa Utara', '71'),
('7107', 'Bolaang Mongondow Utara', '71'),
('7108', 'Siau Tagulandang Biaro', '71'),
('7109', 'Minahasa Tenggara', '71'),
('7110', 'Bolaang Mongondow Selatan', '71'),
('7111', 'Bolaang Mongondow Timur', '71'),
('7171', 'Manado', '71'),
('7172', 'Bitung', '71'),
('7173', 'Tomohon', '71'),
('7174', 'Kotamobagu', '71'),
('7201', 'Banggai Kepulauan', '72'),
('7202', 'Banggai', '72'),
('7203', 'Morowali', '72'),
('7204', 'Poso', '72'),
('7205', 'Donggala', '72'),
('7206', 'Toli-Toli', '72'),
('7207', 'Buol', '72'),
('7208', 'Parigi Moutong', '72'),
('7209', 'Tojo Una-Una', '72'),
('7210', 'Sigi', '72'),
('7211', 'Banggai Laut', '72'),
('7212', 'Morowali Utara', '72'),
('7271', 'Palu', '72'),
('7301', 'Kepulauan Selayar', '73'),
('7302', 'Bulukumba', '73'),
('7303', 'Bantaeng', '73'),
('7304', 'Jeneponto', '73'),
('7305', 'Takalar', '73'),
('7306', 'Gowa', '73'),
('7307', 'Sinjai', '73'),
('7308', 'Maros', '73'),
('7309', 'Pangkajene Dan Kepulauan', '73'),
('7310', 'Barru', '73'),
('7311', 'Bone', '73'),
('7312', 'Soppeng', '73'),
('7313', 'Wajo', '73'),
('7314', 'Sidenreng Rappang', '73'),
('7315', 'Pinrang', '73'),
('7316', 'Enrekang', '73'),
('7317', 'Luwu', '73'),
('7318', 'Tana Toraja', '73'),
('7322', 'Luwu Utara', '73'),
('7325', 'Luwu Timur', '73'),
('7326', 'Toraja Utara', '73'),
('7371', 'Makassar', '73'),
('7372', 'Parepare', '73'),
('7373', 'Palopo', '73'),
('7401', 'Buton', '74'),
('7402', 'Muna', '74'),
('7403', 'Konawe', '74'),
('7404', 'Kolaka', '74'),
('7405', 'Konawe Selatan', '74'),
('7406', 'Bombana', '74'),
('7407', 'Wakatobi', '74'),
('7408', 'Kolaka Utara', '74'),
('7409', 'Buton Utara', '74'),
('7410', 'Konawe Utara', '74'),
('7411', 'Kolaka Timur', '74'),
('7412', 'Konawe Kepulauan', '74'),
('7413', 'Muna Barat', '74'),
('7414', 'Buton Tengah', '74'),
('7415', 'Buton Selatan', '74'),
('7471', 'Kendari', '74'),
('7472', 'Baubau', '74'),
('7501', 'Boalemo', '75'),
('7502', 'Gorontalo', '75'),
('7503', 'Pohuwato', '75'),
('7504', 'Bone Bolango', '75'),
('7505', 'Gorontalo Utara', '75'),
('7571', 'Gorontalo', '75'),
('7601', 'Majene', '76'),
('7602', 'Polewali Mandar', '76'),
('7603', 'Mamasa', '76'),
('7604', 'Mamuju', '76'),
('7605', 'Pasangkayu', '76'),
('7606', 'Mamuju Tengah', '76'),
('8101', 'Kepulauan Tanimbar', '81'),
('8102', 'Maluku Tenggara', '81'),
('8103', 'Maluku Tengah', '81'),
('8104', 'Buru', '81'),
('8105', 'Kepulauan Aru', '81'),
('8106', 'Seram Bagian Barat', '81'),
('8107', 'Seram Bagian Timur', '81'),
('8108', 'Maluku Barat Daya', '81'),
('8109', 'Buru Selatan', '81'),
('8171', 'Ambon', '81'),
('8172', 'Tual', '81'),
('8201', 'Halmahera Barat', '82'),
('8202', 'Halmahera Tengah', '82'),
('8203', 'Kepulauan Sula', '82'),
('8204', 'Halmahera Selatan', '82'),
('8205', 'Halmahera Utara', '82'),
('8206', 'Halmahera Timur', '82'),
('8207', 'Pulau Morotai', '82'),
('8208', 'Pulau Taliabu', '82'),
('8271', 'Ternate', '82'),
('8272', 'Tidore Kepulauan', '82'),
('9101', 'Fakfak', '91'),
('9102', 'Kaimana', '91'),
('9103', 'Teluk Wondama', '91'),
('9104', 'Teluk Bintuni', '91'),
('9105', 'Manokwari', '91'),
('9111', 'Manokwari Selatan', '91'),
('9112', 'Pegunungan Arfak', '91'),
('9201', 'Raja Ampat', '92'),
('9202', 'Sorong', '92'),
('9203', 'Sorong Selatan', '92'),
('9204', 'Maybrat', '92'),
('9205', 'Tambrauw', '92'),
('9271', 'Sorong', '92'),
('9403', 'Jayapura', '94'),
('9408', 'Kepulauan Yapen', '94'),
('9409', 'Biak Numfor', '94'),
('9419', 'Sarmi', '94'),
('9420', 'Keerom', '94'),
('9426', 'Waropen', '94'),
('9427', 'Supiori', '94'),
('9428', 'Mamberamo Raya', '94'),
('9471', 'Jayapura', '94'),
('9501', 'Merauke', '95'),
('9502', 'Boven Digoel', '95'),
('9503', 'Mappi', '95'),
('9504', 'Asmat', '95'),
('9601', 'Mimika', '96'),
('9602', 'Dogiyai', '96'),
('9603', 'Deiyai', '96'),
('9604', 'Nabire', '96'),
('9605', 'Paniai', '96'),
('9606', 'Intan Jaya', '96'),
('9607', 'Puncak', '96'),
('9608', 'Puncak Jaya', '96'),
('9701', 'Nduga', '97'),
('9702', 'Jayawijaya', '97'),
('9703', 'Lanny Jaya', '97'),
('9704', 'Tolikara', '97'),
('9705', 'Mamberamo Tengah', '97'),
('9706', 'Yalimo', '97'),
('9707', 'Yahukimo', '97'),
('9708', 'Pegunungan Bintang', '97');

-- --------------------------------------------------------

--
-- Table structure for table `dltujuanprov`
--

DROP TABLE IF EXISTS `dltujuanprov`;
CREATE TABLE IF NOT EXISTS `dltujuanprov` (
  `id_dltujuanprov` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_tujuanprov` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_dltujuanprov`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dltujuanprov`
--

INSERT INTO `dltujuanprov` (`id_dltujuanprov`, `nama_tujuanprov`) VALUES
('11', 'Aceh'),
('12', 'Sumatera Utara'),
('13', 'Sumatera Barat'),
('14', 'Riau'),
('15', 'Jambi'),
('16', 'Sumatera Selatan'),
('17', 'Bengkulu'),
('18', 'Lampung'),
('19', 'Kepulauan Bangka Belitung'),
('21', 'Kepulauan Riau'),
('31', 'DKI Jakarta'),
('32', 'Jawa Barat'),
('33', 'Jawa Tengah'),
('34', 'DI Yogyakarta'),
('35', 'Jawa Timur'),
('36', 'Banten'),
('51', 'Bali'),
('52', 'Nusa Tenggara Barat'),
('53', 'Nusa Tenggara Timur'),
('61', 'Kalimantan Barat'),
('62', 'Kalimantan Tengah'),
('63', 'Kalimantan Selatan'),
('64', 'Kalimantan Timur'),
('65', 'Kalimantan Utara'),
('71', 'Sulawesi Utara'),
('72', 'Sulawesi Tengah'),
('73', 'Sulawesi Selatan'),
('74', 'Sulawesi Tenggara'),
('75', 'Gorontalo'),
('76', 'Sulawesi Barat'),
('81', 'Maluku'),
('82', 'Maluku Utara'),
('91', 'Papua Barat'),
('92', 'Papua Barat Daya'),
('94', 'Papua'),
('95', 'Papua Selatan'),
('96', 'Papua Tengah'),
('97', 'Papua Pegunungan');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kategori`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `timestamp`) VALUES
(1, 'Rapat, Pertemuan, dan Diskusi', '2024-09-11 07:00:00'),
(2, 'Pelatihan, Workshop, dan Bimbingan Teknis', '2024-09-11 07:00:00'),
(3, 'Rakor, Ratek, dan Bimtek', '2024-09-11 07:00:00'),
(4, 'Pleno, Rekon, FGD', '2024-09-11 07:00:00'),
(5, 'Jurassik dan BeKuda', '2024-09-11 07:00:00'),
(6, 'Evaluasi dan Pemeriksaan', '2024-09-11 07:00:00'),
(7, 'Uji Kompetensi', '2024-09-11 07:00:00'),
(8, 'Seminar dan Knowledge Sharing', '2024-09-11 07:00:00'),
(9, 'Narasumber, Kompetisi, dan Penghargaan Eksternal', '2024-09-11 07:00:00'),
(10, 'Lainnya', '2024-09-11 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

DROP TABLE IF EXISTS `laporan`;
CREATE TABLE IF NOT EXISTS `laporan` (
  `id_laporan` bigint NOT NULL AUTO_INCREMENT,
  `laporan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dokumentasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `uploader` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval` int NOT NULL DEFAULT '0',
  `timestamp_laporan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_laporan_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_laporan`)
) ENGINE=MyISAM AUTO_INCREMENT=685 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `linkapp`
--

DROP TABLE IF EXISTS `linkapp`;
CREATE TABLE IF NOT EXISTS `linkapp` (
  `id_linkapp` bigint NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `views` bigint NOT NULL DEFAULT '0',
  `active` tinyint NOT NULL DEFAULT '1',
  `owner` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_linkapp`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `linkmat`
--

DROP TABLE IF EXISTS `linkmat`;
CREATE TABLE IF NOT EXISTS `linkmat` (
  `id_linkmat` bigint NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `views` bigint NOT NULL DEFAULT '0',
  `active` tinyint NOT NULL DEFAULT '0',
  `owner` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_linkmat`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobildinas`
--

DROP TABLE IF EXISTS `mobildinas`;
CREATE TABLE IF NOT EXISTS `mobildinas` (
  `id_mobildinas` bigint NOT NULL AUTO_INCREMENT,
  `mulai` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `selesai` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `keperluan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keperluan_lainnya` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `borrower` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval` tinyint NOT NULL DEFAULT '0',
  `alasan_tolak_batal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mobildinas`)
) ENGINE=MyISAM AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobildinaskeperluan`
--

DROP TABLE IF EXISTS `mobildinaskeperluan`;
CREATE TABLE IF NOT EXISTS `mobildinaskeperluan` (
  `id_mobildinaskeperluan` int NOT NULL AUTO_INCREMENT,
  `nama_mobildinaskeperluan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mobildinaskeperluan`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mobildinaskeperluan`
--

INSERT INTO `mobildinaskeperluan` (`id_mobildinaskeperluan`, `nama_mobildinaskeperluan`, `timestamp`) VALUES
(1, 'Agenda di DPRD atau OPD Lainnya', '2024-02-07 07:45:29'),
(2, 'Narasumber di Luar Kantor BPS Provinsi Bengkulu', '2024-02-07 07:45:29'),
(3, 'Koordinasi/Enumerasi Data', '2024-02-07 07:45:29'),
(4, 'Agenda Kehumasan', '2024-02-07 07:45:29'),
(5, 'Agenda Keuangan (ke Bank)', '2024-02-07 07:45:29'),
(6, 'Keperluan Lainnya', '2024-02-07 07:46:47');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  `link_id` bigint NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8036 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patches`
--

DROP TABLE IF EXISTS `patches`;
CREATE TABLE IF NOT EXISTS `patches` (
  `id_patches` bigint NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` text,
  `is_notification` tinyint NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`id_patches`)
) ENGINE=MyISAM AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

DROP TABLE IF EXISTS `pengguna`;
CREATE TABLE IF NOT EXISTS `pengguna` (
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `nipbaru` bigint NOT NULL,
  `nip` bigint NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nomor_hp` varchar(14) NOT NULL,
  `tgl_daftar` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `level` tinyint NOT NULL DEFAULT '1',
  `approver_mobildinas` tinyint NOT NULL DEFAULT '0',
  `sk_maker` tinyint NOT NULL DEFAULT '0',
  `theme` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`),
  UNIQUE KEY `nip` (`nip`),
  UNIQUE KEY `nipbaru` (`nipbaru`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`username`, `password`, `nipbaru`, `nip`, `nama`, `nomor_hp`, `tgl_daftar`, `tgl_update`, `level`, `approver_mobildinas`, `sk_maker`, `theme`) VALUES
('afif', '5f4dcc3b5aa765d61d8327deb882cf99', 198103202003121006, 340017087, 'Afif Afandi, SST, M.Si.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('aldilla.nugroho', '5f4dcc3b5aa765d61d8327deb882cf99', 199703222022012001, 340060498, 'Aldilla Devitasari Nugroho S.Tr.Stat.', '6285664991937', '2023-04-11 16:11:19', '2025-01-03 01:49:16', 1, 0, 0, 1),
('amalela', '5f4dcc3b5aa765d61d8327deb882cf99', 197106231994012001, 340014255, 'Amalela Neti, SE.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('ardiansyah3', '5f4dcc3b5aa765d61d8327deb882cf99', 198203262011011011, 340054730, 'Ardiansyah S.E.', '6285664991937', '2025-01-13 10:10:00', '2025-01-13 03:10:00', 1, 0, 0, 0),
('aswien', '5f4dcc3b5aa765d61d8327deb882cf99', 198710172009121004, 340053269, 'Aswien Oktavian Perdana, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('aypratama', '5f4dcc3b5aa765d61d8327deb882cf99', 199308152014121001, 340057005, 'Auliya Yudha Pratama, S.ST., M.Stat.', '6285664991937', '2023-02-15 20:49:45', '2024-08-07 18:46:40', 1, 0, 0, 1),
('bangkit.nurcahyo', '5f4dcc3b5aa765d61d8327deb882cf99', 199308262022031005, 340061209, 'Bangkit Nurcahyo, S.Ak', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('betty', '5f4dcc3b5aa765d61d8327deb882cf99', 197101151992012001, 340013031, 'Betty Viozita, SE', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('boby.fernando', '5f4dcc3b5aa765d61d8327deb882cf99', 199402022017011001, 340057697, 'Boby Fernando, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('budi.ansori', '5f4dcc3b5aa765d61d8327deb882cf99', 197901212011011006, 340054732, 'Budi Ansori, S.P.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 1, 0, 1),
('budih', '5f4dcc3b5aa765d61d8327deb882cf99', 196309251988021001, 340011854, 'Budi Hardiyono, S.Si, M.E', '6285664991937', '2023-02-15 20:49:45', '2024-03-07 20:21:16', 2, 0, 0, 0),
('budik', '5f4dcc3b5aa765d61d8327deb882cf99', 197408291993011001, 340013488, 'Budi Kurniawan, SST, M.Si.', '6285664991937', '2023-02-15 20:49:45', '2023-05-31 00:51:15', 2, 0, 0, 0),
('bukhariadam', '5f4dcc3b5aa765d61d8327deb882cf99', 198707242010121004, 340054189, 'Arie Bukhari Adam Semenguk, SST, M.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('dianpratiwi-pppk', '5f4dcc3b5aa765d61d8327deb882cf99', 199210202024212015, 340062812, 'Dian Pratiwi A.Md.Kom.', '6285664991937', '2024-03-04 08:21:51', '2024-03-04 01:21:51', 1, 0, 0, 0),
('dina.livie', '5f4dcc3b5aa765d61d8327deb882cf99', 197905282005022002, 340017557, 'Dina Darliviyarsih, S.E., M.Stat.', '6285664991937', '2023-02-15 20:49:45', '2024-03-07 20:26:29', 1, 0, 0, 0),
('dota', '5f4dcc3b5aa765d61d8327deb882cf99', 198705092011011010, 340054735, 'Dota Dwi Rely, S.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('edwine', '5f4dcc3b5aa765d61d8327deb882cf99', 197708301999121001, 340015991, 'Edwin Erifiandi, SST.,M.Si', '6285664991937', '2023-02-15 20:49:45', '2023-07-24 01:15:12', 2, 0, 0, 0),
('ega.afni', '5f4dcc3b5aa765d61d8327deb882cf99', 198504012011012012, 340054736, 'Ega Afri Neni, S.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('eka.putrawansyah', '5f4dcc3b5aa765d61d8327deb882cf99', 197403272009011008, 340052073, 'Eka Putrawansyah, SE', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('eko.fajar', '5f4dcc3b5aa765d61d8327deb882cf99', 197907102002121007, 340016480, 'Eko Fajariyanto, S.ST, M.Stat', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('elyasumarni', '5f4dcc3b5aa765d61d8327deb882cf99', 196812121989032002, 340012260, 'Elya Sumarni, SE', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('esti.kartika', '5f4dcc3b5aa765d61d8327deb882cf99', 198804092010122003, 340054191, 'Esti Kartika Rini, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('farelya', '5f4dcc3b5aa765d61d8327deb882cf99', 198808252012112001, 340055891, 'Ratih Farelya, SST, M.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('fatmasari', '5f4dcc3b5aa765d61d8327deb882cf99', 198606242009022008, 340051129, 'Fatmasari Damayanti, S.Si.,M.Si', '6285664991937', '2023-02-15 20:49:45', '2025-01-15 01:24:09', 2, 0, 0, 0),
('fikratuz.isman', '5f4dcc3b5aa765d61d8327deb882cf99', 199305232014122001, 340057066, 'Fikratuz Auliyah Adima Isman, SST, M.Stat.', '6285664991937', '2023-02-15 20:49:45', '2024-03-07 20:27:30', 1, 0, 0, 0),
('fitriar', '5f4dcc3b5aa765d61d8327deb882cf99', 198108252003122001, 340016989, 'Fitri Aryati, SST, M.Si', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('fkurniawati', '5f4dcc3b5aa765d61d8327deb882cf99', 199110242014102001, 340056952, 'Fera Kurniawati, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('galuh.diantoro', '5f4dcc3b5aa765d61d8327deb882cf99', 199203162016021001, 340057393, 'Galuh Diantoro, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('guswandi', '5f4dcc3b5aa765d61d8327deb882cf99', 198508052009021001, 340050104, 'Guswandi Alfian, SST', '6285664991937', '2023-02-15 20:49:45', '2025-01-15 01:24:24', 2, 0, 0, 1),
('hafidh.redho', '5f4dcc3b5aa765d61d8327deb882cf99', 200005072022011003, 340061088, 'Hafidh Redho Nasrullah, A.Md.Kb.N', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('hafrizal', '5f4dcc3b5aa765d61d8327deb882cf99', 197103051991011001, 340012675, 'Hafrizal', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('hendarto', '5f4dcc3b5aa765d61d8327deb882cf99', 198307042010031002, 340053581, 'Hendarto, S.E', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('hendri3', '5f4dcc3b5aa765d61d8327deb882cf99', 197402011998031003, 340015622, 'Hendri, SST, M.Si.', '6285664991937', '2023-02-15 20:49:45', '2025-01-15 02:56:51', 2, 0, 0, 0),
('herlinawaty', '5f4dcc3b5aa765d61d8327deb882cf99', 197802231999122001, 340016007, 'Herlinawaty, S.Si, M.Si', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('hestin.rahmanita', '5f4dcc3b5aa765d61d8327deb882cf99', 198812302010122005, 340054193, 'Hestin Rahmanita SST, M.Si.', '6285664991937', '2025-01-13 10:10:00', '2025-01-13 03:10:00', 1, 0, 0, 0),
('jomecho', '5f4dcc3b5aa765d61d8327deb882cf99', 198701022012111001, 340055936, 'Tommy Jomecho, SST, M.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('kiki.fajri', '5f4dcc3b5aa765d61d8327deb882cf99', 198907112011011004, 340054747, 'Kiki Fajri, SE', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('mardhatilla', '5f4dcc3b5aa765d61d8327deb882cf99', 199905292023022001, 340061886, 'Mardhatilla S.Tr.Stat.', '6285664991937', '2024-02-23 16:47:45', '2024-09-17 22:29:32', 1, 0, 0, 0),
('maulinda', '5f4dcc3b5aa765d61d8327deb882cf99', 198811062012122003, 340056025, 'Siti Maulinda, S.T.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('meidian.rinaldi', '5f4dcc3b5aa765d61d8327deb882cf99', 199805192021041001, 340060197, 'Meidian Rinaldi S.Tr.Stat.', '6285664991937', '2023-11-20 10:53:40', '2023-11-20 03:53:40', 1, 0, 0, 1),
('meidio.talo', '5f4dcc3b5aa765d61d8327deb882cf99', 199505112018021001, 340058349, 'Meidio Talo Prista, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('nagatondi-pppk', '5f4dcc3b5aa765d61d8327deb882cf99', 198808032023211005, 340062287, 'Naga Tondi Hasibuan S.IKom', '6285664991937', '2023-08-01 11:34:47', '2023-08-01 04:34:47', 1, 0, 0, 0),
('nelse.trivianita', '5f4dcc3b5aa765d61d8327deb882cf99', 199410092017012001, 340057699, 'Nelse Trivianita, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('nofriani', '5f4dcc3b5aa765d61d8327deb882cf99', 199111192014102002, 340056745, 'Nofriani, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 0, 0, 0, 0),
('novrian', '5f4dcc3b5aa765d61d8327deb882cf99', 198311072006021003, 340017824, 'Novrian Pratama, SST, M.Si', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('nur.iman', '5f4dcc3b5aa765d61d8327deb882cf99', 198706162009121001, 340053279, 'Nur Iman Taufik, S.ST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('nurlaisa', '5f4dcc3b5aa765d61d8327deb882cf99', 197112041992032002, 340013198, 'Nurlaisa, SE', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('nurtia', '5f4dcc3b5aa765d61d8327deb882cf99', 199409052017012001, 340057671, 'Nurtia, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('rahmahulfah', '5f4dcc3b5aa765d61d8327deb882cf99', 198603082008012001, 340020094, 'Rahmah Ulfah, SST.,M.Sc', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('rasyidka', '5f4dcc3b5aa765d61d8327deb882cf99', 199311132016021001, 340057255, 'Abdur Rasyid Karim Amrullah, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('ratnaka', '5f4dcc3b5aa765d61d8327deb882cf99', 198701292011012008, 340054404, 'Ratna Kusuma Astuti, S.Si, M.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('renypuspa', '5f4dcc3b5aa765d61d8327deb882cf99', 198704122009122007, 340053280, 'Reny Puspasari SST, M.E.', '6285664991937', '2024-04-16 10:17:10', '2024-04-16 03:17:10', 1, 0, 0, 0),
('reyronald', '5f4dcc3b5aa765d61d8327deb882cf99', 198705182009021001, 340051287, 'Rey Ronald Purba S.Stat., M.M.', '6285664991937', '2023-02-15 20:49:45', '2024-03-07 20:26:00', 1, 0, 0, 0),
('rizwaldi', '5f4dcc3b5aa765d61d8327deb882cf99', 196802171988031001, 340011927, 'Rizwandi', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('roky', '5f4dcc3b5aa765d61d8327deb882cf99', 198007102011012011, 340054762, 'Roky Yulita, S.Psi.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 1, 1),
('rolianardi', '5f4dcc3b5aa765d61d8327deb882cf99', 198408192009021006, 340050233, 'Rolian Ardi, SST, M.T.', '6285664991937', '2023-02-15 20:49:45', '2024-07-25 19:43:17', 1, 0, 0, 0),
('saharudin', '5f4dcc3b5aa765d61d8327deb882cf99', 196508072009111001, 340053027, 'Saharudin', '6285664991937', '2023-02-15 20:49:45', '2024-03-07 20:23:38', 2, 0, 0, 0),
('sahranudin', '5f4dcc3b5aa765d61d8327deb882cf99', 197109291993021001, 340013583, 'Sahranudin, S.E., M.Si.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:31:52', 1, 0, 0, 1),
('sekbps17', '5f4dcc3b5aa765d61d8327deb882cf99', 999999999999999999, 999999999, 'Sekretaris BPS Prov. Bengkulu', '6285664991937', '2023-05-23 13:11:19', '2024-09-17 22:30:13', 1, 0, 0, 0),
('suhanderi', '5f4dcc3b5aa765d61d8327deb882cf99', 198701012011011016, 340054770, 'Suhanderi, S.H., M.H.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 1, 0),
('syifa.nurhidayah', '5f4dcc3b5aa765d61d8327deb882cf99', 199611212019012001, 340059002, 'Syifa Nurhidayah S.Tr.Stat.', '6285664991937', '2023-08-08 16:20:09', '2023-08-08 09:20:09', 1, 0, 0, 1),
('taufan.hidayat', '5f4dcc3b5aa765d61d8327deb882cf99', 197707302006041008, 450017079, 'Taufan Hidayat', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('taufik.rahman', '5f4dcc3b5aa765d61d8327deb882cf99', 198902052012121001, 340056023, 'KMS. Taufik Rahman S.Si., M.E.', '6285664991937', '2024-08-01 13:19:45', '2024-07-31 23:32:41', 1, 0, 0, 0),
('teuku_fr', '5f4dcc3b5aa765d61d8327deb882cf99', 197610111997121001, 340015502, 'Teuku Fahrulriza, S.Si., M.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('ts.masruroh', '5f4dcc3b5aa765d61d8327deb882cf99', 199402042019032003, 340059192, 'Tsamrotul Masruroh, S.Stat.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('wahyu.setiadi', '5f4dcc3b5aa765d61d8327deb882cf99', 198106122003121004, 340017036, 'Wahyu Setiadi S.ST, M.Ec.Dev.', '6285664991937', '2024-08-19 10:49:45', '2024-08-19 04:14:00', 1, 0, 0, 0),
('widiyaningsih', '5f4dcc3b5aa765d61d8327deb882cf99', 198107272004122001, 340017324, 'Widiyaningsih, SST, M.M.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('wina.prima', '5f4dcc3b5aa765d61d8327deb882cf99', 198805102010122008, 340054195, 'Wina Prima Nurmala, SST, M.Si.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('winrizal', '5f4dcc3b5aa765d61d8327deb882cf99', 196608251988021001, 340011837, 'Ir. Win Rizal, M.E.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 1),
('yamanora.rosalin', '5f4dcc3b5aa765d61d8327deb882cf99', 199401292016022001, 340057644, 'Yamanora Sylvia Rosalin, SST', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('yogos', '5f4dcc3b5aa765d61d8327deb882cf99', 198203292004121001, 340017326, 'Dwi Yogo Supriyanto, SST, M.E.', '6285664991937', '2023-02-15 20:49:45', '2023-08-08 02:23:01', 2, 0, 0, 0),
('yosepoktavianus', '5f4dcc3b5aa765d61d8327deb882cf99', 198610092009021003, 340050274, 'Yosep Oktavianus Sitohang, SST, M.Stat', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('yuli.widiastuti', '5f4dcc3b5aa765d61d8327deb882cf99', 198007142002122004, 340016578, 'Yuli Widiastuti, S.M.', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0),
('yuni.marliana', '5f4dcc3b5aa765d61d8327deb882cf99', 196906161994032003, 340014857, 'Yuni Marliana, S.Sos', '6285664991937', '2023-02-15 20:49:45', '2023-04-18 04:14:00', 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `popups`
--

DROP TABLE IF EXISTS `popups`;
CREATE TABLE IF NOT EXISTS `popups` (
  `id_popups` int NOT NULL AUTO_INCREMENT,
  `judul_popups` varchar(255) NOT NULL,
  `rincian_popups` text NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_popups`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `id_project` bigint NOT NULL AUTO_INCREMENT,
  `tahun` year NOT NULL,
  `nama_project` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_team` bigint NOT NULL,
  `panggilan_project` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktif` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_project`)
) ENGINE=MyISAM AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectmember`
--

DROP TABLE IF EXISTS `projectmember`;
CREATE TABLE IF NOT EXISTS `projectmember` (
  `id_projectmember` bigint NOT NULL AUTO_INCREMENT,
  `fk_project` bigint NOT NULL,
  `pegawai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_status` tinyint NOT NULL DEFAULT '1',
  `timetstamp_projectmember_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_projectmember`)
) ENGINE=MyISAM AUTO_INCREMENT=883 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id_rooms` bigint NOT NULL AUTO_INCREMENT,
  `nama_ruangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp_rooms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rooms`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id_rooms`, `nama_ruangan`, `timestamp_rooms`) VALUES
(1, 'Aula Bunga Kibut BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(2, 'Aula Raflesia BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(3, 'Ruang Mako/Agro BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(4, 'Ruang Kepala BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(5, 'Ruang Pengolahan, TI dan Metodologi (PTM) BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(6, 'Ruang Statistik Sosial BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(7, 'Ruang Statistik Distribusi BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(8, 'Ruang Neraca Wilayah dan Analisis Statistik BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(9, 'Ruang Statistik Produksi BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(10, 'Ruang Bagian Umum BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(11, 'Unit Pelayanan Statistik Terpadu (PST) BPS Provinsi Bengkulu', '2023-05-02 04:29:56'),
(12, 'Lapangan Kantor BPS Provinsi Bengkulu', '2023-05-30 02:10:03'),
(13, 'Zoom Meeting/Google Meeting', '2024-02-16 19:36:50');

-- --------------------------------------------------------

--
-- Table structure for table `sk`
--

DROP TABLE IF EXISTS `sk`;
CREATE TABLE IF NOT EXISTS `sk` (
  `id_sk` bigint NOT NULL AUTO_INCREMENT,
  `nomor_sk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_sk` date NOT NULL,
  `tentang_sk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dalam_sk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reporter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sk`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratkode`
--

DROP TABLE IF EXISTS `suratkode`;
CREATE TABLE IF NOT EXISTS `suratkode` (
  `id_suratkode` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` tinyint NOT NULL,
  `rincian_suratkode` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_suratkode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suratkode`
--

INSERT INTO `suratkode` (`id_suratkode`, `jenis`, `rincian_suratkode`) VALUES
('PS', 1, 'PERUMUSAN KEBIJAKAN DI BIDANG STATISTIK MELIPUTI; METODOLOGI DAN INFORMASI STATISTIK, STATISTIK SOSIAL, STATISTIK PRODUKSI, STATISTIK DISTRIBUSI DAN JASA, NERACA DAN ANALISIS STATISTIK'),
('SS', 1, 'SENSUS PENDUDUK, SENSUS PERTANIAN DAN SENSUS EKONOMI'),
('VS', 1, 'SURVEI'),
('KS', 1, 'KONSOLIDASI DATA STATISTIK'),
('ES', 1, 'EVALUASI DAN PELAPORAN SENSUS, SURVEI DAN KONSOLIDASI DATA'),
('KU', 2, 'KEUANGAN'),
('KP', 2, 'KEPEGAWAIAN'),
('PR', 2, 'PERENCANAAN'),
('HK', 2, 'HUKUM'),
('OT', 2, 'ORGANISASI  DAN TATA LAKSANA'),
('HM', 2, 'HUBUNGAN  MASYARAKAT'),
('KA', 2, 'KEARSIPAN'),
('RT', 2, 'KERUMAHTANGGAAN'),
('PL', 2, 'PERLENGKAPAN'),
('DL', 2, 'PENDIDIKAN DAN LATIHAN'),
('PK', 2, 'KEPUSTAKAAN'),
('IF', 2, 'INFORMATIKA'),
('PW', 2, 'PENGAWASAN'),
('TS', 2, 'TRANSFORMASI STATISTIK');

-- --------------------------------------------------------

--
-- Table structure for table `suratmasuk`
--

DROP TABLE IF EXISTS `suratmasuk`;
CREATE TABLE IF NOT EXISTS `suratmasuk` (
  `id_suratmasuk` bigint NOT NULL AUTO_INCREMENT,
  `pengirim_suratmasuk` varchar(255) NOT NULL,
  `perihal_suratmasuk` text NOT NULL,
  `tanggal_diterima` date NOT NULL,
  `nomor_suratmasuk` varchar(255) NOT NULL,
  `tanggal_suratmasuk` date NOT NULL,
  `sifat` tinyint NOT NULL,
  `fk_suratmasukpejabat` varchar(50) NOT NULL,
  `reporter` varchar(50) NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_suratmasuk`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratmasukdisposisi`
--

DROP TABLE IF EXISTS `suratmasukdisposisi`;
CREATE TABLE IF NOT EXISTS `suratmasukdisposisi` (
  `id_suratmasukdisposisi` bigint NOT NULL AUTO_INCREMENT,
  `level_disposisi` varchar(2) NOT NULL,
  `fk_suratmasuk` bigint NOT NULL,
  `tanggal_disposisi` date NOT NULL,
  `pemberi_disposisi` varchar(50) NOT NULL,
  `tujuan_disposisi_team` bigint DEFAULT NULL,
  `tujuan_disposisi_pegawai` varchar(50) DEFAULT NULL,
  `instruksi` text NOT NULL,
  `status_penyelesaian` tinyint DEFAULT NULL,
  `laporan_penyelesaian` text,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_suratmasukdisposisi`)
) ENGINE=MyISAM AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratmasukpejabat`
--

DROP TABLE IF EXISTS `suratmasukpejabat`;
CREATE TABLE IF NOT EXISTS `suratmasukpejabat` (
  `id_suratmasukpejabat` int NOT NULL AUTO_INCREMENT,
  `pegawai` varchar(50) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_suratmasukpejabat`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suratmasukpejabat`
--

INSERT INTO `suratmasukpejabat` (`id_suratmasukpejabat`, `pegawai`, `jabatan`, `status`) VALUES
(1, 'winrizal', 'Kepala BPS Provinsi Bengkulu', 1),
(2, 'sahranudin', 'Kepala Bagian Umum BPS Provinsi Bengkulu', 1);

-- --------------------------------------------------------

--
-- Table structure for table `suratrepo`
--

DROP TABLE IF EXISTS `suratrepo`;
CREATE TABLE IF NOT EXISTS `suratrepo` (
  `id_suratrepo` bigint NOT NULL AUTO_INCREMENT,
  `fk_agenda` bigint DEFAULT NULL,
  `penerima_suratrepo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_suratrepo` date NOT NULL,
  `perihal_suratrepo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_undangan` tinyint DEFAULT NULL,
  `lampiran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '-',
  `fk_suratsubkode` int NOT NULL,
  `jenis` tinyint NOT NULL DEFAULT '0',
  `nomor_suratrepo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi_suratrepo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `isi_lampiran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `isi_lampiran_orientation` tinyint NOT NULL DEFAULT '0',
  `pihak_pertama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pihak_kedua` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ttd_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ttd_by_jabatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tembusan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `owner` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_suratrepo_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_suratrepo`)
) ENGINE=MyISAM AUTO_INCREMENT=2213 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratrepoeks`
--

DROP TABLE IF EXISTS `suratrepoeks`;
CREATE TABLE IF NOT EXISTS `suratrepoeks` (
  `id_suratrepoeks` bigint NOT NULL AUTO_INCREMENT,
  `fk_agenda` bigint DEFAULT NULL,
  `penerima_suratrepoeks` text NOT NULL,
  `tanggal_suratrepoeks` date NOT NULL,
  `perihal_suratrepoeks` text NOT NULL,
  `lampiran` varchar(255) DEFAULT '-',
  `fk_suratsubkode` int NOT NULL,
  `sifat` tinyint NOT NULL DEFAULT '0',
  `jenis` tinyint NOT NULL DEFAULT '0',
  `nomor_suratrepoeks` varchar(255) NOT NULL,
  `isi_suratrepoeks` text,
  `isi_lampiran` text,
  `isi_lampiran_orientation` tinyint NOT NULL DEFAULT '0',
  `ttd_by` varchar(50) DEFAULT NULL,
  `tembusan` text,
  `owner` varchar(50) NOT NULL,
  `invisibility` tinyint NOT NULL DEFAULT '0',
  `shared_to` tinyint DEFAULT NULL,
  `approver` varchar(50) NOT NULL,
  `komentar` text,
  `jumlah_revisi` tinyint NOT NULL DEFAULT '0',
  `approval` tinyint NOT NULL DEFAULT '0',
  `sent_by` tinyint DEFAULT NULL,
  `is_sent_by_sek` tinyint DEFAULT NULL,
  `timestamp_sent_by_sek` timestamp NULL DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_suratrepoeks_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_suratrepoeks`)
) ENGINE=MyISAM AUTO_INCREMENT=3713 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suratrepoeksttd`
--

DROP TABLE IF EXISTS `suratrepoeksttd`;
CREATE TABLE IF NOT EXISTS `suratrepoeksttd` (
  `id_suratrepoeksttd` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_suratrepoeksttd`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `suratrepoeksttd`
--

INSERT INTO `suratrepoeksttd` (`id_suratrepoeksttd`, `nama`, `jabatan`, `deleted`) VALUES
(1, 'WIN RIZAL', 'Kepala Badan Pusat Statistik<br/>Provinsi Bengkulu,', 0),
(2, 'SAHRANUDIN', 'a.n. Kepala BPS Provinsi Bengkulu\r\n<br/>\r\nKepala Bagian Umum', 0);

-- --------------------------------------------------------

--
-- Table structure for table `suratsubkode`
--

DROP TABLE IF EXISTS `suratsubkode`;
CREATE TABLE IF NOT EXISTS `suratsubkode` (
  `id_suratsubkode` int NOT NULL AUTO_INCREMENT,
  `fk_suratkode` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_suratsubkode` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rincian_suratsubkode` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_suratsubkode`)
) ENGINE=MyISAM AUTO_INCREMENT=769 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suratsubkode`
--

INSERT INTO `suratsubkode` (`id_suratsubkode`, `fk_suratkode`, `kode_suratsubkode`, `rincian_suratsubkode`) VALUES
(1, 'KU', '000', 'PELAKSANAAN ANGGARAN'),
(2, 'KU', '010', 'Ketentuan/Peraturan Menteri Keuangan Menyangkut Pelaksanaan dan Penatausahaan'),
(3, 'KU', '100', 'REALISASI PENDAPATAN / PENERIMAAN NEGARA'),
(4, 'KU', '110', 'Surat Setoran Pajak (SSP)'),
(5, 'KU', '120', 'Surat Setoran Bukan Pajak (SSBP)'),
(6, 'KU', '130', 'Bukti Penerimaan Bukan Pajak (PNBP)'),
(7, 'KU', '140', 'Dana Bagi Hasil yang bersumber dari Pajak'),
(8, 'KU', '141', 'Pajak Bumi Bangunan'),
(9, 'KU', '142', 'Bea Perolehan Hak Atas Tanah dan Bangunan (BPHTB)'),
(10, 'KU', '143', 'Pajak Penghasilan (PPh) pasal 21, 25, dan 29'),
(11, 'KU', '150', 'Bukti setor sisa anggaran lebih atau bukti setor pengembalian belanja (SSBP)'),
(12, 'KU', '160', 'Bunga dan/ atau jasa giro pada Bank'),
(13, 'KU', '170', 'Piutang Negara'),
(14, 'KU', '180', 'Pengelolaan Investasi dan Penyertaan Modal'),
(15, 'KU', '200', 'PENGELOLAAN PERBENDAHARAAN'),
(16, 'KU', '210', 'Pejabat Penguji dan Penandatanganan SPM'),
(17, 'KU', '220', 'Bendahara Penerimaan'),
(18, 'KU', '230', 'Bendahara Pengeluaran'),
(19, 'KU', '240', 'Kartu Pengawasan Pembayaran Penghasilan Pegawai (KP4)'),
(20, 'KU', '250', 'Pengembalian Belanja'),
(21, 'KU', '260', 'Pembukuan Anggaran'),
(22, 'KU', '261', 'Buku Kas Umum (BKU)'),
(23, 'KU', '262', 'Buku Kas Pembantu'),
(24, 'KU', '263', 'Kartu Realisasi Anggaran dan Pengawasan Realisasi Anggaran'),
(25, 'KU', '270', 'Berita Acara Pemeriksaan Kas'),
(26, 'KU', '280', 'Daftar Gaji / Kartu Gaji'),
(27, 'KU', '300', 'PENGELUARAN ANGGARAN'),
(29, 'KU', '310', 'Belanja Bahan'),
(30, 'KU', '320', 'Belanja Barang'),
(31, 'KU', '330', 'Belanja Jasa (Konsultan, Profesi)'),
(32, 'KU', '340', 'Belanja Perjalanan'),
(33, 'KU', '350', 'Belanja Pegawai'),
(34, 'KU', '360', 'Belanja Paket Meeting Dalam Kota'),
(35, 'KU', '370', 'Belanja Paket Meeting Luas kota'),
(36, 'KU', '380', 'Belanja Akun Kombinasi'),
(37, 'KU', '400', 'VERIFIKASI ANGGARAN'),
(38, 'KU', '410', 'Surat Permintaan Pembayaran (SPP) beserta lampirannya'),
(39, 'KU', '420', 'Surat Perintah Membayar (SPM), Surat Perintah Pencairan Dana (SP2D)'),
(40, 'KU', '500', 'PELAPORAN'),
(41, 'KU', '510', 'Akuntansi Keuangan'),
(42, 'KU', '511', 'Berita Acara Pemeriksaan Kas'),
(43, 'KU', '512', 'Kas/Register Penutupan Kas'),
(44, 'KU', '513', 'Laporan Pendapatan Negara'),
(45, 'KU', '514', 'Arsip Data Komputer (ADK)'),
(46, 'KU', '520', 'Pengumpulan, Pemantauan, Evaluasi, dan Laporan Keuangan'),
(47, 'KU', '521', 'Keadaan Kredit Anggaran (LKKA) Bulanan/Triwulanan/Semesteran'),
(52, 'KU', '530', 'Rekonsiliasi Data Laporan Keuangan'),
(53, 'KU', '600', 'BANTUAN PINJAMAN LUAR NEGERI'),
(54, 'KU', '610', 'Permohonan Pinjaman Luar Negeri (Blue Book)'),
(55, 'KU', '620', 'Dokumen Kesanggupan Negara Donor (Gray Book)'),
(56, 'KU', '630', 'Memorandum of Understand (MOU) dan dokumen sejenisnya'),
(57, 'KU', '640', 'Loan Agreement Pinjaman/Hibah Luar Negeri (PHLN), Legal Opinion, Surat Menyurat dengan lender, konsultan.'),
(58, 'KU', '650', 'Alokasi dan Relokasi Penggunaan Dana Pinjaman/Hibah Luar Negeri'),
(59, 'KU', '660', 'Penarikan Dana Bantuan Luar Negeri (BLN)'),
(60, 'KU', '661', 'Aplikasi Penarikan Dana Bantuan Luar Negeri (BLN) berikut lampirannya'),
(65, 'KU', '662', 'Otorisasi Penarikan Dana (Payment Advice)'),
(66, 'KU', '663', 'Replenisment (permintaan penarikan dana dari negara donor) meliputi :'),
(71, 'KU', '670', 'Realisasi Pencairan Dana Bantuan Luar Negeri'),
(74, 'KU', '680', 'Ketentuan / Peraturan yang menyangkut bantuan/pinjaman luar negeri'),
(75, 'KU', '690', 'Laporan-laporan pelaksanaan bantuan pinjaman luar negeri'),
(76, 'KU', '691', 'Staff Appraisal Report'),
(77, 'KU', '692', 'Report/Laporan yang terdiri dari'),
(81, 'KU', '693', 'Laporan Hutang Negara'),
(84, 'KU', '694', 'Completion Report/Annual Report'),
(85, 'KU', '700', 'PENGELOLA APBN / DANA PINJAMAN / HIBAH LUAR NEGERI (PHLN)'),
(86, 'KU', '710', 'Keputusan Kepala BPS tentang penetapan'),
(87, 'KU', '711', 'Kuasa Pengguna Anggaran (KPA), Pejabat Pembuat Komitmen (PPK)'),
(88, 'KU', '712', 'Pejabat Pembuat Daftar Gaji'),
(89, 'KU', '713', 'Penandatangan SPM'),
(90, 'KU', '714', 'Bendahara Penerimaan/Pengeluaran, Pengelola Barang'),
(91, 'KU', '800', 'SISTEM AKUNTANSI INSTANSI (SAI)'),
(92, 'KU', '810', 'Manual Implementasi Sistem Akuntansi Instansi (SAI)'),
(93, 'KU', '820', 'Arsip Data Komputer dan Berita Acara Rekonsiliasi'),
(94, 'KU', '830', 'a. Daftar Transaksi (DT), Pengeluaran (PK), Penerimaan (PN)'),
(98, 'KU', '840', 'Listing (Daftar rekaman Penerimaan), Buku Temuan dan Tindakan Lain (SAI)'),
(99, 'KU', '850', 'Laporan Realisasi Bulanan SAI'),
(100, 'KU', '860', 'Laporan Realisasi Triwulanan SAI dari Unit Akuntansi Wilayah (UAW) dan Gabungan Semua UAW/Unit Akuntansi Kantor Pusat Instansi (UAKPI)'),
(101, 'KU', '900', 'PERTANGGUNGJAWABAN KEUANGAN NEGARA'),
(102, 'KU', '910', 'Laporan Hasil Pemeriksaan atas Laporan Keuangan oleh BPK RI'),
(103, 'KU', '920', 'Hasil Pengawasan dan Pemeriksaan Internal'),
(104, 'KU', '930', 'Laporan Aparat Pemeriksa Fungsional'),
(105, 'KU', '931', 'Laporan Hasil Pemeriksaan'),
(106, 'KU', '932', 'Memorandum Hasil Pemeriksaan (MHP)'),
(107, 'KU', '933', 'Tindak Lanjut / Tanggapan LHP'),
(108, 'KU', '940', 'Dokumentasi Penyelesaiaan Keuangan Negara'),
(109, 'KU', '941', 'Tuntutan Perbendaharaan'),
(110, 'KU', '942', 'Tuntutan Ganti Rugi'),
(112, 'KP', '000', 'FORMASI PEGAWAI'),
(113, 'KP', '010', 'Usulan dari unit kerja'),
(114, 'KP', '020', 'Usulan permintaan formasi kepada Menpan dan Kepala BKN'),
(115, 'KP', '030', 'Persetujuan Menpan'),
(116, 'KP', '040', 'Penetapan Formasi'),
(117, 'KP', '050', 'Penetapan Formasi Khusus'),
(118, 'KP', '100', 'PENGADAAN DAN PENGANGKATAN PEGAWAI'),
(119, 'KP', '110', 'Proses Penerimaan Pegawai'),
(120, 'KP', '111', 'Pengumuman'),
(121, 'KP', '112', 'Seleksi Administrasi'),
(122, 'KP', '113', 'Pemanggilan Peserta Tes'),
(123, 'KP', '114', 'Pelaksanaan Ujian (tertulis, psikotes, wawancara)'),
(124, 'KP', '115', 'Keputusan hasil ujian'),
(125, 'KP', '120', 'Penetapan Pengumuman Kelulusan'),
(126, 'KP', '130', 'Berkas Lamaran yang tidak diterima'),
(127, 'KP', '140', 'Nota Usul dan Kelengkapan Penetapan NIP'),
(128, 'KP', '150', 'Nota Usul pengangkatan CPNS menjadi PNS'),
(129, 'KP', '160', 'Nota Usul pengangkatan CPNS menjadi PNS lebih dari 2 tahun'),
(130, 'KP', '170', 'SK CPNS / PNS Kolektif'),
(131, 'KP', '200', 'BERKAS PEGAWAI TIDAK TETAP / MITRA STATISTIK'),
(132, 'KP', '300', 'PEMBINAAN KARIR PEGAWAI'),
(133, 'KP', '310', 'Diklat Kursus / Tugas Belajar / Ujian Dinas / Izin Belajar Pegawai'),
(134, 'KP', '311', 'Surat Perintah / Surat Tugas / SK / Surat Izin'),
(135, 'KP', '312', 'Laporan Kegiatan Pengembangan Diri'),
(136, 'KP', '313', 'Surat Tanda Tamat Pendidikan dan Pelatihan'),
(137, 'KP', '320', 'Ujian Kompetensi'),
(138, 'KP', '321', 'Assesment Test Pegawai'),
(139, 'KP', '322', 'Pemetaan / Mapping Talent Pegawai'),
(140, 'KP', '330', 'Daftar Penilaian Pelaksanaan Pekerjaan (DP3) dan Sasaran Kinerja Pegawai'),
(141, 'KP', '340', 'Pakta Integritas Pegawai'),
(142, 'KP', '350', 'Laporan Hasil Kekayaan Penyelenggaraan Negara (LHKPN)'),
(143, 'KP', '360', 'Daftar Usul Penetapan Angka Kredit Fungsional'),
(144, 'KP', '370', 'Disiplin Pegawai'),
(145, 'KP', '371', 'Daftar Hadir'),
(146, 'KP', '372', 'Rekapitulasi Daftar Hadir'),
(147, 'KP', '380', 'Berkas Hukuman Disiplin'),
(148, 'KP', '390', 'Penghargaan dan Tanda Jasa (Satya Lencana/Bintang Jasa)'),
(149, 'KP', '400', 'PENYELESAIAN PENGELOLAAN KEBERATAN PEGAWAI'),
(150, 'KP', '500', 'MUTASI PEGAWAI'),
(151, 'KP', '510', 'Alih Status, Pindah Instansi, Pindah Wilayah Kerja, Diperbantukan, Dipekerjakan, Penugasan Sementara, Mutasi Antar Perwakilan, Mutasi ke dan dari perwakilan Sementara, Mutasi Antar Unit'),
(152, 'KP', '520', 'Nota Persetujuan/Pertimbangan Kepala BKN'),
(153, 'KP', '530', 'Mutasi Keluarga'),
(154, 'KP', '531', 'Surat Izin Pernikahan/Perceraian'),
(155, 'KP', '532', 'Surat Penolakan Izin Pernihakan/Perceraian'),
(156, 'KP', '533', 'Akte Nikah/Cerai'),
(157, 'KP', '534', 'Surat Keterangan Meninggal Dunia'),
(158, 'KP', '540', 'Usul kenaikan pangkat/golongan/jabatan'),
(159, 'KP', '550', 'Usul pengangkatan dan pemberhentian dalam jabatan Struktural/Fungsional'),
(160, 'KP', '560', 'Usul Penetapan Perubahan Data Dasar/Status/Kedudukan Hukum Pegawai'),
(161, 'KP', '570', 'Peninjauan Masa Kerja'),
(162, 'KP', '580', 'Berkas Baperjakat'),
(163, 'KP', '600', 'ADMINISTRASI PEGAWAI'),
(164, 'KP', '610', 'Dokumentasi Identitas Pegawai'),
(165, 'KP', '611', 'Usul Penetapan Karpeg/KPE/Karis/Karsu'),
(166, 'KP', '612', 'Keanggotaan Organisasi Profesi/Kedinasan'),
(167, 'KP', '613', 'Laporan Pajak Penghasilan Pribadi (LP2P)'),
(168, 'KP', '614', 'Keterangan Penerimaan Penghasilan Pegawai (KP4)'),
(169, 'KP', '620', 'Berkas Kepegawaian dan Daftar Urut Kepangkatan (DUK)'),
(170, 'KP', '630', 'Berkas Perorangan Pegawai Negeri Sipil'),
(216, 'KP', '640', 'Berkas Perseorangan Pejabat Negara'),
(217, 'KP', '641', 'Kepala  BPS'),
(218, 'KP', '642', 'Pejabat Negara  Lain yang ditentukan oleh Undang-Undang'),
(219, 'KP', '650', 'Surat Perintah  Dinas/Surat Tugas'),
(220, 'KP', '660', 'Berkas Cuti Pegawai'),
(221, 'KP', '661', 'Cuti Sakit'),
(222, 'KP', '662', 'Cuti Bersalin'),
(223, 'KP', '663', 'Cuti Tahunan'),
(224, 'KP', '664', 'Cuti Alasan Penting'),
(225, 'KP', '665', 'Cuti Luar Tanggungan Negara (CLTN)'),
(226, 'KP', '700', 'KESEJAHTERAAN PEGAWAI'),
(227, 'KP', '710', 'Berkas Tentang Layanan Tunjangan/ Gaji'),
(228, 'KP', '720', 'Berkas Tentang Layanan Pemeliharaan Kesehatan Pegawai'),
(229, 'KP', '730', 'Berkas Tentang Layanan Asuransi Pegawai'),
(230, 'KP', '740', 'Berkas Tentang Layanan Bantuan Sosial'),
(231, 'KP', '750', 'Berkas Tentang Layanan Olahraga Dan  Rekreasi'),
(232, 'KP', '760', 'Berkas Tentang Layanan Pengurusan Jenasah'),
(233, 'KP', '770', 'Berkas Tentang Layanan Organisasi Non Kedinasan (Korpri,  Dharma Wanita,  Koperasi)'),
(234, 'KP', '800', 'PEMBERHENTIAN  PEGAWAI TANPA HAK PENSIUN'),
(235, 'KP', '900', 'USUL PEMBERHENTIAN  DAN PENETAPAN PENSIUN PEGAWAI/JANDA/DUDA & PNS YANG TEWAS'),
(237, 'PR', '000', 'POKOK-POKOK KEBIJAKAN DAN STRATEGI PEMBANGUNAN'),
(238, 'PR', '010', 'Pengumpulan Data'),
(239, 'PR', '020', 'Rencana Pembangunan Jangka Panjang (RPJP)'),
(240, 'PR', '030', 'Rencana Pembangunan Jangka Panjang (RPJP)'),
(241, 'PR', '040', 'Rencana Kerja Pemerintah (RKP)'),
(242, 'PR', '050', 'Penyelenggaraan Musyawarah Perencanaan Pembangunan (Musrenbang)'),
(243, 'PR', '100', 'PENYUSUNAN RENCANA'),
(244, 'PR', '110', 'Rencana Kegiatan Teknis'),
(245, 'PR', '120', 'Rencana Kegiatan Non Teknis'),
(246, 'PR', '130', 'Keterpaduan Rencana Teknis dan Non teknis'),
(247, 'PR', '200', 'PROGRAM KERJA TAHUNAN'),
(248, 'PR', '210', 'Usulan Unit Kerja beserta data pendukungnya'),
(249, 'PR', '220', 'Program Kerja Tahunan Unit Kerja'),
(250, 'PR', '230', 'Program Kerja Tahunan Instansi/Lembaga'),
(251, 'PR', '300', 'RENCANA ANGGARAN PENDAPATAN DAN BELANJA NEGARA (RAPBN)'),
(252, 'PR', '310', 'Penyusunan RAPBN'),
(253, 'PR', '311', 'Arah  kebijakan Umum,  Strategi,  Prioritas dan Renstra'),
(256, 'PR', '312', 'Rencana Kerja dan Anggaran Kementrian/ Lembaga (RKAKL)'),
(257, 'PR', '313', 'Rencana Satuan Anggaran Per  Satuan Kerja (SAPSK),  Satuan Rincian Alokasi Anggaran (SRAA)'),
(258, 'PR', '320', 'Penyampaian APBN kepada DPR RI'),
(259, 'PR', '321', 'Nota Keuangan pemerintah dan  Rancangan \nUndang-Undang RAPBN'),
(262, 'PR', '322', 'Pembahasan RAPBN oleh  Komisi  DPR RI'),
(263, 'PR', '323', 'Risalah Rapat Dengar Pendapat dengan DPR RI'),
(264, 'PR', '324', 'Nota Jawaban DPR RI'),
(265, 'PR', '330', 'Undang-Undang Anggaran pendapatan dan  Belanja Negara (APBN)  dan  Rencana Pembangunan Tahunan (REPETA)'),
(266, 'PR', '400', 'PENYUSUNAN ANGGARAN PENDAPATAN NEGARA (APBN)'),
(267, 'PR', '410', 'Ketetapan Pagu Indikatif/Pagu Sementara'),
(268, 'PR', '420', 'Ketetapan Pagu Definitif'),
(269, 'PR', '430', 'Rencana Kerja Anggaran (RKA) Lembaga Negara dan Badan Pemerintah (LNBP)'),
(270, 'PR', '440', 'Daftar Isian Pelaksanaan Anggaran (DIPA) dan Revisinya'),
(271, 'PR', '450', 'Petunjuk Operasional Kegiatan (POK) dan Revisinya'),
(272, 'PR', '460', 'Petunjuk Teknis Tata Laksana Keterpaduan Kegiatan dan Pengelolaan Anggaran'),
(273, 'PR', '470', 'Target Penerimaan Negara Bukan Pajak'),
(274, 'PR', '500', 'PENYUSUNAN STANDAR HARGA MONITORING PROGRAM'),
(275, 'PR', '510', 'Pedoman Pengumpulan dan Pengolahan Data Standar Harga'),
(276, 'PR', '520', 'Pedoman Teknis Monitoring Program dan Kegiatan'),
(277, 'PR', '530', 'Pedoman Teknis Evaluasi dan Pelaporan Program'),
(278, 'PR', '600', 'LAPORAN'),
(279, 'PR', '610', 'Laporan Khusus'),
(280, 'PR', '611', 'Pemantauan Prioritas'),
(281, 'PR', '612', 'Laporan Pelaksanaan Kegiatan Atas Permintaan Eksternal'),
(282, 'PR', '613', 'Laporan Atas Pelaksanaan Kegiatan/Program Tertentu'),
(283, 'PR', '614', 'Rapat Dengar Pendapat dengan DPR RI'),
(284, 'PR', '620', 'Laporan Progress Report'),
(285, 'PR', '630', 'Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP)'),
(286, 'PR', '640', 'Laporan Berkala (harian,  mingguan,  bulanan, triwulanan,  semesteran,  tahunan)'),
(287, 'PR', '700', 'EVALUASI PROGRAM'),
(288, 'PR', '710', 'Evaluasi Program Unit Kerja'),
(289, 'PR', '720', 'Evaluasi Program Lembaga/Instansi'),
(291, 'HK', '000', 'PROGRAM LEGISLASI'),
(292, 'HK', '010', 'Bahan/Materi Program Legislasi Nasional dan Instansi'),
(293, 'HK', '020', 'Program Legislasi Lembaga/Instansi'),
(294, 'HK', '100', 'PERATURAN PIMPINAN LEMBAGA/INSTANSI'),
(296, 'HK', '110', 'Peraturan Kepala BPS'),
(297, 'HK', '200', 'KEPUTUSAN /KETETAPAN PIMPINAN LEMBAGA/INSTANSI'),
(299, 'HK', '300', 'INSTRUKSI SURAT EDARAN'),
(301, 'HK', '310', 'Instruksi/Surat Edaran Kepala BPS'),
(302, 'HK', '320', 'Instruksi/Surat Edaran Pejabat Tinggi Madya dan Pejabat Tinggi Pratama'),
(303, 'HK', '400', 'SURAT PERINTAH'),
(304, 'HK', '410', 'Surat Perintah Kepala BPS'),
(305, 'HK', '420', 'Surat Perintah Pejabat Madya'),
(306, 'HK', '430', 'Surat Perintah Pejabat Pratama'),
(307, 'HK', '500', 'PEDOMAN'),
(309, 'HK', '600', 'NOTA KESEPAHAMAN'),
(310, 'HK', '610', 'Dalam Negeri'),
(311, 'HK', '620', 'Luar Negeri'),
(312, 'HK', '700', 'DOKUMENTASI HUKUM'),
(314, 'HK', '800', 'SOSIALISASI /PENYULUHAN / PEMBINAAN HUKUM'),
(315, 'HK', '810', 'Berkas yang  berhubungan dengan kegiatan sosialisasi atau penyuluhan hukum'),
(316, 'HK', '820', 'Laporan hasil pelaksanaan sosialisasi penyuluhan hukum'),
(317, 'HK', '900', 'BANTUAN KONSULTASI HUKUM/ ADVOKASI'),
(319, 'HK', '1000', 'KASUS/SENGKETA HUKUM'),
(320, 'HK', '1010', 'Pidana'),
(322, 'HK', '1011', 'Proses verbal mulai dari penyelidikan, penyidikan sampai dengan vonis'),
(323, 'HK', '1012', 'Berkas pembelaan dan bantuan hukum'),
(324, 'HK', '1013', 'Telaah hukum dan opini hukum'),
(325, 'HK', '1020', 'Perdata'),
(326, 'HK', '1021', 'Proses gugatan sampai dengan putusan'),
(327, 'HK', '1022', 'Berkas pembelaan dan  bantuan hukum'),
(328, 'HK', '1023', 'Telaah hukum dan opini hukum'),
(329, 'HK', '1030', 'Tata Usaha Negara'),
(330, 'HK', '1031', 'Proses gugatan sampai dengan putusan'),
(331, 'HK', '1032', 'Berkas pembelaan dan  bantuan hukum'),
(332, 'HK', '1033', 'Telaah hukum dan opini  hukum'),
(333, 'HK', '1040', 'Arbitrase'),
(334, 'HK', '1041', 'Proses gugatan sampai dengan putusan'),
(335, 'HK', '1042', 'Berkas pembelaan dan  bantuan hukum'),
(336, 'HK', '1043', 'Telaah hukum dan opini  hukum'),
(338, 'OT', '000', 'ORGANISASI'),
(340, 'OT', '010', 'Pembentukan Organisasi'),
(341, 'OT', '020', 'Pengubahan Organisasi'),
(342, 'OT', '030', 'Pembubaran Organisasi'),
(343, 'OT', '040', 'Evaluasi Kelembagaan'),
(345, 'OT', '050', 'Uraian Jabatan'),
(347, 'OT', '100', 'TATA LAKSANA'),
(348, 'OT', '110', 'Standar Kompetensi Jabatan Struktural dan Fungsional'),
(350, 'OT', '120', 'Tata Hubungan  Kerja'),
(352, 'OT', '130', 'Sistem dan Prosedur'),
(355, 'HM', '000', 'KEPROTOKOLAN'),
(356, 'HM', '010', 'Penyelenggaraan acara kedinasan  (upacara, pelantikan, peresmian dan jamuan termasuk acara peringatan hari-hari besar)'),
(357, 'HM', '020', 'Agenda kegiatan pimpinan'),
(358, 'HM', '030', 'Kunjungan  dinas'),
(359, 'HM', '031', 'Kunjungan  dinas dalam dan luar  negeri'),
(360, 'HM', '032', 'Kunjungan  dinas pimpinan lembaga/instansi'),
(361, 'HM', '033', 'Kunjungan  dinas pejabat lain/pegawai'),
(362, 'HM', '040', 'Buku tamu'),
(363, 'HM', '050', 'Daftar nama/alamat kantor/pejabat'),
(364, 'HM', '100', 'LIPUTAN MEDIA MASSA'),
(366, 'HM', '200', 'PENYAJIAN  INFORMASI KELEMBAGAAN'),
(368, 'HM', '300', 'HUBUNGAN ANTAR LEMBAGA'),
(369, 'HM', '310', 'Hubungan antar lembaga pemerintah'),
(370, 'HM', '320', 'Hubungan dengan organisasi sosial/LSM'),
(371, 'HM', '330', 'Hubungan dengan perusahaan'),
(372, 'HM', '340', 'Hubungan dengan perguruan tinggi/sekolah : magang, Pendidikan Sistem Ganda,  Praktek Kerja Lapangan (PKL)'),
(373, 'HM', '350', 'Forum Kehumasan (Bakohumas/Perhumas)'),
(374, 'HM', '360', 'Hubungan dengan media massa :'),
(378, 'HM', '400', 'DENGAR PENDAPAT/HEARING DPR'),
(379, 'HM', '500', 'PENYIAPAN BAHAN MATERI  PIMPINAN'),
(380, 'HM', '600', 'PUBLIKASI MELALUI MEDIA CETAK MAUPUN ELEKTRONIK'),
(381, 'HM', '700', 'PAMERAN/SAYEMBARA/LOMBA/FESTIVAL, PEMBUATAN SPANDUK DAN IKLAN'),
(382, 'HM', '800', 'PENGHARGAAN/KENANG-KENANGAN/ CINDERA MATA'),
(383, 'HM', '900', 'UCAPAN TERIMA KASIH, UCAPAN SELAMAT,  BELASUNGKAWA, PERMOHONAN MAAF'),
(385, 'KA', '000', 'PENCETAKAN'),
(386, 'KA', '010', 'Penyiapan pembuatan buku kerja dan kalender BPS'),
(387, 'KA', '020', 'Penerimaan permintaan mencetak dan naskah yang akan dicetak.'),
(388, 'KA', '030', 'Menyusun perencanaan cetak'),
(389, 'KA', '040', 'Pencetakan naskah, surat, dokumen secara digital dan risograph'),
(390, 'KA', '100', 'PENGURUSAN SURAT'),
(391, 'KA', '110', 'Surat Masuk / Surat Keluar'),
(392, 'KA', '120', 'Penggunaan Aplikasi Surat Menyurat'),
(393, 'KA', '200', 'PENGELOLAAN ARSIP DINAMIS'),
(394, 'KA', '210', 'Penyusunan Sistem Arsip'),
(395, 'KA', '220', 'Penciptaan dan Pemberkasan Arsip'),
(396, 'KA', '230', 'Pengolahan Data Base menjadi lnformasi'),
(397, 'KA', '240', 'Alih Media'),
(398, 'KA', '300', 'PENYIMPANAN DAN PEMELIHARAAN ARSIP'),
(399, 'KA', '310', 'Daftar Arsip'),
(400, 'KA', '320', 'Pemeliharaan Arsip dan Ruang Penyimpanan (seperti kegiatan fumigasi)'),
(401, 'KA', '330', 'Daftar Pencarian Arsip'),
(402, 'KA', '340', 'Daftar Arsip Informasi Publik'),
(403, 'KA', '350', 'Daftar Arsip Vital/ Aset'),
(404, 'KA', '360', 'Layanan  Arsip (peminjam, pengguna arsip)'),
(405, 'KA', '370', 'Persetujuan Jadwal Retensi Arsip'),
(406, 'KA', '400', 'PEMINDAHAN ARSIP'),
(407, 'KA', '410', 'Pemindahan Arsip Inaktif'),
(408, 'KA', '420', 'Daftar Arsip yang Dipindahkan'),
(409, 'KA', '500', 'PEMUSNAHAN ARSIP YANG TIDAK BERNILAI GUNA'),
(410, 'KA', '510', 'Berita Acara Pemusnahan'),
(411, 'KA', '520', 'Daftar Arsip yang Dimusnahkan'),
(412, 'KA', '530', 'Rekomendasi/Pertimbangan/Pemusnahan Arsip dari ANRI'),
(413, 'KA', '540', 'Surat Keputusan Pemusnahan'),
(414, 'KA', '600', 'PENYERAHAN ARSIP STATIS'),
(415, 'KA', '610', 'Berita Acara Serah Terima Arsip'),
(416, 'KA', '620', 'Daftar Arsip yang Diserahkan'),
(417, 'KA', '700', 'PEMBINAAN KEARSIPAN'),
(418, 'KA', '710', 'Pembinaan Arsiparis'),
(419, 'KA', '720', 'Apresiasi/ Sosialisasi/ Penyuluhan Kearsipan, Diklat Profesi'),
(420, 'KA', '730', 'Bimbingan Teknis'),
(421, 'KA', '740', 'Penilaian dan sertifikasi SDM kearsipan'),
(422, 'KA', '750', 'Supervisi dan Monitoring'),
(423, 'KA', '760', 'Penilaian dan Akreditasi  Unit Kerja  Kearsipan'),
(424, 'KA', '770', 'Implementasi Pengelolaan Arsip Elektronik'),
(425, 'KA', '780', 'Penghargaan Kearsipan'),
(426, 'KA', '790', 'Pengawasan Kearsipan'),
(428, 'RT', '000', 'TELEKOMUNIKASI'),
(430, 'RT', '100', 'ADMINISTRASI PENGGUNAAN FASILITAS KANTOR'),
(432, 'RT', '200', 'PENGURUSAN KENDARAAN DINAS'),
(433, 'RT', '210', 'Pengurusan Surat Kendaraan Dinas'),
(434, 'RT', '220', 'Pemeliharaan dan Perbaikan'),
(435, 'RT', '230', 'Pengurusan Kehilangan dan Masalah Kendaraan'),
(436, 'RT', '300', 'PEMELIHARAAN GEDUNG DAN TAMAN'),
(437, 'RT', '310', 'Pertamanan / Lanscaping'),
(438, 'RT', '320', 'Penghijauan'),
(439, 'RT', '330', 'Perbaikan Gedung'),
(440, 'RT', '340', 'Perbaikan Rumah Dinas/Wisma'),
(441, 'RT', '350', 'Kebersihan Gedung dan Taman'),
(442, 'RT', '400', 'PENGELOLAAN JARINGAN LISTRIK, AIR, TELEPON DAN KOMPUTER'),
(443, 'RT', '410', 'Perbaikan / Pemeliharaan'),
(444, 'RT', '420', 'Pemasangan'),
(445, 'RT', '500', 'KETERTIBAN DAN KEAMANAN'),
(446, 'RT', '510', 'Pengamanan, penjagaan dan pengawasan terhadap pejabat, kantor, dan rumah dinas'),
(447, 'RT', '511', 'Daftar Nama Satuan Pengamanan'),
(448, 'RT', '512', 'Daftar Jaga/Daftar Piket'),
(449, 'RT', '513', 'Surat Izin Keluar Masuk Orang atau Barang'),
(450, 'RT', '520', 'Laporan Ketertiban dan Keamanan'),
(451, 'RT', '521', 'Kehilangan, Kerusakan, Kecelakaan, Gangguan'),
(452, 'RT', '600', 'ADMINISTRASI PENGELOLAAN PARKIR'),
(454, 'PL', '000', 'Rencana Kebutuhan Barang dan Jasa'),
(455, 'PL', '010', 'Usulan Unit Kerja'),
(456, 'PL', '020', 'Rencana Kebutuhan  lembaga Pusat/Daerah'),
(457, 'PL', '100', 'Berkas Perkenalan'),
(458, 'PL', '200', 'Pengadaan Barang'),
(459, 'PL', '210', 'Pengadaan/pembelian barang tidak melalui lelang (pengadaan langsung)'),
(463, 'PL', '220', 'Pengadaan/pembelian barang melalui lelang'),
(468, 'PL', '230', 'Perolehan barang melalui bantuan/hibah'),
(469, 'PL', '240', 'Pengadaan barang melalui tukar menukar'),
(470, 'PL', '250', 'Pemanfaatan barang melalui pinjam pakai'),
(471, 'PL', '260', 'Pemanfaatan barang melalui sewa'),
(472, 'PL', '270', 'Pemanfaatan barang melalui kerjasama pemanfaatan'),
(473, 'PL', '280', 'Pemanfaatan barang melalui bangun serah guna/bangun serah guna'),
(474, 'PL', '300', 'Pengadaan Jasa'),
(476, 'PL', '400', 'Laporan kemajuan pelaksanaan belanja modal'),
(477, 'PL', '500', 'INVENTARISASI'),
(478, 'PL', '510', 'Inventarisasi Ruangan/ Gedung/Bangunan'),
(479, 'PL', '511', 'Daftar Inventaris Ruangan (DIR)'),
(480, 'PL', '512', 'Buku Inventaris/Pembantu Inventaris Ruangan'),
(481, 'PL', '520', 'Inventarisasi Barang'),
(482, 'PL', '521', 'Daftar Opname Fisik Barang lnventaris (DOFBI)'),
(483, 'PL', '522', 'Daftar Inventaris Barang (DIB)'),
(484, 'PL', '523', 'Daftar Kartu Inventaris Barang'),
(485, 'PL', '524', 'Buku Inventaris/Pembantu Inventaris Barang'),
(486, 'PL', '530', 'Penyusunan Laporan Tahunan Inventaris BMN'),
(487, 'PL', '540', 'Sensus BMN'),
(488, 'PL', '600', 'PENYIMPANAN'),
(489, 'PL', '610', 'Penatausahaan Penyimpanan Barang/Publikasi'),
(490, 'PL', '611', 'Tanda terima/ surat pengantar/ surat pengiriman barang'),
(491, 'PL', '612', 'Surat Pernyataan harga dan mutu barang'),
(492, 'PL', '613', 'Berita Acara serah terima barang hasil pengadaan'),
(493, 'PL', '614', 'Buku Penerimaan'),
(494, 'PL', '615', 'Buku Persediaan barang/ kartu stock barang'),
(495, 'PL', '616', 'Kartu barang/ kartu gudang'),
(496, 'PL', '620', 'Penyusunan Laporan persedian barang'),
(497, 'PL', '700', 'PENYALURAN'),
(498, 'PL', '710', 'Penatausahaan penyaluran barang/publikasi'),
(499, 'PL', '711', 'Surat Permintaan dari unit kerja/formulir permintaan'),
(500, 'PL', '712', 'Surat Perintah Mengeluarkan Barang (SPMB)'),
(501, 'PL', '713', 'Surat Perintah Mengeluarkan barang Inventaris'),
(502, 'PL', '714', 'Berita Acara Serah terima Distribusi Barang'),
(503, 'PL', '800', 'PENGHAPUSAN BMN'),
(504, 'PL', '810', 'Penghapusan Barang Bergerak/Barang Inventaris Kantor'),
(512, 'PL', '900', 'BUKTI-BUKTI  KEPEMILIKAN DAN KELENGKAPAN BMN'),
(520, 'DL', '000', 'PERENCANAAN DIKLAT'),
(521, 'DL', '010', 'Analisa Kebutuhan Penyelenggaraan Diklat'),
(522, 'DL', '020', 'Sistem dan Metode'),
(523, 'DL', '030', 'Kurikulum'),
(524, 'DL', '040', 'Bahan Ajar/ Modul'),
(525, 'DL', '050', 'Konsultasi Penyelenggaraan Diklat'),
(526, 'DL', '100', 'AKREDITASI LEMBAGA DIKLAT'),
(527, 'DL', '110', 'Surat Permohonan Akreditasi'),
(528, 'DL', '120', 'Laporan Hasil Verifikasi Lapangan'),
(529, 'DL', '130', 'Berita Acara Rapat Verifikasi'),
(530, 'DL', '140', 'Berita Acara Rapat Tim Penilai'),
(531, 'DL', '150', 'Surat Keputusan Penetapan Akreditasi'),
(532, 'DL', '160', 'Sertifikat Akreditasi'),
(533, 'DL', '170', 'Laporan Akreditasi Lembaga Diklat'),
(534, 'DL', '200', 'PENYELENGGARAAN DIKLAT'),
(535, 'DL', '210', 'Prajabatan'),
(536, 'DL', '220', 'Kepemimpinan'),
(537, 'DL', '230', 'Teknis'),
(538, 'DL', '240', 'Fungsional'),
(539, 'DL', '250', 'Evaluasi Pasca Diklat'),
(540, 'DL', '300', 'SERTIFIKASI SUMBERDAYA MANUSIA KEDIKLATAN'),
(542, 'DL', '400', 'SISTEM INFORMASI DIKLAT'),
(543, 'DL', '410', 'Data Lembaga  Diklat'),
(544, 'DL', '420', 'Data Prasarana Diklat'),
(545, 'DL', '430', 'Data Sarana Diklat'),
(546, 'DL', '440', 'Data Pengelola Diklat'),
(547, 'DL', '450', 'Data Penyelenggara Diklat'),
(548, 'DL', '460', 'Data Widyaiswara'),
(549, 'DL', '470', 'Data Program Diklat'),
(550, 'DL', '500', 'REGISTRASI  SERTIFIKASI/STTPL  Peserta Diklat'),
(551, 'DL', '510', 'Surat Permohonan Kode Registrasi'),
(552, 'DL', '520', 'Buku Registrasi'),
(553, 'DL', '530', 'Surat Penyampaian Kode  Registrasi'),
(554, 'DL', '600', 'EVALUASI PENYELENGGARAAN DIKLAT'),
(555, 'DL', '610', 'Evaluasi Materi Penyelenggaraan'),
(556, 'DL', '620', 'Evaluasi Pengajar / Instruktur/ Fasilitator'),
(557, 'DL', '630', 'Evaluasi Peserta'),
(558, 'DL', '640', 'Evaluasi Sarana dan Prasarana'),
(559, 'DL', '650', 'Evaluasi Alumni  Peserta'),
(560, 'DL', '660', 'Laporan  Penyelenggaran'),
(561, 'DL', '670', 'Evaluasi Alumni Diklat'),
(563, 'PK', '000', 'PENYIMPANAN DEPOSIT BAHAN PUSTAKA'),
(564, 'PK', '010', 'Bukti Penerimaan Koleksi Bahan Pustaka Deposit'),
(565, 'PK', '020', 'Administrasi Pengolahan Deposit Bahan Pustaka'),
(566, 'PK', '100', 'PENGADAAN BAHAN PUSTAKA'),
(567, 'PK', '110', 'Buku Induk Koleksi'),
(568, 'PK', '120', 'Daftar Buku Terseleksi'),
(569, 'PK', '130', 'Daftar Buku Dalam Pemesanan'),
(570, 'PK', '140', 'Daftar Buku Dalam Permintaan'),
(571, 'PK', '200', 'PENGOLAHAN BAHAN PUSTAKA'),
(572, 'PK', '210', 'Lembar  Kerja  Pengolahan Bahan Pustaka (buram, pengkatalogan)'),
(573, 'PK', '220', 'Shell List/Jajaran Kartu Utama (master list)'),
(574, 'PK', '230', 'Daftar Tambahan Buku (assesion list)'),
(575, 'PK', '240', 'Daftar/Jajaran Kendali (subjek dan pengarang)'),
(576, 'PK', '300', 'LAYANAN JASA PERPUSTAKAAN DAN INFORMASI'),
(577, 'PK', '310', 'Data dan statistic anggota,  pengunjung dan peminjaman bahan pustaka'),
(578, 'PK', '320', 'Pertanyaan, Rujukan dan Jawaban'),
(579, 'PK', '400', 'PRESERVASI BAHAN PUSTAKA'),
(580, 'PK', '410', 'Survei Kondisi Bahan Pustaka'),
(581, 'PK', '420', 'Reprografi Bahan Pustaka'),
(582, 'PK', '500', 'PEMBINAAN PERPUSTAKAAN'),
(583, 'PK', '510', 'Bimbingan Teknis'),
(584, 'PK', '520', 'Penyuluhan'),
(585, 'PK', '530', 'Sosialisasi'),
(587, 'IF', '000', 'RENCANA STRATEGIS MASTERPLAN PEMBANGUNAN SISTEM INFORMASI'),
(588, 'IF', '100', 'DOKUMENTASI ARSITEKTUR'),
(589, 'IF', '110', 'Sistern  lnformasi'),
(590, 'IF', '120', 'Sistem Aplikasi'),
(591, 'IF', '130', 'Infrastruktur'),
(592, 'IF', '200', 'PEREKAMAN DAN PEMUTAKHIRAN DATA'),
(593, 'IF', '210', 'Formulir Isian'),
(594, 'IF', '220', 'Daftar Petugas Perekaman'),
(595, 'IF', '230', 'Jadwal Pelaksanaan'),
(596, 'IF', '240', 'Laporan Hasil Perekaman dan Pemutakhiran Data'),
(597, 'IF', '300', 'MIGRASI SISTEM APLIKASI DATA'),
(598, 'IF', '400', 'DOKUMEN HOSTING'),
(599, 'IF', '410', 'Formulir Permintaan Hosting'),
(600, 'IF', '420', 'Layanan Hasil Uji Kelayakan'),
(601, 'IF', '430', 'Laporan Pelaksanaan Hosting'),
(602, 'IF', '500', 'LAYANAN BACK-UP DATA DIGITAL'),
(604, 'PW', '000', 'RENCANA PENGAWASAN'),
(605, 'PW', '010', 'Rencana Strategis Pengawasan'),
(606, 'PW', '020', 'Rencana Kerja Tahunan'),
(607, 'PW', '030', 'Rencana Kinerja Tahunan'),
(608, 'PW', '040', 'Penetapan Kinerja Tahunan'),
(609, 'PW', '050', 'Rakor Pengawasan Tingkat Nasional'),
(610, 'PW', '100', 'PELAKSANAAN PENGAWASAN'),
(611, 'PW', '110', 'Naskah-naskah yang berkaitan dengan audit mulai dari surat penugasan sampai dengan surat menyurat'),
(612, 'PW', '120', 'Laporan Hasil Audit (LHA), Laporan Hasil Pemeriksaaan Operasional (LHPO), Laporan Hasil Evaluasi (LHE), Laporan Akuntan (LA), Laporan Auditor Independent (LAI) yang memerlukan Tindak Lanjut (TL).'),
(613, 'PW', '130', 'Laporan Basil Audit Investigasi (LHAI) yang mengandung unsur Tindak Pidana Korupsi (TPK) dan memerlukan tindak lanjut'),
(614, 'PW', '140', 'Laporan Perkembangan Penanganan Surat Pengaduan Masyarakat'),
(615, 'PW', '150', 'Laporan Pemutakhiran Data'),
(616, 'PW', '160', 'Laporan Perkembangan BMN'),
(617, 'PW', '170', 'Laporan kegiatan pendampingan penyusunan laporan keuangan dan Reviu Departmen/LPND'),
(618, 'PW', '180', 'Good Corporate Governance (GCG)'),
(620, 'TS', '000', 'PENYUSUNAN RENCANA KEGIATAN BIDANG TRANSFORMASI STATISTIK {TOR)'),
(621, 'TS', '010', 'Transformasi Proses Bisnis Statistik'),
(622, 'TS', '020', 'Transformasi TIK dan Komunikasi'),
(623, 'TS', '030', 'Transformasi Manajemen Sumberdaya Manusia dan Kelembagaan'),
(624, 'TS', '100', 'PENYUSUNAN BAHAN TERKAIT TRANSFORMASI STATISTIK'),
(625, 'TS', '110', 'Rencana Sarana dan Prasarana Transformasi Statistik'),
(626, 'TS', '120', 'Statistical Busines Frame Work and Architecture (SBFA)'),
(627, 'TS', '130', 'Analysis Document'),
(628, 'TS', '140', 'CSL'),
(629, 'TS', '150', 'BPR'),
(630, 'TS', '160', 'Sosialisasi, internalisasi, workshop terkait kegiatan transformasi'),
(631, 'TS', '170', 'Deliverables'),
(632, 'TS', '200', 'MANAJEMEN PERUBAHAN'),
(633, 'TS', '210', 'Steering Committee (Dewan Pengarah)'),
(634, 'TS', '220', 'Change Agent'),
(635, 'TS', '230', 'Change Leader'),
(636, 'TS', '240', 'Change Champion'),
(637, 'TS', '250', 'Working Group'),
(638, 'TS', '260', 'Evaluasi dan Monitoring Perkembangan Program STATCAP CERDAS; Sensus Daring/ CPOC'),
(639, 'TS', '270', 'Sosialisasi, Internalisasi, Workshop terkait kegiatan Manajemen Perubahan, Kick of Meeting'),
(640, 'TS', '300', 'KETERPADUAN TRANSFORMASI'),
(641, 'TS', '310', 'Mendukung lmplementasi Transformasi : CAPI SAKERNAS, Continous Survey'),
(642, 'TS', '400', 'LAPORAN TRANSFORMASI  STATISTIK'),
(643, 'TS', '410', 'Laporan Kemajuan'),
(644, 'TS', '420', 'Laporan Bulanan'),
(645, 'TS', '430', 'Laporan Triwulanan'),
(646, 'TS', '440', 'Laporan Tahunan'),
(648, 'PS', '000', 'Pengkajian dan Pengusulan Kebijakan'),
(649, 'PS', '100', 'Penyiapan Kebijakan'),
(650, 'PS', '200', 'Masukan dan Dukungan dalam penyusunan kebijakan'),
(651, 'PS', '300', 'Pengembangan desain dan standardisasi'),
(652, 'PS', '400', 'Penerapan Norma, Standar, Prosedur, dan Kriteria (NSPK)'),
(654, 'SS', '000', 'PERENCANAAN'),
(655, 'SS', '010', 'Master Plan dan Network Planning'),
(656, 'SS', '020', 'Perumusan dan Penyusunan Bahan'),
(657, 'SS', '021', 'Penyiapan bahan penyusunan rancangan sensus'),
(658, 'SS', '022', 'Penyusunan metode pencacahan sensus'),
(659, 'SS', '023', 'Penentuan volume sensus'),
(660, 'SS', '024', 'Penyusunan desain penarikan sampel'),
(661, 'SS', '025', 'Penyusunan kerangka sampel'),
(662, 'SS', '030', 'Studi pendahuluan (desk study)'),
(663, 'SS', '100', 'PERSIAPAN SENSUS'),
(664, 'SS', '110', 'Rancangan Organisasi'),
(665, 'SS', '120', 'Penyusunan Kuesioner'),
(666, 'SS', '130', 'Penyusunan Konsep dan Definisi'),
(667, 'SS', '140', 'Penyusunan Metodologi (organisasi, lapangan, ukuran statistik)'),
(668, 'SS', '150', 'Penyusunan Buku Pedoman (pencacahan, pengawasan, pengolahan)'),
(669, 'SS', '160', 'Penyusunan Peta Wilayah Kerja dan Muatan Peta Wilayah'),
(670, 'SS', '170', 'Penyusunan Pedoman Sosialisasi'),
(671, 'SS', '180', 'Penyusunan Program Pengolahan (rule validasi pemeriksaan entri tabulasi)'),
(672, 'SS', '190', 'Koordinasi Intern/Ekstern'),
(673, 'SS', '200', 'PELATIHAN/UJICOBA'),
(674, 'SS', '210', 'Pelatihan Instruktur'),
(675, 'SS', '220', 'Pelatihan Petugas'),
(676, 'SS', '230', 'Pelatihan Petuhas Pengolahan'),
(677, 'SS', '240', 'Perancangan Tabel'),
(678, 'SS', '250', 'Pelaksanaan Ujicoba Kuisioner Sensus (meliputi realibilitas kuesioner dan sistem pengolahan'),
(679, 'SS', '260', 'Pelaksanaan Ujicoba Kuesioner Metodologi Sensus (meliputi ujicoba pelaksanaan pencacahan, organisasi, dan jumlah sampel)'),
(680, 'SS', '300', 'PELAKSANAAN LAPANGAN'),
(681, 'SS', '310', 'Listing'),
(682, 'SS', '320', 'Pemilihan Sampel'),
(683, 'SS', '330', 'Pengumpulan Data'),
(684, 'SS', '340', 'Pemeriksaan Data'),
(685, 'SS', '350', 'Pengawasan Lapangan'),
(686, 'SS', '360', 'Monitoring Kualitas'),
(687, 'SS', '400', 'PENGOLAHAN'),
(688, 'SS', '410', 'Pengolahan Dokumen (penerimaan/pengiriman, pengelompokan/batching)'),
(689, 'SS', '420', 'Pemeriksaan Dokumen dan Pengkodean (editing/coding)'),
(690, 'SS', '430', 'Perekaman Data (entri/scanner)'),
(691, 'SS', '440', 'Tabulasi Data'),
(692, 'SS', '450', 'Pemeriksaan Tabulasi'),
(693, 'SS', '560', 'Laporan Konsistensi Tabulasi'),
(694, 'SS', '500', 'ANALISIS DAN PENYAJIAN HASIL SENSUS'),
(695, 'SS', '510', 'Pembahasan Angka Hasil Pengolahan'),
(696, 'SS', '520', 'Penyusunan Angka Sementara'),
(697, 'SS', '530', 'Penyusunan Angka Tetap'),
(698, 'SS', '540', 'Penyusunan/Pembahasan Draf Publikasi'),
(699, 'SS', '550', 'Analisis Data Sensus'),
(700, 'SS', '560', 'Penyusunan Diseminasi Hasil Sensus'),
(701, 'SS', '600', 'DISEMINASI HASIL SENSUS'),
(702, 'SS', '610', 'Penyusunan Bahan Diseminasi'),
(706, 'SS', '620', 'Sosialisasi hasil sensus melalui berbagai media'),
(707, 'SS', '630', 'Layanan Promosi Statistik'),
(709, 'VS', '000', 'PERENCANAAN'),
(710, 'VS', '010', 'Master Plan dan Network Planning'),
(711, 'VS', '020', 'Perumusan dan Penyusunan Bahan'),
(712, 'VS', '021', 'Penyiapan bahan penyusunan rancangan survei'),
(713, 'VS', '022', 'Penyusunan metode pencacahan Survei'),
(714, 'VS', '023', 'Penentuan volume Survei'),
(715, 'VS', '024', 'Penyusunan desain penarikan sampel'),
(716, 'VS', '025', 'Penyusunan kerangka sampel'),
(717, 'VS', '030', 'Studi pendahuluan (desk study)'),
(718, 'VS', '100', 'PERSIAPAN SURVEI'),
(719, 'VS', '110', 'Rancangan Organisasi'),
(720, 'VS', '120', 'Penyusunan Kuesioner'),
(721, 'VS', '130', 'Penyusunan Konsep dan Definisi'),
(722, 'VS', '140', 'Penyusunan Metodologi (organisasi, lapangan, ukuran statistik)'),
(723, 'VS', '150', 'Penyusunan Buku Pedoman (pencacahan, pengawasan, pengolahan)'),
(724, 'VS', '160', 'Penyusunan Peta Wilayah Kerja dan Muatan Peta Wilayah'),
(725, 'VS', '170', 'Penyusunan Pedoman Sosialisasi'),
(726, 'VS', '180', 'Penyusunan Program Pengolahan (rule validasi pemeriksaan entri tabulasi)'),
(727, 'VS', '190', 'Koordinasi Intern/Ekstern'),
(728, 'VS', '200', 'PELATIHAN/UJICOBA'),
(729, 'VS', '210', 'Pelatihan Instruktur'),
(730, 'VS', '220', 'Pelatihan Petugas'),
(731, 'VS', '230', 'Pelatihan Petugas Pengolahan'),
(732, 'VS', '240', 'Perancangan Tabel'),
(733, 'VS', '250', 'Pelaksanaan Ujicoba Kuesioner Survei (meliputi realibilitas kuesioner dan sistem pengolahan'),
(734, 'VS', '260', 'Pelaksanaan Ujicoba Kuesioner Metodologi Sensus (meliputi ujicoba pelaksanaan pencacahan, organisasi, dan jumlah sampel)'),
(735, 'VS', '300', 'PELAKSANAAN LAPANGAN'),
(736, 'VS', '310', 'Listing'),
(737, 'VS', '320', 'Pemilihan Sampel'),
(738, 'VS', '330', 'Pengumpulan Data'),
(739, 'VS', '340', 'Pemeriksaan Data'),
(740, 'VS', '350', 'Pengawasan Lapangan'),
(741, 'VS', '360', 'Monitoring Kualitas'),
(742, 'VS', '400', 'PENGOLAHAN'),
(743, 'VS', '410', 'Pengelolaan Dokumen (penerimaan/pengiriman, pengelompokan/batching)'),
(744, 'VS', '420', 'Pemeriksaan Dokumen dan Pengkodean (editing/coding)'),
(745, 'VS', '430', 'Perekaman Data (entri/scanner)'),
(746, 'VS', '440', 'Tabulasi Data (entri/scanner)'),
(747, 'VS', '450', 'Pemeriksaan Tabulasi'),
(748, 'VS', '460', 'Laporan Konsistensi Tabulasi'),
(749, 'VS', '500', 'ANALISIS DAN PENYAJIAN HASIL SURVEI'),
(750, 'VS', '510', 'Pembahasan Angka Hasil Pengolahan'),
(751, 'VS', '520', 'Penyusunan Angka Sementara'),
(752, 'VS', '530', 'Penyusunan Angka Proyeksi/Ramalan'),
(753, 'VS', '540', 'Penyusunan Angka Tetap'),
(754, 'VS', '550', 'Penyusunan/Pembahasan Draf Publikasi'),
(755, 'VS', '560', 'Analisis Data Survei'),
(756, 'VS', '570', 'Penyusunan Diseminasi Hasil Survei'),
(757, 'VS', '600', 'DISEMINASI HASIL SURVEI'),
(758, 'VS', '610', 'Penyusun Bahan Diseminasi'),
(762, 'VS', '620', 'Sosialisasi hasil survei melalui berbagai media'),
(763, 'VS', '630', 'Layanan Promosi Statistik'),
(765, 'KS', '000', 'KOMPILASI DATA'),
(766, 'KS', '100', 'ANALISIS DATA'),
(767, 'KS', '200', 'PENYUSUNAN PUBLIKASI');

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `id_team` bigint NOT NULL AUTO_INCREMENT,
  `nama_team` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `panggilan_team` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_team`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teamleader`
--

DROP TABLE IF EXISTS `teamleader`;
CREATE TABLE IF NOT EXISTS `teamleader` (
  `id_teamleader` bigint NOT NULL AUTO_INCREMENT,
  `nama_teamleader` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_team` bigint NOT NULL,
  `leader_status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_teamleader`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zooms`
--

DROP TABLE IF EXISTS `zooms`;
CREATE TABLE IF NOT EXISTS `zooms` (
  `id_zooms` bigint NOT NULL AUTO_INCREMENT,
  `fk_agenda` bigint NOT NULL,
  `jenis_zoom` tinyint NOT NULL,
  `jenis_surat` tinyint NOT NULL,
  `fk_surat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proposer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_zooms`)
) ENGINE=MyISAM AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zoomstype`
--

DROP TABLE IF EXISTS `zoomstype`;
CREATE TABLE IF NOT EXISTS `zoomstype` (
  `id_zoomstype` int NOT NULL AUTO_INCREMENT,
  `nama_zoomstype` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kuota` int NOT NULL DEFAULT '100',
  `active` tinyint NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_zoomstype`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zoomstype`
--

INSERT INTO `zoomstype` (`id_zoomstype`, `nama_zoomstype`, `kuota`, `active`, `timestamp`) VALUES
(1, 'Zoom A', 100, 1, '2024-02-02 21:39:37'),
(2, 'Zoom B', 100, 1, '2024-02-02 21:39:37');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
