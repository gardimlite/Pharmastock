<?php
include 'koneksi.php';

// --- LOGIC: STATISTIK DASHBOARD ---
// 1. Hitung Request Pending
$q_pending = mysqli_query($con, "SELECT COUNT(*) as total FROM requests WHERE status='pending'");
$d_pending = mysqli_fetch_assoc($q_pending);
$count_pending = $d_pending['total'];

// 2. Hitung Stok Menipis (< 50)
$q_low = mysqli_query($con, "SELECT COUNT(*) as total FROM stocks WHERE stock_qty < 50");
$d_low = mysqli_fetch_assoc($q_low);
$count_low = $d_low['total'];

// 3. Total Transaksi (Approved)
$q_income = mysqli_query($con, "SELECT SUM(total_price) as total FROM requests WHERE status='approved'");
$d_income = mysqli_fetch_assoc($q_income);
$total_income = $d_income['total'] ?? 0;

// --- CHART 1: Permintaan Per Bulan (Tahun Ini) ---
$monthly_labels = [];
$monthly_data = [];
// Inisialisasi array 12 bulan (0 - 11) dengan nilai 0
for ($i = 1; $i <= 12; $i++) {
    $monthly_data_map[$i] = 0;
}

$query_month = "SELECT MONTH(created_at) as bulan, COUNT(*) as total 
                FROM requests 
                WHERE YEAR(created_at) = YEAR(CURDATE()) 
                GROUP BY MONTH(created_at)";
$res_month = mysqli_query($con, $query_month);
while ($row = mysqli_fetch_assoc($res_month)) {
    $monthly_data_map[$row['bulan']] = (int)$row['total'];
}

// Convert ke format array untuk Chart.js
$month_names = ["Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agst", "Sept", "Okt", "Nov", "Des"];
foreach ($monthly_data_map as $bulan_num => $total) {
    $monthly_labels[] = $month_names[$bulan_num - 1]; // Index array mulai dari 0
    $monthly_data[] = $total;
}

// --- CHART 2: Permintaan Per Tahun (5 Tahun Terakhir) ---
$yearly_labels = [];
$yearly_data = [];

$query_year = "SELECT YEAR(created_at) as tahun, COUNT(*) as total 
               FROM requests 
               WHERE created_at >= DATE_SUB(NOW(), INTERVAL 5 YEAR) 
               GROUP BY YEAR(created_at) 
               ORDER BY tahun ASC";
$res_year = mysqli_query($con, $query_year);

