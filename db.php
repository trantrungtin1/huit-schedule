<?php
require 'vendor/autoload.php'; // Dòng này rất quan trọng để nạp Faker!

// Gắn cứng thông tin kết nối thay vì dùng .env
$host = '127.0.0.1';
$db   = 'huit_db';
$user = 'root';
$pass = ''; // Mặc định Laragon không có mật khẩu

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}