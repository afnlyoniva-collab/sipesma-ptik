-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Nov 2026 pada 06.14
-- Versi server: 10.11.16-MariaDB
-- Versi PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `sipesma`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_surat`
--

CREATE TABLE `jenis_surat` (
  `id` int(11) NOT NULL,
  `nama_surat` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_surat`
--

INSERT INTO `jenis_surat` (`id`, `nama_surat`, `deskripsi`) VALUES
(1, 'Surat Keterangan Aktif Kuliah', 'Surat keterangan bahwa mahasiswa masih aktif kuliah'),
(2, 'Surat Pengantar Penelitian', 'Surat pengantar untuk keperluan penelitian'),
(3, 'Surat Keterangan Lulus', 'Surat keterangan bahwa mahasiswa telah menyelesaikan studi'),
(4, 'Surat Permohonan Magang', 'Surat permohonan untuk melaksanakan kegiatan magang'),
(5, 'Surat Keterangan Beasiswa', 'Surat keterangan untuk keperluan pengajuan beasiswa'),
(6, 'surat melaksanakan pkli', 'untuk PKLI');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jenis_surat_id` int(11) NOT NULL,
  `keperluan` text NOT NULL,
  `status` enum('menunggu','diproses','disetujui','ditolak') DEFAULT 'menunggu',
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan`
--

INSERT INTO `pengajuan` (`id`, `user_id`, `jenis_surat_id`, `keperluan`, `status`, `catatan_admin`, `created_at`) VALUES
(1, 2, 1, 'saya ingin mengikuti program magang', 'disetujui', 'sedang diperiksa admin', '2026-11-10 07:39:59'),
(2, 3, 1, 'APPLY BEASISWA', 'ditolak', 'SISTEM SEDANG BERMASALAH,AJUKAN BSK', '2026-11-11 04:18:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','admin') DEFAULT 'mahasiswa',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Afnel Yoniva Marbun', 'afnlyoniva@gmail.com', '$2y$12$pts0G5AooYRLgfXvon6gNO3XDKzpvd7z8ccSG7usqNCLk2iapzhXa', 'admin', '2026-11-10 04:45:49'),
(2, 'Verani Sinurat', 'verni@gmail.com', '$2y$12$DYWcs.6.ub9cHJqYX28SH.OyZYXH3puYHw3L0vxQ.aUBIC8lw3haC', 'mahasiswa', '2026-11-10 07:39:10'),
(3, 'febri harahap', 'febriazhari67@gmail.com', '$2y$12$MJn0FhyGC4LA0O1ZYEhj5.Z1V1QguS6I5d26Y/rWzcPlLj/q2bAZC', 'mahasiswa', '2026-11-10 10:14:21');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `jenis_surat`
--
ALTER TABLE `jenis_surat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `jenis_surat_id` (`jenis_surat_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `jenis_surat`
--
ALTER TABLE `jenis_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `pengajuan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pengajuan_ibfk_2` FOREIGN KEY (`jenis_surat_id`) REFERENCES `jenis_surat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
