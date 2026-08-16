<?php
session_start();

if (!isset($_SESSION['eventList'])) {
    $_SESSION['eventList'] = [];
}

// Xử lý khi Chủ CLB bấm Duyệt hoặc Từ chối
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if (isset($_SESSION['eventList'][$id])) {
        if ($action === 'approve') {
            $_SESSION['eventList'][$id]['status'] = 'Đã duyệt';
        } elseif ($action === 'reject') {
            $_SESSION['eventList'][$id]['status'] = 'Không duyệt';
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
    <title>Trang Duyệt Sự Kiện - Chủ CLB</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 20px; 
            background-color: #f8f9fa; 
        }
        .container { 
            max-width: 1100px; 
            margin: 0 auto; 
            background: white; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        h2 { 
            color: #0056b3; 
            border-bottom: 2px solid #0056b3; 
            padding-bottom: 10px; 
            margin-top: 0;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #dee2e6; 
            padding: 12px; 
            text-align: left; 
        }
        th { 
            background-color: #e9ecef; 
            color: #212529;
        }
        
        .action-btn { 
            text-decoration: none; 
            padding: 6px 12px; 
            border-radius: 4px; 
            font-size: 13px; 
            font-weight: bold; 
            color: white; 
            display: inline-block; 
            margin-right: 5px;
        }
        .btn-approve { background-color: #0056b3; }
        .btn-approve:hover { background-color: #004085; }
        
        .btn-reject { background-color: #dc3545; }
        .btn-reject:hover { background-color: #c82333; }
        
        .badge { 
            padding: 5px 10px; 
            border-radius: 4px; 
            font-weight: bold; 
            font-size: 12px; 
            display: inline-block; 
        }
        .badge-pending { background-color: #ffeeba; color: #856404; }
        .badge-approved { background-color: #d4edda; color: #155724; }
        .badge-rejected { background-color: #f8d7da; color: #721c24; }
        
        .text-processed {
            color: #6c757d;
            font-style: italic;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>DANH SÁCH ĐỀ XUẤT SỰ KIỆN CẦN DUYỆT (QUYỀN CHỦ CLB)</h2>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên Sự Kiện</th>
                <th>Thông Tin Chi Tiết</th>
                <th>Quy Mô</th>
                <th>Trạng Thái Hiện Tại</th>
                <th>Quyết Định Duyệt</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($_SESSION['eventList'])): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Hiện chưa có đơn đề xuất nào được gửi lên.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($_SESSION['eventList'] as $index => $item): ?>
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
                                <span class="badge badge-pending">Chờ duyệt</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['status'] === 'Chưa duyệt'): ?>
                                <a href="admin.php?action=approve&id=<?= $index ?>" class="action-btn btn-approve" onclick="return confirm('Xác nhận ĐỒNG Ý duyệt sự kiện này?')">Duyệt</a>
                                <a href="admin.php?action=reject&id=<?= $index ?>" class="action-btn btn-reject" onclick="return confirm('Xác nhận TỪ CHỐI sự kiện này?')">Từ chối</a>
                            <?php else: ?>
                                <span class="text-processed">Đã xử lý</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>