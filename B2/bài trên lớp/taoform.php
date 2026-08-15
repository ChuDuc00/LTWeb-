<?php 
if (isset ($_POST["tinh"])) {
    $ten = $_POST["ten"];
    $soluong = $_POST["soluong"];
    $dongia = $_POST["dongia"];

    $tongtien = $soluong * $dongia;

    echo "<h3>Kết Quả:</h3>";
    echo "Tài liệu: <strong>$ten </strong><br>";
    echo "số tiền: <strong>. numberformat($tongtien, 0, ',', '.') . ' VNĐ</strong><br>";
}
?>