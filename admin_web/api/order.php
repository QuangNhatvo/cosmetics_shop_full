<?php
// admin_web/api/order.php
include '../../includes/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $total = $conn->query("SELECT COUNT(*) as t FROM orders")->fetch_assoc()['t'];
        $totalPages = ceil($total / $limit);

        // Lấy danh sách đơn hàng + Tên khách hàng
        $sql = "SELECT o.order_id, o.order_date, o.total_amount, o.status, u.name as customer_name 
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.user_id
                ORDER BY o.order_date DESC
                LIMIT $limit OFFSET $offset";
        
        $result = $conn->query($sql);
        $data = [];
        while($row = $result->fetch_assoc()) {
            $row['total_amount'] = floatval($row['total_amount']);
            $data[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => intval($total)
            ]
        ]);
    }
    elseif ($action === 'detail') {
        // Lấy chi tiết 1 đơn hàng cụ thể cho Modal View
        $id = intval($_GET['order_id']);
        
        $sqlInfo = "SELECT o.*, u.name as customer_name 
                    FROM orders o LEFT JOIN users u ON o.user_id = u.user_id 
                    WHERE o.order_id = $id";
        $info = $conn->query($sqlInfo)->fetch_assoc();

        $sqlItems = "SELECT oi.*, p.name 
                     FROM order_items oi JOIN products p ON oi.product_id = p.product_id 
                     WHERE oi.order_id = $id";
        $itemsRes = $conn->query($sqlItems);
        $items = [];
        while($row = $itemsRes->fetch_assoc()) $items[] = $row;

        echo json_encode([
            'status' => 'success',
            'data' => [
                'info' => $info,
                'items' => $items
            ]
        ]);
    }
}
?>