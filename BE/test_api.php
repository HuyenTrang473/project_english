<?php
$ch = curl_init('http://localhost:8000/api/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'teacher1@tienganhonline.test',
    'password' => 'password123'
]));

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo "=== Login Test ===\n";
if (isset($data['access_token'])) {
    $token = $data['access_token'];
    echo "✓ Token: " . substr($token, 0, 20) . "...\n";
    echo "✓ User: " . $data['user']['name'] . "\n\n";

    // Now test the test API
    $ch = curl_init('http://localhost:8000/api/teacher/bai-tests');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $tests = json_decode($response, true);
    echo "=== Tests Retrieved ===\n";
    echo "Total Tests: " . $tests['data']['total'] . "\n";
    echo "Per Page: " . $tests['data']['per_page'] . "\n";
    foreach ($tests['data']['data'] as $test) {
        echo "- " . $test['ten_bai_test'] . " (Status: " . $test['trang_thai'] . ")\n";
    }
} else {
    echo "✗ Login failed:\n";
    print_r($data);
}
