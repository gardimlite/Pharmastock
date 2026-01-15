<?php
// Matikan error HTML agar JSON bersih
error_reporting(0);
ini_set('display_errors', 0);

include 'koneksi.php';

header('Content-Type: application/json; charset=utf-8');
$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validasi parameter (Pastikan Android mengirim key 'price', bukan 'price_per_unit')
    // Sesuai logcat Anda: user_id, medicine_name, qty, price
    if (isset($_POST['user_id']) && isset($_POST['medicine_name']) && isset($_POST['qty']) && isset($_POST['price'])) {
        
        $user_id       = $_POST['user_id'];
        $medicine_name = $_POST['medicine_name'];
        $qty           = $_POST['qty'];
        $price         = $_POST['price']; // Harga Satuan dari Android

        // 1. HITUNG TOTAL HARGA (Wajib untuk kolom total_price)
        // Pastikan dikonversi ke angka dulu
        $qty_int = intval($qty);
        $price_int = doubleval($price);
        $total_price = $qty_int * $price_int;

        // Sanitasi String
        $user_id       = mysqli_real_escape_string($con, $user_id);
        $medicine_name = mysqli_real_escape_string($con, $medicine_name);
        $qty           = mysqli_real_escape_string($con, $qty);
        $price         = mysqli_real_escape_string($con, $price);
        $total_price   = mysqli_real_escape_string($con, $total_price);

        // 2. QUERY INSERT YANG BENAR (Sesuai Gambar Tabel Anda)
        // Kolom DB: user_id, medicine_name, qty, price_per_unit, total_price, status
        
        $query = "INSERT INTO requests (user_id, medicine_name, qty, price_per_unit, total_price, status, created_at) 
                  VALUES ('$user_id', '$medicine_name', '$qty', '$price', '$total_price', 'pending', NOW())";
        
        if (mysqli_query($con, $query)) {
            $response['status'] = true;
            $response['message'] = "Request Berhasil! Total: Rp " . number_format($total_price);
        } else {
            $response['status'] = false;
            $response['message'] = "Gagal SQL: " . mysqli_error($con);
        }

    } else {
        $response['status'] = false;
        $response['message'] = "Data tidak lengkap";
    }
} else {
    $response['status'] = false;
    $response['message'] = "Metode Request Salah";
}

echo json_encode($response);
?>