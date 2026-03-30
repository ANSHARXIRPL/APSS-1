-- APSS schema
CREATE DATABASE IF NOT EXISTS `apss_db`;
USE `apss_db`;

CREATE TABLE IF NOT EXISTS admin (
  username VARCHAR(50) PRIMARY KEY,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS siswa (
  nis VARCHAR(30) PRIMARY KEY,
  kelas VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  ket_kategori VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS input_aspirasi (
  id_pelaporan INT AUTO_INCREMENT PRIMARY KEY,
  nis VARCHAR(30) NOT NULL,
  id_kategori INT NOT NULL,
  lokasi VARCHAR(255) NOT NULL,
  ket TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ia_siswa FOREIGN KEY (nis) REFERENCES siswa(nis) ON DELETE CASCADE,
  CONSTRAINT fk_ia_kat FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);

CREATE TABLE IF NOT EXISTS aspirasi (
  id_aspirasi INT PRIMARY KEY,
  status ENUM('Menunggu','Proses','Selesai') NOT NULL DEFAULT 'Menunggu',
  id_kategori INT NOT NULL,
  feedback TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_as_ia FOREIGN KEY (id_aspirasi) REFERENCES input_aspirasi(id_pelaporan) ON DELETE CASCADE,
  CONSTRAINT fk_as_kat FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);

-- seed minimal
INSERT IGNORE INTO admin(username,password) VALUES ('admin','admin123');
INSERT IGNORE INTO siswa(nis,kelas) VALUES ('12345','XII IPA 1'),('67890','XI IPS 2');
INSERT IGNORE INTO kategori(ket_kategori) VALUES ('Fasilitas'),('Kebersihan'),('Keamanan');
