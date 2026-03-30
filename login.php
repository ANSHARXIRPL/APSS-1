<?php
require_once __DIR__ . '/config/config.php';

if (isset($_SESSION['role'])) {
    if (is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/admin/index.php');
    if (is_siswa()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/siswa/index.php');
}

$err = $_GET['err'] ?? '';
$success = $_GET['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';

    if ($role === 'admin') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if ($username === '' || $password === '') {
            redirect('login.php?err=Lengkapi username dan password');
        }
        $stmt = $conn->prepare('SELECT username, password FROM admin WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if ($row['password'] === $password) {
                $_SESSION['role'] = 'admin';
                $_SESSION['username'] = $row['username'];
                redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/admin/index.php');
            }
        }
        redirect('login.php?err=Username atau password salah');
    } elseif ($role === 'siswa') {
        $nis = trim($_POST['nis'] ?? '');
        if ($nis === '') redirect('login.php?err=Masukkan NIS');
        $stmt = $conn->prepare('SELECT nis, kelas FROM siswa WHERE nis = ? LIMIT 1');
        $stmt->bind_param('s', $nis);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $_SESSION['role'] = 'siswa';
            $_SESSION['nis'] = $row['nis'];
            $_SESSION['kelas'] = $row['kelas'];
            redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/siswa/index.php');
        }
        redirect('login.php?err=NIS tidak ditemukan');
    } else {
        redirect('login.php?err=Pilih role login');
    }
}

include __DIR__ . '/layout/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Login APSS</h3>
                </div>
                <div class="card-body">
                    <?php if ($err): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="role" class="form-label">Pilih Role</label>
                            <select name="role" id="role" class="form-select" required onchange="toggleFields()">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>

                        <div id="adminFields" style="display:none;">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password">
                            </div>
                        </div>

                        <div id="siswaFields" style="display:none;">
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="text" name="nis" id="nis" class="form-control" placeholder="Masukkan NIS">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="daftar.php">Belum punya akun? Daftar sebagai Siswa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    const role = document.getElementById('role').value;
    const adminFields = document.getElementById('adminFields');
    const siswaFields = document.getElementById('siswaFields');

    adminFields.style.display = role === 'admin' ? 'block' : 'none';
    siswaFields.style.display = role === 'siswa' ? 'block' : 'none';
}
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>



