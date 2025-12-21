<?php
session_start();
include 'includes/db.php'; 
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_web/index.php");
    } else {
        header("Location: customer_web/index.php");
    }
    exit;
}

$error = '';

if (isset($_POST['btn_login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            if ($user['role'] === 'admin') {
                header("Location: admin_web/index.php");
            } else {
                header("Location: customer_web/index.php"); 
            }
            exit();
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Email này chưa được đăng ký!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; }
        .login-box h2 { text-align: center; margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #0056b3; }
        .error { color: red; text-align: center; margin-bottom: 10px; font-size: 14px; background: #ffe6e6; padding: 10px; border-radius: 4px; }

        .register-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .register-link a { color: #007bff; text-decoration: none; font-weight: bold; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Đăng Nhập</h2>
        
        <?php if(!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" name="btn_login">Đăng Nhập</button>
        </form>

        <div class="register-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </div>
</body>
</html>