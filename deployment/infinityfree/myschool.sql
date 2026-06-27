-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: myshcool
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bab_mapel`
--

DROP TABLE IF EXISTS `bab_mapel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bab_mapel` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kelas_mapel_id` bigint unsigned NOT NULL,
  `nama_bab` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bab_mapel_kelas_mapel_id_foreign` (`kelas_mapel_id`),
  CONSTRAINT `bab_mapel_kelas_mapel_id_foreign` FOREIGN KEY (`kelas_mapel_id`) REFERENCES `kelas_mapel` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bab_mapel`
--

LOCK TABLES `bab_mapel` WRITE;
/*!40000 ALTER TABLE `bab_mapel` DISABLE KEYS */;
INSERT INTO `bab_mapel` VALUES (1,5,'Thoharoh',1,'2026-06-06 23:50:22','2026-06-06 23:50:22');
/*!40000 ALTER TABLE `bab_mapel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('sdtq-nurul-ilmi-cache-5c785c036466adea360111aa28563bfd556b5fba','i:1;',1780816680),('sdtq-nurul-ilmi-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1780816680;',1780816680);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gurus`
--

DROP TABLE IF EXISTS `gurus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gurus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_kk` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gurus_nip_unique` (`nip`),
  UNIQUE KEY `gurus_nik_unique` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gurus`
--

LOCK TABLES `gurus` WRITE;
/*!40000 ALTER TABLE `gurus` DISABLE KEYS */;
INSERT INTO `gurus` VALUES (1,'198501010001','Ahmad Fauzan, S.Pd','1985-01-01','081234567801','ahmad@myschool.com','3201010101010001','3201010101010002','guru1.jpg',NULL,NULL),(2,'198602020002','Siti Aisyah, S.Pd','1986-02-02','081234567802','siti@myschool.com','3201010101010003','3201010101010004','guru2.jpg',NULL,NULL),(3,'198703030003','Budi Santoso, S.Pd','1987-03-03','081234567803','budi@myschool.com','3201010101010005','3201010101010006','guru3.jpg',NULL,NULL),(4,'198804040004','Dewi Lestari, S.Pd','1988-04-04','081234567804','dewi@myschool.com','3201010101010007','3201010101010008','guru4.jpg',NULL,NULL),(5,'198905050005','Rudi Hartono, S.Pd','1989-05-05','081234567805','rudi@myschool.com','3201010101010009','3201010101010010','guru5.jpg',NULL,NULL);
/*!40000 ALTER TABLE `gurus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_siswa` int NOT NULL DEFAULT '0',
  `tahun_pelajaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `nama_penambah` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guru_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_user_id_foreign` (`user_id`),
  KEY `kelas_guru_id_foreign` (`guru_id`),
  CONSTRAINT `kelas_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kelas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES (1,'Kelas 1A',28,'2020 - Ganjil',NULL,'2026-06-06 21:31:22',1,'putra',1),(2,'Kelas 1B',30,'2020 - Ganjil',NULL,'2026-06-06 21:31:30',1,'putra',2),(3,'Kelas 2A',27,'2020 - Ganjil',NULL,NULL,1,'putra',3),(4,'Kelas 2B',29,'2020 - Ganjil',NULL,NULL,1,'putra',4),(5,'Kelas 3A',31,'2020 - Ganjil',NULL,NULL,1,'putra',5),(6,'Kelas 3B',30,'2020 - Ganjil',NULL,NULL,1,'putra',1),(7,'Kelas 4A',26,'2020 - Ganjil',NULL,NULL,1,'putra',2),(8,'Kelas 4B',28,'2020 - Ganjil',NULL,NULL,1,'putra',3),(9,'Kelas 5A',30,'2020 - Ganjil',NULL,NULL,1,'putra',4),(10,'Kelas 5B',27,'2020 - Ganjil',NULL,NULL,1,'putra',5),(11,'Kelas 6A',25,'2020 - Ganjil',NULL,NULL,1,'putra',1),(12,'Kelas 6B',24,'2020 - Ganjil',NULL,NULL,1,'putra',2);
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas_mapel`
--

DROP TABLE IF EXISTS `kelas_mapel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas_mapel` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kelas_id` bigint unsigned NOT NULL,
  `mapel_id` bigint unsigned NOT NULL,
  `jumlah_uh` tinyint NOT NULL DEFAULT '3',
  `guru_id` bigint unsigned NOT NULL,
  `tahun_pelajaran_id` bigint unsigned NOT NULL,
  `jam_pelajaran` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kelas_mapel_unique` (`kelas_id`,`mapel_id`,`tahun_pelajaran_id`),
  KEY `kelas_mapel_mapel_id_foreign` (`mapel_id`),
  KEY `kelas_mapel_guru_id_foreign` (`guru_id`),
  KEY `kelas_mapel_tahun_pelajaran_id_foreign` (`tahun_pelajaran_id`),
  CONSTRAINT `kelas_mapel_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_mapel_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_mapel_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_mapel_tahun_pelajaran_id_foreign` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas_mapel`
--

LOCK TABLES `kelas_mapel` WRITE;
/*!40000 ALTER TABLE `kelas_mapel` DISABLE KEYS */;
INSERT INTO `kelas_mapel` VALUES (2,1,11,3,3,1,0,'2026-06-06 23:49:57','2026-06-06 23:49:57'),(3,1,12,3,1,1,0,'2026-06-06 23:49:57','2026-06-06 23:49:57'),(4,1,13,3,4,1,0,'2026-06-06 23:49:57','2026-06-06 23:49:57'),(5,1,1,4,2,1,0,'2026-06-06 23:49:57','2026-06-07 00:12:44'),(6,1,2,3,5,1,0,'2026-06-06 23:49:57','2026-06-06 23:49:57'),(7,1,16,3,3,1,0,'2026-06-06 23:49:57','2026-06-06 23:49:57');
/*!40000 ALTER TABLE `kelas_mapel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mata_pelajaran`
--

DROP TABLE IF EXISTS `mata_pelajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mata_pelajaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_rapot` enum('dinniyyah','akademik','tahfidz') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'akademik',
  `kkm` int NOT NULL DEFAULT '75',
  `kelompok` enum('A','B','C') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mata_pelajaran_kode_mapel_unique` (`kode_mapel`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mata_pelajaran`
--

LOCK TABLES `mata_pelajaran` WRITE;
/*!40000 ALTER TABLE `mata_pelajaran` DISABLE KEYS */;
INSERT INTO `mata_pelajaran` VALUES (1,'PAI','Pendidikan Agama Islam','akademik',75,'A',1,NULL,NULL),(2,'PPKN','Pendidikan Pancasila dan Kewarganegaraan','akademik',75,'A',2,NULL,NULL),(3,'BIND','Bahasa Indonesia','akademik',75,'A',3,NULL,NULL),(4,'MTK','Matematika','akademik',75,'A',4,NULL,NULL),(5,'IPAS','Ilmu Pengetahuan Alam dan Sosial','akademik',75,'A',5,NULL,NULL),(6,'SBDP','Seni Budaya dan Prakarya','akademik',75,'B',6,NULL,NULL),(7,'PJOK','Pendidikan Jasmani Olahraga dan Kesehatan','akademik',75,'B',7,NULL,NULL),(8,'BING','Bahasa Inggris','akademik',75,'B',8,NULL,NULL),(9,'MULOK','Muatan Lokal','akademik',75,'B',9,NULL,NULL),(10,'TIK','Teknologi Informasi dan Komunikasi','akademik',75,'B',10,NULL,NULL),(11,'AQID','Aqidah Akhlak','dinniyyah',75,'C',11,NULL,NULL),(12,'FIQH','Fiqih','dinniyyah',75,'C',12,NULL,NULL),(13,'QHAD','Quran Hadits','dinniyyah',75,'C',13,NULL,NULL),(14,'SKI','Sejarah Kebudayaan Islam','dinniyyah',75,'C',14,NULL,NULL),(15,'ARAB','Bahasa Arab','dinniyyah',75,'C',15,NULL,NULL),(16,'THF1','Tahfidz Al-Quran','tahfidz',75,'C',16,NULL,NULL);
/*!40000 ALTER TABLE `mata_pelajaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_04_29_023811_add_phone_to_users_table',1),(5,'2026_04_29_034536_create_kelas_table',1),(6,'2026_04_29_040035_add_user_id_to_kelas_table',1),(7,'2026_04_29_080531_add_tahun_pelajaran_to_kelas_table',1),(8,'2026_05_07_061939_create_gurus_table',1),(9,'2026_05_07_082109_add_guru_id_to_kelas_table',1),(10,'2026_05_13_030807_create_siswas_table',1),(13,'2026_05_19_044544_create_tahun_pelajaran_table',2),(14,'2026_05_19_044545_create_mata_pelajaran_table',2),(15,'2026_05_19_044546_create_nilai_table',3),(16,'2026_05_21_054611_update_nilai_table_use_kelas_mapel',3),(17,'2026_05_24_065255_add_nilai_fields_to_nilai_table',3),(18,'2026_05_21_012903_add_jenis_rapot_to_mata_pelajaran_table',4),(19,'2026_05_21_012906_create_kelas_mapel_table',4),(20,'2026_05_24_040148_add_role_to_users_table',3),(21,'2026_05_24_091042_increase_tahun_pelajaran_length_on_kelas_table',5),(22,'2026_05_25_041027_add_uh_to_nilai_table',5),(23,'2026_05_26_044054_create_bab_mapel_table',5),(24,'2026_05_26_044128_create_sub_bab_mapel_table',5),(25,'2026_05_26_044223_create_nilai_harian_table',5),(26,'2026_05_29_064304_add_user_id_to_nilai_harian_table',5),(27,'2026_05_29_071436_add_nama_fields_to_nilai_harian_table',5),(28,'2026_05_30_205023_add_jumlah_uh_to_kelas_mapel_table',3),(29,'2026_05_30_232707_add_nisn_to_siswas_table',6),(30,'2026_06_03_024502_create_sessions_table',6),(31,'2026_06_03_024502_create_sessions_table',6),(32,'2026_06_03_052850_add_jenis_rapot_to_mata_pelajaran_table',6),(34,'2026_06_07_070150_add_uh_columns_to_nilai_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nilai`
--

DROP TABLE IF EXISTS `nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `kelas_mapel_id` bigint unsigned NOT NULL,
  `tahun_pelajaran_id` bigint unsigned NOT NULL,
  `guru_id` bigint unsigned DEFAULT NULL,
  `uh1` decimal(5,2) DEFAULT NULL,
  `uh2` decimal(5,2) DEFAULT NULL,
  `uh3` decimal(5,2) DEFAULT NULL,
  `uh4` decimal(5,2) DEFAULT NULL,
  `uh5` decimal(5,2) DEFAULT NULL,
  `uh6` decimal(5,2) DEFAULT NULL,
  `rata_uh` decimal(5,2) DEFAULT NULL,
  `rph` int DEFAULT '0',
  `pts` int DEFAULT '0',
  `pas` int DEFAULT '0',
  `hpa` decimal(5,2) DEFAULT NULL,
  `predikat` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nilai_siswa_id_foreign` (`siswa_id`),
  CONSTRAINT `nilai_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai`
--

LOCK TABLES `nilai` WRITE;
/*!40000 ALTER TABLE `nilai` DISABLE KEYS */;
INSERT INTO `nilai` VALUES (1,21,5,1,2,100.00,100.00,100.00,90.00,NULL,NULL,97.50,98,0,0,NULL,NULL,'2026-06-07 00:12:05','2026-06-07 00:12:49');
/*!40000 ALTER TABLE `nilai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nilai_harian`
--

DROP TABLE IF EXISTS `nilai_harian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai_harian` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `sub_bab_mapel_id` bigint unsigned NOT NULL,
  `tahun_pelajaran_id` bigint unsigned NOT NULL,
  `nilai` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `nama_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas_mapel_id` bigint unsigned DEFAULT NULL,
  `nama_mapel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nilai_harian_unique` (`siswa_id`,`sub_bab_mapel_id`,`tahun_pelajaran_id`),
  KEY `nilai_harian_sub_bab_mapel_id_foreign` (`sub_bab_mapel_id`),
  KEY `nilai_harian_tahun_pelajaran_id_foreign` (`tahun_pelajaran_id`),
  KEY `nilai_harian_user_id_foreign` (`user_id`),
  KEY `nilai_harian_kelas_mapel_id_foreign` (`kelas_mapel_id`),
  CONSTRAINT `nilai_harian_kelas_mapel_id_foreign` FOREIGN KEY (`kelas_mapel_id`) REFERENCES `kelas_mapel` (`id`),
  CONSTRAINT `nilai_harian_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_harian_sub_bab_mapel_id_foreign` FOREIGN KEY (`sub_bab_mapel_id`) REFERENCES `sub_bab_mapel` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_harian_tahun_pelajaran_id_foreign` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_harian_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai_harian`
--

LOCK TABLES `nilai_harian` WRITE;
/*!40000 ALTER TABLE `nilai_harian` DISABLE KEYS */;
INSERT INTO `nilai_harian` VALUES (1,21,1,1,100.00,'2026-06-06 23:51:18','2026-06-06 23:51:18',2,NULL,5,NULL),(2,21,2,1,80.00,'2026-06-06 23:51:20','2026-06-06 23:51:20',2,NULL,5,NULL);
/*!40000 ALTER TABLE `nilai_harian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswas`
--

DROP TABLE IF EXISTS `siswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelas_id` bigint unsigned DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan_ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan_ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penghasilan_ayah` int DEFAULT NULL,
  `penghasilan_ibu` int DEFAULT NULL,
  `anak_ke` tinyint DEFAULT NULL,
  `tahun_masuk` year DEFAULT NULL,
  `status` enum('Aktif','Lulus','Pindah','Drop Out') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `user_id` bigint unsigned NOT NULL,
  `nama_penambah` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswas_nis_unique` (`nis`),
  UNIQUE KEY `siswas_nisn_unique` (`nisn`),
  KEY `siswas_kelas_id_foreign` (`kelas_id`),
  KEY `siswas_user_id_foreign` (`user_id`),
  CONSTRAINT `siswas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswas`
--

LOCK TABLES `siswas` WRITE;
/*!40000 ALTER TABLE `siswas` DISABLE KEYS */;
INSERT INTO `siswas` VALUES (21,'20200001','1000000001','Ahmad Fauzan','L','2013-01-15','Jl. Melati No.1','081234567801',1,'default.jpg','Hendra Fauzi','Siti Aminah','Wiraswasta','Ibu Rumah Tangga',5000000,0,1,2020,'Aktif',1,'putra',NULL,NULL),(22,'20200002','1000000002','Siti Aisyah','P','2013-02-10','Jl. Melati No.2','081234567802',2,'default.jpg','Budi Santoso','Nurhayati','Petani','Pedagang',4000000,2500000,2,2020,'Aktif',1,'putra',NULL,NULL),(23,'20200003','1000000003','Muhammad Rizky','L','2013-03-20','Jl. Melati No.3','081234567803',3,'default.jpg','Agus Riyanto','Dewi Sartika','Guru','Guru',7000000,5000000,1,2020,'Aktif',1,'putra',NULL,NULL),(24,'20200004','1000000004','Nurul Hidayah','P','2013-04-12','Jl. Melati No.4','081234567804',4,'default.jpg','Joko Susilo','Yuni Kartika','Karyawan','Ibu Rumah Tangga',4500000,0,3,2020,'Aktif',1,'putra',NULL,NULL),(25,'20200005','1000000005','Andi Saputra','L','2013-05-08','Jl. Melati No.5','081234567805',5,'default.jpg','Rudi Hartono','Sri Wahyuni','Wiraswasta','Pedagang',6000000,3000000,2,2020,'Aktif',1,'putra',NULL,NULL),(26,'20200006','1000000006','Dewi Lestari','P','2013-06-14','Jl. Melati No.6','081234567806',6,'default.jpg','Slamet Riyadi','Rina Marlina','Petani','Petani',3500000,2000000,1,2020,'Aktif',1,'putra',NULL,NULL),(27,'20200007','1000000007','Budi Santoso','L','2013-07-18','Jl. Melati No.7','081234567807',7,'default.jpg','Yusuf Hidayat','Marlina','PNS','PNS',8000000,6000000,2,2020,'Aktif',1,'putra',NULL,NULL),(28,'20200008','1000000008','Putri Amelia','P','2013-08-25','Jl. Melati No.8','081234567808',8,'default.jpg','Rahmat Hidayat','Sulastri','Wiraswasta','Guru',5500000,4500000,1,2020,'Aktif',1,'putra',NULL,NULL),(29,'20200009','1000000009','Rizal Maulana','L','2013-09-09','Jl. Melati No.9','081234567809',9,'default.jpg','Dedi Kurniawan','Lina Fitriani','Sopir','Pedagang',4000000,2500000,3,2020,'Aktif',1,'putra',NULL,NULL),(30,'20200010','1000000010','Aulia Rahma','P','2013-10-30','Jl. Melati No.10','081234567810',10,'default.jpg','Herman Saputra','Wati','Wiraswasta','Ibu Rumah Tangga',5000000,0,1,2020,'Aktif',1,'putra',NULL,NULL);
/*!40000 ALTER TABLE `siswas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_bab_mapel`
--

DROP TABLE IF EXISTS `sub_bab_mapel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_bab_mapel` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bab_mapel_id` bigint unsigned NOT NULL,
  `nama_sub_bab` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_bab_mapel_bab_mapel_id_foreign` (`bab_mapel_id`),
  CONSTRAINT `sub_bab_mapel_bab_mapel_id_foreign` FOREIGN KEY (`bab_mapel_id`) REFERENCES `bab_mapel` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_bab_mapel`
--

LOCK TABLES `sub_bab_mapel` WRITE;
/*!40000 ALTER TABLE `sub_bab_mapel` DISABLE KEYS */;
INSERT INTO `sub_bab_mapel` VALUES (1,1,'Niat',1,'2026-06-06 23:50:41','2026-06-06 23:50:41'),(2,1,'Rukun',1,'2026-06-06 23:50:55','2026-06-06 23:50:55');
/*!40000 ALTER TABLE `sub_bab_mapel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tahun_pelajaran`
--

DROP TABLE IF EXISTS `tahun_pelajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tahun_pelajaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('Ganjil','Genap') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tahun_pelajaran_tahun_semester_unique` (`tahun`,`semester`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tahun_pelajaran`
--

LOCK TABLES `tahun_pelajaran` WRITE;
/*!40000 ALTER TABLE `tahun_pelajaran` DISABLE KEYS */;
INSERT INTO `tahun_pelajaran` VALUES (1,'2020','Ganjil',NULL,NULL,1,'2026-06-06 21:09:04','2026-06-06 23:49:16');
/*!40000 ALTER TABLE `tahun_pelajaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'putra','putra@gmail.com',NULL,'$2y$12$9ub9P6D9tkd737TzTDyP3OFJOk2ZQV67tG56oHopOr7GSGWXsboU2','user','uQ8NMijbhxi8wfOOkC1thZiv026VArhLjnKcijVBP6SfJfIwIpkEtJ3x7pcv','2026-06-02 20:00:59','2026-06-02 20:00:59'),(2,'Admin','admin@test.com',NULL,'$2y$12$JALF7LCvgaDKy/xrmS9bcepU/.L6ajKNVAqupIWENugx9C1tQTW3i','admin',NULL,'2026-06-02 20:06:29','2026-06-02 20:06:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-19  8:37:39
