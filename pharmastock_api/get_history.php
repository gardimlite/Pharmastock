<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        
        // QUERY DISESUAIKAN DENGAN NAMA KOLOM DI DB ANDA
        $query = "SELECT 
                    id as request_id, 
                    medicine_name, 
                    qty, 
                    price_per_unit as price, 
                    total_price, 
                    status, 
                    created_at as request_date 
                  FROM requests 
                  WHERE user_id = '$user_id' 
                  ORDER BY created_at DESC";
        
        $result = mysqli_query($con, $query);
        
        if ($result) {
            $data = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            
            echo json_encode(array(
                "status" => true, 
                "message" => "Berhasil mengambil data",
                "data" => $data
            ));
        } else {
            echo json_encode(array("status" => false, "message" => "Gagal query: " . mysqli_error($con)));
        }
    } else {
        echo json_encode(array("status" => false, "message" => "User ID tidak ditemukan"));
    }
}
?>