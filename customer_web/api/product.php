<?php
include '../../includes/db.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';

    if ($action === 'categories') {
        $sql = "SELECT * FROM categories";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        
        echo json_encode(['status' => 'success', 'data' => $data]);
        exit;
    }

    elseif ($action === 'detail') {
        if (!isset($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu ID sản phẩm']);
            exit;
        }

        $id = intval($_GET['id']);

        $sql = "SELECT p.*, c.name as category_name, IFNULL(i.quantity, 0) as stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.product_id = $id AND p.status = 'active'";
        
        $result = $conn->query($sql);
        $product = $result->fetch_assoc();

        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh']);
            exit;
        }

        $product['price'] = floatval($product['price']);
        $product['discount'] = floatval($product['discount']);
        $product['stock'] = intval($product['stock']);

        $sql_imgs = "SELECT image_url, alt_text, is_main FROM product_images WHERE product_id = $id";
        $res_imgs = $conn->query($sql_imgs);
        $images = [];
        while ($row = $res_imgs->fetch_assoc()) {
            $images[] = $row;
        }
        
        $product['gallery'] = $images;

        echo json_encode(['status' => 'success', 'data' => $product]);
        exit;
    }


    else {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; 
        $offset = ($page - 1) * $limit;

        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
        $keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest'; 
        $whereClause = "WHERE p.status = 'active'";

        if ($category_id > 0) {
            $whereClause .= " AND p.category_id = $category_id";
        }

        if (!empty($keyword)) {
            $whereClause .= " AND (p.name LIKE '%$keyword%' OR p.description LIKE '%$keyword%')";
        }

        $orderBy = "ORDER BY p.created_at DESC"; 
        if ($sort === 'price_asc') $orderBy = "ORDER BY p.price ASC";
        if ($sort === 'price_desc') $orderBy = "ORDER BY p.price DESC";

        $sqlCount = "SELECT COUNT(*) as total FROM products p $whereClause";
        $resCount = $conn->query($sqlCount);
        $totalItems = $resCount->fetch_assoc()['total'];
        $totalPages = ceil($totalItems / $limit);

        $sql = "SELECT p.product_id, p.name, p.price, p.discount, p.image_url, 
                       c.name as category_name, IFNULL(i.quantity, 0) as stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                $whereClause
                $orderBy
                LIMIT $offset, $limit";

        $result = $conn->query($sql);
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $row['price'] = floatval($row['price']);
            $row['discount'] = floatval($row['discount']);
            $products[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => intval($totalItems),
                'limit' => $limit
            ]
        ]);
        exit;
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>