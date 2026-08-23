<?php
session_start();

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}

// 1. Xử lý gửi Form (Thêm mới hoặc Cập nhật sự kiện bị từ chối)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_submit'])) {
    $editId = $_POST['edit_id'] ?? '';
    
    $eventData = [
        'id' => !empty($editId) ? $editId : uniqid(),
        'name' => trim($_POST['name'] ?? ''),
        'type' => $_POST['type'] ?? '',
        'time' => $_POST['time'] ?? '',
        'location' => trim($_POST['location'] ?? ''),
        'participants' => intval($_POST['participants'] ?? 0),
        'scale' => $_POST['scale'] ?? '',
        'detail' => trim($_POST['detail'] ?? ''),
        'status' => 'Chờ duyệt', // Reset trạng thái về Chờ duyệt
        'admin_note' => '' // Xóa ghi chú cũ
    ];

    if (!empty($editId)) {
        foreach ($_SESSION['eventList'] as &$item) {
            if (isset($item['id']) && $item['id'] == $editId) {
                $item = $eventData;
                break;
            }
        }
    } else {
        $_SESSION['eventList'][] = $eventData;
    }

    header("Location: index.php");
    exit();
}

// 2. Lấy dữ liệu để đổ vào Form khi bấm "Chỉnh sửa"
$editData = null;
if (isset($_GET['edit_id'])) {
    foreach ($_SESSION['eventList'] as $item) {
        if (isset($item['id']) && $item['id'] == $_GET['edit_id']) {
            $editData = $item;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Ban Chủ Nhiệm - Đề Xuất Sự Kiện</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8; margin: 0; padding: 40px 20px; color: #334155; display: flex; flex-direction: column; align-items: center; gap: 30px; }
        .form-container { background: #ffffff; padding: 40px 50px; border-radius: 24px; width: 100%; max-width: 1150px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); box-sizing: border-box; }
        h2 { color: #1e3a8a; font-size: 20px; font-weight: 700; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-top: 0; margin-bottom: 25px; text-transform: uppercase; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full-width { grid-column: span 2; }
        label { font-size: 13px; font-weight: 600; color: #475569; }
        input, select, textarea { padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; }
        input:focus, select:focus, textarea:focus { border-color: #3b82f6; }
        .btn-submit { background: #1e3a8a; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; margin-top: 10px; transition: 0.2s; }
        .btn-submit:hover { background: #2563eb; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 15px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 14px 16px; text-align: left; vertical-align: top; }
        th { background: #f8fafc; color: #475569; font-weight: 600; white-space: nowrap; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; white-space: nowrap; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .admin-note-box { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 10px 12px; border-radius: 6px; color: #991b1b; font-size: 13px; margin-top: 6px; }
        .btn-edit { display: inline-block; margin-top: 8px; padding: 6px 12px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>

<div class="form-container">
    <h2><?= $editData ? 'CHỈNH SỬA & GỬI LẠI ĐỀ XUẤT' : 'GỬI ĐỀ XUẤT SỰ KIỆN MỚI' ?></h2>
    
    <form method="POST" action="index.php">
        <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editData['id'] ?? '') ?>">

        <div class="form-grid">
            <div class="form-group">
                <label>Tên Sự Kiện *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($editData['name'] ?? '') ?>" placeholder="Nhập tên sự kiện">
            </div>

            <div class="form-group">
                <label>Loại Sự Kiện *</label>
                <select name="type" required>
                    <option value="Hội thảo / Workshop" <?= ($editData['type'] ?? '') === 'Hội thảo / Workshop' ? 'selected' : '' ?>>Hội thảo / Workshop</option>
                    <option value="Cuộc thi" <?= ($editData['type'] ?? '') === 'Cuộc thi' ? 'selected' : '' ?>>Cuộc thi</option>
                    <option value="Hoạt động Môi trường" <?= ($editData['type'] ?? '') === 'Hoạt động Môi trường' ? 'selected' : '' ?>>Hoạt động Môi trường</option>
                    <option value="Giải trí / Văn nghệ" <?= ($editData['type'] ?? '') === 'Giải trí / Văn nghệ' ? 'selected' : '' ?>>Giải trí / Văn nghệ</option>
                </select>
            </div>

            <div class="form-group">
                <label>Thời Gian Tổ Chức *</label>
                <input type="datetime-local" name="time" required value="<?= htmlspecialchars($editData['time'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Địa Điểm *</label>
                <input type="text" name="location" required value="<?= htmlspecialchars($editData['location'] ?? '') ?>" placeholder="Địa điểm dự kiến">
            </div>

            <div class="form-group">
                <label>Dự Kiến Số Lượng Người Tham Gia</label>
                <input type="number" name="participants" value="<?= htmlspecialchars($editData['participants'] ?? '50') ?>">
            </div>

            <div class="form-group">
                <label>Quy Mô Sự Kiện</label>
                <select name="scale">
                    <option value="Quy mô Nhỏ" <?= ($editData['scale'] ?? '') === 'Quy mô Nhỏ' ? 'selected' : '' ?>>Quy mô Nhỏ (< 50 người)</option>
                    <option value="Quy mô Vừa" <?= ($editData['scale'] ?? '') === 'Quy mô Vừa' ? 'selected' : '' ?>>Quy mô Vừa (50 - 200 người)</option>
                    <option value="Quy mô Lớn" <?= ($editData['scale'] ?? '') === 'Quy mô Lớn' ? 'selected' : '' ?>>Quy mô Lớn (> 200 người)</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label>Chi Tiết Nội Dung Sự Kiện</label>
                <textarea name="detail" rows="3" placeholder="Mô tả tóm tắt nội dung..."><?= htmlspecialchars($editData['detail'] ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit" name="btn_submit" class="btn-submit">
            <?= $editData ? 'Cập Nhật & Gửi Duyệt Lại' : 'Gửi Đề Xuất Sự Kiện' ?>
        </button>
        <?php if ($editData): ?>
            <a href="index.php" style="margin-left: 10px; font-size: 13px; color: #64748b; text-decoration: none;">Hủy sửa</a>
        <?php endif; ?>
    </form>
</div>

<div class="form-container">
    <h2>DANH SÁCH ĐỀ XUẤT</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông Tin Chi Tiết</th>
                <th>Mô Tả Nội Dung</th>
                <th style="text-align: center;">Trạng Thái</th>
                <th style="width: 30%;">Phản Hồi / Lý Do Từ Admin</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            $hasPendingOrRejected = false;

            if (!empty($_SESSION['eventList'])):
                foreach ($_SESSION['eventList'] as $item): 
                    $status = $item['status'] ?? 'Chờ duyệt';

                    // LỌC: Đã duyệt thì BỎ QUA không hiển thị
                    if ($status === 'Đã duyệt') {
                        continue; 
                    }

                    $hasPendingOrRejected = true;
                    $adminNote = $item['admin_note'] ?? '';
                    $itemId = $item['id'] ?? '';
            ?>
                    <tr>
                        <td style="text-align: center;"><?= $stt++ ?></td>
                        <td><b><?= htmlspecialchars($item['name'] ?? '') ?></b></td>
                        <td>
                            <b>Loại:</b> <?= htmlspecialchars($item['type'] ?? '') ?><br>
                            <b>Thời gian:</b> <?= isset($item['time']) && !empty($item['time']) ? date('d/m/Y H:i', strtotime($item['time'])) : '' ?><br>
                            <b>Địa điểm:</b> <?= htmlspecialchars($item['location'] ?? '') ?> (<?= htmlspecialchars($item['participants'] ?? '0') ?> người)<br>
                            <b>Quy mô:</b> <?= htmlspecialchars($item['scale'] ?? '') ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars($item['detail'] ?? 'Không có nội dung mô tả.')) ?></td>
                        <td style="text-align: center;">
                            <?php if ($status === 'Từ chối'): ?>
                                <span class="badge badge-rejected">Từ chối</span>
                            <?php else: ?>
                                <span class="badge badge-pending">Chờ duyệt</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($adminNote)): ?>
                                <div class="admin-note-box">
                                    <strong>Lý do từ Admin:</strong><br>
                                    <?= nl2br(htmlspecialchars($adminNote)) ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-style: italic;">Chưa có ghi chú</span>
                            <?php endif; ?>

                            <?php if ($status === 'Từ chối'): ?>
                                <br>
                                <a href="index.php?edit_id=<?= $itemId ?>" class="btn-edit">✏️ Chỉnh sửa & Nộp lại</a>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php 
                endforeach; 
            endif;

            if (!$hasPendingOrRejected): ?>
                <tr><td colspan="6" style="text-align:center; color: #94a3b8; padding: 25px;">Không có đề xuất nào đang chờ duyệt hoặc bị từ chối</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>