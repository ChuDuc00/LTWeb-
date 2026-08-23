<?php
$conn = mysqli_connect("localhost", "root", "", "QLyCLB");
mysqli_set_charset($conn, "utf8");

// 1. Xử lý Duyệt / Từ chối đơn đăng ký (GET)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reg_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $status = 'Đã duyệt';
    } elseif ($action === 'reject') {
        $status = 'Từ chối';
    }

    if (isset($status)) {
        $stmt = mysqli_prepare($conn, "UPDATE event_registrations SET approval_status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $reg_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: manage_registrations.php");
    exit();
}

// 2. Xử lý Cập nhật điểm danh & Lý do vắng (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_attendance') {
    $reg_id = intval($_POST['reg_id']);
    $attendance_status = $_POST['attendance_status'];
    $absence_reason = trim($_POST['absence_reason']);

    // Nếu không chọn Vắng thì tự động xóa lý do vắng cũ (nếu có)
    if ($attendance_status !== 'Vắng') {
        $absence_reason = NULL;
    }

    $stmt = mysqli_prepare($conn, "UPDATE event_registrations SET attendance_status = ?, absence_reason = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $attendance_status, $absence_reason, $reg_id);
    mysqli_stmt_execute($stmt);

    header("Location: manage_registrations.php");
    exit();
}
?>