<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$is_logged_in = $user_id > 0 ? 'true' : 'false';

include '../includes/header.php'; 
?>

<style>
    :root {
        --theme-pink: #f4aeb9; 
        --theme-pink-dark: #e89aa7;
        --theme-text: #333;
        --bg-gray: #f5f5f5;
        --border-color: #e5e5e5;
    }

    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: #fff;
        color: var(--theme-text);
    }

    .container-detail {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 15px;
    }

    .breadcrumb {
        background: transparent;
        font-size: 13px;
        color: #888;
        padding: 0;
        margin-bottom: 20px;
    }
    .breadcrumb a { color: #888; text-decoration: none; }
    .breadcrumb span { margin: 0 5px; }

    .product-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
    }

    .gallery-container {
        flex: 0 0 45%;
        max-width: 45%;
        display: flex;
        gap: 10px;
    }
    
    .thumb-list-vertical {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 70px; 
    }
    .thumb-item {
        width: 70px;
        height: 70px;
        border: 1px solid transparent;
        cursor: pointer;
        opacity: 0.7;
        transition: 0.2s;
    }
    .thumb-item img {
        width: 100%; height: 100%; object-fit: contain; border: 1px solid #eee;
    }
    .thumb-item.active {
        border-color: var(--theme-text);
        opacity: 1;
    }
    .thumb-item:hover { opacity: 1; }

    .main-image-box {
        flex-grow: 1;
        height: 450px; 
        border: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .main-image-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .info-container {
        flex: 0 0 50%;
        max-width: 50%;
    }

    .product-title {
        font-size: 22px;
        font-weight: 500;
        line-height: 1.3;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
    }

    .variant-label { font-size: 13px; font-weight: bold; margin-bottom: 8px; display: block; }
    .variant-options { display: flex; gap: 10px; margin-bottom: 25px; }
    .variant-btn {
        padding: 5px 15px;
        border: 1px solid #ddd;
        background: #fff;
        font-size: 13px;
        cursor: pointer;
    }
    .variant-btn.selected { border: 1px solid #333; background: #f9f9f9; }

    .action-group {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }
    .btn-custom {
        padding: 12px 0;
        width: 180px;
        text-transform: uppercase;
        font-weight: 600;
        font-size: 13px;
        border: 1px solid #333;
        cursor: pointer;
        text-align: center;
        transition: 0.2s;
    }
    
    .btn-add-cart {
        background: #fff;
        color: #333;
    }
    .btn-add-cart:hover { background: #f0f0f0; }

    .btn-buy-now {
        background: #fce8ea; 
        color: #d05866;
        border-color: #fce8ea;
    }
    .btn-buy-now:hover {
        background: #fadce0;
    }

    .btn-disabled {
        background: #eee; color: #999; border-color: #eee; cursor: not-allowed;
    }

    .info-accordion {
        border-top: 1px solid #eee;
    }
    .accordion-item {
        border-bottom: 1px solid #eee;
    }
    .accordion-header {
        padding: 15px 0;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        cursor: pointer;
    }
    .accordion-content {
        padding-bottom: 15px;
        font-size: 13px;
        color: #666;
        display: none; 
    }

    .detail-text-section {
        margin-top: 50px;
        max-width: 800px;
    }
    .detail-heading { font-weight: bold; font-size: 16px; margin-bottom: 15px; text-transform: uppercase; }
    .detail-content { font-size: 14px; line-height: 1.6; color: #444; }

    .review-section { margin-top: 50px; padding-top: 30px; border-top: 1px solid #eee; }
    .star-input i { color: #ddd; cursor: pointer; font-size: 20px; }
    .star-input i.active { color: #ffc107; }

    @media (max-width: 768px) {
        .gallery-container, .info-container { flex: 0 0 100%; max-width: 100%; }
        .gallery-container { flex-direction: column-reverse; } 
        .thumb-list-vertical { flex-direction: row; width: 100%; overflow-x: auto; }
        .thumb-item { width: 60px; height: 60px; flex-shrink: 0; }
    }
</style>

<div class="container-detail">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> <span>/</span> 
        <a href="#" id="breadCategory">Sản phẩm</a> <span>/</span> 
        <span id="breadName">...</span>
    </div>

    <div id="loadingBox" class="text-center py-5">
        <div class="spinner-border text-secondary" role="status"></div>
        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
    </div>

    <div class="product-layout d-none" id="productContent">
        
        <div class="gallery-container">
            <div class="thumb-list-vertical" id="galleryThumbs">
                </div>
            <div class="main-image-box">
                <img id="mainImage" src="" alt="Main Product Image">
            </div>
        </div>

        <div class="info-container">
            <h1 class="product-title" id="productName">Tên sản phẩm</h1>
            <div class="product-price" id="productPrice">0đ</div>

            <div class="mb-4">
                <span class="variant-label">Dung tích</span>
                <div class="variant-options">
                    <button class="variant-btn selected">Full size</button>
                </div>
            </div>

            <div class="mb-3 d-flex align-items-center">
                <span class="me-3 font-weight-bold" style="font-size:13px;">Số lượng:</span>
                <input type="number" id="qtyInput" value="1" min="1" class="form-control text-center" style="width: 60px; height: 35px;">
                <span id="stockStatus" class="ms-3 text-muted" style="font-size:12px;"></span>
            </div>

            <div class="action-group" id="actionButtons">
                </div>

            <div class="info-accordion">
                <div class="accordion-item">
                    <div class="accordion-header">
                        Đơn vị vận chuyển <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-content" style="display:block;">
                        <p class="mb-1"><i class="fas fa-truck text-muted me-2"></i> Giao hàng tiêu chuẩn (2-4 ngày)</p>
                        <p class="mb-0"><i class="fas fa-box text-muted me-2"></i> Đổi trả miễn phí trong 7 ngày</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        Thông tin bảo hành <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-text-section d-none" id="detailSection">
        <div class="detail-heading">Chi tiết sản phẩm</div>
        <div class="detail-content" id="productDesc">
            </div>
    </div>

    <div class="review-section d-none" id="reviewSection">
        <div class="detail-heading">Đánh giá sản phẩm</div>
        
        <div id="reviewList">
            </div>

        <?php if($is_logged_in == 'true'): ?>
        <div class="mt-4 p-3 bg-light rounded">
            <h6>Viết đánh giá của bạn</h6>
            <div class="mb-2 star-input">
                <i class="fas fa-star active" onclick="rate(1)"></i>
                <i class="fas fa-star active" onclick="rate(2)"></i>
                <i class="fas fa-star active" onclick="rate(3)"></i>
                <i class="fas fa-star active" onclick="rate(4)"></i>
                <i class="fas fa-star active" onclick="rate(5)"></i>
                <input type="hidden" id="ratingValue" value="5">
            </div>
            <textarea id="reviewMsg" class="form-control mb-2" rows="3" placeholder="Nhập nội dung đánh giá..."></textarea>
            <button class="btn btn-dark btn-sm" onclick="postReview()">Gửi đánh giá</button>
        </div>
        <?php else: ?>
            <p class="mt-3"><a href="login.php">Đăng nhập</a> để viết đánh giá.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    let currentStock = 0;
    let productData = null;

    document.addEventListener("DOMContentLoaded", () => {
        if(!productId) {
            alert("Sản phẩm không tồn tại!");
            return;
        }
        fetchProduct();
        fetchReviews();
   
        updateHeaderCartDisplay();
    });

    function fetchProduct() {
        fetch(`api/product.php?action=detail&id=${productId}`)
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                productData = res.data;
                renderProduct(res.data);
                document.getElementById('loadingBox').classList.add('d-none');
                document.getElementById('productContent').classList.remove('d-none');
                document.getElementById('productContent').classList.add('d-flex');
                document.getElementById('detailSection').classList.remove('d-none');
                document.getElementById('reviewSection').classList.remove('d-none');
            } else {
                document.getElementById('loadingBox').innerHTML = `<h4 class="text-danger">${res.message}</h4>`;
            }
        })
        .catch(err => console.error(err));
    }

    function renderProduct(p) {
        document.getElementById('breadCategory').innerText = p.category_name;
        document.getElementById('breadName').innerText = p.name;
        document.getElementById('productName').innerText = p.name;
        document.getElementById('productPrice').innerText = parseInt(p.price).toLocaleString('vi-VN') + 'đ';
        document.getElementById('productDesc').innerHTML = p.description ? p.description.replace(/\n/g, '<br>') : 'Đang cập nhật...';
        
        currentStock = p.stock;
        document.getElementById('stockStatus').innerText = `(Còn ${p.stock} sản phẩm)`;

        const mainImgSrc = p.image_url.startsWith('http') ? p.image_url : '../' + p.image_url;
        document.getElementById('mainImage').src = mainImgSrc;

        let galleryHtml = '';
        galleryHtml += `<div class="thumb-item active" onclick="changeImg('${mainImgSrc}', this)">
                            <img src="${mainImgSrc}">
                        </div>`;
        
        if(p.gallery && p.gallery.length > 0) {
            p.gallery.forEach(img => {
                let src = img.image_url.startsWith('http') ? img.image_url : '../' + img.image_url;
                galleryHtml += `<div class="thumb-item" onclick="changeImg('${src}', this)">
                                    <img src="${src}">
                                </div>`;
            });
        }
        document.getElementById('galleryThumbs').innerHTML = galleryHtml;

        const actionDiv = document.getElementById('actionButtons');
        if(p.stock > 0) {
            actionDiv.innerHTML = `
                <button class="btn-custom btn-add-cart" onclick="addToCart(false)">
                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                </button>
                <button class="btn-custom btn-buy-now" onclick="addToCart(true)">
                    Mua ngay
                </button>
            `;
        } else {
            actionDiv.innerHTML = `<button class="btn-custom btn-disabled" disabled>Hết hàng</button>`;
        }
    }

    function changeImg(src, el) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
    }

    function addToCart(isBuyNow) {
        if(!productData) return;
        
        const qty = parseInt(document.getElementById('qtyInput').value);
        if(qty > currentStock) {
            alert('Số lượng vượt quá tồn kho!');
            return;
        }

        let cart = JSON.parse(localStorage.getItem('shopping_cart')) || [];

        let existing = cart.find(i => i.product_id == productData.product_id);

        if(existing) {
            existing.quantity += qty;
        } else {
            cart.push({ 
                product_id: productData.product_id, 
                name: productData.name,
                price: productData.price,
                image: productData.image_url,
                quantity: qty 
            });
        }

        localStorage.setItem('shopping_cart', JSON.stringify(cart));

        updateHeaderCartDisplay(); 
        window.dispatchEvent(new Event('cartUpdated')); 
        window.dispatchEvent(new Event('storage')); 
    
        if(isBuyNow) {
            window.location.href = 'checkout.php';
        } else {
            alert('Đã thêm vào giỏ hàng thành công!');
        }
    }

    function updateHeaderCartDisplay() {
        let cart = JSON.parse(localStorage.getItem('shopping_cart')) || [];
        let totalQty = cart.reduce((acc, item) => acc + item.quantity, 0);
        

        const badgeElements = document.querySelectorAll('.cart-count, #cart-count, .badge-cart, #cartQty');
        
        badgeElements.forEach(el => {
            el.innerText = totalQty;
            el.style.display = totalQty > 0 ? 'inline-block' : 'none';
        });
    }

    function fetchReviews() {
        fetch(`api/review.php?product_id=${productId}`)
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                let html = '';
                if(res.data.reviews.length === 0) {
                    html = '<p class="text-muted fst-italic">Chưa có đánh giá nào.</p>';
                } else {
                    res.data.reviews.forEach(r => {
                        let stars = '';
                        for(let i=1; i<=5; i++) stars += i<=r.rating ? '<i class="fas fa-star text-warning" style="font-size:12px"></i>' : '<i class="far fa-star text-muted" style="font-size:12px"></i>';
                        html += `
                            <div class="border-bottom py-3">
                                <div class="fw-bold" style="font-size:14px;">${r.user_name} <span class="fw-normal text-muted ms-2" style="font-size:12px;">${new Date(r.review_date).toLocaleDateString()}</span></div>
                                <div class="mb-1">${stars}</div>
                                <div style="font-size:14px;">${r.comment}</div>
                            </div>
                        `;
                    });
                }
                document.getElementById('reviewList').innerHTML = html;
            }
        })
        .catch(console.error);
    }

    function rate(val) {
        document.getElementById('ratingValue').value = val;
        document.querySelectorAll('.star-input i').forEach((el, idx) => {
            if(idx < val) { el.classList.remove('far'); el.classList.add('fas', 'active'); }
            else { el.classList.remove('fas', 'active'); el.classList.add('far'); }
        });
    }

    function postReview() {
        const rating = document.getElementById('ratingValue').value;
        const comment = document.getElementById('reviewMsg').value;
        if(!comment) return alert('Vui lòng nhập nội dung');

        fetch('api/review.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ product_id: productId, rating: rating, comment: comment })
        })
        .then(res => res.json())
        .then(res => {
            alert(res.message);
            if(res.status === 'success') {
                document.getElementById('reviewMsg').value = '';
                fetchReviews();
            }
        });
    }
</script>

<?php include '../includes/footer.php'; ?>