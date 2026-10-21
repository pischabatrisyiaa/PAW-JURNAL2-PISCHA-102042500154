CREATE DATABASE IF NOT EXISTS skema_lms
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE skema_lms;

DROP TABLE IF EXISTS tugas;
DROP TABLE IF EXISTS materi;
DROP TABLE IF EXISTS mata_kuliah;

CREATE TABLE mata_kuliah (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  dosen VARCHAR(100) NOT NULL,
  semester TINYINT UNSIGNED NOT NULL
);

CREATE TABLE materi (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mata_kuliah_id INT UNSIGNED NOT NULL,
  judul VARCHAR(150) NOT NULL,
  isi TEXT NOT NULL,
  tautan VARCHAR(255) NOT NULL,
  FOREIGN KEY (mata_kuliah_id)
    REFERENCES mata_kuliah(id) ON DELETE RESTRICT
);

CREATE TABLE tugas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mata_kuliah_id INT UNSIGNED NOT NULL,
  judul VARCHAR(150) NOT NULL,
  instruksi TEXT NOT NULL,
  tenggat DATE NOT NULL,
  status VARCHAR(20) NOT NULL,
  FOREIGN KEY (mata_kuliah_id)
    REFERENCES mata_kuliah(id) ON DELETE RESTRICT
);

INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES
  ('ALP-101', 'Algoritma dan Pemrograman', 'Dosen ALP', 1),
  ('PAW-101', 'Pengembangan Aplikasi Website', 'Dosen PAW', 3);

INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES
  (1, 'Pengantar Algoritma',
   'Materi membahas pengertian algoritma dan cara menyusun langkah penyelesaian masalah.',
   'https://id.wikipedia.org/wiki/Algoritma'),
  (2, 'Dasar PHP',
   'Materi membahas sintaks dasar PHP, variabel, dan cara membaca isian form.',
   'https://www.php.net/manual/en/langref.php');

INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
  (1, 'Latihan Flowchart',
   'Buat flowchart untuk program yang menentukan bilangan ganjil atau genap.',
   '2026-11-15', 'Aktif'),
  (2, 'Latihan CRUD',
   'Buat aplikasi CRUD sederhana dengan PHP dan MySQL sesuai instruksi modul.',
   '2026-11-30', 'Draf');
