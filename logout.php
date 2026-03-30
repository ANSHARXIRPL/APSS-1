<?php
session_start();
session_destroy();
header('Location: /APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php?success=Berhasil logout');
exit;