-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Mar 2026 pada 17.09
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sinta_monitoring`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `judul` text NOT NULL,
  `judul_hash` varchar(64) DEFAULT NULL,
  `penulis` text DEFAULT NULL,
  `penerbit` varchar(255) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `tahun` varchar(10) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id`, `dosen_id`, `judul`, `judul_hash`, `penulis`, `penerbit`, `isbn`, `tahun`, `url`, `created_at`, `updated_at`) VALUES
(3076, 3, 'Industri Halal di Indonesia', 'e92dda0715153c8a39bc6145ffe93e67', '', '', '', '2023', '', '2026-03-11 18:35:43', '2026-03-11 18:58:50'),
(3077, 3, 'Bank dan Lembaga Keuangan Syariah', '5f210cf52af79398f61716465ba52c6b', '', '', '', '2023', '', '2026-03-11 18:35:43', '2026-03-11 18:58:50'),
(3078, 3, 'Etika Bisnis dalam Kajian Islam', '7fad06a148f4707a378b614bd30aa418', '', '', '', '2023', '', '2026-03-11 18:35:43', '2026-03-11 18:58:50'),
(3079, 3, 'Pengantar Manajemen', '0f18c0841cc9f3b23c97c746af4c978f', '', '', '', '2022', '', '2026-03-11 18:35:43', '2026-03-11 18:58:50'),
(3080, 3, 'Wakaf Saham Syariah Dari Tori Ke Praktik', '9a8cd08bb61926080cae088b2a5b9cef', '', '', '', '2022', '', '2026-03-11 18:35:43', '2026-03-11 18:58:50'),
(3092, 4, 'Manajemen Bisnis (Transformasi Digital Dalam Bisnis)', 'dccbb8428c4ad9603be4bc2136993c6e', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3093, 4, 'MSDM  (PERSPEKTIF ELECTRONIC HUMAN RESOURCE MANAGEMENT)', '9d1faa49db73e6f83ddaa3957666cce0', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3094, 4, 'Dasar - Dasar Manajemen Implementasi Dalam Organisasi', 'c367ac6e764c4ff438f30090d73c83e8', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3095, 4, 'DASAR-DASAR MANAJEMEN (PENDEKATAN DIGITALISASI MANAJEMEN)', '085b6b4886a38e52083dd220b337f739', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3096, 4, 'MSDM (TEORI DAN PENERAPAN DALAM ORGANISASI)', '37e4db3c3d5a968bfcecff1b57c8f942', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3097, 4, 'ETIKA BISNIS (TEORI DAN IMPLEMENTASINYA DALAM PERUSAHAAN)', '604a477b5424e1d6de1e1c3c525e2c91', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3098, 4, 'MSDM   Praktik HRM dalam Transformasi Digital', '4b6ea46d25c72479b6301ec3b452d528', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3099, 4, 'Pengantar Ilmu Manajemen (Tipologi Manajemen Dan Modern)', '41309f0eae2a1d9d396a1ef4518abd60', NULL, NULL, NULL, '2024', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3100, 4, 'Manajemen Sektor Publik', '64733ffa01edf302c7a6e6327c2c7828', NULL, NULL, NULL, '2020', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3101, 4, 'Dasar-Dasar Manajemen Dan Bisnis', 'd3f7cfd6d7ebbceec32bc33440ab2468', NULL, NULL, NULL, '2020', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43'),
(3102, 4, 'Manajemen Sumber Daya Manusia: Strategi Dan Perubahan Dalam Rangka Meningkatkan Kinerja Pegawai Dan Organisasi', 'cb8519d97b79b7a038b2dd316c880d1a', NULL, NULL, NULL, '2016', '', '2026-03-13 16:03:43', '2026-03-13 16:03:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `sinta_id` varchar(50) DEFAULT NULL,
  `sinta_url` varchar(500) DEFAULT NULL,
  `scholar_url` varchar(500) DEFAULT NULL,
  `sinta_score_overall` decimal(10,2) DEFAULT 0.00,
  `sinta_score_3year` decimal(10,2) DEFAULT 0.00,
  `h_index_scopus` int(11) DEFAULT 0,
  `h_index_gs` int(11) DEFAULT 0,
  `foto` varchar(500) DEFAULT NULL,
  `last_scraped` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`id`, `nama`, `nidn`, `prodi`, `sinta_id`, `sinta_url`, `scholar_url`, `sinta_score_overall`, `sinta_score_3year`, `h_index_scopus`, `h_index_gs`, `foto`, `last_scraped`, `created_at`, `updated_at`) VALUES
(3, 'Dr.H.Fikry Ramadhan Suhendar.,Lc,.M.A', '0422058603', 'Manajemen', '6736129', 'https://sinta.kemdiktisaintek.go.id/authors/profile/6736129', 'https://scholar.google.com/citations?hl=id&user=To1XcKAAAAAJ', 0.00, 0.00, 0, 0, NULL, '2026-03-12 01:58:50', '2026-03-11 18:34:25', '2026-03-11 18:58:50'),
(4, 'Dr.Hj. Imas Komariyah, S.E, M.Si, M.H.', '0410086803', 'Manajemen', '6024693', 'https://sinta.kemdiktisaintek.go.id/authors/profile/6024693', 'https://scholar.google.com/citations?hl=id&user=aQ72cK4AAAAJ', 0.00, 0.00, 0, 0, NULL, '2026-03-13 23:03:43', '2026-03-13 16:02:51', '2026-03-13 16:03:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hki`
--

CREATE TABLE `hki` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `judul` text NOT NULL,
  `judul_hash` varchar(64) DEFAULT NULL,
  `pemegang` varchar(500) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `tahun` varchar(10) DEFAULT NULL,
  `nomor` varchar(100) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hki`
--

INSERT INTO `hki` (`id`, `dosen_id`, `judul`, `judul_hash`, `pemegang`, `kategori`, `tahun`, `nomor`, `url`, `created_at`, `updated_at`) VALUES
(3099, 3, 'Program Komputer Si akad STIE Miftahul Huda, SAKU STIE Miftahul Huda  dan SANTI STIE Miftahul Huda', '791c9a266b462643b99c4c974882f9e7', NULL, NULL, '2024', NULL, '', '2026-03-11 18:35:38', '2026-03-11 18:58:50'),
(3100, 3, 'Etika Bisnis Dalam Kajian Islam', '510b706190e1a713b98530f13b54edcd', NULL, NULL, '2023', NULL, '', '2026-03-11 18:35:38', '2026-03-11 18:58:50'),
(3101, 3, 'Bank Dan Lembaga Keuangan Syariah', 'cf998bdd1cd543901ec6ad1011bc7dad', NULL, NULL, '2023', NULL, '', '2026-03-11 18:35:38', '2026-03-11 18:58:50'),
(3102, 3, 'Buku Pengantar Manajemen', '2b8ced36b13808cb9a2e660485dc12cf', NULL, NULL, '2023', NULL, '', '2026-03-11 18:35:38', '2026-03-11 18:58:50'),
(3103, 3, 'Industri Halal di Indonesia', 'e92dda0715153c8a39bc6145ffe93e67', NULL, NULL, '2023', NULL, '', '2026-03-11 18:35:38', '2026-03-11 18:58:50'),
(3104, 3, 'WAKAF SAHAM SYARIAH DI INDONESIA DARI TEORI KE  PRAKTIK', 'b40a67b601a0b4f928454f7e0ac2932a', NULL, NULL, '2022', NULL, '', '2026-03-11 18:35:38', '2026-03-11 18:58:50'),
(3118, 4, 'ETIKA BISNIS (TEORI DAN IMPLEMENTASINYA DALAM  PERUSAHAAN)', '7d449007c1d34f5fdd3b1694bdc07094', NULL, NULL, '2024', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3119, 4, 'MSDM (TEORI DAN PENERAPANNYA DALAM ORGANISASI)', '7b5e2bacc913f407f3e0c696ae194d7d', NULL, NULL, '2024', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3120, 4, 'Dasar-Dasar Manajemen Dan Bisnis', 'd3f7cfd6d7ebbceec32bc33440ab2468', NULL, NULL, '2021', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3121, 4, 'Manajemen Konflik Berbasis Sekolah', '64318ea8c697e77fa3f6a2d900962ea7', NULL, NULL, '2021', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3122, 4, 'Manajemen Sektor Publik', '64733ffa01edf302c7a6e6327c2c7828', NULL, NULL, '2021', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3123, 4, 'Pengaruh Budaya Organisasi dan Motivasi Guru Terhadap Kompetensi Guru Serta Implikasinya Pada Kinerja Guru (Survei di SMK Kabupaten Lebak, Kabupaten Cilegon dan Kota Tangerang Selatan)', 'ede571d794a54474e6e1057b8dd34d4d', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3124, 4, 'Analisis Pengaruh Kepemimpinan Transformasional, budaya organisasi, dan Motivasi Pegawai Terhadap Kompetensi Pegawai dan Kepuasan Kerja (Survey Pada Hotel Non Bintang di Kota Bandung, Kabupaten Bandung dan Kota Cimahi)', 'e1382d7d007eb71d8cd7660d01db3656', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3125, 4, 'Factors Of Farming Competence OF Facilitators Through Involvement In The Process Of Teaching And Learning And Work Environment On Development Empowerment Teacher And Eduacation Personal (PPPPTK-IPA) Indonesia', '5940dd12020ee9563e59c4d4fbad232e', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3126, 4, 'Suplly Chain Management Industry Tannery In Garut', '1c89af56e27e526ee39bf89b9f0c25df', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3127, 4, 'Evaluasi Strategi Pemasaran dalam Rangka Perancangan Startegi Pemasaran Pada PT Bank Jawa Barat Kantor Cabang Syariah', '77453c0611791c8e5b426fb248871fc2', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3128, 4, 'Kompetensi Tenaga Pelaksana Lapangan Konstruksi Bangunan Gedung', 'b9337b6d47f4df5bb3d8e560d056e15e', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3129, 4, 'Implentasi Bauran Pemasaran Kepariwisataan Dalam Upaya Peningkatan Kunjungan Wisatawan', '4a5febf3f2918882bba83c4d539d5a44', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3130, 4, 'Pengaruh Peran Kepemimpinan dan Komitmen Organisasi Terhadap Kepuasan Kerja Guru (Survei pada SMK Kota Serang, Kabupaten Serang dan Kabupaten Tangerang)', '223cd479c351e15b61a0794c4c189865', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3131, 4, 'Manajemen Sumber Daya Manusia', 'f7248066d386ccef21bae70db99c4006', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42'),
(3132, 4, 'Improving The Performance Of Facilitator Through Individual Characteristics And Motivation In Development Of Empowerment Tecaher And Eduactioan Personal (P4TK) Bandung-Indonesia', '46067f31c7ee159e44ea7663e6971a09', NULL, NULL, '2019', NULL, '', '2026-03-13 16:03:42', '2026-03-13 16:03:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `publikasi_garuda`
--

CREATE TABLE `publikasi_garuda` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `judul` text NOT NULL,
  `judul_hash` varchar(64) DEFAULT NULL,
  `penulis` text DEFAULT NULL,
  `nama_jurnal` text DEFAULT NULL,
  `tahun` varchar(10) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `publikasi_garuda`
--

INSERT INTO `publikasi_garuda` (`id`, `dosen_id`, `judul`, `judul_hash`, `penulis`, `nama_jurnal`, `tahun`, `url`, `created_at`, `updated_at`) VALUES
(6202, 3, 'Sustainable Agriculture in Subang: Integrating Local Wisdom, Sharia Principles, and Agribusiness Innovation', 'bfd18b90418021556d482a79c407f5da', NULL, NULL, '2026', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5909134', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6203, 3, 'Business Portfolio and IT Strategy Alignment: Enhancing Performance through Digital Innovation at PT Telkom Indonesia', '1163ac5d88007b04c616445f064409e9', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5181096', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6204, 3, 'Analisis Perbandingan Multi Level Marketing Syariah Dan Non-Syariah Berdasarkan Prinsip-Prinsip Ekonomi Islam', 'd8f87f372da49f31b3c33f634f3cddcc', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5835838', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6205, 3, 'Analisis Perlakuan Akuntansi Atas Transaksi Ijarah Dan Ijarah Muntahiya Bittamlik (IMBT) Berdasarkan PSAK 107', '37a8ebb10bf202a303101c3c06c62ee4', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5888110', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6206, 3, 'Digital Leadership Transformation In Enhancing Generation Z Employee Performance', '2e62d2548525f7815bc828ee86b4f46a', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/6028582', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6207, 3, 'Merger Tiga Bank Syariah Di Indonesia Dan Dampaknya Terhadap Harga Saham Bank Syariah Indonesia Tbk.', 'b1a0148193d5f5d362ae22ad74c12480', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/6055395', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6208, 3, 'Dampak Penerapan Budaya Organisasi Dan Sistem Kompensasi Serta Pengaruhnya Terhadap Kinerja Karyawan Dengan Dimediasi Kepuasan Kerja', '2dd34ad6af68465e42a527ff408bd107', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4430598', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6209, 3, 'Opportunities and challenges halal tourism in Indonesia in the era of human-centered technology (society 5.0)', '506d7198ea592955780c6bc304cb7477', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5643084', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6210, 3, 'Comparison of Fundamental Analysis of PT Sharia Shares. Indofood Sukses Makmur Tbk (INDF) & PT. Mayora Indah Tbk (MYOR) in 2022 as an investment decision', 'a398cefc53bb51b511f6cee03be209a4', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3996910', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6211, 3, 'Analysis of Stock Waqf Regulations as the Foundation for Implementation in Indonesia', '3ad0c7fd4bacf81e9f2236913976bd97', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5036830', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6212, 3, 'Accounting For Developmental Transactions', 'afc97a71e1da60abef15bec5529d3af6', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4948537', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6213, 3, 'Mekanisme Pengangkatan Pegawai dalam Konsep Islam', '8aecfbf3c1626a4f3ca2c3b12db8f48c', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4059668', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6214, 3, 'Paradigma Dan Teori Akuntansi Syariah', 'ccc8cb982161f5b1e7505a8f45a7082b', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4949128', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6215, 3, 'Akad Wakalah bil Ujroh sebagai Solusi Transaksi Bisnis di Era Digital', 'f388a5fdfef5d0afd3a9abfe39e2b9ac', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5558275', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6216, 3, 'Work Discipline And Organizational Citizenship Behavior', '209adde4000b492fb50f574fb2d1323b', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4142716', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6217, 3, 'wakalah WAKALAH BI AL-ISTISMAR SEBAGAI SOLUSI INVESTASI', 'a74a71350c7884f55fcd524e1b920de2', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5019101', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6218, 3, 'Qaidah Fiqhiyah Hukum Ekonomi Syariah Dalam Tinjauan Muamalat', 'af789129751b72283855d1ff2cddd76b', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4989412', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6219, 3, 'CORPORATE INCOME TAX RATES BEFORE AND DURING THE COVID-19 PANDEMIC BASED ON FINANCIAL PERFORMANCE IN (Case Study at PT. Kalbe Farma TBK )', '69d610168d770b64d36c3a1dc7bdc984', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3453688', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6220, 3, 'Ziswaf Role in the Formation of Justice of the Muslim Redistributive', '4e9c954cf41c003ae058b5665963dad2', NULL, NULL, '2021', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3944603', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6221, 3, 'Financial Policies Supporting Acceleration of Sustainable Economic and Fiscal Growth in Indonesia', 'c2bf4e9d384bca0c46570b2d6b656c86', NULL, NULL, '2020', 'https://garuda.kemdiktisaintek.go.id/documents/detail/2910158', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6222, 3, 'Konsep Akad Dalam Lingkup Ekonomi Syariah', 'ac0dd2cdf39833ea80a64abe2f294021', NULL, NULL, '2019', 'https://garuda.kemdiktisaintek.go.id/documents/detail/2083324', '2026-03-11 18:35:33', '2026-03-11 18:58:50'),
(6278, 4, 'Optimalisasi Uji Kompetensi Keahlian dalam Menyiapkan Lulusan SMK Siap Kerja', 'aec8de41203e331c06a5c5b5b181c2a1', NULL, NULL, '2026', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5955650', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6279, 4, 'Transformasi Digital dalam Pengelolaan Sumber Daya Manusia: Analisis Dampak Artificial Intelligence terhadap Produktivitas dan Engagement Karyawan di Era Industri 5.0', 'd18bb0f14aa18b0a2e13dbb07eeb3870', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4868461', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6280, 4, 'Analysis of the Impact of Talent Retention and Competency Development Programs on Turnover Intention and Organizational Effectiveness in Banking Companies in Central Java', 'df7b054bea9021d1a5e8d7f91f19da33', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4964299', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6281, 4, 'Business Portfolio and IT Strategy Alignment: Enhancing Performance through Digital Innovation at PT Telkom Indonesia', '1163ac5d88007b04c616445f064409e9', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5181096', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6282, 4, 'Analisis Budaya Kerja dan Fokus Pelanggan dalam Meningkatkan Kualitas Produk CV Digital Printing', '6baaff53bca157942c5c785ab982aa28', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5506676', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6283, 4, 'Data-Driven Marketing Strategy: The Role Of Big Data And Business Intelligence At Pt Pertamina Retail Fuel Marketing', 'ea936c03c590c83f3731387fdcad9cad', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5512112', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6284, 4, 'Optimalisasi Pengembangan Karier, Keseimbangan Kehidupan Kerja, dan Manajemen Stres untuk Meningkatkan Kinerja Karyawan pada UMKM Pengrajin Kulit', 'e2e199427b30951af905fbed1c5dd05a', NULL, NULL, '2025', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5765808', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6285, 4, 'The Influence of Work-Life Balance Policy, Work Culture, and Managerial Support on Employee Retention in Creative Industries in West Java', '5811233865d93181b3591e84aa5a32da', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5611426', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6286, 4, 'Outsourcing Employee Recruitment And Selection Process', 'f9f1de29203c7fd0a2351bea4f374459', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4244722', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6287, 4, 'Leadership Practices on Employee Performance with Motivation and Job Satisfaction', 'dfa17500e0db4b906a4211a0147c675c', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4142057', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6288, 4, 'Exploring the Influence of Digital Transformation, Change Management, and Employee Engagement on Organizational Performance of Non Profit Organization in Indonesia', '53769e4286dad4809187e78e7f22f696', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4819523', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6289, 4, 'The Influence of Customer Orientation and Social Media Utilization on MSMe Performance Growth', '64dfc7a3e042e662f2a29b14695e590a', NULL, NULL, '2024', 'https://garuda.kemdiktisaintek.go.id/documents/detail/5714863', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6290, 4, 'THE IMPACT OF WORK MOTIVATION, WORK ENVIRONMENT, AND CAREER DEVELOPMENT ON EMPLOYEE JOB SATISFACTION', '364095ebb57a000e34941f951c8d2f34', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3283490', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6291, 4, 'EMPLOYEE JOB SATISFACTION AND ITS RELATIONSHIP TO MOTIVATION, COMMUNICATION, AND JOB STRESS', '8ec4baea7c5073d07d7a286c2ddab4be', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3284173', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6292, 4, 'Study Of Employment Characteristics And Entrepreneur Competency As Effort To Increase SMEs Performance', 'c0e537061ae162f99e6d6f373a485622', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4624549', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6293, 4, 'Analisis Pengaruh Budaya Kerja dan Disiplin Kerja Terhadap Produktivitas Kerja Karyawan pada Industri Kertas Daur Ulang CV Kridasana (Survey pada Bagian Produksi)', 'c733a65891736451f3e699bb46ca9c9d', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3453933', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6294, 4, 'APPLICATION OF ARTIFICIAL INTELLIGENCE IN INCREASING EMPLOYEE INTEGRITY', '7cc66c7ae6a40230e209d75e884ddfa7', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3783843', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6295, 4, 'Blockchain Technology in Human Resource Management: Role in the World of Work', '57f799b95ce129023dde1771d40003c2', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3804711', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6296, 4, 'Implementasi strategis dan kepemimpinan dalam meningkatkan kinerja perusahaan di perusahaan daerah jasa & kepariwisataan', '66322e50acae4e3bd6ee5240f73a667c', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3915011', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6297, 4, 'Hubungan Antara Beban Kerja dan Kompetensi Terhadap Komitmen Karyawan: The Relationship Between Workload and Competency on Employee Commitment', '7c7be875ff3ae5c2b97f80ad64a9a7b5', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4563841', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6298, 4, 'The Influence Of Leadership Ethics And Organizational Culture', 'c85b98e4310fe293f2d1603b2dd066c7', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3471948', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6299, 4, 'Work Discipline And Organizational Citizenship Behavior', '209adde4000b492fb50f574fb2d1323b', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4142716', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6300, 4, 'Work Stress In Finatama Bandung Affects Individual Performance', '98e3b944bafaacca47eb97e99e2cd835', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3448735', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6301, 4, 'Hubungan Antara Beban Kerja dan Kompetensi Terhadap Komitmen Karyawan', 'af1d79bdcfc1fe60f657b75d7b391635', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/4580365', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6302, 4, 'THE RELATIONSHIP BETWEEN ORGANIZATIONAL CULTURE AND COMPETENCE WITH ORGANIZATIONAL COMMITMENT IN EMPLOYEES OF BUMD BINJAI, NORTH SUMATRA', '9f2458997af7f22afb3e8b0569a3e1a7', NULL, NULL, '2023', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3739184', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6303, 4, 'Protection of Women in Disaster Emergency Situations; Seven Stages of Participatory Capacity and Vulnerability Analysis', 'ea70ead0d5b19d89dc75359d5fcd4e83', NULL, NULL, '2022', 'https://garuda.kemdiktisaintek.go.id/documents/detail/2874960', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6304, 4, 'Pemasaran Online Melalui Penerapan Iklan Secara Digital', '3e8fbfd0fc7e08fe99f3189a8064671f', NULL, NULL, '2020', 'https://garuda.kemdiktisaintek.go.id/documents/detail/2363621', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6305, 4, 'Pemanfaatan Limbah Kulit Jagung Sebagai Upaya Pengembangan Usaha IKM Pembuat Kertas Seni', '71ffd39d6c8570bbca9218aded27e671', NULL, NULL, '2020', 'https://garuda.kemdiktisaintek.go.id/documents/detail/3747781', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6306, 4, 'Implementasi Bauran Pemasaran Kepariwisataan Dalam Upaya Peningkatan Kunjungan Wisatawan', '2515e6a8cdc22de6fce300396f85f4b8', NULL, NULL, '2019', 'https://garuda.kemdiktisaintek.go.id/documents/detail/2124643', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6307, 4, 'Pengaruh Peran Kepemimpinan dan Komitmen Organisasi terhadap Kepuasan Kerja Guru', '44dbf1bca03610c12a7ec01f444db663', NULL, NULL, '2019', 'https://garuda.kemdiktisaintek.go.id/documents/detail/973653', '2026-03-13 16:03:41', '2026-03-13 16:03:41'),
(6308, 4, 'PENGARUH KEPEMIMPINAN DAN BUDAYA ORGANISASI TEHADAP KINERJA PEGAWAI PADA HOTEL NON BINTANG DI BANDUNG', '8cd8de0d8f736adfdadbb727e6a391c2', NULL, NULL, '2017', 'https://garuda.kemdiktisaintek.go.id/documents/detail/1855029', '2026-03-13 16:03:41', '2026-03-13 16:03:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `publikasi_gs`
--

CREATE TABLE `publikasi_gs` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `judul` text NOT NULL,
  `judul_hash` varchar(64) DEFAULT NULL,
  `penulis` text DEFAULT NULL,
  `nama_jurnal` text DEFAULT NULL,
  `jilid` varchar(50) DEFAULT NULL,
  `terbitan` varchar(50) DEFAULT NULL,
  `halaman` varchar(100) DEFAULT NULL,
  `tahun` varchar(10) DEFAULT NULL,
  `sitasi` int(11) DEFAULT 0,
  `url` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `publikasi_scopus`
--

CREATE TABLE `publikasi_scopus` (
  `id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `judul` text NOT NULL,
  `judul_hash` varchar(64) DEFAULT NULL,
  `penulis` text DEFAULT NULL,
  `nama_jurnal` text DEFAULT NULL,
  `tahun` varchar(10) DEFAULT NULL,
  `sitasi` int(11) DEFAULT 0,
  `quartile` varchar(10) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `publikasi_scopus`
--

INSERT INTO `publikasi_scopus` (`id`, `dosen_id`, `judul`, `judul_hash`, `penulis`, `nama_jurnal`, `tahun`, `sitasi`, `quartile`, `url`, `created_at`, `updated_at`) VALUES
(15, 4, 'Studies on the implementation of management strategy in programs preservation culture (Case study on Tourism and Culture Department West Java Province)', '86d0ccb4b7100ade2e84044886beb16f', NULL, NULL, '2016', 0, NULL, 'https://www.scopus.com/record/display.uri?eid=2-s2.0-84971378002&origin=resultslist', '2026-03-13 16:03:34', '2026-03-13 16:03:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_buku_dosen_judul` (`dosen_id`,`judul_hash`);

--
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hki`
--
ALTER TABLE `hki`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_hki_dosen_judul` (`dosen_id`,`judul_hash`);

--
-- Indeks untuk tabel `publikasi_garuda`
--
ALTER TABLE `publikasi_garuda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_garuda_dosen_judul` (`dosen_id`,`judul_hash`);

--
-- Indeks untuk tabel `publikasi_gs`
--
ALTER TABLE `publikasi_gs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_gs_dosen_judul` (`dosen_id`,`judul_hash`);

--
-- Indeks untuk tabel `publikasi_scopus`
--
ALTER TABLE `publikasi_scopus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_scopus_dosen_judul` (`dosen_id`,`judul_hash`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3103;

--
-- AUTO_INCREMENT untuk tabel `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `hki`
--
ALTER TABLE `hki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3133;

--
-- AUTO_INCREMENT untuk tabel `publikasi_garuda`
--
ALTER TABLE `publikasi_garuda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6314;

--
-- AUTO_INCREMENT untuk tabel `publikasi_gs`
--
ALTER TABLE `publikasi_gs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7200;

--
-- AUTO_INCREMENT untuk tabel `publikasi_scopus`
--
ALTER TABLE `publikasi_scopus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `buku_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hki`
--
ALTER TABLE `hki`
  ADD CONSTRAINT `hki_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `publikasi_garuda`
--
ALTER TABLE `publikasi_garuda`
  ADD CONSTRAINT `publikasi_garuda_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `publikasi_gs`
--
ALTER TABLE `publikasi_gs`
  ADD CONSTRAINT `publikasi_gs_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `publikasi_scopus`
--
ALTER TABLE `publikasi_scopus`
  ADD CONSTRAINT `publikasi_scopus_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
