<?php
include 'koneksi.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $cek_request = mysqli_query($con, "SELECT * FROM requests WHERE id = '$id'");
    $data_req = mysqli_fetch_assoc($cek_request);

    $nama_obat = $data_req['medicine_name'];
    $jumlah_minta = $data_req['qty'];
    $status_sekarang = $data_req['status'];

    if ($status_sekarang != 'pending') {
        echo "<script>alert('Request sudah diproses!'); window.location='index.php';</script>";
        exit;
    }

    if ($status == 'approved') {
        // Cek stok dulu
        $cek_stok = mysqli_query($con, "SELECT stock_qty FROM stocks WHERE medicine_name = '$nama_obat'");
        
        if (mysqli_num_rows($cek_stok) > 0) {
            $data_stok = mysqli_fetch_assoc($cek_stok);
            
            // Validasi: Stok cukup gak?
            if ($data_stok['stock_qty'] >= $jumlah_minta) {
                // PENGURANGAN (Barang Keluar ke User)
                $update_stok = "UPDATE stocks SET stock_qty = stock_qty - $jumlah_minta WHERE medicine_name = '$nama_obat'";
                mysqli_query($con, $update_stok);
            } else {
                echo "<script>alert('Stok tidak cukup!'); window.location='index.php';</script>";
                exit;
            }
        }
    }

    $query_update = "UPDATE requests SET status = '$status' WHERE id = '$id'";
    mysqli_query($con, $query_update);
    header("Location: index.php");
}
?>