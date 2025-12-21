<?php
session_start();
include 'includes/db.php';
$error = '';
$success = '';

if (isset($_POST['btn_register'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : ''; 
    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : ''; 


    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        $checkEmail = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            $error = "Email này đã được sử dụng!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $role = 'customer';
            
            $sql = "INSERT INTO users (name, email, password, role, phone, address) 
                    VALUES ('$name', '$email', '$hashed_password', '$role', '$phone', '$address')";

            if ($conn->query($sql) === TRUE) {
                $success = "Đăng ký thành công! <a href='login.php'>Đăng nhập ngay</a>";
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f2f5; margin: 0; }
        .register-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; }
        .register-box h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; font-size: 0.9em; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #218838; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
        .link { text-align: center; margin-top: 15px; font-size: 14px; }
        .link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Đăng Ký</h2>
        
        <?php if(!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone">
                </div>

                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="address">
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Nhập lại mật khẩu</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="btn_register">Đăng Ký</button>
            </form>
        <?php endif; ?>

        <div class="link">
            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
        </div>
    </div>
</body>
</html>