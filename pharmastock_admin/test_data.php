<?php
include 'koneksi.php';

// Data dummy
$staff_id = "123456";
$nama = "Dr. Contoh (Dummy)";
$password = "rahasia123";

// Cek dulu biar gak double
$cek = mysqli_query($con, "SELECT * FROM registration_requests WHERE staff_id = '$staff_id'");
if (mysqli_num_rows($cek) == 0) {
    $query = "INSERT INTO registration_requests (staff_id, nama, password) VALUES ('$staff_id', '$nama', '$password')";
    if (mysqli_query($con, $query)) {
        echo "<h1>Berhasil!</h1>";
        echo "<p>Satu data dummy telah ditambahkan.</p>";
        echo "<p><a href='users.php'>Buka users.php sekarang</a> untuk melihat perubahannya.</p>";
    } else {
        echo "Gagal insert: " . mysqli_error($con);
    }
} else {
    echo "<h1>Data sudah ada!</h1>";
    echo "<p><a href='users.php'>Buka users.php</a> untuk mengecek.</p>";
}
?>
