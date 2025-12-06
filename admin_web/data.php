<?php
$mockData = [
    'products' => [
        ['id' => 1, 'name' => "Laptop Dell XPS 13", 'category' => "Laptop", 'price' => 25000000, 'stock' => 15],
        ['id' => 2, 'name' => "iPhone 14 Pro", 'category' => "Điện thoại", 'price' => 27990000, 'stock' => 8],
        ['id' => 3, 'name' => "Tai nghe Sony WH-1000XM5", 'category' => "Phụ kiện", 'price' => 8490000, 'stock' => 25],
        ['id' => 4, 'name' => "Chuột Logitech MX Master 3", 'category' => "Phụ kiện", 'price' => 2490000, 'stock' => 40],
        ['id' => 5, 'name' => "Màn hình LG UltraFine", 'category' => "Màn hình", 'price' => 15000000, 'stock' => 5]
    ],
    'orders' => [
        ['id' => "ORD001", 'customer' => "Nguyễn Văn A", 'date' => "2023-10-25", 'total' => 25000000, 'status' => "pending", 'items' => [['name' => "Laptop Dell XPS 13", 'qty' => 1]]],
        ['id' => "ORD002", 'customer' => "Trần Thị B", 'date' => "2023-10-24", 'total' => 8490000, 'status' => "shipping", 'items' => [['name' => "Tai nghe Sony", 'qty' => 1]]],
        ['id' => "ORD003", 'customer' => "Lê Văn C", 'date' => "2023-10-23", 'total' => 30480000, 'status' => "completed", 'items' => [['name' => "iPhone 14 Pro", 'qty' => 1], ['name' => "Chuột Logitech", 'qty' => 1]]],
        ['id' => "ORD004", 'customer' => "Phạm Thị D", 'date' => "2023-10-22", 'total' => 2490000, 'status' => "cancelled", 'items' => [['name' => "Chuột Logitech", 'qty' => 1]]]
    ],
    'customers' => [
        ['id' => 1, 'name' => "Nguyễn Văn A", 'email' => "nguyena@gmail.com", 'phone' => "0901234567", 'orders' => 5, 'spent' => 120000000],
        ['id' => 2, 'name' => "Trần Thị B", 'email' => "tranb@gmail.com", 'phone' => "0909876543", 'orders' => 2, 'spent' => 15000000],
        ['id' => 3, 'name' => "Lê Văn C", 'email' => "lec@gmail.com", 'phone' => "0912345678", 'orders' => 8, 'spent' => 250000000]
    ],
    'users' => [
        ['id' => 1, 'name' => "Nguyễn Văn D", 'email' => "nguyend@gmail.com", 'username' => "nguyend", 'password' => "123", 'role' => "admin"],
        ['id' => 2, 'name' => "Nguyễn Văn A", 'email' => "nguyena@gmail.com", 'username' => "nguyena", 'password' => "123", 'role' => "customer"]
    ]
];
?>