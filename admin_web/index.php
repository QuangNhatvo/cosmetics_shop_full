<?php require_once 'data.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./src/styles/style.css">
</head>
<body>
    <aside class="sidebar">
        <h2>Shop Admin</h2>
        <ul class="menu">
            <li><a href="#" onclick="navigate('dashboard')" id="nav-dashboard" class="active"><i class="fas fa-home"></i> Tổng Quan</a></li>
            <li><a href="#" onclick="navigate('products')" id="nav-products"><i class="fas fa-box"></i> Sản Phẩm</a></li>
            <li><a href="#" onclick="navigate('orders')" id="nav-orders"><i class="fas fa-shopping-cart"></i> Đơn Hàng</a></li>
            <li><a href="#" onclick="navigate('customers')" id="nav-customers"><i class="fas fa-users"></i> Khách Hàng</a></li>
            <li><a href="#" onclick="navigate('users')" id="nav-users"><i class="fas fa-user-shield"></i> Người Dùng</a></li>
            <li><a href="#" onclick="navigate('settings')" id="nav-settings"><i class="fas fa-cog"></i> Cài Đặt</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <header>
            <h3 id="page-title">Bảng Điều Khiển</h3>
            <div class="user-info">
                <span>Admin User</span>
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </header>

        <div id="app" class="content"></div>
    </div>

    <div id="modal-overlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-title">Tiêu đề</h3>
                <button onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="modal-body"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Đóng</button>
                <button class="btn btn-primary" id="modal-action-btn">Lưu</button>
            </div>
        </div>
    </div>

    <script>
        const phpData = <?php echo json_encode($mockData); ?>;
    </script>
    <script src="./src/js/main.js"></script>
</body>
</html>