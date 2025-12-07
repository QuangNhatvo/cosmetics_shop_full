<?php
include '../../includes/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    $sql = "SELECT user_id, name, email, phone, address, role, created_at FROM users ORDER BY user_id DESC";
    $result = $conn->query($sql);
    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) $users[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $users]);
    exit;
}

elseif ($method === 'POST') {
    if (!isset($_POST['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing action']);
        exit;
    }

    $action = $_POST['action'];


    if ($action === 'create') {
        if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền tên, email và mật khẩu']);
            exit;
        }

        $email = $conn->real_escape_string($_POST['email']);
        $name = $conn->real_escape_string($_POST['name']);
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
        $addr = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
        $role = isset($_POST['role']) ? $conn->real_escape_string($_POST['role']) : 'customer';

        $check = $conn->query("SELECT user_id FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email đã tồn tại']);
            exit;
        }

        $sql = "INSERT INTO users (name, email, password, phone, address, role) 
                VALUES ('$name', '$email', '$pass', '$phone', '$addr', '$role')";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Tạo người dùng thành công']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi DB: ' . $conn->error]);
        }
        exit;
    }

    elseif ($action === 'update') {
        if (empty($_POST['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
            exit;
        }

        $id = intval($_POST['user_id']);
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $addr = $conn->real_escape_string($_POST['address']);
        $role = $conn->real_escape_string($_POST['role']);

        $sql = "UPDATE users SET name='$name', phone='$phone', address='$addr', role='$role' WHERE user_id=$id";

        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name='$name', phone='$phone', address='$addr', role='$role', password='$pass' WHERE user_id=$id";
        }

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi DB: ' . $conn->error]);
        }
        exit;
    }

    elseif ($action === 'delete') {
        if (empty($_POST['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
            exit;
        }
        $id = intval($_POST['user_id']);

        try {
            if ($conn->query("DELETE FROM users WHERE user_id=$id")) {
                echo json_encode(['status' => 'success', 'message' => 'Đã xóa người dùng']);
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Không thể xóa (User này đã có đơn hàng/dữ liệu liên quan)']);
        }
        exit;
    }
}
?>