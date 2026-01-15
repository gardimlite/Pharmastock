<?php
include 'koneksi.php';

// --- LOGIKA CRUD ---

// 1. TAMBAH OBAT BARU (CREATE)
if (isset($_POST['add_medicine'])) {
    $name = $_POST['medicine_name'];
    $category = $_POST['category'];
    $qty = $_POST['stock_qty'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $expiry = $_POST['expiry_date'];

    $query = "INSERT INTO stocks (medicine_name, category, stock_qty, unit, price, expiry_date) 
              VALUES ('$name', '$category', '$qty', '$unit', '$price', '$expiry')";
    
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Obat berhasil ditambahkan!'); window.location='stok_obat.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah obat: " . mysqli_error($con) . "');</script>";
    }
}

// 2. EDIT DATA OBAT (UPDATE)
if (isset($_POST['edit_medicine'])) {
    $id = $_POST['id_obat'];
    $name = $_POST['medicine_name'];
    $category = $_POST['category'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $expiry = $_POST['expiry_date'];
    // Note: Stok biasanya diupdate lewat fitur Restock, tapi bisa juga disini jika mau koreksi manual.
    // Kita biarkan stok diedit manual juga untuk koreksi opname.
    $qty = $_POST['stock_qty'];

    $query = "UPDATE stocks SET 
              medicine_name='$name', category='$category', stock_qty='$qty', 
              unit='$unit', price='$price', expiry_date='$expiry' 
              WHERE id='$id'";

    if (mysqli_query($con, $query)) {
        echo "<script>alert('Data obat berhasil diperbarui!'); window.location='stok_obat.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($con) . "');</script>";
    }
}

// 3. HAPUS OBAT (DELETE)
if (isset($_POST['delete_medicine'])) {
    $id = $_POST['id_obat'];
    $query = "DELETE FROM stocks WHERE id='$id'";
    
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Obat berhasil dihapus!'); window.location='stok_obat.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: " . mysqli_error($con) . "');</script>";
    }
}

