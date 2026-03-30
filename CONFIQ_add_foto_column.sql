-- Tambah kolom foto untuk bukti pengaduan
ALTER TABLE input_aspirasi ADD COLUMN foto VARCHAR(255) NULL AFTER ket;
