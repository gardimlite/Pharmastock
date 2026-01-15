<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Ambil data dari tabel request
    $query_get = "SELECT * FROM registration_requests WHERE id = '$id'";
    $result = mysqli_query($con, $query_get);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        $nip      = $data['staff_id'];
        $nama     = $data['nama'];
        $password = $data['password'];

        // 2. Cek apakah NIP sudah ada di tabel users (untuk menghindari duplikat error)
        $cek_double = mysqli_query($con, "SELECT * FROM users WHERE nip = '$nip'");
        if (mysqli_num_rows($cek_double) > 0) {
            echo "<script>
                alert('Gagal: User dengan NIP $nip sudah ada di data User Aktif.');
                window.location='users.php';
            </script>";
            exit;
        }

        // 3. Pindahkan ke tabel users
        // Default: role = 'staff', status = 'active'
        $query_insert = "INSERT INTO users (nip, name, password, role, status) 
                         VALUES ('$nip', '$nama', '$password', 'staff', 'active')";

        if (mysqli_query($con, $query_insert)) {
            // 4. Jika berhasil masuk tabel users, hapus dari tabel requests
            mysqli_query($con, "DELETE FROM registration_requests WHERE id = '$id'");

            echo "<script>
                alert('User berhasil diapprove dan diaktifkan!');
                window.location='users.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal Insert ke Users: " . mysqli_error($con) . "');
                window.location='users.php';
            </script>";
        }

    } else {
        echo "<script>alert('Data registration tidak ditemukan!'); window.location='users.php';</script>";
    }

} else {
    // Jika diakses langsung tanpa parameter
    header("Location: users.php");
}
?>
