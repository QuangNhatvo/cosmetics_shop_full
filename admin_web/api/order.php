<?php
require_once '../../includes/db.php'; 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $order_id = $data['order_id'];
    $status = $data['status'];

    $allowed_status = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

    if (!in_array($status, $allowed_status)) {
        echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
        exit;
    }

    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $conn->error]);
    }
}
?>