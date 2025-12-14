<?php
include '../../includes/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT p.product_id, p.name, p.image_url, IFNULL(i.quantity, 0) as stock, i.last_updated FROM products p LEFT JOIN inventory i ON p.product_id = i.product_id WHERE p.status = 'active' ORDER BY i.quantity ASC";
    $result = $conn->query($sql);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['stock'] = intval($row['stock']);
            $data[] = $row;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

elseif ($method === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'import') {
        if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu']);
            exit;
        }

        $pid = intval($_POST['product_id']);
        $qty_add = intval($_POST['quantity']);

        if ($qty_add <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Số lượng nhập phải lớn hơn 0']);
            exit;
        }

        $check = $conn->query("SELECT quantity FROM inventory WHERE product_id = $pid");
        
        if ($check->num_rows > 0) {
            $sql = "UPDATE inventory SET quantity = quantity + $qty_add, last_updated = NOW() WHERE product_id = $pid";
        } else {
            $sql = "INSERT INTO inventory (product_id, quantity, last_updated) VALUES ($pid, $qty_add, NOW())";
        }

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Đã nhập kho thành công!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi DB: ' . $conn->error]);
        }
        exit;
    }
}
?>