<?php
include '../includes/db.php';

// Chốt chặn bảo mật
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    // Nếu là Admin lỡ tay vào đây thì cũng cho về, hoặc cho ở lại tùy logic
    // Nhưng nếu chưa login thì bắt buộc về login
    header("Location: ../login.php");
    exit();
}

// --- Nội dung trang Customer ---
?>
<!DOCTYPE html>
<html>
<body>
    <h1>Chào khách hàng thân yêu</h1>
    <?php include 'data.php'; ?>
    <a href="../logout.php">Đăng xuất</a>
</body>
</html>