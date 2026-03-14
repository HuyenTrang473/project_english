<?php
$pdo = new PDO('mysql:host=localhost;dbname=be_23', 'root', '');
$stmt = $pdo->prepare("UPDATE users SET role = 'giao_vien' WHERE id = 1");
$stmt->execute();
echo "Updated user 1 to role 'giao_vien'\n";

// Verify the change
$stmt = $pdo->query("SELECT id, name, email, role FROM users WHERE id = 1");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
