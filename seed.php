<?php
require 'db.php';

// Khởi tạo Faker với ngôn ngữ Tiếng Việt
$faker = Faker\Factory::create('vi_VN');

echo "Đang sinh dữ liệu mẫu...\n";

// 1. Sinh 3 Cơ sở (Branches)
$branch_names = ['Cơ sở chính Tân Phú', 'Phòng máy khu C', 'Trung tâm liên kết Q10'];
foreach ($branch_names as $name) {
    $stmt = $pdo->prepare("INSERT INTO branches (branch_name, address) VALUES (?, ?)");
    $stmt->execute([$name, $faker->address]);
}

// 2. Sinh 10 Giảng viên (Teachers)
$expertises = ['Lập trình Web', 'Cơ sở dữ liệu', 'Hệ thống thông tin', 'Mạng máy tính'];
for ($i = 0; $i < 10; $i++) {
    $stmt = $pdo->prepare("INSERT INTO teachers (full_name, email, phone, expertise) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $faker->name,
        $faker->unique()->safeEmail,
        $faker->phoneNumber,
        $faker->randomElement($expertises) // Chọn ngẫu nhiên 1 chuyên môn
    ]);
}

echo "Hoàn tất sinh dữ liệu Cơ sở và Giảng viên!\n";