<?php
session_start();
include '../../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

$user_id = $_SESSION['user_id']; 
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT user_id, name, email, phone, address, role FROM users WHERE user_id = $user_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy thông tin']);
    }
    exit;
}

elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST; 

    $action = isset($input['action']) ? $input['action'] : '';

    if ($action === 'update_info') {
        if (empty($input['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Tên không được để trống']);
            exit;
        }

        $name = $conn->real_escape_string($input['name']);
        $phone = isset($input['phone']) ? $conn->real_escape_string($input['phone']) : '';
        $address = isset($input['address']) ? $conn->real_escape_string($input['address']) : '';

        $sql = "UPDATE users SET name = '$name', phone = '$phone', address = '$address' WHERE user_id = $user_id";

        if ($conn->query($sql)) {
            $_SESSION['user_name'] = $name; 
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật thông tin thành công']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $conn->error]);
        }
        exit;
    }

    elseif ($action === 'change_password') {
        $old_pass = $input['old_password'] ?? '';
        $new_pass = $input['new_password'] ?? '';

        if (empty($old_pass) || empty($new_pass)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ mật khẩu']);
            exit;
        }

        $check = $conn->query("SELECT password FROM users WHERE user_id = $user_id");
        $row = $check->fetch_assoc();

        if (!password_verify($old_pass, $row['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu cũ không chính xác']);
            exit;
        }

        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hashed_pass' WHERE user_id = $user_id");
        
        echo json_encode(['status' => 'success', 'message' => 'Đổi mật khẩu thành công']);
        exit;
    }
}
?>