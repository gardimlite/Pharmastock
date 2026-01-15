<?php
error_reporting(0);
ini_set('display_errors', 0);

// Panggil file koneksi pusat
include 'koneksi.php';

header('Content-Type: application/json; charset=utf-8');
$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['staff_id']) && isset($_POST['password'])) {
        
        $input_nip = $_POST['staff_id'];
        $input_pass = $_POST['password'];

        // Query nip (sesuai tabel users Anda)
        $query = "SELECT * FROM users WHERE nip = '$input_nip' AND password = '$input_pass'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $response['status'] = true;
            $response['message'] = "Login Berhasil";
            $response['user_id'] = $data['nip'];
            $response['name'] = $data['name'];
        } else {
            $response['status'] = false;
            $response['message'] = "Login Gagal: NIP/Pass Salah";
        }
    } else {
        $response['status'] = false;
        $response['message'] = "Parameter kurang";
    }
} else {
    $response['status'] = false;
    $response['message'] = "Method Salah";
}

echo json_encode($response);