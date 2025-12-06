<?php
session_start();
include '../../includes/db.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    
    if (!isset($_GET['product_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing product_id']);
        exit;
    }

    $pid = intval($_GET['product_id']);

    $sql = "SELECT r.review_id, r.rating, r.comment, r.review_date, u.name as user_name
            FROM reviews r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.product_id = $pid
            ORDER BY r.review_date DESC";

    $result = $conn->query($sql);
    $reviews = [];
    $total_stars = 0;
    $count = 0;

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['rating'] = intval($row['rating']);
            $total_stars += $row['rating'];
            $count++;
            $reviews[] = $row;
        }
    }

    $avg_rating = $count > 0 ? round($total_stars / $count, 1) : 0;

    echo json_encode([
        'status' => 'success',
        'data' => [
            'summary' => [
                'average_rating' => $avg_rating,
                'total_reviews' => $count
            ],
            'reviews' => $reviews
        ]
    ]);
    exit;
}

elseif ($method === 'POST') {

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để đánh giá']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    if (empty($input['product_id']) || empty($input['rating'])) {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin sản phẩm hoặc số sao']);
        exit;
    }

    $pid = intval($input['product_id']);
    $rating = intval($input['rating']);
    $comment = isset($input['comment']) ? $conn->real_escape_string($input['comment']) : '';

    if ($rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Đánh giá phải từ 1 đến 5 sao']);
        exit;
    }
    
    $check_buy = "SELECT o.order_id 
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.order_id
                  WHERE o.user_id = $user_id 
                  AND oi.product_id = $pid 
                  AND (o.status = 'delivered' OR o.status = 'completed') 
                  LIMIT 1";

    $has_bought = $conn->query($check_buy);

    if ($has_bought->num_rows === 0) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua và đã nhận hàng thành công.'
        ]);
        exit;
    }

    $check_dup = "SELECT review_id FROM reviews WHERE user_id = $user_id AND product_id = $pid";
    if ($conn->query($check_dup)->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
        exit;
    }

    $sql = "INSERT INTO reviews (user_id, product_id, rating, comment) 
            VALUES ($user_id, $pid, $rating, '$comment')";

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Cảm ơn bạn đã đánh giá!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi Database: ' . $conn->error]);
    }
    exit;
}
?>