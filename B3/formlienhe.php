<?php
// biến khởi tạo giá trị null
$name = $email = $subject = $message = "";
$errors = [];
$successMessage = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // lấy giữ liệu , giữ lại nếu lỗi 
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    //kiểm tra dữ liệu 
    if (empty($name)) {
        $errors[] = "Họ tên không được để trống!";
    }
    
    if (empty($message)) {
        $errors[] = "Nội dung liên hệ không được để trống!";
    }

    if (empty($email)) {
        $errors[] = "Email không được để trống!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không đúng định dạng (Ví dụ đúng: abc@gmail.com)!";
    }

    if (empty($errors)) {
        $successMessage = "Gửi thông tin liên hệ thành công! Cảm ơn bạn $name.";

        $name = $email = $subject = $message = "";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên Hệ</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 30px; }
        .container { max-width: 550px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #0056b3; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-top: 0; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="email"], select, textarea {
            width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        button { background-color: #0056b3; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%; font-size: 15px; }
        button:hover { background-color: #004085; }
       
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        .alert-error ul { margin: 0; padding-left: 20px; }
       
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; }
        .required { color: red; }
    </style>
</head>
<body>


<div class="container">
    <h2>LIÊN HỆ</h2>

    Chuduc19102006@gmail.com

    <?php if (!empty($successMessage)): ?>
        <div class="alert-success">
             <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <strong>⚠️ Có lỗi xảy ra:</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        
        <?= htmlspecialchars($name) ?> 
        <div class="form-group">
            <label for="name">Họ và Tên <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Nguyễn Văn A">
        </div>

        <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="example@gmail.com">
        </div>

        <div class="form-group">
            <label for="subject">Chủ đề:</label>
            <select id="subject" name="subject">
                <option value="Tư vấn" <?= $subject === 'Tư vấn' ? 'selected' : '' ?>>Tư vấn</option>
                <option value="Góp ý" <?= $subject === 'Góp ý' ? 'selected' : '' ?>>Góp ý</option>
                <option value="Khác" <?= $subject === 'Khác' ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message">Nội dung <span class="required">*</span></label>
            <textarea id="message" name="message" rows="4" placeholder="Nhập nội dung..."><?= htmlspecialchars($message) ?></textarea>
        </div>

        <button type="submit">Gửi Liên Hệ</button>
    </form>
</div>

</body>
</html>