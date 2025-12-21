<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location.href='../login.php';</script>"; 
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$user_info = ['name' => '', 'phone' => '', 'address' => ''];

$sql = "SELECT name, phone, address FROM users WHERE user_id = $user_id";
$res = $conn->query($sql);
if($res && $res->num_rows > 0) {
    $user_info = $res->fetch_assoc();
}

include '../includes/header.php'; 
?>

<style>
    body { background-color: #f8f9fa; }
    .checkout-wrapper { padding: 40px 0; }
    
    .section-header {
        font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;
        padding-bottom: 10px; border-bottom: 2px solid #eee; color: #333;
    }

    .form-card {
        background: #fff; border-radius: 10px; padding: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e9ecef;
        margin-bottom: 25px;
    }

    .form-floating > .form-control:focus ~ label { color: #ff6b6b; }
    .form-control:focus { border-color: #ff6b6b; box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25); }

    .custom-radio-group { display: flex; flex-direction: column; gap: 15px; }
    
    .radio-card {
        position: relative; display: flex; align-items: center; justify-content: space-between;
        padding: 15px 20px; border: 2px solid #e9ecef; border-radius: 8px;
        cursor: pointer; transition: all 0.2s ease; background: #fff;
    }
    .radio-card:hover { border-color: #ffb3b3; background: #fff5f5; }
    .radio-card.active { border-color: #ff6b6b; background-color: #fff0f0; }
    .radio-card input[type="radio"] { display: none; }
    
    .radio-mark {
        width: 20px; height: 20px; border: 2px solid #ccc; border-radius: 50%;
        margin-right: 15px; position: relative;
    }
    .radio-card.active .radio-mark { border-color: #ff6b6b; }
    .radio-card.active .radio-mark::after {
        content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 10px; height: 10px; background: #ff6b6b; border-radius: 50%;
    }

    .order-summary {
        background: #fff; border-radius: 10px; padding: 25px;
        border: 1px solid #e9ecef; position: sticky; top: 100px;
    }

    .product-item {
        display: flex; align-items: flex-start; margin-bottom: 15px; padding-bottom: 15px;
        border-bottom: 1px solid #f1f1f1;
    }
    
    .product-img {
        width: 70px; height: 70px; object-fit: cover;
        border-radius: 6px; border: 1px solid #eee; margin-right: 15px;
        background-color: #f0f0f0;
    }

    .product-info { flex: 1; font-size: 14px; }
    .product-name { font-weight: 600; line-height: 1.4; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .qty-control-group {
        display: flex; align-items: center;
        background: #f8f9fa; border: 1px solid #ddd;
        border-radius: 4px; width: fit-content;
    }
    .qty-btn {
        width: 28px; height: 28px; border: none; background: white;
        font-weight: bold; cursor: pointer; color: #555;
        display: flex; align-items: center; justify-content: center;
    }
    .qty-btn:hover { background-color: #eee; }
    .qty-input {
        width: 35px; text-align: center; border: none;
        border-left: 1px solid #ddd; border-right: 1px solid #ddd;
        background: transparent; font-size: 13px; font-weight: 600;
    }
    .qty-input:focus { outline: none; }
    .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #666; }
    .total-row {
        display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px;
        border-top: 2px dashed #ddd; font-size: 18px; font-weight: 700; color: #d63031;
    }

    .btn-checkout {
        background: #ff6b6b; color: white; font-weight: 700; text-transform: uppercase;
        width: 100%; padding: 12px; border-radius: 50px; border: none; margin-top: 20px; transition: 0.3s;
    }
    .btn-checkout:hover { background: #ee5253; box-shadow: 0 4px 12px rgba(238, 82, 83, 0.4); }
    .btn-checkout:disabled { background: #ccc; cursor: not-allowed; }
</style>

<div class="checkout-wrapper">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="cart.php" class="text-decoration-none text-muted">Giỏ hàng</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Thanh toán</li>
            </ol>
        </nav>

        <form id="checkoutForm">
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-card">
                        <h3 class="section-header"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Địa chỉ nhận hàng</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="recipientName" placeholder="Họ tên" value="<?php echo htmlspecialchars($user_info['name']); ?>" required>
                                    <label for="recipientName">Họ và tên người nhận</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phone" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($user_info['phone']); ?>" required>
                                    <label for="phone">Số điện thoại</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="address" placeholder="Địa chỉ" value="<?php echo htmlspecialchars($user_info['address']); ?>" required>
                                    <label for="address">Địa chỉ chi tiết</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Ghi chú" id="note" style="height: 80px"></textarea>
                                    <label for="note">Ghi chú (Tùy chọn)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h3 class="section-header"><i class="fas fa-truck me-2 text-primary"></i>Phương thức vận chuyển</h3>
                        <div class="custom-radio-group">
                            <div class="radio-card active" onclick="selectShipping(this, 30000)">
                                <div class="d-flex align-items-center">
                                    <div class="radio-mark"></div>
                                    <div>
                                        <div class="fw-bold">Giao hàng Tiêu chuẩn</div>
                                        <small class="text-muted">3-5 ngày</small>
                                    </div>
                                </div>
                                <div class="fw-bold">30.000đ</div>
                                <input type="radio" name="shipping" value="standard" checked>
                            </div>

                            <div class="radio-card" onclick="selectShipping(this, 50000)">
                                <div class="d-flex align-items-center">
                                    <div class="radio-mark"></div>
                                    <div>
                                        <div class="fw-bold">Giao hàng Hỏa tốc</div>
                                        <small class="text-muted">Nhận trong 24h</small>
                                    </div>
                                </div>
                                <div class="fw-bold">50.000đ</div>
                                <input type="radio" name="shipping" value="express">
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h3 class="section-header"><i class="fas fa-wallet me-2 text-success"></i>Phương thức thanh toán</h3>
                        <div class="custom-radio-group">
                            <div class="radio-card active" onclick="selectPayment(this)">
                                <div class="d-flex align-items-center">
                                    <div class="radio-mark"></div>
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                        <div>
                                            <div class="fw-bold">Thanh toán khi nhận hàng (COD)</div>
                                            <small class="text-muted">Thanh toán tiền mặt cho shipper</small>
                                        </div>
                                    </div>
                                </div>
                                <input type="radio" name="payment" value="cash" checked>
                            </div>

                            <div class="radio-card" onclick="selectPayment(this)">
                                <div class="d-flex align-items-center">
                                    <div class="radio-mark"></div>
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-university fa-2x text-primary"></i>
                                        <div>
                                            <div class="fw-bold">Chuyển khoản ngân hàng</div>
                                            <small class="text-muted">Quét mã QR VietQR</small>
                                        </div>
                                    </div>
                                </div>
                                <input type="radio" name="payment" value="bank_transfer">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary">
                        <h4 class="mb-4">Đơn hàng của bạn</h4>
                        
                        <div id="cart-items-wrapper">
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm" role="status"></div> Đang tải...
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="summary-row">
                                <span>Tạm tính</span>
                                <span class="fw-bold" id="subtotal-price">0đ</span>
                            </div>
                            <div class="summary-row">
                                <span>Phí vận chuyển</span>
                                <span class="fw-bold" id="shipping-price">30.000đ</span>
                            </div>
                            <div class="total-row">
                                <span>TỔNG CỘNG</span>
                                <span id="total-price">0đ</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-checkout">
                            ĐẶT HÀNG NGAY
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>

    let cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
    let productsMap = {};
    let shippingFee = 30000; 

    function selectShipping(element, fee) {
        document.querySelectorAll('input[name="shipping"]').forEach(el => el.closest('.radio-card').classList.remove('active'));
        element.classList.add('active');
        element.querySelector('input').checked = true;
        shippingFee = fee;
        recalculateTotals();
    }

    function selectPayment(element) {
        document.querySelectorAll('input[name="payment"]').forEach(el => el.closest('.radio-card').classList.remove('active'));
        element.classList.add('active');
        element.querySelector('input').checked = true;
    }

    document.addEventListener("DOMContentLoaded", async () => {
        const wrapper = document.getElementById('cart-items-wrapper');

        if(cart.length === 0) {
            wrapper.innerHTML = '<p class="text-center text-muted">Giỏ hàng trống.</p>';
            return;
        }

        wrapper.innerHTML = ''; 

        const promises = cart.map(item => 
            fetch(`api/product.php?action=detail&id=${item.product_id}`).then(res => res.json())
        );

        try {
            const results = await Promise.all(promises);

            results.forEach((res, index) => {
                let p = res.data || res; 

                if (p && p.name) {
                    productsMap[p.product_id] = p;

                    let imgSrc = 'https://via.placeholder.com/300x300?text=No+Image';
                    let apiImage = p.image_url || p.image; 

                    if(apiImage) {
                        if(apiImage.startsWith('http')) {
                            imgSrc = apiImage;
                        } else {
                            imgSrc = '../' + apiImage; 
                        }
                    }

                    const currentQty = cart[index].quantity;
                    const price = parseFloat(p.price);

                    const html = `
                        <div class="product-item" id="item-${p.product_id}">
                            <img src="${imgSrc}" class="product-img" alt="${p.name}" 
                                 onerror="this.src='https://via.placeholder.com/60?text=Err'">
                            
                            <div class="product-info w-100">
                                <div class="product-name">${p.name}</div>
                                <div class="d-flex justify-content-between align-items-end mt-2">
                                    
                                    <div class="qty-control-group">
                                        <button type="button" class="qty-btn" onclick="updateQty(${p.product_id}, -1)">-</button>
                                        <input type="number" class="qty-input" value="${currentQty}" readonly>
                                        <button type="button" class="qty-btn" onclick="updateQty(${p.product_id}, 1)">+</button>
                                    </div>

                                    <div class="text-end">
                                        <div class="small text-muted" style="font-size: 11px;">${price.toLocaleString()}đ</div>
                                        <div class="fw-bold text-dark item-total-price">
                                            ${(price * currentQty).toLocaleString()}đ
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    wrapper.insertAdjacentHTML('beforeend', html);
                }
            });

            recalculateTotals();

        } catch (err) {
            console.error("Lỗi:", err);
            wrapper.innerHTML = '<p class="text-danger text-center">Lỗi tải sản phẩm</p>';
        }
    });

    function updateQty(productId, change) {
    const itemIndex = cart.findIndex(item => item.product_id == productId);
    if (itemIndex === -1) return;

    let newQty = cart[itemIndex].quantity + change;

    if (newQty <= 0) {
        const isConfirmed = confirm("Xóa sản phẩm này khỏi giỏ hàng?");
        if (isConfirmed) {
            cart.splice(itemIndex, 1);
            
            localStorage.setItem('shopping_cart', JSON.stringify(cart));

            if (cart.length === 0) {
                location.reload(); 
            } else {
                document.getElementById(`item-${productId}`).remove();
                recalculateTotals();
                updateHeaderBadge(); 
            }
        }
    } 

    else {
        cart[itemIndex].quantity = newQty;
        localStorage.setItem('shopping_cart', JSON.stringify(cart)); // Lưu lại
        
        const itemRow = document.getElementById(`item-${productId}`);
        itemRow.querySelector('.qty-input').value = newQty;
        
        const p = productsMap[productId];
        if (p) {
            const lineTotal = parseFloat(p.price) * newQty;
            itemRow.querySelector('.item-total-price').innerText = lineTotal.toLocaleString() + 'đ';
        }
        recalculateTotals();
        updateHeaderBadge(); 
    }
}

function updateHeaderBadge() {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

    const badge = document.querySelector('.badge') || document.querySelector('.cart-count') || document.querySelector('#cart-count');
    
    if (badge) {
        badge.innerText = totalItems;
    }
}

    function recalculateTotals() {
        let subTotal = 0;
        
        cart.forEach(item => {
            const p = productsMap[item.product_id];
            if (p) {
                subTotal += parseFloat(p.price) * item.quantity;
            }
        });

        document.getElementById('subtotal-price').innerText = subTotal.toLocaleString() + 'đ';
        document.getElementById('shipping-price').innerText = shippingFee.toLocaleString() + 'đ';
        document.getElementById('total-price').innerText = (subTotal + shippingFee).toLocaleString() + 'đ';
    }

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.querySelector('.btn-checkout');
        const oldText = btn.innerText;
        const recipientName = document.getElementById('recipientName').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const address = document.getElementById('address').value.trim();
        
        if (!recipientName || !phone || !address) {
            alert("Vui lòng điền đầy đủ thông tin giao hàng!");
            return;
        }

        btn.innerText = 'ĐANG XỬ LÝ...';
        btn.disabled = true;

        const payload = {
            action: 'checkout',
            recipient_name: recipientName,
            phone: phone,
            address: address,
            note: document.getElementById('note').value,
            payment_method: document.querySelector('input[name="payment"]:checked').value,
            shipping_method: document.querySelector('input[name="shipping"]:checked').value,
            items: cart
        };

        fetch('api/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Đặt hàng thành công! Mã đơn: " + data.order_id);
                localStorage.removeItem('cart_items');
                window.location.href = 'profile.php';
            } else {
                alert("Lỗi: " + data.message);
                btn.disabled = false;
                btn.innerText = oldText;
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi kết nối server");
            btn.disabled = false;
            btn.innerText = oldText;
        });
    });
</script>

<?php include '../includes/footer.php'; ?>