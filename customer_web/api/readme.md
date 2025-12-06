# Cấu trúc API
## Product Managerment
### Method Get (lấy danh sách)
```bash
curl "http://localhost:8000/customer_web/api/product.php"
```

### Method Get (tìm kiếm + lọc)
```bash
curl "http://localhost:8000/customer_web/api/product.php?keyword=Son&category_id=1&sort=price_asc&page=1"
```
Tìm kiếm theo từ khóa (vd: son), lọc danh mục id = 1

### Method Get (by ID - lấy chi tiết sản phẩm)
```bash
curl "http://localhost:8000/customer_web/api/product.php?action=detail&id=5"
```

### Method Get (lấy danh sách danh mục - category)
```bash
curl "http://localhost:8000/customer_web/api/product.php?action=categories"
```

## Order Managerment
### Method Post (Create - Đặt hàng)
Tạo đơn hàng mới -> Hệ thống tự động kiếm tra tồn kho, trừ kho -> lưu thông tin giao hàng và thanh toán.

```bash
curl -X POST http://localhost:8000/customer_web/api/order.php \
     --cookie "PHPSESSID=session_code" \
     -H "Content-Type: application/json" \
     -d '{
           "action": "checkout",
           "recipient_name": "Nguyen Van A",
           "address": "123 Le Loi, Quan 1, TP.HCM",
           "phone": "0909123456",
           "payment_method": "cod",
           "items": [
               { "product_id": 5, "quantity": 1 },
               { "product_id": 6, "quantity": 2 }
           ]
         }'
```

### Method Get (list - xem lịch sử đơn hàng)
```bash
curl "http://localhost:8000/customer_web/api/order.php?action=list" \
     --cookie "PHPSESSID=session_code"
```

### Method GEt (by ID - chi tiết đơn hàng)
```bash
curl "http://localhost:8000/customer_web/api/order.php?action=detail&order_id=12" \
     --cookie "PHPSESSID=session_code"
```

### Method Post (delete - hủy đơn)
```bash
curl -X POST http://localhost:8000/customer_web/api/order.php \
     --cookie "PHPSESSID=session_code" \
     -H "Content-Type: application/json" \
     -d '{
           "action": "cancel",
           "order_id": 12
         }'
```

## Review Managerment
### MEthod Get (by ID - sản phẩm cần xem đánh giá)
```bash
curl "http://localhost:8000/customer_web/api/review.php?product_id=5"
```

### Method Post (gửi đánh giá)
```bash
curl -X POST http://localhost:8000/customer_web/api/review.php \
     --cookie "PHPSESSID=session_code" \
     -H "Content-Type: application/json" \
     -d '{
           "product_id": 5,
           "rating": 5,
           "comment": "Chat luong tuyet voi, dong goi can than!"
         }'
```

