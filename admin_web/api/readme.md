# Cấu trúc các api:
## user managerment
### Method Get
```bash
curl -X GET http://localhost:8000/admin_web/api/user.php
```

### Method Post
```bash
curl -X POST http://localhost:8000/admin_web/api/user.php \
     -d "action=create_admin" \
     -d "email=manager@shop.com" \
     -d "password=123456" \
     -d "name=Quan Ly Kho" \
     -d "phone=0912345678" \
     -d "address=Kho Tong Da Nang"
```

### Method Post reset pwd về mặc định (123456)
```bash
curl -X POST http://localhost:8000/admin_web/api/user.php \
     -d "action=reset_pass" \
     -d "user_id=1"
```

## Product managerment
### Method Get (lấy toàn bộ danh sách)
```bash
curl -X GET http://localhost:8000/admin_web/api/product.php
```

### Method Post (create - thêm sản phẩm mới)
```bash
curl -X POST http://localhost:8000/admin_web/api/product.php \
     -d "action=create" \
     -d "name=Kem Duong Da Ban Dem" \
     -d "category_id=1" \
     -d "price=250000" \
     -d "stock=50" \
     -d "description=Kem duong am chuyen sau" \
     -d "status=active"
```

### Method Post (Update)
```bash
curl -X POST http://localhost:8000/admin_web/api/product.php \
     -d "action=update" \
     -d "product_id=15" \
     -d "price=300000" \
     -d "stock=200"
```
### Method Post (Delete - xóa sản phẩm)
```bash
curl -X POST http://localhost:8000/admin_web/api/product.php \
     -d "action=delete" \
     -d "product_id=15"
```

## Product image managerment
### Method Get (lấy theo sản phẩm cụ thể  - id)
```bash
curl "http://localhost:8000/admin_web/api/product_image.php?product_id=1"
```

### Method Post (Upload ảnh mới)
```bash
curl -X POST http://localhost:8000/admin_web/api/product_image.php \
     -F "action=upload" \
     -F "product_id=1" \
     -F "alt_text=Anh mat sau san pham" \
     -F "is_main=0" \
     -F "image=@test.jpg"  --> thay bằng đường dẫn ở máy
```

### Method Post (Delete)
```bash
curl -X POST http://localhost:8000/admin_web/api/product_image.php \
     -d "action=delete" \
     -d "image_id=10"
```

## Category Managerment 
### Method Get
```bash
curl -X GET http://localhost:8000/admin_web/api/category.php
```

### Method Post (Create):
```bash
curl -X POST http://localhost:8000/admin_web/api/category.php \
     -d "action=create" \
     -d "name=Nuoc Hoa Cao Cap" \
     -d "description=Cac dong nuoc hoa nhap khau Phap, Y"
```

### Method Post (Delete):
```bash
curl -X POST http://localhost:8000/admin_web/api/category.php \
     -d "action=delete" \
     -d "category_id=3"
```