// 4. RESTOCK CEPAT (EXISTING FEATURE)
if (isset($_POST['tambah_stok'])) {
    $id_obat = $_POST['id_obat'];
    $qty_masuk = $_POST['qty_masuk'];

    $query_tambah = "UPDATE stocks SET stock_qty = stock_qty + $qty_masuk WHERE id = '$id_obat'";
    
    if (mysqli_query($con, $query_tambah)) {
        echo "<script>alert('Berhasil restock $qty_masuk unit!'); window.location='stok_obat.php';</script>";
    } else {
        echo "<script>alert('Gagal restock!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok - PharmaStock</title>
    
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
        .badge-stock-low { background-color: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
        .badge-stock-ok { background-color: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
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
                    <li class="nav-item"><a href="stok_obat.php" class="nav-link active fw-bold">Stok Obat</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link">Kelola User</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Manajemen Stok Obat</h4>
                <p class="text-muted mb-0">Kelola data obat, harga, dan stok gudang.</p>
            </div>
            <button class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="bi bi-plus-lg me-2"></i> Tambah Obat Baru
            </button>
        </div>

        <div class="card main-card bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>Data Live Stock</h5>
                <div class="input-group w-auto">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" placeholder="Cari Obat..." id="searchInput">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4 text-center">ID</th>
                                <th>Nama Obat</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Exp. Date</th>
                                <th class="text-center">Restock Cepat</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM stocks ORDER BY id DESC";
                            $result = mysqli_query($con, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $isLowStock = ($row['stock_qty'] < 50);
                                    $stockBadgeClass = $isLowStock ? 'badge-stock-low' : 'badge-stock-ok';
                            ?>
                            <tr>
                                <td class="ps-4 text-center text-muted">#<?= $row['id'] ?></td>
                                <td class="fw-bold text-dark"><?= $row['medicine_name'] ?></td>
                                <td><span class="badge bg-light text-secondary border"><?= $row['category'] ?></span></td>
                                <td class="text-center">
                                    <span class="badge <?= $stockBadgeClass ?> p-2 rounded-pill">
                                        <?= $row['stock_qty'] ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted"><?= $row['unit'] ?></td>
                                <td class="text-end fw-semibold">Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                                <td class="text-center text-muted small"><?= $row['expiry_date'] ?></td>
                                
                                <!-- Form Restock Cepat -->
                                <td>
                                    <form method="POST" class="d-flex gap-1 justify-content-center align-items-center">
                                        <input type="hidden" name="id_obat" value="<?= $row['id'] ?>">
                                        <input type="number" name="qty_masuk" class="form-control form-control-sm text-center" style="width: 70px;" placeholder="+Qty" min="1" required>
                                        <button type="submit" name="tambah_stok" class="btn btn-sm btn-success btn-action" title="Simpan Restock">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                </td>

                                <!-- Tombol Aksi Edit/Delete -->
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button class="btn btn-sm btn-warning btn-action text-white btn-edit" 
                                                data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= $row['medicine_name'] ?>"
                                                data-category="<?= $row['category'] ?>"
                                                data-qty="<?= $row['stock_qty'] ?>"
                                                data-unit="<?= $row['unit'] ?>"
                                                data-price="<?= $row['price'] ?>"
                                                data-expiry="<?= $row['expiry_date'] ?>">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        
                                        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus obat ini?');">
                                            <input type="hidden" name="id_obat" value="<?= $row['id'] ?>">
                                            <button type="submit" name="delete_medicine" class="btn btn-sm btn-danger btn-action">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center py-5 text-muted'>Data stok kosong.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADD -->
    <div class="modal fade" id="modalAdd" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-capsule me-2"></i>Tambah Obat Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" name="medicine_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="category" class="form-select" required>
                                    <option value="Antibiotik">Antibiotik</option>
                                    <option value="Analgesik">Analgesik</option>
                                    <option value="Vitamin">Vitamin</option>
                                    <option value="Sirup Batuk">Sirup Batuk</option>
                                    <option value="Antiseptik">Antiseptik</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Satuan</label>
                                <select name="unit" class="form-select" required>
                                    <option value="Box">Box</option>
                                    <option value="Strip">Strip</option>
                                    <option value="Botol">Botol</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Pcs">Pcs</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" name="stock_qty" class="form-control" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga per Unit (Rp)</label>
                                <input type="number" name="price" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Kadaluarsa</label>
                            <input type="date" name="expiry_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_medicine" class="btn btn-primary">Simpan Obat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Data Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_obat" id="edit_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" name="medicine_name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="category" id="edit_category" class="form-select" required>
                                    <option value="Antibiotik">Antibiotik</option>
                                    <option value="Analgesik">Analgesik</option>
                                    <option value="Vitamin">Vitamin</option>
                                    <option value="Sirup Batuk">Sirup Batuk</option>
                                    <option value="Antiseptik">Antiseptik</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Satuan</label>
                                <select name="unit" id="edit_unit" class="form-select" required>
                                    <option value="Box">Box</option>
                                    <option value="Strip">Strip</option>
                                    <option value="Botol">Botol</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Pcs">Pcs</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok (Koreksi)</label>
                                <input type="number" name="stock_qty" id="edit_qty" class="form-control" required min="0">
                                <small class="text-muted">Gunakan fitur Restock untuk penambahan barang masuk.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga per Unit (Rp)</label>
                                <input type="number" name="price" id="edit_price" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Kadaluarsa</label>
                            <input type="date" name="expiry_date" id="edit_expiry" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_medicine" class="btn btn-warning">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script untuk mengisi Modal Edit -->
    <script>
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.getAttribute('data-id');
                document.getElementById('edit_name').value = this.getAttribute('data-name');
                document.getElementById('edit_category').value = this.getAttribute('data-category');
                document.getElementById('edit_qty').value = this.getAttribute('data-qty');
                document.getElementById('edit_unit').value = this.getAttribute('data-unit');
                document.getElementById('edit_price').value = this.getAttribute('data-price');
                document.getElementById('edit_expiry').value = this.getAttribute('data-expiry');
            });
        });

        // Simple Search Filter
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
