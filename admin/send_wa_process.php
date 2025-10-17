<?php
include "../app/config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $telepon = $_POST['telepon'];
    $pesan = $_POST['pesan'];
    $reminder_ke = $_POST['reminder_ke'];

    // --- PENTING: Pengaturan untuk API WAGATE ---
    // URL ini HARUS menunjuk ke server 'wagate' yang Anda jalankan dengan perintah 'php artisan serve'.
    // Secara default, alamatnya adalah http://127.0.0.1:8000
    $apiUrl = "http://127.0.0.1:8000/api/send-message"; 
    
    $data = [
        'nomor' => $telepon,
        'pesan' => $pesan,
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    // Tambahkan timeout untuk mencegah halaman menunggu terlalu lama
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); 

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    $api_response = json_decode($response, true);

    $status_kirim = 'failed';
    $error_message = '';

    if ($curl_error) {
        $status_kirim = 'failed';
        $error_message = "Curl Error: " . $curl_error;
    } elseif ($http_code == 200 && isset($api_response['status']) && $api_response['status'] == 'success') {
        $status_kirim = 'sent';
    } else {
        $status_kirim = 'failed';
        $error_message = isset($api_response['message']) ? $api_response['message'] : 'Gagal terhubung atau mendapat respon tidak valid dari WhatsApp Gateway.';
    }

    // Simpan ke log
    $waktu_kirim = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO log_kirim_wa (arsip_id, nama, telepon, pesan, status, waktu_kirim, reminder_ke) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $id, $nama, $telepon, $pesan, $status_kirim, $waktu_kirim, $reminder_ke);
    
    if ($stmt->execute()) {
        if ($status_kirim == 'sent') {
            echo json_encode(['status' => 'success', 'message' => 'Pesan berhasil dikirim ke ' . $nama]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim pesan ke ' . $nama . '. Error: ' . $error_message]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan log ke database.']);
    }

    $stmt->close();
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
