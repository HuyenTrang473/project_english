<?php
$pdo = new PDO('mysql:host=localhost;dbname=be_23', 'root', '');
$stmt = $pdo->query("SELECT id, name, email, role FROM users LIMIT 10");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
