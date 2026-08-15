<?php
// 1. Tạo mảng student gồm 3 sinh viên
$students = [
    ["name" => "Chu Đức", "midterm" => 7.5, "final" => 8.5],
    ["name" => "Ai ĐÓ ", "midterm" => 4.0, "final" => 4.5],
    ["name" => "Who", "midterm" => 6.0, "final" => 5.5]
];

// 2. Hàm tính điểm trung bình
function calculate_average($midterm, $final) {
    return ($midterm + $final) / 2;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng Điểm Sinh Viên</title>
    <style>
        table { width: 600px; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h2>BẢNG ĐIỂM SINH VIÊN</h2>
    <table>
        <thead>
            <tr>
                <th>Họ và Tên</th>
                <th>Điểm Giữa Kỳ</th>
                <th>Điểm Cuối Kỳ</th>
                <th>Điểm Trung Bình</th>
                <th>Kết Quả</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // 3. Dùng foreach để in bảng HTML
            foreach ($students as $sv): 
                $dtb = calculate_average($sv['midterm'], $sv['final']);
                
                // 4. Thêm cột kết quả đạt nếu ĐTB >= 5
                $ketQua = ($dtb >= 5) ? "Đạt" : "Chưa đạt";
                
                // 5. Mã hóa tên sinh viên bằng htmlspecialchars
                $tenMaHoa = htmlspecialchars($sv['name'], ENT_QUOTES, 'UTF-8');
            ?>
                <tr>
                    <td style="text-align: left;"><?php echo $tenMaHoa; ?></td>
                    <td><?php echo $sv['midterm']; ?></td>
                    <td><?php echo $sv['final']; ?></td>
                    <td><?php echo number_format($dtb, 1); ?></td>
                    <td><strong><?php echo $ketQua; ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>