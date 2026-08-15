<?php
session_start();

function classifyEventScale($participants) {
    if ($participants >= 200) {
        return "Quy mô Lớn";
    } elseif ($participants >= 50) {
        return "Quy mô Vừa";
    } else {
        return "Quy mô Nhỏ";
    }
}

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}


if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['eventList'][$id])) {
        if ($_SESSION['eventList'][$id]['status'] !== 'Đã duyệt') {
            unset($_SESSION['eventList'][$id]);
            $_SESSION['eventList'] = array_values($_SESSION['eventList']);
        }
    }
    header("Location: index.php");
    exit;
}

$editIndex = -1;
$editData = ['name' => '', 'type' => 'Hội thảo / Workshop', 'time' => '', 'location' => '', 'participants' => ''];

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['eventList'][$id])) {
        if ($_SESSION['eventList'][$id]['status'] !== 'Đã duyệt') {
            $editIndex = $id;
            $editData = $_SESSION['eventList'][$id];
        }
    }
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveEvent'])) {
    $name = trim($_POST['eventName'] ?? '');
    $type = $_POST['eventType'] ?? '';
    $time = trim($_POST['eventTime'] ?? '');
    $location = trim($_POST['eventLocation'] ?? '');
    $participants = (int)($_POST['maxParticipants'] ?? 0);
    $currentIndex = (int)$_POST['editIndex'];

    
    $currentTime = time();
    $eventTime = strtotime($time);

    
    if (empty($name) || empty($time) || empty($location) || $participants <= 0) {
        $error = "Vui lòng nhập đầy đủ thông tin hợp lệ!";
    } elseif ($eventTime < $currentTime) {
    
        $error = "Thời gian diễn ra sự kiện không thể ở trong quá khứ! Vui lòng chọn thời gian từ thời điểm hiện tại trở đi.";
    } else {
        $scale = classifyEventScale($participants);

        if ($currentIndex >= 0 && isset($_SESSION['eventList'][$currentIndex])) {
            $_SESSION['eventList'][$currentIndex]['name'] = htmlspecialchars($name);
            $_SESSION['eventList'][$currentIndex]['type'] = htmlspecialchars($type);
            $_SESSION['eventList'][$currentIndex]['time'] = htmlspecialchars($time);
            $_SESSION['eventList'][$currentIndex]['location'] = htmlspecialchars($location);
            $_SESSION['eventList'][$currentIndex]['participants'] = $participants;
            $_SESSION['eventList'][$currentIndex]['scale'] = $scale;
        } else {
            $_SESSION['eventList'][] = [
                'name' => htmlspecialchars($name),
                'type' => htmlspecialchars($type),
                'time' => htmlspecialchars($time),
                'location' => htmlspecialchars($location),
                'participants' => $participants,
                'scale' => $scale,
                'status' => 'Chưa duyệt'
            ];
        }

        header("Location: index.php");
        exit;
    }
}


$minDateTime = date('Y-m-d\TH:i');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đề Xuất Sự Kiện CLB</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f8f9fa; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
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
        
        .alert-error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; }
        
        .action-btn { text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 13px; font-weight: bold; color: white; display: inline-block; }
        .btn-edit { background-color: #ffc107; color: #212529; }
        .btn-delete { background-color: #dc3545; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; display: inline-block; }
        .badge-pending { background-color: #ffeeba; color: #856404; }
        .badge-approved { background-color: #d4edda; color: #155724; }
        .badge-rejected { background-color: #f8d7da; color: #721c24; }
        
        .text-done { color: #28a745; font-weight: bold; }
        .badge-time { font-size: 12px; padding: 3px 6px; border-radius: 3px; font-weight: bold; }
        .time-past { background-color: #e2e3e5; color: #383d41; }
        .time-future { background-color: #cce5ff; color: #004085; }
    </style>
</head>
<body>

<div class="container">
    <h2><?= $editIndex >= 0 ? "CẬP NHẬT ĐỀ XUẤT SỰ KIỆN" : "GỬI ĐỀ XUẤT SỰ KIỆN MỚI" ?></h2>

    <?php if (!empty($error)): ?>
        <div class="alert-error">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="editIndex" value="<?= $editIndex ?>">

        <div class="form-group">
            <label for="eventName">Tên sự kiện:</label>
            <input type="text" id="eventName" name="eventName" required value="<?= $editData['name'] ?>">
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
            <input type="datetime-local" id="eventTime" name="eventTime" min="<?= $minDateTime ?>" required value="<?= $editData['time'] ?>">
        </div>

        <div class="form-group">
            <label for="eventLocation">Địa điểm tổ chức:</label>
            <input type="text" id="eventLocation" name="eventLocation" required value="<?= $editData['location'] ?>">
        </div>

        <div class="form-group">
            <label for="maxParticipants">Số lượng tối đa:</label>
            <input type="number" id="maxParticipants" name="maxParticipants" min="1" required value="<?= $editData['participants'] ?>">
        </div>

        <button type="submit" name="saveEvent"><?= $editIndex >= 0 ? "Cập Nhật Đề Xuất" : "Gửi Đề Xuất Sự Kiện" ?></button>
        <?php if ($editIndex >= 0): ?>
            <a href="index.php" class="btn-cancel">Hủy bỏ</a>
        <?php endif; ?>
    </form>

    <h2>DANH SÁCH ĐỀ XUẤT SỰ KIỆN</h2>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông tin chi tiết</th>
                <th>Quy Mô</th>
                <th>Trạng Thái Duyệt</th>
                <th>Cập Nhật</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($_SESSION['eventList'])): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Chưa có đề xuất nào.</td>
                </tr>
            <?php else: ?>
                <?php 
                $now = date('Y-m-d\TH:i');
                foreach ($_SESSION['eventList'] as $index => $item): 
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><b><?= $item['name'] ?></b></td>
                        <td>
                            <b>Loại:</b> <?= $item['type'] ?><br>
                            <b>Thời gian:</b> <?= date('d/m/Y H:i', strtotime($item['time'])) ?><br>
                            <b>Địa điểm:</b> <?= $item['location'] ?> (<?= $item['participants'] ?> người)
                        </td>
                        <td><?= $item['scale'] ?></td>
                        
                        <td>
                            <?php if ($item['status'] === 'Đã duyệt'): ?>
                                <span class="badge badge-approved">Đã duyệt</span>
                            <?php elseif ($item['status'] === 'Không duyệt'): ?>
                                <span class="badge badge-rejected">Không duyệt</span>
                            <?php else: ?>
                                <span class="badge badge-pending">Chưa duyệt</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($item['status'] === 'Đã duyệt'): ?>
                                <span class="text-done">✓ Hoàn thành</span>
                            <?php else: ?>
                                <a href="index.php?action=edit&id=<?= $index ?>" class="action-btn btn-edit">Sửa</a>
                                <a href="index.php?action=delete&id=<?= $index ?>" class="action-btn btn-delete" onclick="return confirm('Xóa sự kiện này?')">Xóa</a>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php 
                            if ($item['status'] === 'Đã duyệt') {
                                if ($item['time'] < $now) {
                                    echo '<span class="badge-time time-past">Đã diễn ra</span>';
                                } else {
                                    echo '<span class="badge-time time-future">Chưa diễn ra</span>';
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>