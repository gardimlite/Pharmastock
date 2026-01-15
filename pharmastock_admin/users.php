<?php
include 'koneksi.php';

// --- LOGIKA CRUD USER ---

// 1. TAMBAH USER (CREATE)
if (isset($_POST['add_user'])) {
    $nip = $_POST['nip'];
    $name = $_POST['name'];
    $password = $_POST['password']; // Plain text as per existing DB (not recommended for production but follows existing pattern)
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Cek NIP duplikat
    $cek = mysqli_query($con, "SELECT * FROM users WHERE nip = '$nip'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIP sudah terdaftar!');</script>";
    } else {
        $query = "INSERT INTO users (nip, name, password, role, status) VALUES ('$nip', '$name', '$password', '$role', '$status')";
        if (mysqli_query($con, $query)) {
            echo "<script>alert('User berhasil ditambahkan!'); window.location='users.php';</script>";
        } else {
            echo "<script>alert('Gagal tambah user: " . mysqli_error($con) . "');</script>";
        }
    }
}

// 2. EDIT USER (UPDATE)
if (isset($_POST['edit_user'])) {
    $id = $_POST['id_user'];
    $nip = $_POST['nip'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $query = "UPDATE users SET nip='$nip', name='$name', password='$password', role='$role', status='$status' WHERE id='$id'";
    
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Data user berhasil diperbarui!'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('Gagal update user: " . mysqli_error($con) . "');</script>";
    }
}

// 3. HAPUS USER (DELETE)
if (isset($_POST['delete_user'])) {
    $id = $_POST['id_user'];
    // Prevent deleting self (optional logic, assuming ID 1 is super admin or current session check)
    // For now just delete.
    $query = "DELETE FROM users WHERE id='$id'";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('User berhasil dihapus!'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus user: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - PharmaStock</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .main-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.03);
        }
        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark navbar-expand-lg mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-hospital-fill"></i> PharmaStock Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Dashboard</a></li>
                    <li class="nav-item"><a href="stok_obat.php" class="nav-link">Stok Obat</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link active fw-bold">Kelola User</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Manajemen Pengguna</h4>
                <p class="text-muted mb-0">Kelola akun staff dan admin aplikasi.</p>
            </div>
            <button class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                <i class="bi bi-person-plus-fill me-2"></i> Tambah User
            </button>
        </div>

        <!-- TABEL REQUEST REGISTRATION -->
        <?php
        $q_req = mysqli_query($con, "SELECT * FROM registration_requests ORDER BY request_date DESC");
        if (mysqli_num_rows($q_req) > 0) {
        ?>
        <div class="card mb-4 border-warning shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 py-3 border-bottom border-warning">
                <h5 class="mb-0 fw-bold text-warning-emphasis">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>Permintaan Registrasi Baru
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Staff ID / NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Waktu Request</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($req = mysqli_fetch_assoc($q_req)) { ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= htmlspecialchars($req['staff_id']) ?></td>
                                <td><?= htmlspecialchars($req['nama']) ?></td>
                                <td class="text-muted small"><?= $req['request_date'] ?></td>
                                <td class="text-center pe-4">
                                    <a href="approve_user.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Approve user ini?')">
                                        <i class="bi bi-check-lg me-1"></i> Approve
                                    </a>
                                    <a href="reject_user.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 ms-1" onclick="return confirm('Tolak permintaan ini?')">
                                        <i class="bi bi-x-lg me-1"></i> Reject
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
        <!-- END TABEL REQUEST -->

        <div class="card main-card bg-white">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-people me-2 text-primary"></i>Daftar User</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4 text-center">ID</th>
                                <th>NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM users ORDER BY id ASC";
                            $result = mysqli_query($con, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $roleBadge = ($row['role'] == 'admin') ? 'bg-primary' : 'bg-secondary';
                                    $statusBadge = ($row['status'] == 'active') ? 'bg-success' : 'bg-danger';
                            ?>
                            <tr>
                                <td class="ps-4 text-center text-muted">#<?= $row['id'] ?></td>
                                <td class="fw-bold"><?= $row['nip'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><span class="badge <?= $roleBadge ?>"><?= ucfirst($row['role']) ?></span></td>
                                <td><span class="badge <?= $statusBadge ?>"><?= ucfirst($row['status']) ?></span></td>
                                
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button class="btn btn-sm btn-warning btn-action text-white btn-edit-user" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditUser"
                                                data-id="<?= $row['id'] ?>"
                                                data-nip="<?= $row['nip'] ?>"
                                                data-name="<?= $row['name'] ?>"
                                                data-password="<?= $row['password'] ?>"
                                                data-role="<?= $row['role'] ?>"
                                                data-status="<?= $row['status'] ?>">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        
                                        <form method="POST" onsubmit="return confirm('Yakin hapus user ini?');">
                                            <input type="hidden" name="id_user" value="<?= $row['id'] ?>">
                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger btn-action">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Belum ada data user.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADD USER -->
    <div class="modal fade" id="modalAddUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah User Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" name="nip" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="text" name="password" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select">
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_user" class="btn btn-primary">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT USER -->
    <div class="modal fade" id="modalEditUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_user" id="edit_id">
                        
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" id="edit_nip" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="text" name="password" id="edit_password" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" id="edit_role" class="form-select">
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_user" class="btn btn-warning">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const editUserButtons = document.querySelectorAll('.btn-edit-user');
        editUserButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.getAttribute('data-id');
                document.getElementById('edit_nip').value = this.getAttribute('data-nip');
                document.getElementById('edit_name').value = this.getAttribute('data-name');
                document.getElementById('edit_password').value = this.getAttribute('data-password');
                document.getElementById('edit_role').value = this.getAttribute('data-role');
                document.getElementById('edit_status').value = this.getAttribute('data-status');
            });
        });
    </script>
</body>
</html>
