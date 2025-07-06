-- Membuat database
CREATE DATABASE IF NOT EXISTS simprakdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Menggunakan database yang baru dibuat
USE simprakdb;

-- Membuat tabel users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Membuat tabel mata_praktikum
CREATE TABLE `mata_praktikum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_matakuliah` varchar(50) NOT NULL,
  `semester` varchar(100) NOT NULL,
  `sks` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Membuat tabel krs
CREATE TABLE `krs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `praktikum_id` int,
  `user_id` int,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`praktikum_id`) REFERENCES mata_praktikum(id) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Membuat tabel modul
CREATE TABLE `modul` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `praktikum_id` INT,
  `judul` VARCHAR(50),
  `file_name` VARCHAR(255),
  `file_type` VARCHAR(100),
  `file_data` LONGBLOB,
  `deskripsi` VARCHAR(255),
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`praktikum_id`) REFERENCES mata_praktikum(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Membuat tabel laporan
CREATE TABLE `laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int,
  `praktikum_id` int,
  `text` TEXT,
  `nilai` int,
  `file_name` VARCHAR(255),
  `file_type` VARCHAR(100),
  `file_data` LONGBLOB,
  `feedback` VARCHAR(255),
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`praktikum_id`) REFERENCES mata_praktikum(id) ON DELETE CASCADE,
  FOREIGN KEY (`mahasiswa_id`) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=