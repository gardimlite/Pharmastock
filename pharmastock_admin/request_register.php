<?php
include 'koneksi.php';

// Set header agar dikenali sebagai respon JSON
header('Content-Type: application/json');

// Cek method request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data. Support baik x-www-form-urlencoded maupun Raw JSON
    $staff_id = $_POST['staff_id'] ?? '';
    $nama     = $_POST['nama'] ?? '';
    $password = $_POST['password'] ?? '';

    // Jika kosong, coba ambil dari php://input (Raw JSON)
    if (empty($staff_id) || empty($nama) || empty($password)) {
        $json = json_decode(file_get_contents('php://input'), true);
        $staff_id = $json['staff_id'] ?? '';
        $nama     = $json['nama'] ?? '';
        $password = $json['password'] ?? '';
    }

    // Validasi input
    if (empty($staff_id) || empty($nama) || empty($password)) {
        echo json_encode([
            "status" => false, 
            "message" => "Data tidak lengkap (staff_id, nama, password harus diisi)"
        ]);
        exit;
    }

    // Cek apakah staff_id sudah ada di tabel users (sudah terdaftar resmi)
    $cek_user = mysqli_query($con, "SELECT id FROM users WHERE nip = '$staff_id'");
    if (mysqli_num_rows($cek_user) > 0) {
        echo json_encode([
            "status" => false,
            "message" => "NIP/Staff ID ini sudah terdaftar sebagai user aktif."
        ]);
        exit;
    }

    // Cek apakah staff_id sudah ada di tabel registration_requests (sedang menunggu)
    $cek_req = mysqli_query($con, "SELECT id FROM registration_requests WHERE staff_id = '$staff_id'");
    if (mysqli_num_rows($cek_req) > 0) {
        echo json_encode([
            "status" => false,
            "message" => "Registrasi anda sedang menunggu persetujuan admin."
        ]);
        exit;
    }

    // Insert ke tabel registration_requests
    $query = "INSERT INTO registration_requests (staff_id, nama, password) VALUES ('$staff_id', '$nama', '$password')";
    
    if (mysqli_query($con, $query)) {
        echo json_encode([
            "status" => true,
            "message" => "Registrasi berhasil dikirim. Harap tunggu persetujuan Admin."
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Gagal mengirim registrasi: " . mysqli_error($con)
        ]);
    }

} else {
    echo json_encode([
        "status" => false,
        "message" => "Metode request tidak valid"
    ]);
}
?>
