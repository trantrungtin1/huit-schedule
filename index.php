<?php
// Bật hiển thị lỗi PHP (giúp bạn dễ debug nếu có lỗi)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối cơ sở dữ liệu
require 'db.php';

// Lấy giá trị lọc từ URL (nếu người dùng có bấm nút Lọc)
$filter_branch = $_GET['branch_id'] ?? '';
$filter_teacher = $_GET['teacher_id'] ?? '';

// Lấy danh sách Cơ sở và Giảng viên từ CSDL để đổ vào ô thả xuống (Select box)
$branches = $pdo->query("SELECT * FROM branches")->fetchAll();
$teachers = $pdo->query("SELECT * FROM teachers")->fetchAll();

// Khởi tạo câu truy vấn cơ bản lấy lịch dạy (Kết nối 3 bảng bằng JOIN)
$sql = "SELECT s.*, t.full_name, b.branch_name 
        FROM schedules s 
        JOIN teachers t ON s.teacher_id = t.id 
        JOIN branches b ON s.branch_id = b.id 
        WHERE 1=1";
$params = [];

// Thêm điều kiện lọc nếu người dùng có chọn Cơ sở
if (!empty($filter_branch)) {
    $sql .= " AND s.branch_id = ?";
    $params[] = $filter_branch;
}

// Thêm điều kiện lọc nếu người dùng có chọn Giảng viên
if (!empty($filter_teacher)) {
    $sql .= " AND s.teacher_id = ?";
    $params[] = $filter_teacher;
}

// Sắp xếp lịch dạy theo Thứ và Ca học
$sql .= " ORDER BY s.day_of_week ASC, s.shift_or_periods ASC";

// Thực thi câu truy vấn
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$schedules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>HUIT Schedule Hub - Quản lý lịch giảng dạy</title>
    <!-- Nhúng thư viện Bootstrap 5 để giao diện đẹp hơn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">HUIT Schedule Hub - Quản lý lịch giảng dạy</h2>
    
    <!-- Form Lọc Dữ Liệu -->
    <form method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
        <div class="col-md-4">
            <select name="branch_id" class="form-select">
                <option value="">-- Tất cả cơ sở --</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $filter_branch == $b['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['branch_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="teacher_id" class="form-select">
                <option value="">-- Tất cả giảng viên --</option>
                <?php foreach ($teachers as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $filter_teacher == $t['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Lọc lịch dạy</button>
            <a href="index.php" class="btn btn-secondary">Xóa lọc</a>
            <a href="create.php" class="btn btn-success float-end">+ Thêm lịch mới</a>
        </div>
    </form>

    <!-- Bảng hiển thị Lịch dạy -->
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>STT</th>
                <th>Giảng viên</th>
                <th>Môn học</th>
                <th>Cơ sở</th>
                <th>Phòng học</th>
                <th>Thứ</th>
                <th>Ca/Tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($schedules) > 0): ?>
                <?php foreach ($schedules as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= htmlspecialchars($row['branch_name']) ?></td>
                        <td><?= htmlspecialchars($row['room']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['day_of_week']) ?></span></td>
                        <td><?= htmlspecialchars($row['shift_or_periods']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-danger">Không có lịch dạy nào phù hợp.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>