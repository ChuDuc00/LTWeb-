<?php
// Giả lập kết nối CSDL (Thay thông số của bạn vào đây)
$conn = mysqli_connect("localhost", "root", "", "QLyCLB");
mysqli_set_charset($conn, "utf8");

// Lấy danh sách sự kiện để làm bộ lọc
$events_query = mysqli_query($conn, "SELECT id, event_name FROM events WHERE status = 'Đã duyệt'");

// Lấy tham số lọc sự kiện từ URL (nếu có)
$selected_event = isset($_GET['event_id']) ? $_GET['event_id'] : '';

// Query lấy danh sách đơn đăng ký
$sql = "SELECT r.id AS reg_id, u.student_code, u.full_name, e.event_name, 
               r.approval_status, r.attendance_status, r.absence_reason
        FROM event_registrations r
        JOIN users u ON r.student_id = u.id
        JOIN events e ON r.event_id = e.id";

if (!empty($selected_event)) {
    $sql .= " WHERE r.event_id = '$selected_event'";
}
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn đăng ký & Điểm danh</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f9; }
        h2 { color: #333; }
        .filter-box { margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; color: #fff; }
        .bg-pending { background-color: #ffc107; color: #000; }
        .bg-approved { background-color: #28a745; }
        .bg-rejected { background-color: #dc3545; }
        .btn { padding: 5px 10px; color: #fff; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #dc3545; }
        .btn-save { background-color: #17a2b8; }
    </style>
</head>
<body>

    <h2>Quản lý Đơn đăng ký & Điểm danh Sự kiện</h2>

    <!-- Bộ lọc sự kiện -->
    <div class="filter-box">
        <form method="GET" action="">
            <label><b>Lọc theo sự kiện:</b> </label>
            <select name="event_id" onchange="this.form.submit()">
                <option value="">-- Tất cả sự kiện --</option>
                <?php while ($ev = mysqli_fetch_assoc($events_query)): ?>
                    <option value="<?= $ev['id'] ?>" <?= $selected_event == $ev['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ev['event_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <!-- Bảng danh sách đơn -->
    <table>
        <thead>
            <tr>
                <th>Mã SV</th>
                <th>Họ & Tên</th>
                <th>Sự kiện</th>
                <th>Duyệt đơn</th>
                <th>Thao tác duyệt</th>
                <th>Điểm danh</th>
                <th>Lý do vắng</th>
                <th>Cập nhật điểm danh</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_code']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        
                        <!-- Trạng thái duyệt đơn -->
                        <td>
                            <?php 
                                $status = $row['approval_status'];
                                $badge_class = ($status == 'Đã duyệt') ? 'bg-approved' : (($status == 'Từ chối') ? 'bg-rejected' : 'bg-pending');
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                        </td>

                        <!-- Thao tác Duyệt / Từ chối -->
                        <td>
                            <a href="process_registration.php?action=approve&id=<?= $row['reg_id'] ?>" class="btn btn-approve">Duyệt</a>
                            <a href="process_registration.php?action=reject&id=<?= $row['reg_id'] ?>" class="btn btn-reject">Từ chối</a>
                        </td>

                        <!-- Form Điểm danh -->
                        <form action="process_registration.php" method="POST">
                            <input type="hidden" name="action" value="update_attendance">
                            <input type="hidden" name="reg_id" value="<?= $row['reg_id'] ?>">
                            
                            <td>
                                <select name="attendance_status">
                                    <option value="Bình thường" <?= $row['attendance_status'] == 'Bình thường' ? 'selected' : '' ?>>Bình thường</option>
                                    <option value="Đi muộn" <?= $row['attendance_status'] == 'Đi muộn' ? 'selected' : '' ?>>Đi muộn</option>
                                    <option value="Vắng" <?= $row['attendance_status'] == 'Vắng' ? 'selected' : '' ?>>Vắng</option>
                                </select>
                            </td>

                            <td>
                                <input type="text" name="absence_reason" value="<?= htmlspecialchars($row['absence_reason'] ?? '') ?>" placeholder="Nhập lý do nếu vắng">
                            </td>

                            <td>
                                <button type="submit" class="btn btn-save">Lưu</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;">Chưa có đơn đăng ký nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>