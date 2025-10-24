<?php

// Atur header agar responsnya adalah JSON
header('Content-Type: application/json');

// 1. URL Webhook n8n Anda (Ganti dengan URL Anda)
$webhook_url = 'https://n8n.muzakie.my.id/webhook-test/68eb4b6e-ff9b-47d7-94de-dba0dbbde638';

// 2. Ambil data dari JavaScript (yang dikirim lewat 'body: formData')
// Kita gunakan '??' untuk memberi nilai default jika data tidak ada
$nama = $_POST['nama'] ?? 'Anonim';
$pesan = $_POST['pesan'] ?? 'Tidak ada pesan';

// 3. Susun data yang ingin Anda kirim ke n8n
$data_body = [
    'nama_pengirim' => $nama,
    'isi_pesan' => $pesan,
    'sumber' => 'Web Chat'
];

// 4. Konversi array PHP menjadi string JSON
$data_json = json_encode($data_body);

// 5. Inisialisasi cURL
$ch = curl_init();

// 6. Set Opsi cURL
curl_setopt($ch, CURLOPT_URL, $webhook_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_json)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Tambahkan timeout 10 detik

// 7. Eksekusi cURL
$response_n8n = curl_exec($ch);

// 8. Cek jika ada error cURL
if (curl_errno($ch)) {
    // Jika cURL gagal, kirim respons error ke JavaScript
    http_response_code(500); // Server Error
    echo json_encode([
        'status' => 'error',
        'message' => 'Error cURL: ' . curl_error($ch)
    ]);
} else {
    // Jika cURL berhasil, kirim respons sukses ke JavaScript
    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil dikirim ke n8n.',
        'response_from_n8n' => json_decode($response_n8n) // Opsional: teruskan respons n8n
    ]);
}

// 9. Tutup sesi cURL
curl_close($ch);

?>