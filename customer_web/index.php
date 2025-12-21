<?php
session_start();
include '../includes/header.php'; 
?>

<style>
    :root {
        --primary-color: #ff6b6b;
        --secondary-color: #333;
        --bg-light: #f8f9fa;
        --border-color: #eee;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
        --shadow-md: 0 5px 15px rgba(0,0,0,0.1);
        --transition: all 0.3s ease;
    }

    body {
        background-color: var(--bg-light);
        color: #444;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    .hero-banner {
        background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1351&q=80');
        background-size: cover;
        background-position: center;
        height: 350px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        margin-bottom: 40px;
        border-radius: 0 0 20px 20px;
        box-shadow: var(--shadow-md);
    }

    .hero-content h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .hero-content p {
        font-size: 1.2rem;
        margin-bottom: 25px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .page-content {
        display: flex;
        gap: 40px;
        padding-bottom: 50px;
    }

    .filters-sidebar {
        width: 260px;
        flex-shrink: 0;
        background: white;
        padding: 25px;
        border-radius: 12px;
        height: fit-content;
        box-shadow: var(--shadow-sm);
    }

    .filter-group {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .filter-group:last-child { border-bottom: none; }

    .filter-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-body ul { list-style: none; padding: 0; margin: 0; }
    .filter-body li { margin-bottom: 10px; }

    .filter-body label {
        cursor: pointer;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: color 0.2s;
    }

    .filter-body label:hover { color: var(--primary-color); }
    .filter-body input[type="radio"] { accent-color: var(--primary-color); transform: scale(1.1); }

    .product-listing { flex-grow: 1; }

    .sort-bar {
        display: flex;
        justify-content: flex-end;
        gap: 20px;
        margin-bottom: 25px;
        background: white;
        padding: 15px 25px;
        border-radius: 50px;
        box-shadow: var(--shadow-sm);
        align-items: center;
    }

    .sort-option {
        cursor: pointer;
        font-weight: 500;
        color: #888;
        transition: var(--transition);
        padding: 5px 10px;
        border-radius: 20px;
    }

    .sort-option:hover, .sort-option.active {
        color: white;
        background-color: var(--primary-color);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 25px;
    }

    .product-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        transition: var(--transition);
        border: 1px solid transparent;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
        border-color: #ffeaea;
    }

    .product-card a { text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%; }

    .product-image {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
        background-color: white;
    }

    .product-image img {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: contain;
        padding: 20px;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img { transform: scale(1.05); }

    .product-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .product-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 10px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        margin-top: auto;
        margin-bottom: 15px;
    }

    .card-actions {
        padding: 0 20px 20px;
        margin-top: auto;
    }

    .btn-add-cart {
        width: 100%;
        background-color: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        padding: 8px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-add-cart:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    .btn-disabled {
        background-color: #e9ecef;
        color: #6c757d;
        border: 2px solid #e9ecef;
        cursor: not-allowed;
    }

    .pagination { justify-content: center; margin-top: 40px; }
    .page-link { color: var(--secondary-color); border: none; margin: 0 5px; border-radius: 5px !important; }
    .page-item.active .page-link { background-color: var(--primary-color); border-color: var(--primary-color); }
    .page-link:hover { color: var(--primary-color); background-color: #ffeaea; }

    @media (max-width: 991px) {
        .page-content { flex-direction: column; }
        .filters-sidebar { width: 100%; margin-bottom: 20px; }
        .hero-banner { height: 250px; }
        .hero-content h1 { font-size: 2rem; }
    }
</style>

<div class="hero-banner">
    <div class="hero-content">
        <h1>Vẻ Đẹp Tự Nhiên</h1>
        <p>Khám phá bộ sưu tập mỹ phẩm cao cấp mới nhất 2025</p>
    </div>
</div>

<main class="container">
    <div class="page-content">
        <aside class="filters-sidebar">
            <div class="filter-group">
                <div class="filter-header">
                    <h3><i class="fas fa-list-ul me-2"></i> Danh mục</h3>
                </div>
                <div class="filter-body">
                    <ul id="category-list-ul">
                        <li><i class="fas fa-spinner fa-spin text-muted"></i> Đang tải...</li>
                    </ul>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-header">
                    <h3><i class="fas fa-filter me-2"></i> Lọc theo giá</h3>
                </div>
                <div class="filter-body">
                    <div class="d-flex gap-2 align-items-center mb-3">
                        <input type="number" class="form-control form-control-sm" placeholder="Min">
                        <span>-</span>
                        <input type="number" class="form-control form-control-sm" placeholder="Max">
                    </div>
                    <button class="btn btn-dark btn-sm w-100" disabled>Áp dụng</button>
                </div>
            </div>
        </aside>

        <section class="product-listing">
            <div class="sort-bar">
                <span class="text-muted me-2">Sắp xếp:</span>
                <span class="sort-option active" data-sort="newest" onclick="changeSort(this)">Mới nhất</span>
                <span class="sort-option" data-sort="price_asc" onclick="changeSort(this)">Giá tăng dần</span>
                <span class="sort-option" data-sort="price_desc" onclick="changeSort(this)">Giá giảm dần</span>
            </div>

            <div class="product-grid" id="product-container">
                <div class="text-center w-100 p-5" style="grid-column: 1 / -1;">
                    <i class="fas fa-spinner fa-spin fa-3x text-secondary"></i>
                    <p class="mt-3 text-muted">Đang tải sản phẩm xịn sò...</p>
                </div>
            </div>
            
            <div class="mt-4" id="pagination"></div>
        </section>
    </div>
</main>

<script>
let currentSort = 'newest';
let currentCategory = 0;
let currentKeyword = '';

document.addEventListener("DOMContentLoaded", function() {
    loadCategories();
    loadProducts();

    const searchForm = document.querySelector('.search-form');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = searchForm.querySelector('input[name="keyword"]');
            currentKeyword = input ? input.value : '';
            loadProducts(1);
        });
    }
});

