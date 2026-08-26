<?php
require_once 'EventRepository.php';

// Xử lý Form bằng phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') createEvent($_POST);
    elseif ($action === 'update') updateEvent($_POST['event_id'], $_POST);
    elseif ($action === 'delete') deleteEvent($_POST['event_id']); 

    header('Location: events.php');
    exit;
}

$editData = isset($_GET['edit_id']) ? getEventById($_GET['edit_id']) : null;
$events = getAllEvents();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sự Kiện</title>
    <style>
        body { font-family: sans-serif; background: #f0f4f8; padding: 30px; display: flex; flex-direction: column; align-items: center; gap: 20px; }
        .box { background: #fff; padding: 25px; border-radius: 16px; width: 100%; max-width: 900px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { color: #1e3a8a; margin-top: 0; border-bottom: 2px solid #3b82f6; padding-bottom: 8px; }
        input, textarea { width: 100%; padding: 8px; margin: 6px 0 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .btn { background: #1e3a8a; color: #fff; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body>

<div class="box">
    <h2><?= $editData ? 'SỬA SỰ KIỆN' : 'THÊM SỰ KIỆN MỚI' ?></h2>
    <form method="POST">
        <input type="hidden" name="action" value="<?= $editData ? 'update' : 'create' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($editData['event_id']) ?>">
        <?php endif; ?>

        <label>Tên sự kiện:</label>
        <input type="text" name="event_name" required value="<?= htmlspecialchars($editData['event_name'] ?? '') ?>">

        <label>Thời gian bắt đầu:</label>
        <input type="datetime-local" name="start_time" required value="<?= isset($editData['start_time']) ? date('Y-m-d\TH:i', strtotime($editData['start_time'])) : '' ?>">

        <label>Địa điểm:</label>
        <input type="text" name="location" required value="<?= htmlspecialchars($editData['location'] ?? '') ?>">

        <label>Số chỗ (Slots):</label>
        <input type="number" name="slots" required value="<?= htmlspecialchars($editData['slots'] ?? '50') ?>">

        <label>Mô tả:</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>

        <button type="submit" class="btn"><?= $editData ? 'Lưu thay đổi' : 'Tạo sự kiện' ?></button>
        <?php if ($editData): ?><a href="events.php" style="margin-left: 10px;">Hủy</a><?php endif; ?>
    </form>
</div>

<div class="box">
    <h2>DANH SÁCH SỰ KIỆN</h2>
    <table>
        <thead>
            <tr>
                <th>Tên Sự Kiện</th>
                <th>Thời Gian / Địa Điểm</th>
                <th>Số Chỗ</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $item): ?>
                <tr>
                    <td><b><?= htmlspecialchars($item['event_name']) ?></b></td>
                    <td><?= date('d/m/Y H:i', strtotime($item['start_time'])) ?><br><small><?= htmlspecialchars($item['location']) ?></small></td>
                    <td><?= $item['slots'] ?></td>
                    <td>
                        <a href="events.php?edit_id=<?= $item['event_id'] ?>" style="color: #2563eb; font-weight: bold; text-decoration: none;">Sửa</a> | 
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="event_id" value="<?= $item['event_id'] ?>">
                            <button type="submit" style="background:none; border:none; color:#ef4444; font-weight:bold; cursor:pointer;">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>