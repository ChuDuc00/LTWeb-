<?php
session_start();

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}

// Xử lý Duyệt / Từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $act = $_POST['action'];
    $note = trim($_POST['admin_note'] ?? '');

    foreach ($_SESSION['eventList'] as &$item) {
        if (isset($item['id']) && $item['id'] == $id) {
            $item['status'] = ($act === 'approve') ? 'Đã duyệt' : 'Từ chối';
            $item['admin_note'] = $note; 
            break;
        }
    }
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Duyệt Đề Xuất Sự Kiện</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8; margin: 0; padding: 40px 20px; color: #334155; display: flex; justify-content: center; }
        .form-container { background: #ffffff; padding: 45px 50px; border-radius: 28px; width: 100%; max-width: 1150px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); }
        h2 { color: #1e3a8a; font-size: 22px; font-weight: 700; border-bottom: 2px solid #3b82f6; padding-bottom: 12px; margin-top: 0; margin-bottom: 30px; text-transform: uppercase; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 20px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 14px 16px; text-align: left; vertical-align: top; }
        th { background: #f8fafc; color: #475569; font-weight: 600; white-space: nowrap; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; white-space: nowrap; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .btn-action { background: #1e3a8a; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; }
        .btn-action:hover { background: #2563eb; }
        .detail-box { background: #f8fafc; padding: 10px 14px; border-left: 3px solid #1e3a8a; border-radius: 0 6px 6px 0; font-style: italic; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #fff; padding: 25px 30px; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .modal-content h3 { margin-top: 0; color: #1e3a8a; font-size: 18px; }
        .modal-content textarea { width: 100%; height: 100px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; margin: 12px 0 20px 0; box-sizing: border-box; resize: vertical; }
        .modal-btns { display: flex; justify-content: flex-end; gap: 10px; }
        .btn-approve { background: #16a34a; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .btn-reject { background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .btn-cancel { background: #94a3b8; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>DUYỆT ĐỀ XUẤT SỰ KIỆN</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông Tin Chi Tiết</th>
                <th>Mô Tả Nội Dung</th>
                <th style="text-align: center;">Trạng Thái</th>
                <th style="text-align: center;">Thao Tác Duyệt</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            $hasData = false;

            if (!empty($_SESSION['eventList'])):
                foreach ($_SESSION['eventList'] as $item): 
                    $status = $item['status'] ?? 'Chờ duyệt';

                    // LỌC: Bỏ qua sự kiện "Đã duyệt"
                    if ($status === 'Đã duyệt') {
                        continue;
                    }

                    $hasData = true;
                    $itemId = $item['id'] ?? '';
                    $itemName = htmlspecialchars($item['name'] ?? '');
                    $currentNote = htmlspecialchars($item['admin_note'] ?? '');
            ?>
                    <tr>
                        <td style="text-align: center;"><?= $stt++ ?></td>
                        <td><b><?= $itemName ?></b></td>
                        <td>
                            <b>Loại:</b> <?= htmlspecialchars($item['type'] ?? '') ?><br>
                            <b>Thời gian:</b> <?= isset($item['time']) && !empty($item['time']) ? date('d/m/Y H:i', strtotime($item['time'])) : '' ?><br>
                            <b>Địa điểm:</b> <?= htmlspecialchars($item['location'] ?? '') ?> (<?= htmlspecialchars($item['participants'] ?? '0') ?> người)<br>
                            <b>Quy mô:</b> <?= htmlspecialchars($item['scale'] ?? '') ?>
                        </td>
                        <td><div class="detail-box"><?= nl2br(htmlspecialchars($item['detail'] ?? 'Không có nội dung mô tả.')) ?></div></td>
                        <td style="text-align: center;">
                            <?php if ($status === 'Từ chối'): ?>
                                <span class="badge badge-rejected">Từ chối / Chờ sửa</span>
                            <?php else: ?>
                                <span class="badge badge-pending">Chờ duyệt</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <button class="btn-action" onclick="openModal('<?= $itemId ?>', '<?= addslashes($itemName) ?>', '<?= addslashes($currentNote) ?>')">
                                Xử lý đề xuất
                            </button>
                        </td>
                    </tr>
            <?php 
                endforeach; 
            endif;

            if (!$hasData): ?>
                <tr><td colspan="6" style="text-align:center; color: #94a3b8; padding: 25px;">Không có sự kiện nào cần duyệt</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="actionModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Xử lý đề xuất</h3>
        <form method="POST" action="admin.php">
            <input type="hidden" name="id" id="modalEventId">
            <label style="font-size: 13px; font-weight: 600; color: #475569;">Ghi chú / Lý do (Gửi Ban Chủ Nhiệm):</label>
            <textarea name="admin_note" id="modalAdminNote" placeholder="Ví dụ: Cần bổ sung thêm dự trù kinh phí..."></textarea>
            
            <div class="modal-btns">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" name="action" value="reject" class="btn-reject" onclick="return confirm('Xác nhận TỪ CHỐI đề xuất này?')">Từ Chối / Báo Sửa</button>
                <button type="submit" name="action" value="approve" class="btn-approve">Duyệt Sự Kiện</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id, name, note) {
    document.getElementById('modalEventId').value = id;
    document.getElementById('modalTitle').innerText = 'Đánh giá: ' + name;
    document.getElementById('modalAdminNote').value = note;
    document.getElementById('actionModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('actionModal').style.display = 'none';
}
</script>

</body>
</html>