function changeSort(element) {
    document.querySelectorAll('.sort-option').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
    currentSort = element.getAttribute('data-sort');
    loadProducts(1);
}

function changeCategory(catId) {
    currentCategory = catId;
    loadProducts(1);
}

function loadCategories() {
    fetch('api/product.php?action=categories')
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                const list = document.getElementById('category-list-ul');
                let html = `<li>
                                <label>
                                    <input type="radio" name="category_filter" value="0" checked onchange="changeCategory(0)"> 
                                    <span class="ms-2">Tất cả sản phẩm</span>
                                </label>
                            </li>`;
                
                res.data.forEach(cat => {
                    html += `
                        <li>
                            <label>
                                <input type="radio" name="category_filter" value="${cat.category_id}" onchange="changeCategory(${cat.category_id})"> 
                                <span class="ms-2">${cat.name}</span>
                            </label>
                        </li>
                    `;
                });
                list.innerHTML = html;
            }
        })
        .catch(err => console.error("Lỗi:", err));
}

function loadProducts(page = 1) {
    const container = document.getElementById('product-container');
    container.innerHTML = '<div class="text-center w-100 p-5" style="grid-column: 1/-1"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';
    
    const url = `api/product.php?page=${page}&limit=9&category_id=${currentCategory}&keyword=${encodeURIComponent(currentKeyword)}&sort=${currentSort}`;

    fetch(url) 
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                renderProducts(data.data);
                renderPagination(data.pagination);
            } else {
                container.innerHTML = '<div class="text-center text-danger w-100" style="grid-column: 1/-1">Không tải được dữ liệu.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="text-center text-danger w-100" style="grid-column: 1/-1">Lỗi kết nối server.</div>';
        });
}

function renderProducts(products) {
    const container = document.getElementById('product-container');
    let html = '';

    if (!products || products.length === 0) {
        container.innerHTML = '<div class="text-center py-5 w-100" style="grid-column: 1/-1"><h5>Không tìm thấy sản phẩm nào phù hợp.</h5></div>';
        return;
    }

    products.forEach(p => {
        const price = parseInt(p.price).toLocaleString('vi-VN');
        
        let imgSrc = 'https://via.placeholder.com/300x300?text=No+Image';
        if(p.image_url) {
            if(p.image_url.startsWith('http')) {
                imgSrc = p.image_url;
            } else {
                imgSrc = '../' + p.image_url; 
            }
        }

        const categoryName = p.category_name ? p.category_name : 'Mỹ phẩm';

        let btnHtml;
        let badgeHtml = '';

        if (p.stock > 0) {
            btnHtml = `<button class="btn btn-add-cart" onclick="addToCart(event, ${p.product_id})">
                            <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                       </button>`;
        } else {
            badgeHtml = `<div style="position:absolute; top:10px; right:10px; background: #6c757d; color:white; padding: 2px 8px; border-radius: 4px; font-size: 12px; z-index:2;">Hết hàng</div>`;
            btnHtml = `<button class="btn btn-add-cart btn-disabled" disabled>Hết hàng</button>`;
        }

        html += `
            <div class="product-card">
                ${badgeHtml}
                <a href="product_details.php?id=${p.product_id}">
                    <div class="product-image">
                        <img src="${imgSrc}" alt="${p.name}" loading="lazy">
                    </div>
                    <div class="product-info">
                        <div class="product-category">${categoryName}</div>
                        <h3 class="product-name" title="${p.name}">${p.name}</h3>
                        <div class="product-price">${price}₫</div>
                    </div>
                </a>
                <div class="card-actions">
                    ${btnHtml}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function renderPagination(paging) {
    const div = document.getElementById('pagination');
    if(!paging || paging.total_pages <= 1) {
        div.innerHTML = '';
        return;
    }

    let html = '<nav><ul class="pagination">';
    
    html += `<li class="page-item ${paging.current_page == 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="loadProducts(${paging.current_page - 1})"><i class="fas fa-chevron-left"></i></button>
             </li>`;

    for (let i = 1; i <= paging.total_pages; i++) {
        const active = i == paging.current_page ? 'active' : '';
        html += `<li class="page-item ${active}">
                    <button class="page-link" onclick="loadProducts(${i})">${i}</button>
                 </li>`;
    }

    html += `<li class="page-item ${paging.current_page == paging.total_pages ? 'disabled' : ''}">
                <button class="page-link" onclick="loadProducts(${paging.current_page + 1})"><i class="fas fa-chevron-right"></i></button>
             </li>`;

    html += '</ul></nav>';
    div.innerHTML = html;
}


function addToCart(event, productId) {
    event.preventDefault(); 
    event.stopPropagation();

    let cart = JSON.parse(localStorage.getItem('cart_items') || '[]');
    let found = cart.find(item => item.product_id === productId);
    
    if(found) {
        found.quantity++;
    } else {
        cart.push({ product_id: productId, quantity: 1 });
    }

    localStorage.setItem('cart_items', JSON.stringify(cart));

    if (typeof updateHeaderCartCount === "function") {
        updateHeaderCartCount();
    }


    const btn = event.target.closest('button');
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-check"></i> Đã thêm';
    btn.style.backgroundColor = '#28a745'; 
    btn.style.color = 'white';
    btn.style.borderColor = '#28a745';
    
    setTimeout(() => {
        btn.innerHTML = originalContent;
        btn.style.backgroundColor = '';
        btn.style.color = '';
        btn.style.borderColor = '';
    }, 1500);
}
</script>

<?php 
include '../includes/footer.php'; 
?>