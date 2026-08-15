<?php
session_start();

// 1. Hàm tự định nghĩa phân loại quy mô sự kiện
function classifyEventScale($participants) {
    if ($participants >= 200) {
        return "Quy mô Lớn (Hội trường/Sân trường)";
    } elseif ($participants >= 50) {
        return "Quy mô Vừa (Phòng hội thảo)";
    } else {
        return "Quy mô Nhỏ (Phòng học thường)";
    }
}

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}

// 2. Xử lý XÓA sự kiện
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['eventList'][$id])) {
        unset($_SESSION['eventList'][$id]);
        $_SESSION['eventList'] = array_values($_SESSION['eventList']); // Re-index mảng
    }
    header("Location: index.php");
    exit;
}

// 3. Chuẩn bị dữ liệu nếu đang ở chế độ SỬA
$editIndex = -1;
$editData = ['name' => '', 'type' => 'Hội thảo / Workshop', 'time' => '', 'location' => '', 'participants' => ''];

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['eventList'][$id])) {
        $editIndex = $id;
        $editData = $_SESSION['eventList'][$id];
    }
}

// 4. Xử lý THÊM MỚI hoặc CẬP NHẬT sự kiện
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveEvent'])) {
    $name = trim($_POST['eventName'] ?? '');
    $type = $_POST['eventType'] ?? '';
    $time = trim($_POST['eventTime'] ?? '');
    $location = trim($_POST['eventLocation'] ?? '');
    $participants = (int)($_POST['maxParticipants'] ?? 0);
    $currentIndex = (int)$_POST['editIndex'];

    if (empty($name) || empty($time) || empty($location) || $participants <= 0) {
        $error = "Vui lòng nhập đầy đủ thông tin hợp lệ!";
    } else {
        $scale = classifyEventScale($participants);
        $eventData = [
            'name' => htmlspecialchars($name),
            'type' => htmlspecialchars($type),
            'time' => htmlspecialchars($time),
            'location' => htmlspecialchars($location),
            'participants' => $participants,
            'scale' => $scale
        ];

        if ($currentIndex >= 0 && isset($_SESSION['eventList'][$currentIndex])) {
            // Cập nhật sự kiện cũ
            $_SESSION['eventList'][$currentIndex] = $eventData;
        } else {
            // Thêm sự kiện mới
            $_SESSION['eventList'][] = $eventData;
        }

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sự kiện</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; line-height: 1.6; background-color: #f8f9fa; }
        .container { max-width: 1050px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #0056b3; border-bottom: 2px solid #0056b3; padding-bottom: 8px; }
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 160px; font-weight: bold; }
        input, select { padding: 8px; width: 280px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 9px 20px; background-color: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #004085; }
        .btn-cancel { background-color: #6c757d; text-decoration: none; padding: 9px 15px; color: white; border-radius: 4px; font-size: 14px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background-color: #e9ecef; }
        .alert-error { color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .action-btn { text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 13px; font-weight: bold; color: white; }
        .btn-edit { background-color: #ffc107; color: #212529; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="container">
    <h2><?= $editIndex >= 0 ? "CẬP NHẬT SỰ KIỆN" : "QUẢN LÝ SỰ KIỆN CÂU LẠC BỘ" ?></h2>

    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="editIndex" value="<?= $editIndex ?>">

        <div class="form-group">
            <label for="eventName">Tên sự kiện:</label>
            <input type="text" id="eventName" name="eventName" required value="<?= $editData['name'] ?>" placeholder="VD: Workshop GenAI 2026">
        </div>

        <div class="form-group">
            <label for="eventType">Loại sự kiện:</label>
            <select id="eventType" name="eventType">
                <?php
                $types = ["Hội thảo / Workshop", "Cuộc thi", "Hoạt động Ngoại khóa", "Giao lưu / Teambuilding"];
                foreach ($types as $t) {
                    $selected = ($editData['type'] === $t) ? "selected" : "";
                    echo "<option value='$t' $selected>$t</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="eventTime">Thời gian diễn ra:</label>
            <input type="datetime-local" id="eventTime" name="eventTime" required value="<?= $editData['time'] ?>">
        </div>

        <div class="form-group">
            <label for="eventLocation">Địa điểm tổ chức:</label>
            <input type="text" id="eventLocation" name="eventLocation" required value="<?= $editData['location'] ?>" placeholder="VD: Hội trường A2">
        </div>

        <div class="form-group">
            <label for="maxParticipants">Số lượng tối đa:</label>
            <input type="number" id="maxParticipants" name="maxParticipants" min="1" required value="<?= $editData['participants'] ?>" placeholder="VD: 150">
        </div>

        <button type="submit" name="saveEvent"><?= $editIndex >= 0 ? "Cập Nhật Sự Kiện" : "Thêm Sự Kiện" ?></button>
        <?php if ($editIndex >= 0): ?>
            <a href="index.php" class="btn-cancel">Hủy bỏ</a>
        <?php endif; ?>
    </form>

    <h2>DANH SÁCH SỰ KIỆN ĐÃ ĐĂNG KÝ</h2>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên Sự Kiện</th>
                <th>Loại / Thời Gian / Địa Điểm</th>
                <th>Số Lượng</th>
                <th>Phân Loại Quy Mô</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($_SESSION['eventList'])): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Chưa có sự kiện nào được đăng ký.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($_SESSION['eventList'] as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><b><?= $item['name'] ?></b></td>
                        <td>
                            <b>Loại:</b> <?= $item['type'] ?><br>
                            <b>Thời gian:</b> <?= date('d/m/Y H:i', strtotime($item['time'])) ?><br>
                            <b>Địa điểm:</b> <?= $item['location'] ?>
                        </td>
                        <td><?= $item['participants'] ?> người</td>
                        <td><?= $item['scale'] ?></td>
                        <td>
                            <a href="index.php?action=edit&id=<?= $index ?>" class="action-btn btn-edit">Sửa</a>
                            <a href="index.php?action=delete&id=<?= $index ?>" class="action-btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>