<?php

// Atur header agar responsnya selalu JSON
header('Content-Type: application/json');

// 1. URL Webhook n8n Anda (Ganti dengan URL Anda)
$webhook_url = 'https://n8n.muzakie.my.id/webhook/d4ba8012-54cf-435e-80c6-a06d9d493a81';

// 2. Ambil data pesan dari JavaScript
$pesan_user = $_POST['pesan'] ?? 'Tidak ada pesan';

// 3. Susun data yang akan dikirim ke n8n
$data_body = [
    'isi_pesan' => $pesan_user,
    'user_id' => 'user_123' // Anda bisa tambahkan info lain
];
$data_json = json_encode($data_body);

// 4. Inisialisasi cURL
$ch = curl_init();

// 5. Set Opsi cURL
curl_setopt($ch, CURLOPT_URL, $webhook_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_json)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Sangat penting!
curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Beri waktu n8n untuk memproses (misal: 30 detik)

// 6. Eksekusi cURL dan TUNGGU balasan dari n8n
$response_n8n = curl_exec($ch);

// 7. Cek jika ada error cURL
if (curl_errno($ch)) {
    http_response_code(500); // Server Error
    echo json_encode([
        'status' => 'error',
        'message' => 'Error cURL: ' . curl_error($ch)
    ]);
    curl_close($ch);
    exit; // Hentikan script
}

// 8. Tutup cURL
curl_close($ch);

// 9. Proses balasan dari n8n
// Kita asumsikan n8n membalas dengan format JSON, misal: {"reply": "Ini balasan bot"}
$data_n8n = json_decode($response_n8n, true);

// Periksa apakah JSON valid dan ada 'key' balasannya
if (json_last_error() !== JSON_ERROR_NONE || !isset($data_n8n['reply'])) {
    // Jika n8n tidak membalas dengan benar
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Format balasan dari n8n tidak valid.',
        'raw_response' => $response_n8n // Tampilkan raw response untuk debug
    ]);
} else {
    // 10. KIRIM BALASAN n8n KEMBALI KE JAVASCRIPT
    echo json_encode([
        'status' => 'success',
        'balasan_dari_n8n' => $data_n8n['reply'] // Ambil 'key' balasan
    ]);
}

?>