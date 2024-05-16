<?php
require 'config.php';

$username = 'admin';
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->execute([$username, $hashedPassword]);

echo "User registered successfully.";
?>
