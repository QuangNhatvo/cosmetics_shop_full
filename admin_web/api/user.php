<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { http_response_code(403); exit; }

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT user_id, name, email, phone, address, role, created_at FROM users";
    $result = $conn->query($sql);

    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $users
    ]);
    exit;
}

elseif ($method === 'POST') {
    if (!isset($_POST['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing action']);
        exit;
    }

    $action = $_POST['action'];

    if ($action === 'create_admin') {

        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing email or password']);
            exit;
        }

        $email = $conn->real_escape_string($_POST['email']);
        $raw_pass = $_POST['password'];
        $role = 'admin'; 

        $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : 'Admin';
        $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
        $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';

        $hashed_pass = password_hash($raw_pass, PASSWORD_DEFAULT);

        $check = $conn->query("SELECT user_id FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            exit;
        }

        $sql = "INSERT INTO users (name, email, password, phone, address, role)
                VALUES ('$name', '$email', '$hashed_pass', '$phone', '$address', '$role')";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Admin created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    }

    elseif ($action === 'reset_pass') {

        if (!isset($_POST['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
            exit;
        }

        $user_id = intval($_POST['user_id']);
        $new_pass = password_hash('123456', PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password='$new_pass' WHERE user_id=$user_id";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Password reset to default (123456)']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }

        exit;
    }

    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }
}
?>