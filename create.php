<?php
require 'db.php';

$branches = $pdo->query("SELECT * FROM branches")->fetchAll();

$step = 1;
$available_teachers = [];

// Xử lý khi người dùng submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['find_teacher'])) {
        // BƯỚC 1: Tìm giảng viên không bận vào Thứ và Ca này
        $step = 2;
        $day = $_POST['day_of_week'];
        $shift = $_POST['shift_or_periods'];
        
        // Truy vấn: Lấy các giảng viên KHÔNG CÓ trong danh sách lịch dạy vào cùng Thứ & Ca
        $sql = "SELECT * FROM teachers WHERE id NOT IN (
                    SELECT teacher_id FROM schedules WHERE day_of_week = ? AND shift_or_periods = ?
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$day, $shift]);
        $available_teachers = $stmt->fetchAll();
        
    } elseif (isset($_POST['save_schedule'])) {
        // BƯỚC 2: Lưu lịch dạy vào CSDL
        $sql = "INSERT INTO schedules (teacher_id, branch_id, subject_name, room, day_of_week, shift_or_periods) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['teacher_id'], 
            $_POST['branch_id'], 
            $_POST['subject_name'], 
            $_POST['room'], 
            $_POST['day_of_week'], 
            $_POST['shift_or_periods']
        ]);
        // Quay về trang chủ sau khi lưu thành công
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Lịch Dạy Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 700px;">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Thêm Lịch Giảng Dạy Mới</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="create.php">
                <!-- Giữ lại dữ liệu đã nhập nếu đang ở Bước 2 -->
                <div class="mb-3">
                    <label class="form-label">Môn học:</label>
                    <input type="text" name="subject_name" class="form-control" required 
                           value="<?= $_POST['subject_name'] ?? '' ?>" <?= $step == 2 ? 'readonly' : '' ?>>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Cơ sở:</label>
                    <?php if ($step == 1): ?>
                        <select name="branch_id" class="form-select" required>
                            <?php foreach ($branches as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['branch_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="hidden" name="branch_id" value="<?= $_POST['branch_id'] ?>">
                        <input type="text" class="form-control" readonly 
                               value="<?= htmlspecialchars(array_column($branches, 'branch_name', 'id')[$_POST['branch_id']] ?? '') ?>">
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phòng học:</label>
                        <input type="text" name="room" class="form-control" required 
                               value="<?= $_POST['room'] ?? '' ?>" <?= $step == 2 ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Thứ:</label>
                        <?php if ($step == 1): ?>
                            <select name="day_of_week" class="form-select">
                                <option value="Thứ 2">Thứ 2</option>
                                <option value="Thứ 3">Thứ 3</option>
                                <option value="Thứ 4">Thứ 4</option>
                                <option value="Thứ 5">Thứ 5</option>
                                <option value="Thứ 6">Thứ 6</option>
                                <option value="Thứ 7">Thứ 7</option>
                            </select>
                        <?php else: ?>
                            <input type="text" name="day_of_week" class="form-control" readonly value="<?= $_POST['day_of_week'] ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ca / Tiết:</label>
                        <?php if ($step == 1): ?>
                            <select name="shift_or_periods" class="form-select">
                                <option value="Sáng (Tiết 1-3)">Sáng (Tiết 1-3)</option>
                                <option value="Sáng (Tiết 4-6)">Sáng (Tiết 4-6)</option>
                                <option value="Chiều (Tiết 7-9)">Chiều (Tiết 7-9)</option>
                                <option value="Chiều (Tiết 10-12)">Chiều (Tiết 10-12)</option>
                                <option value="Tối">Tối</option>
                            </select>
                        <?php else: ?>
                            <input type="text" name="shift_or_periods" class="form-control" readonly value="<?= $_POST['shift_or_periods'] ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($step == 1): ?>
                    <button type="submit" name="find_teacher" class="btn btn-primary w-100">Kiểm tra & Gợi ý Giảng viên</button>
                <?php endif; ?>

                <!-- BƯỚC 2: Hiển thị danh sách giảng viên trống lịch -->
                <?php if ($step == 2): ?>
                    <hr>
                    <h5 class="text-primary">Gợi ý Giảng viên (Trống lịch vào thời gian này)</h5>
                    <div class="mb-4">
                        <?php if (count($available_teachers) > 0): ?>
                            <select name="teacher_id" class="form-select border-primary">
                                <?php foreach ($available_teachers as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?> - Chuyên môn: <?= htmlspecialchars($t['expertise']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <div class="alert alert-danger">Không có giảng viên nào trống lịch vào thời gian này!</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="create.php" class="btn btn-secondary">Chọn lại thời gian</a>
                        <?php if (count($available_teachers) > 0): ?>
                            <button type="submit" name="save_schedule" class="btn btn-success">Lưu lịch dạy</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="index.php" class="text-decoration-none">← Quay lại trang quản lý</a>
        </div>
    </div>
</div>
</body>
</html>