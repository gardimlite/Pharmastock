<?php
error_reporting(0);
include 'koneksi.php';
header('Content-Type: application/json; charset=utf-8');

if (!$con) { echo json_encode(['medicines' => []]); exit; }

// Ambil Nama DAN Harga
$query = "SELECT medicine_name, price FROM stocks ORDER BY medicine_name ASC";
$result = mysqli_query($con, $query);

$medicines = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['medicine_name'])) {
            $medicines[] = [
                'medicine_name' => $row['medicine_name'],
                'price' => $row['price'] // Tambahkan harga
            ];
        }
    }
}
echo json_encode(['medicines' => $medicines]);
?>