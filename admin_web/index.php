<?php 
require_once '../includes/db.php'; 


$sql_products = "SELECT p.product_id as id, p.name, c.name as category, p.price, IFNULL(i.quantity, 0) as stock 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 LEFT JOIN inventory i ON p.product_id = i.product_id 
                 ORDER BY p.product_id DESC";
$res_products = $conn->query($sql_products);
$products = [];
if ($res_products) {
    while($row = $res_products->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $row['price'] = (float)$row['price'];
        $row['stock'] = (int)$row['stock'];
        $products[] = $row;
    }
}


$sql_orders = "SELECT o.order_id as id, u.name as customer, o.order_date as date, o.total_amount as total, o.status 
               FROM orders o 
               LEFT JOIN users u ON o.user_id = u.user_id 
               ORDER BY o.order_date DESC";
$res_orders = $conn->query($sql_orders);
$orders_map = []; 
if ($res_orders) {
    while($row = $res_orders->fetch_assoc()) {
        $row['total'] = (float)$row['total'];
        $row['items'] = []; 
        $orders_map[$row['id']] = $row;
    }
}

if (!empty($orders_map)) {
    $order_ids = implode(',', array_keys($orders_map));
    $sql_items = "SELECT oi.order_id, p.name, oi.quantity as qty 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.product_id 
                  WHERE oi.order_id IN ($order_ids)";
    $res_items = $conn->query($sql_items);
    if ($res_items) {
        while($row = $res_items->fetch_assoc()) {
            $oid = $row['order_id'];
            if (isset($orders_map[$oid])) {
                $orders_map[$oid]['items'][] = [
                    'name' => $row['name'],
                    'qty' => (int)$row['qty']
                ];
            }
        }
    }
}
$orders = array_values($orders_map); 

$sql_customers = "SELECT u.user_id as id, u.name, u.email, u.phone, 
                  COUNT(o.order_id) as orders, 
                  IFNULL(SUM(o.total_amount), 0) as spent 
                  FROM users u 
                  LEFT JOIN orders o ON u.user_id = o.user_id 
                  WHERE u.role = 'customer' 
                  GROUP BY u.user_id";
$res_customers = $conn->query($sql_customers);
$customers = [];
if ($res_customers) {
    while($row = $res_customers->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $row['orders'] = (int)$row['orders'];
        $row['spent'] = (float)$row['spent'];
        $customers[] = $row;
    }
}

$sql_users = "SELECT user_id as id, name, email, email as username, role FROM users";
$res_users = $conn->query($sql_users);
$users = [];
if ($res_users) {
    while($row = $res_users->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $users[] = $row;
    }
}

$mockData = [
    'products' => $products,
    'orders' => $orders,
    'customers' => $customers,
    'users' => $users
];
?>


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
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
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