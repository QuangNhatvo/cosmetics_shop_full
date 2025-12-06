<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { http_response_code(403); exit; }

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!isset($_GET['product_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing product_id']);
        exit;
    }

    $pid = intval($_GET['product_id']);
    $sql = "SELECT * FROM product_images WHERE product_id = $pid";
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

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'upload') {
        
        if (!isset($_POST['product_id']) || !isset($_FILES['image'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing product_id or image file']);
            exit;
        }

        $pid = intval($_POST['product_id']);
        $alt = isset($_POST['alt_text']) ? $conn->real_escape_string($_POST['alt_text']) : '';
        $is_main = isset($_POST['is_main']) ? intval($_POST['is_main']) : 0; // 1 hoặc 0

        $upload_dir = __DIR__ . '/../../uploads/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file = $_FILES['image'];
        $filename = time() . '_' . basename($file['name']); 
        $target_file = $upload_dir . $filename;
        

        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowTypes = array('jpg','png','jpeg','gif', 'webp');
        
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {

                $db_path = 'uploads/' . $filename;

                $sql = "INSERT INTO product_images (product_id, image_url, alt_text, is_main) 
                        VALUES ($pid, '$db_path', '$alt', $is_main)";

                if ($conn->query($sql)) {
                    echo json_encode(['status' => 'success', 'message' => 'Image uploaded', 'url' => $db_path]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
                }

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG, GIF, WEBP allowed.']);
        }
        exit;
    }

    elseif ($action === 'delete') {
        if (!isset($_POST['image_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing image_id']);
            exit;
        }

        $img_id = intval($_POST['image_id']);

        $query = $conn->query("SELECT image_url FROM product_images WHERE image_id=$img_id");
        
        if ($query->num_rows > 0) {
            $row = $query->fetch_assoc();
            $file_path = __DIR__ . '/../../' . $row['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            $sql = "DELETE FROM product_images WHERE image_id=$img_id";
            if ($conn->query($sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Image deleted']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image not found']);
        }
        exit;
    }

    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }
}
?>