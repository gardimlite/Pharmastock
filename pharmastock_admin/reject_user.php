<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data dari registration_requests
    $query = "DELETE FROM registration_requests WHERE id = '$id'";

    if (mysqli_query($con, $query)) {
        echo "<script>
            alert('Permintaan registrasi ditolak (dihapus).');
            window.location='users.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menolak user: " . mysqli_error($con) . "');
            window.location='users.php';
        </script>";
    }
} else {
    // Jika diakses langsung tanpa parameter
    header("Location: users.php");
}
?>
