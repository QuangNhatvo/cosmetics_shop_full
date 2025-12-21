<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Khách';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Cosmetics Shop</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa; color: #333; }
        
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; color: #ff6b6b; text-decoration: none; }
        .nav-links a { text-decoration: none; color: #333; margin-left: 20px; font-weight: 500; }
        .nav-links a:hover { color: #ff6b6b; }
        .btn-logout { color: #dc3545 !important; }

        .container { max-width: 800px; margin: 50px auto; text-align: center; padding: 20px; }
        
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .welcome-text { font-size: 1.5em; margin-bottom: 10px; }
        .role-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 0.9em; font-weight: bold; color: white; }
        .bg-admin { background-color: #dc3545; }
        .bg-customer { background-color: #28a745; }
        
        .btn { display: inline-block; padding: 10px 20px; margin: 10px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; transition: 0.3s; }
        .btn-primary { background-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-admin { background-color: #343a40; }
        .btn-admin:hover { background-color: #23272b; }

        .info-box { background: #e9ecef; padding: 15px; border-radius: 5px; text-align: left; margin-top: 20px; font-family: monospace; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">My Cosmetics</a>
        <div class="nav-links">
            <a href="#">Sản phẩm</a>
            <a href="#">Giới thiệu</a>
            
            <?php if ($isLoggedIn): ?>
                <span>Xin chào, <b><?php echo htmlspecialchars($userName); ?></b></span>
                <?php if ($userRole === 'admin'): ?>
                    <a href="admin_web/index.php">Trang quản trị</a>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php">Đăng nhập</a>
                <a href="register.php">Đăng ký</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        
        <?php if (!$isLoggedIn): ?>
            <div class="card">
                <h1>Chào mừng đến với cửa hàng!</h1>
                <p>Bạn đang xem trang này với tư cách là <b>Khách</b>.</p>
                <p>Vui lòng đăng nhập để mua hàng hoặc quản trị hệ thống.</p>
                <br>
                <a href="login.php" class="btn btn-primary">Đăng nhập ngay</a>
            </div>

        <?php else: ?>
            <div class="card">
                <p class="welcome-text">Đăng nhập thành công!</p>
                
                <div>
                    Bạn đang là: 
                    <span class="role-badge <?php echo ($userRole === 'admin') ? 'bg-admin' : 'bg-customer'; ?>">
                        <?php echo strtoupper($userRole); ?>
                    </span>
                </div>

                <div class="info-box">
                    <strong>Session Debug Info:</strong><br>
                    ID: <?php echo $_SESSION['user_id']; ?><br>
                    Email: <?php echo $_SESSION['email']; ?><br>
                    Name: <?php echo $_SESSION['name']; ?>
                </div>

                <br>

                <?php if ($userRole === 'admin'): ?>
                    <p>Bạn có quyền truy cập vào trang quản trị.</p>
                    <a href="admin_web/index.php" class="btn btn-admin">Vào trang Admin</a>
                <?php else: ?>
                    <p>Chúc bạn mua sắm vui vẻ!</p>
                    <a href="#" class="btn btn-primary">Xem giỏ hàng</a>
                <?php endif; ?>
                
                <a href="logout.php" class="btn btn-primary" style="background-color: #6c757d;">Đăng xuất</a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>