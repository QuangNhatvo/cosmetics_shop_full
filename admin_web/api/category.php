<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { http_response_code(403); exit; }

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT * FROM categories ORDER BY category_id DESC";
    $result = $conn->query($sql);

    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
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
        if (!isset($_POST['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing category name']);
            exit;
        }

        $name = $conn->real_escape_string($_POST['name']);
        $desc = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';

        $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$desc')";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Category created', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        exit;
    }

    elseif ($action === 'delete') {
        if (!isset($_POST['category_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing category_id']);
            exit;
        }
        $id = intval($_POST['category_id']);
        $sql = "DELETE FROM categories WHERE category_id=$id";
        
        if ($conn->query($sql)) {
             echo json_encode(['status' => 'success', 'message' => 'Category deleted']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Cannot delete: ' . $conn->error]);
        }
        exit;
    }
}
?>