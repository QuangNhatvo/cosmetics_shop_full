<?php
include '../../includes/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT p.product_id, p.name, p.description, p.price, p.discount, p.image_url, p.status, c.name as category_name, c.category_id, IFNULL(i.quantity, 0) as stock FROM products p LEFT JOIN categories c ON p.category_id = c.category_id LEFT JOIN inventory i ON p.product_id = i.product_id ORDER BY p.product_id DESC";
    $result = $conn->query($sql);

    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['price'] = floatval($row['price']);
            $row['discount'] = floatval($row['discount']);
            $row['stock'] = intval($row['stock']);
            $data[] = $row;
        }
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

elseif ($method === 'POST') {

    if (!isset($_POST['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing action']);
        exit;
    }

    $action = $_POST['action'];

    if ($action === 'create') {

        if (!isset($_POST['name'], $_POST['price'], $_POST['category_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields (name, price, category_id)']);
            exit;
        }

        $name = $conn->real_escape_string($_POST['name']);
        $price = floatval($_POST['price']);
        $cat_id = intval($_POST['category_id']);
        
        $desc = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
        $img  = isset($_POST['image_url']) ? $conn->real_escape_string($_POST['image_url']) : '';
        $disc = isset($_POST['discount']) ? floatval($_POST['discount']) : 0.00;
        $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'active';
        $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

        $conn->begin_transaction();

        try {
            $sql_prod = "INSERT INTO products (name, description, price, discount, category_id, image_url, status) VALUES ('$name', '$desc', $price, $disc, $cat_id, '$img', '$status')";
            
            if (!$conn->query($sql_prod)) {
                throw new Exception("Product Insert Failed: " . $conn->error);
            }

            $new_product_id = $conn->insert_id;

            $sql_inv = "INSERT INTO inventory (product_id, quantity) VALUES ($new_product_id, $stock)";
            
            if (!$conn->query($sql_inv)) {
                throw new Exception("Inventory Insert Failed: " . $conn->error);
            }

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Product created successfully', 'id' => $new_product_id]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    elseif ($action === 'update') {

        if (!isset($_POST['product_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing product_id']);
            exit;
        }

        $pid = intval($_POST['product_id']);
        
        $updates = [];
        if (isset($_POST['name'])) $updates[] = "name='" . $conn->real_escape_string($_POST['name']) . "'";
        if (isset($_POST['description'])) $updates[] = "description='" . $conn->real_escape_string($_POST['description']) . "'";
        if (isset($_POST['price'])) $updates[] = "price=" . floatval($_POST['price']);
        if (isset($_POST['discount'])) $updates[] = "discount=" . floatval($_POST['discount']);
        if (isset($_POST['category_id'])) $updates[] = "category_id=" . intval($_POST['category_id']);
        if (isset($_POST['image_url'])) $updates[] = "image_url='" . $conn->real_escape_string($_POST['image_url']) . "'";
        if (isset($_POST['status'])) $updates[] = "status='" . $conn->real_escape_string($_POST['status']) . "'";

        $conn->begin_transaction();

        try {
            if (!empty($updates)) {
                $sql_prod = "UPDATE products SET " . implode(', ', $updates) . " WHERE product_id=$pid";
                if (!$conn->query($sql_prod)) {
                    throw new Exception("Product Update Failed: " . $conn->error);
                }
            }

            if (isset($_POST['stock'])) {
                $stock = intval($_POST['stock']);
                
                $check_inv = $conn->query("SELECT inventory_id FROM inventory WHERE product_id=$pid");
                
                if ($check_inv && $check_inv->num_rows > 0) {
                    $sql_inv = "UPDATE inventory SET quantity=$stock WHERE product_id=$pid";
                } else {
                    $sql_inv = "INSERT INTO inventory (product_id, quantity) VALUES ($pid, $stock)";
                }

                if (!$conn->query($sql_inv)) {
                    throw new Exception("Inventory Update Failed: " . $conn->error);
                }
            }

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    elseif ($action === 'delete') {

        if (!isset($_POST['product_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing product_id']);
            exit;
        }

        $pid = intval($_POST['product_id']);

        $check_sql = "SELECT order_item_id FROM order_items WHERE product_id = $pid LIMIT 1";
        $check_result = $conn->query($check_sql);

        if ($check_result && $check_result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Không thể xóa vì sản phẩm này đã có đơn hàng liên quan!']);
            exit;
        }

        $conn->begin_transaction();
        try {
            $conn->query("DELETE FROM inventory WHERE product_id=$pid");
            $conn->query("DELETE FROM product_images WHERE product_id=$pid");
            $conn->query("DELETE FROM reviews WHERE product_id=$pid");
            
            $sql = "DELETE FROM products WHERE product_id=$pid";
            if (!$conn->query($sql)) {
                throw new Exception("Delete Failed");
            }

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi hệ thống khi xóa sản phẩm']);
        }
        exit;
    }

    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }
}
?>