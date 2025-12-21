<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Khách';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-color: #ff6b6b; --header-bg: #ffffff; --text-dark: #333; }
        body { font-family: 'Open Sans', sans-serif; }
        .top-stripe-bar { height: 5px; background: linear-gradient(90deg, #ff9a9e 0%, #ff6b6b 100%); width: 100%; }
        .header-nav { background-color: var(--header-bg); box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 0; position: sticky; top: 0; z-index: 1000; }
        .navbar-brand { font-family: 'Anton', sans-serif; font-size: 24px; color: var(--primary-color); text-decoration: none;}
        .search-form-container { flex-grow: 1; max-width: 500px; margin: 0 20px; }
        .search-form { position: relative; display: flex; align-items: center; }
        .search-input { width: 100%; padding: 10px 45px 10px 20px; border: 2px solid #f1f1f1; border-radius: 30px; outline: none; background: #f8f9fa; }
        .search-input:focus { border-color: var(--primary-color); background: #fff; }
        .search-button { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: var(--primary-color); color: white; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        
        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .icon-link { color: var(--text-dark); font-size: 1.2rem; position: relative; transition: 0.3s; text-decoration: none; }
        .icon-link:hover { color: var(--primary-color); }
        
        .auth-link { text-decoration: none; color: var(--text-dark); font-weight: 600; font-size: 14px; gap: 8px; padding: 8px 15px; border-radius: 20px; background: #f8f9fa; transition: 0.3s; }
        .auth-link:hover { background: #ffeaea; color: var(--primary-color); }

        .user-dropdown { position: relative; cursor: pointer; height: 100%; display: flex; align-items: center; }
        
        .user-menu { 
            display: none; 
            position: absolute; 
            top: 100%; 
            right: 0; 
            background: white; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            border-radius: 8px; 
            min-width: 180px; 
            padding: 10px 0; 
            margin-top: 15px; 
            z-index: 1100;
            animation: fadeIn 0.2s ease-in-out;
        }

        .user-menu::before {
            content: "";
            position: absolute;
            top: -20px;
            left: 0;
            width: 100%;
            height: 20px;
            background: transparent; 
        }

        .user-dropdown:hover .user-menu { display: block; }

        .user-menu a { display: block; padding: 10px 20px; color: #333; text-decoration: none; font-size: 14px; transition: all 0.2s; }
        .user-menu a:hover { background: #f8f9fa; color: var(--primary-color); padding-left: 25px; /* Hiệu ứng trượt nhẹ */ }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-badge {
            position: absolute; top: -8px; right: -8px;
            background-color: var(--primary-color); color: white;
            font-size: 10px; font-weight: bold;
            width: 18px; height: 18px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        @media(max-width: 768px) { .search-form-container { display: none; } }
    </style>
</head>
<body>

<header>
    <div class="top-stripe-bar"></div>
    <nav class="header-nav">
        <div class="container-fluid px-lg-5 px-3 d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-gem me-2"></i> COSMETICS
            </a>

            <div class="search-form-container">
                <form class="search-form" action="index.php" method="get">
                    <input class="search-input" type="search" name="keyword" placeholder="Tìm kiếm sản phẩm...">
                    <button class="search-button" type="submit"><i class="fas fa-search" style="font-size: 14px;"></i></button>
                </form>
            </div>

            <div class="nav-actions">
                <a href="checkout.php" class="icon-link" aria-label="Shopping Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cart-count">0</span>
                </a>

                <a href="#" class="icon-link" aria-label="Notifications"><i class="fas fa-bell"></i></a>

                <?php if ($isLoggedIn): ?>
                    <div class="user-dropdown">
                        <a href="profile.php" class="auth-link d-flex align-items-center">
                            <i class="fas fa-user-check"></i>
                            <span class="ms-2"><?php echo htmlspecialchars($userName); ?></span>
                        </a>
                        <div class="user-menu">
                            <a href="profile.php"><i class="far fa-user me-2"></i>Tài khoản của tôi</a>
                            <div class="dropdown-divider" style="border-top: 1px solid #eee; margin: 5px 0;"></div>
                            <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../login.php" class="auth-link d-flex align-items-center">
                        <i class="fas fa-user-circle"></i>
                        <span class="ms-2">Đăng nhập</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<script>
    function updateHeaderCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart_items') || '[]');
        const badge = document.getElementById('cart-count');
        if (badge) {
            const totalQty = cart.reduce((acc, item) => acc + item.quantity, 0);
            badge.innerText = totalQty;
            badge.style.display = totalQty > 0 ? 'flex' : 'none';
        }
    }
    document.addEventListener("DOMContentLoaded", updateHeaderCartCount);
</script>
</body>
</html>