while ($row = mysqli_fetch_assoc($res_year)) {
    $yearly_labels[] = $row['tahun'];
    $yearly_data[] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PharmaStock</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-stat {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .bg-soft-warning { background-color: #fff7ed; color: #c2410c; }
        .bg-soft-danger { background-color: #fef2f2; color: #dc2626; }
        .bg-soft-success { background-color: #f0fdf4; color: #16a34a; }
        
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
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .badge-pending { background-color: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .badge-approved { background-color: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .badge-rejected { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark navbar-expand-lg mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <i class="bi bi-hospital-fill"></i> PharmaStock Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link active fw-bold">Dashboard</a></li>
                    <li class="nav-item"><a href="stok_obat.php" class="nav-link">Stok Obat</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link">Kelola User</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Dashboard Overview</h4>
                <p class="text-muted mb-0">Pantau permintaan obat dan status stok terkini.</p>
            </div>
            <a href="stok_obat.php" class="btn btn-primary shadow-sm px-4 py-2 rounded-pill">
                <i class="bi bi-box-seam me-2"></i> Kelola Live Stock
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Pending Requests -->
            <div class="col-md-4">
                <div class="card card-stat bg-white h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-soft-warning me-3">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Permintaan Pending</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?= $count_pending ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Low Stock -->
            <div class="col-md-4">
                <div class="card card-stat bg-white h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-soft-danger me-3">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Stok Menipis (<50)</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?= $count_low ?> <small class="fs-6 text-muted">Item</small></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Total Income -->
            <div class="col-md-4">
                <div class="card card-stat bg-white h-100 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-soft-success me-3">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Penjualan (Approved)</h6>
                            <h3 class="fw-bold mb-0 text-dark">Rp <?= number_format($total_income, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Chart Bulan -->
            <div class="col-md-8">
                <div class="card main-card bg-white h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Tren Permintaan (Tahun Ini)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartBulan" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <!-- Chart Tahun -->
            <div class="col-md-4">
                <div class="card main-card bg-white h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Statistik Tahunan</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="width: 100%; max-width: 300px;">
                            <canvas id="chartTahun"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card main-card bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-check me-2 text-primary"></i>Daftar Permintaan Obat</h5>
                <div class="input-group w-auto">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" placeholder="Cari ID / Nama..." style="max-width: 200px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">ID Request</th>
                                <th>User</th>
                                <th>Nama Obat</th>
                                <th>Qty</th>
                                <th>Harga/Unit</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil data request urut dari yang terbaru
                            $query = "SELECT * FROM requests ORDER BY id DESC";
                            $result = mysqli_query($con, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Hitung Total Harga
                                    $harga_satuan = $row['price_per_unit'];
                                    $qty = $row['qty'];
                                    $total = $harga_satuan * $qty;

                                    // Tentukan Warna Badge Status
                                    $statusClass = 'badge-pending';
                                    $statusIcon = '<i class="bi bi-clock"></i>';
                                    
                                    if ($row['status'] == 'approved') {
                                        $statusClass = 'badge-approved';
                                        $statusIcon = '<i class="bi bi-check-circle"></i>';
                                    }
                                    if ($row['status'] == 'rejected') {
                                        $statusClass = 'badge-rejected';
                                        $statusIcon = '<i class="bi bi-x-circle"></i>';
                                    }
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?= $row['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                        <span>User <?= $row['user_id'] ?></span>
                                    </div>
                                </td>
                                <td class="fw-semibold text-dark"><?= $row['medicine_name'] ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $qty ?> Unit</span></td>
                                
                                <td class="text-muted">Rp <?= number_format($harga_satuan, 0, ',', '.') ?></td>
                                
                                <td class="fw-bold text-success">
                                    Rp <?= number_format($total, 0, ',', '.') ?>
                                </td>

                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $statusIcon ?> <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                
                                <td class="text-end pe-4">
                                    <?php if ($row['status'] == 'pending') { ?>
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="proses_status.php?id=<?= $row['id'] ?>&status=approved" 
                                               class="btn btn-action btn-success"
                                               data-bs-toggle="tooltip" title="Setujui"
                                               onclick="return confirm('Yakin setujui request ini? Stok akan berkurang otomatis.')">
                                               <i class="bi bi-check-lg"></i>
                                            </a>
                                            
                                            <a href="proses_status.php?id=<?= $row['id'] ?>&status=rejected" 
                                               class="btn btn-action btn-danger"
                                               data-bs-toggle="tooltip" title="Tolak"
                                               onclick="return confirm('Yakin tolak request ini?')">
                                               <i class="bi bi-x-lg"></i>
                                            </a>
                                        </div>
                                    <?php } else { ?>
                                        <span class="text-muted small fst-italic">Selesai</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-5 text-muted'>Belum ada data request.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-top-0">
                <small class="text-muted">Menampilkan <?= mysqli_num_rows($result) ?> data transaksi terakhir.</small>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // --- CHART SETUP ---
        
        // 1. Chart Bulanan (Bar)
        const ctxBulan = document.getElementById('chartBulan').getContext('2d');
        const chartBulan = new Chart(ctxBulan, {
            type: 'bar',
            data: {
                labels: <?= json_encode($monthly_labels) ?>,
                datasets: [{
                    label: 'Jumlah Permintaan',
                    data: <?= json_encode($monthly_data) ?>,
                    backgroundColor: 'rgba(13, 148, 136, 0.7)', // Warna tema
                    borderColor: 'rgba(13, 148, 136, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 2] }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // 2. Chart Tahunan (Doughnut)
        const ctxTahun = document.getElementById('chartTahun').getContext('2d');
        const chartTahun = new Chart(ctxTahun, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($yearly_labels) ?>,
                datasets: [{
                    label: 'Total Pertahun',
                    data: <?= json_encode($yearly_data) ?>,
                    backgroundColor: [
                        '#0f766e',
                        '#0d9488',
                        '#14b8a6',
                        '#5eead4',
                        '#ccfbf1'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 10 }
                    }
                }
            }
        });
    </script>
</body>
</html>
