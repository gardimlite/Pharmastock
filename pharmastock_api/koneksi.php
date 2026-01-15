<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_pharmastock"; // Nama DB Anda yang benar

$con = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_errno()) {
    // Gunakan die() agar eksekusi berhenti total jika gagal
    die(json_encode([
        "status" => false, 
        "message" => "Gagal Konek Database: " . mysqli_connect_error()
    ]));
}

// END OF FILE - Jangan tambah tag penutup PHP 