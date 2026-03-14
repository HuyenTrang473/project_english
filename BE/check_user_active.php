<?php
$pdo = new PDO('mysql:host=localhost;dbname=be_23', 'root', '');
$stmt = $pdo->query("SELECT id, name, email, role, is_active FROM users WHERE id = 1");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
