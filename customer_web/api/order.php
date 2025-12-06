<?php
session_start();
include '../../includes/db.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để thực hiện chức năng này']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';

    if ($action === 'list') {
        $sql = "SELECT o.order_id, o.order_date, o.status, o.total_amount, 
                       p.payment_status, s.shipping_status
                FROM orders o
                LEFT JOIN payments p ON o.order_id = p.order_id
                LEFT JOIN shipping s ON o.order_id = s.order_id
                WHERE o.user_id = $user_id 
                ORDER BY o.order_date DESC";
        
        $result = $conn->query($sql);
        $orders = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['total_amount'] = floatval($row['total_amount']);
                $orders[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $orders]);
        exit;
    }

    elseif ($action === 'detail') {
        if (!isset($_GET['order_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing order_id']);
            exit;
        }
        $order_id = intval($_GET['order_id']);

        $sql_order = "SELECT * FROM orders WHERE order_id = $order_id AND user_id = $user_id";
        $order = $conn->query($sql_order)->fetch_assoc();

        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Đơn hàng không tồn tại hoặc bạn không có quyền xem']);
            exit;
        }

        $sql_items = "SELECT oi.product_id, oi.quantity, oi.price, p.name, p.image_url 
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.product_id
                      WHERE oi.order_id = $order_id";
        $items = [];
        $res_items = $conn->query($sql_items);
        while ($row = $res_items->fetch_assoc()) $items[] = $row;

        $sql_ship = "SELECT recipient_name, shipping_address, shipping_phone, shipping_status, delivered_at 
                     FROM shipping WHERE order_id = $order_id";
        $shipping = $conn->query($sql_ship)->fetch_assoc();

        $sql_pay = "SELECT payment_method, payment_status, paid_at FROM payments WHERE order_id = $order_id";
        $payment = $conn->query($sql_pay)->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'info' => $order,
                'items' => $items,
                'shipping' => $shipping,
                'payment' => $payment
            ]
        ]);
        exit;
    }
}

elseif ($method === 'POST') {
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) $input = $_POST;

    $action = isset($input['action']) ? $input['action'] : 'checkout';

    if ($action === 'checkout') {
        
        if (empty($input['items']) || empty($input['recipient_name']) || empty($input['address']) || empty($input['phone'])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin giao hàng và giỏ hàng']);
            exit;
        }

        $items = $input['items']; 
        $name = $conn->real_escape_string($input['recipient_name']);
        $addr = $conn->real_escape_string($input['address']);
        $phone = $conn->real_escape_string($input['phone']);
        $pay_method = isset($input['payment_method']) ? $conn->real_escape_string($input['payment_method']) : 'cash';

        $conn->begin_transaction(); 

        try {
            $total_amount = 0;
            $order_items_data = [];

            foreach ($items as $item) {
                $pid = intval($item['product_id']);
                $qty = intval($item['quantity']);

                $sql_check = "SELECT p.price, p.discount, i.quantity as stock 
                              FROM products p 
                              JOIN inventory i ON p.product_id = i.product_id 
                              WHERE p.product_id = $pid FOR UPDATE"; 
                
                $res_check = $conn->query($sql_check);
                if ($res_check->num_rows === 0) throw new Exception("Sản phẩm ID $pid không tồn tại");
                
                $prod_data = $res_check->fetch_assoc();
                
                if ($prod_data['stock'] < $qty) {
                    throw new Exception("Sản phẩm ID $pid chỉ còn " . $prod_data['stock'] . " sản phẩm, không đủ để bán.");
                }

                $final_price = floatval($prod_data['price']); 

                $total_amount += $final_price * $qty;

                $order_items_data[] = [
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'price' => $final_price
                ];
            }

            $sql_order = "INSERT INTO orders (user_id, total_amount, status) VALUES ($user_id, $total_amount, 'pending')";
            if (!$conn->query($sql_order)) throw new Exception("Lỗi tạo đơn hàng: " . $conn->error);
            $new_order_id = $conn->insert_id;

            foreach ($order_items_data as $item) {
                $pid = $item['product_id'];
                $qty = $item['quantity'];
                $price = $item['price'];

                $sql_item_insert = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                    VALUES ($new_order_id, $pid, $qty, $price)";
                if (!$conn->query($sql_item_insert)) throw new Exception("Lỗi lưu sản phẩm");

                $sql_update_stock = "UPDATE inventory SET quantity = quantity - $qty WHERE product_id = $pid";
                if (!$conn->query($sql_update_stock)) throw new Exception("Lỗi trừ tồn kho");
            }

            $sql_ship = "INSERT INTO shipping (order_id, recipient_name, shipping_address, shipping_phone, shipping_status)
                         VALUES ($new_order_id, '$name', '$addr', '$phone', 'pending')";
            if (!$conn->query($sql_ship)) throw new Exception("Lỗi lưu thông tin giao hàng");

            $sql_pay = "INSERT INTO payments (order_id, payment_method, payment_status)
                        VALUES ($new_order_id, '$pay_method', 'unpaid')";
            if (!$conn->query($sql_pay)) throw new Exception("Lỗi lưu thông tin thanh toán");

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Đặt hàng thành công!', 'order_id' => $new_order_id]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    elseif ($action === 'cancel') {
        if (empty($input['order_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing order_id']);
            exit;
        }

        $order_id = intval($input['order_id']);

        $conn->begin_transaction();
        try {
            $check = $conn->query("SELECT status FROM orders WHERE order_id = $order_id AND user_id = $user_id FOR UPDATE");
            $order_info = $check->fetch_assoc();

            if (!$order_info) throw new Exception("Không tìm thấy đơn hàng");
            if ($order_info['status'] !== 'pending') throw new Exception("Chỉ có thể hủy đơn hàng khi đang chờ xử lý (Pending)");

            $conn->query("UPDATE orders SET status = 'cancelled' WHERE order_id = $order_id");

            $items = $conn->query("SELECT product_id, quantity FROM order_items WHERE order_id = $order_id");
            while ($item = $items->fetch_assoc()) {
                $pid = $item['product_id'];
                $qty = $item['quantity'];
                $conn->query("UPDATE inventory SET quantity = quantity + $qty WHERE product_id = $pid");
            }

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Đã hủy đơn hàng thành công']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}
?>