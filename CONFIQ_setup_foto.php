<?php
/**
 * Script untuk menambahkan kolom foto ke tabel input_aspirasi
 * Jalankan file ini sekali saja melalui browser atau CLI
 */

require_once __DIR__ . '/config.php';

try {
    // Cek apakah kolom foto sudah ada
    $check = $conn->query("SHOW COLUMNS FROM input_aspirasi LIKE 'foto'");
    
    if ($check->num_rows > 0) {
        echo "<h2 style='color: green;'>✓ Kolom 'foto' sudah ada di database</h2>";
    } else {
        // Tambahkan kolom foto
        $sql = "ALTER TABLE input_aspirasi ADD COLUMN foto VARCHAR(255) NULL AFTER ket";
        
        if ($conn->query($sql)) {
            echo "<h2 style='color: green;'>✓ Kolom 'foto' berhasil ditambahkan ke tabel input_aspirasi</h2>";
        } else {
            echo "<h2 style='color: red;'>✗ Error: " . $conn->error . "</h2>";
        }
    }
    
    // Buat folder uploads jika belum ada
    $uploadDir = __DIR__ . '/../uploads/bukti/';
    if (!is_dir($uploadDir)) {
        if (mkdir($uploadDir, 0777, true)) {
            echo "<h2 style='color: green;'>✓ Folder uploads/bukti/ berhasil dibuat</h2>";
        } else {
            echo "<h2 style='color: orange;'>⚠ Gagal membuat folder uploads/bukti/. Buat manual dengan permission 777</h2>";
        }
    } else {
        echo "<h2 style='color: green;'>✓ Folder uploads/bukti/ sudah ada</h2>";
    }
    
    echo "<br><a href='../login.php'>Kembali ke Login</a>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>✗ Error: " . $e->getMessage() . "</h2>";
}
