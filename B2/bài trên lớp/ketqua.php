<?php 
$diem  = 5;
if ($diem >= 8) {
    $xeploai = "Bạn đạt loại Giỏi";
} elseif ($diem >= 6.5) {
    $xeploai = "Bạn đạt loại Khá";
} elseif ($diem >= 5) {
    $xeploai = "Bạn đạt loại Trung Bình";
} else {
    $xeploai = "Bạn khong đạt ";
}
?> 