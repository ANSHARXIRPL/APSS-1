# Instruksi Setup Fitur Upload Foto

## 1. Tambahkan Kolom Foto ke Database

Jalankan query berikut di database MySQL:

```sql
ALTER TABLE input_aspirasi ADD COLUMN foto VARCHAR(255) NULL AFTER ket;
```

Atau import file `add_foto_column.sql` yang sudah disediakan.

## 2. Pastikan Folder Uploads Ada

Folder `uploads/bukti/` akan dibuat otomatis saat siswa mengupload foto pertama kali.

Jika ingin membuat manual:
- Buat folder `uploads/bukti/` di root project
- Set permission folder menjadi 777 (atau sesuai konfigurasi server)

## 3. Fitur yang Tersedia

- ✅ Siswa dapat upload foto bukti saat input aspirasi (opsional)
- ✅ Format yang didukung: JPG, PNG, GIF
- ✅ Maksimal ukuran: 5MB
- ✅ Preview foto sebelum upload
- ✅ Admin dapat melihat foto di halaman detail aspirasi
- ✅ Foto dapat diklik untuk melihat ukuran penuh

## Catatan Keamanan

- File foto disimpan di folder `uploads/bukti/`
- Nama file menggunakan timestamp dan uniqid untuk menghindari konflik
- Validasi tipe file dan ukuran dilakukan di server
