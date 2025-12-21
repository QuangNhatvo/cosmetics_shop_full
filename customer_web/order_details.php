<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Không tìm thấy ID đơn hàng!'); window.location.href='profile.php';</script>";
    exit();
}

$order_id = intval($_GET['id']);
include '../includes/header.php'; 
?>

<style>
    :root {
        --bg-pink-pastel: #fceef2;
        --bg-white: #ffffff;
        --btn-green-pastel: #c2eac4;
        --btn-green-hover: #a8dcb0;
        --text-dark: #333;
        --border-radius-card: 20px;
        --primary-red: #ff6b6b;
    }

    body {
        background-color: #f8f9fa;
        font-family: 'Open Sans', sans-serif;
    }

    .breadcrumb-area { margin: 20px 0; font-size: 14px; color: #666; }
    .breadcrumb-area a { text-decoration: none; color: #666; }
    .breadcrumb-area a:hover { color: var(--primary-red); }

    .detail-container { max-width: 900px; margin: 0 auto 50px; }

    .info-section {
        background-color: var(--bg-pink-pastel);
        border-radius: var(--border-radius-card);
        padding: 25px;
        margin-bottom: 20px;
        border: 1px solid #f3dce2;
    }

    .section-title {
        font-weight: 700; font-size: 16px; margin-bottom: 15px;
        color: var(--text-dark); display: flex; align-items: center; gap: 10px;
    }

    .order-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .order-id-txt { font-size: 20px; font-weight: 800; color: var(--text-dark); }
    .order-date-txt { font-size: 14px; color: #666; }

    .status-timeline { display: flex; justify-content: space-between; margin-top: 25px; position: relative; }
    .status-timeline::before {
        content: ''; position: absolute; top: 15px; left: 0; right: 0;
        height: 4px; background: #e0e0e0; z-index: 0; border-radius: 10px;
    }
    .step { position: relative; z-index: 1; text-align: center; width: 25%; }
    .step-icon {
        width: 35px; height: 35px; background: #fff;
        border: 4px solid #e0e0e0; border-radius: 50%; margin: 0 auto 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; color: #999; transition: 0.3s;
    }
    .step-text { font-size: 12px; font-weight: 600; color: #999; }
  
    .step.active .step-icon { border-color: var(--primary-red); background: var(--primary-red); color: #fff; }
    .step.active .step-text { color: var(--primary-red); }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .info-box { background: #fff; border-radius: 10px; padding: 15px; height: 100%; }
    .info-row { margin-bottom: 8px; font-size: 14px; }
    .info-label { font-weight: 600; color: #555; width: 100px; display: inline-block; }

    .product-list-card {
        background: #fff; border-radius: var(--border-radius-card);
        padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }
    
    .item-row { display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid #eee; }
    .item-row:last-child { border-bottom: none; }

    .product-link-wrapper {
        text-decoration: none; color: inherit; display: contents; 
    }

    .item-img {
        width: 70px; height: 70px; object-fit: cover;
        border-radius: 8px; background: #f9f9f9; margin-right: 15px;
        border: 1px solid #eee;
        transition: transform 0.2s, border-color 0.2s;
    }

    a.img-link:hover .item-img {
        transform: scale(1.05);
        border-color: var(--primary-red);
    }

    .item-details { flex: 1; }
    
    .item-name { 
        font-weight: 700; color: #333; font-size: 15px; display: block; margin-bottom: 5px;
        transition: color 0.2s;
    }
    
    a.name-link { text-decoration: none; }
    a.name-link:hover .item-name {
        color: var(--primary-red);
        text-decoration: underline;
    }

    .item-meta { font-size: 13px; color: #777; }
    .item-price { font-weight: 700; color: var(--text-dark); }

    .summary-section { margin-top: 20px; text-align: right; font-size: 14px; }
    .sum-row { margin-bottom: 8px; }
    .total-price { font-size: 24px; font-weight: 800; color: var(--primary-red); }

    .btn-back {
        background-color: #eee; color: #333; border-radius: 50px;
        padding: 10px 25px; text-decoration: none; font-weight: 600;
        display: inline-block; transition: 0.2s;
    }
    .btn-back:hover { background-color: #ddd; color: #000; }

    .btn-cancel {
        background-color: #fff0f0; color: var(--primary-red);
        border: 1px solid var(--primary-red); border-radius: 50px;
        padding: 10px 25px; font-weight: 600; cursor: pointer; transition: 0.2s;
    }
    .btn-cancel:hover { background-color: var(--primary-red); color: #fff; }

    @media (max-width: 768px) {
        .info-grid { grid-template-columns: 1fr; }
        .step-text { font-size: 10px; }
    }
</style>

<div class="container detail-container">
    <div class="breadcrumb-area">
        <a href="../index.php">Trang chủ</a> / 
        <a href="profile.php">Tài khoản</a> / 
        <span>Chi tiết đơn hàng #<?php echo $order_id; ?></span>
    </div>

    <div class="info-section">
        <div class="order-header">
            <div>
                <div class="order-id-txt">ĐƠN HÀNG #<?php echo $order_id; ?></div>
                <div class="order-date-txt" id="orderDate">Ngày đặt: ...</div>
            </div>
            <div id="statusBadge">...</div>
        </div>

        <div class="status-timeline" id="timeline"></div>
    </div>

    <div class="info-grid">
        <div class="info-section p-0 border-0 bg-transparent">
            <div class="section-title"><i class="fas fa-map-marker-alt text-danger"></i> Địa chỉ nhận hàng</div>
            <div class="info-box">
                <div class="fw-bold mb-2" id="shipName">...</div>
                <div class="info-row"><i class="fas fa-phone-alt me-2 text-muted"></i><span id="shipPhone">...</span></div>
                <div class="info-row"><i class="fas fa-map me-2 text-muted"></i><span id="shipAddress" style="line-height: 1.4;">...</span></div>
            </div>
        </div>

        <div class="info-section p-0 border-0 bg-transparent">
            <div class="section-title"><i class="fas fa-wallet text-success"></i> Thanh toán & Vận chuyển</div>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Thanh toán:</span> 
                    <span id="paymentMethod" class="fw-bold">...</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span> 
                    <span id="paymentStatus">...</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Vận chuyển:</span> 
                    <span>Giao hàng tiêu chuẩn</span>
                </div>
            </div>
        </div>
    </div>

    <div class="product-list-card mt-3">
        <div class="section-title"><i class="fas fa-box me-2"></i>Sản phẩm</div>
        
        <div id="productList">
            <div class="text-center py-4 text-muted">
                <i class="fas fa-spinner fa-spin me-2"></i> Đang tải thông tin đơn hàng...
            </div>
        </div>

        <div class="summary-section">
            <div class="sum-row">
                <span>Tạm tính:</span> <span class="fw-bold" id="subTotal">0đ</span>
            </div>
            <div class="sum-row">
                <span>Phí vận chuyển:</span> <span class="fw-bold" id="shippingFee">0đ</span>
            </div>
            <hr style="border-top: 1px dashed #ccc;">
            <div class="sum-row">
                <span style="font-size: 16px;">Tổng thanh toán:</span> 
                <span class="total-price" id="grandTotal">0đ</span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4 align-items-center">
        <a href="profile.php" class="btn-back"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
        <button class="btn-cancel d-none" id="btnCancelOrder" onclick="cancelThisOrder()">
            <i class="fas fa-times me-2"></i>Hủy đơn hàng này
        </button>
    </div>

</div>

<script>
    const orderId = <?php echo $order_id; ?>;

    document.addEventListener("DOMContentLoaded", () => {
        loadOrderDetail();
    });

    function loadOrderDetail() {
        fetch(`api/order.php?action=detail&order_id=${orderId}`)
            .then(res => res.json())
            .then(response => {
                if(response.status === 'success') {
                    renderData(response.data);
                } else {
                    alert(response.message);
                    window.location.href = 'profile.php';
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('productList').innerHTML = '<div class="text-center text-danger">Lỗi kết nối tới hệ thống.</div>';
            });
    }

    function renderData(data) {
        const info = data.info;
        const items = data.items;
        const shipping = data.shipping;
        const payment = data.payment;

        document.getElementById('orderDate').innerText = 'Ngày đặt: ' + new Date(info.order_date).toLocaleString('vi-VN');

        document.getElementById('shipName').innerText = shipping.recipient_name;
        document.getElementById('shipPhone').innerText = shipping.shipping_phone;
        document.getElementById('shipAddress').innerText = shipping.shipping_address;
        
        let payMethodText = 'Không rõ';
        if (payment && payment.payment_method) {
            payMethodText = (payment.payment_method === 'cod' || payment.payment_method === 'cash') ? 'Tiền mặt (COD)' : 'Chuyển khoản / Online';
        }
        document.getElementById('paymentMethod').innerText = payMethodText;

        let payStatusText = 'Chưa thanh toán';
        let payClass = 'text-warning';
        if (payment && payment.payment_status === 'paid') {
            payStatusText = 'Đã thanh toán';
            payClass = 'text-success fw-bold';
        }
        document.getElementById('paymentStatus').innerHTML = `<span class="${payClass}">${payStatusText}</span>`;

        const listContainer = document.getElementById('productList');
        listContainer.innerHTML = '';
        
        let calculatedSubTotal = 0;

        items.forEach(item => {
            let price = parseFloat(item.price);
            let qty = parseInt(item.quantity);
            let rowTotal = price * qty;
            calculatedSubTotal += rowTotal;

            let imgSrc = 'https://via.placeholder.com/70?text=No+Img'; 
            if (item.image_url) {
                if (item.image_url.startsWith('http')) {
                    imgSrc = item.image_url;
                } else {
                    imgSrc = '../' + item.image_url; 
                }
            }

            let prodId = item.product_id || item.id; 
            let productUrl = `product_details.php?id=${prodId}`;

            let html = `
                <div class="item-row">
                    <a href="${productUrl}" class="img-link">
                        <img src="${imgSrc}" class="item-img" alt="${item.name}" onerror="this.src='https://via.placeholder.com/70?text=Error'">
                    </a>

                    <div class="item-details">
                        <a href="${productUrl}" class="name-link">
                            <span class="item-name">${item.name}</span>
                        </a>
                        
                        <div class="item-meta">Đơn giá: ${price.toLocaleString('vi-VN')}đ</div>
                        <div class="item-meta">Số lượng: x${qty}</div>
                    </div>
                    <div class="item-price text-end">
                        <div>${rowTotal.toLocaleString('vi-VN')}đ</div>
                    </div>
                </div>
            `;
            listContainer.innerHTML += html;
        });

        let grandTotal = parseFloat(info.total_amount);
        let shippingFee = grandTotal - calculatedSubTotal;
        if(shippingFee < 0) shippingFee = 0; 

        document.getElementById('subTotal').innerText = calculatedSubTotal.toLocaleString('vi-VN') + 'đ';
        document.getElementById('shippingFee').innerText = shippingFee.toLocaleString('vi-VN') + 'đ';
        document.getElementById('grandTotal').innerText = grandTotal.toLocaleString('vi-VN') + 'đ';

        renderTimeline(info.status);
    }

    function renderTimeline(status) {
        const timeline = document.getElementById('timeline');
        const badge = document.getElementById('statusBadge');
        const btnCancel = document.getElementById('btnCancelOrder');

        let badgeHtml = '';
        switch(status) {
            case 'pending': badgeHtml = '<span class="badge bg-warning text-dark">Chờ xử lý</span>'; break;
            case 'confirmed': badgeHtml = '<span class="badge bg-info">Đã xác nhận</span>'; break;
            case 'shipped': badgeHtml = '<span class="badge bg-primary">Đang giao</span>'; break;
            case 'delivered': badgeHtml = '<span class="badge bg-success">Hoàn thành</span>'; break;
            case 'cancelled': badgeHtml = '<span class="badge bg-danger">Đã hủy</span>'; break;
            default: badgeHtml = `<span class="badge bg-secondary">${status}</span>`;
        }
        badge.innerHTML = badgeHtml;

        if (status === 'cancelled') {
            timeline.innerHTML = `
                <div class="alert alert-danger w-100 text-center m-0">
                    <i class="fas fa-ban me-2"></i>Đơn hàng này đã bị hủy.
                </div>`;
            return;
        }

        const steps = [
            { id: 'pending', icon: 'fa-clipboard-list', label: 'Đặt hàng' },
            { id: 'confirmed', icon: 'fa-box-open', label: 'Xác nhận' },
            { id: 'shipped', icon: 'fa-shipping-fast', label: 'Đang giao' },
            { id: 'delivered', icon: 'fa-check-circle', label: 'Hoàn thành' }
        ];

        let activeIndex = -1;
        if(status === 'pending') activeIndex = 0;
        if(status === 'confirmed') activeIndex = 1;
        if(status === 'shipped') activeIndex = 2;
        if(status === 'delivered') activeIndex = 3;

        if(status === 'pending') {
            btnCancel.classList.remove('d-none');
        } else {
            btnCancel.classList.add('d-none');
        }

        let html = '';
        steps.forEach((step, index) => {
            let isActive = index <= activeIndex ? 'active' : '';
            html += `
                <div class="step ${isActive}">
                    <div class="step-icon"><i class="fas ${step.icon}"></i></div>
                    <div class="step-text">${step.label}</div>
                </div>
            `;
        });
        timeline.innerHTML = html;
    }

    function cancelThisOrder() {
        if(!confirm('Bạn chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.')) return;
        
        fetch('api/order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 
                action: 'cancel', 
                order_id: orderId 
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi kết nối tới máy chủ.');
        });
    }
</script>

<?php include '../includes/footer.php'; ?>