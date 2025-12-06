let mockData = typeof phpData !== 'undefined' ? phpData : {};

const app = document.getElementById('app');
const pageTitle = document.getElementById('page-title');
const modalOverlay = document.getElementById('modal-overlay');
const modalBody = document.getElementById('modal-body');
const modalTitle = document.getElementById('modal-title');
const modalActionBtn = document.getElementById('modal-action-btn');

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function navigate(page) {
    document.querySelectorAll('.menu a').forEach(el => el.classList.remove('active'));
    document.getElementById(`nav-${page}`).classList.add('active');

    switch(page) {
        case 'dashboard': renderDashboard(); break;
        case 'products': renderProducts(); break;
        case 'orders': renderOrders(); break;
        case 'customers': renderCustomers(); break;
        case 'users': renderUsers(); break;
        case 'settings': renderSettings(); break;
    }
}

function renderDashboard() {
    pageTitle.textContent = "Tổng Quan";
    const totalRev = mockData.orders.reduce((sum, order) => sum + (order.status !== 'cancelled' ? order.total : 0), 0);
    const newOrders = mockData.orders.filter(o => o.status === 'pending').length;

    app.innerHTML = `
        <div class="stats-grid">
            <div class="card">
                <div class="card-info">
                    <h3>${newOrders}</h3>
                    <p>Đơn chờ xử lý</p>
                </div>
                <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>${formatCurrency(totalRev)}</h3>
                    <p>Doanh thu</p>
                </div>
                <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>${mockData.products.length}</h3>
                    <p>Sản phẩm</p>
                </div>
                <div class="card-icon"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>${mockData.customers.length}</h3>
                    <p>Khách hàng</p>
                </div>
                <div class="card-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-header">
                <h3>Đơn hàng mới nhất</h3>
                <button class="btn btn-primary btn-sm" onclick="navigate('orders')">Xem tất cả</button>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Khách hàng</th>
                            <th>Ngày</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mockData.orders.slice(0, 5).map(order => `
                            <tr>
                                <td>${order.id}</td>
                                <td>${order.customer}</td>
                                <td>${order.date}</td>
                                <td>${formatCurrency(order.total)}</td>
                                <td><span class="status ${order.status}">${getStatusText(order.status)}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderProducts() {
    pageTitle.textContent = "Quản Lý Sản Phẩm";
    app.innerHTML = `
        <div class="panel">
            <div class="panel-header">
                <h3>Danh sách sản phẩm</h3>
                <button class="btn btn-primary" onclick="openProductModal()"><i class="fas fa-plus"></i> Thêm mới</button>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mockData.products.map(p => `
                            <tr>
                                <td>${p.id}</td>
                                <td>${p.name}</td>
                                <td>${p.category}</td>
                                <td>${formatCurrency(p.price)}</td>
                                <td>${p.stock}</td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick="alert('Chức năng sửa ID: ${p.id}')"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteProduct(${p.id})"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderOrders() {
    pageTitle.textContent = "Quản Lý Đơn Hàng";
    app.innerHTML = `
        <div class="panel">
            <div class="panel-header">
                <h3>Danh sách đơn hàng</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Ngày Đặt</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mockData.orders.map(o => `
                            <tr>
                                <td>${o.id}</td>
                                <td>${o.customer}</td>
                                <td>${o.date}</td>
                                <td>${formatCurrency(o.total)}</td>
                                <td><span class="status ${o.status}">${getStatusText(o.status)}</span></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="viewOrder('${o.id}')"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderCustomers() {
    pageTitle.textContent = "Danh Sách Khách Hàng";
    app.innerHTML = `
        <div class="panel">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Đơn hàng</th>
                            <th>Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mockData.customers.map(c => `
                            <tr>
                                <td>${c.id}</td>
                                <td>${c.name}</td>
                                <td>${c.email}</td>
                                <td>${c.phone}</td>
                                <td>${c.orders}</td>
                                <td>${formatCurrency(c.spent)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderUsers() {
    pageTitle.textContent = "Quản Lý Người Dùng";
    app.innerHTML = `
        <div class="panel">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Tên đăng nhập</th>
                            <th>Vai trò</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mockData.users.map(u => `
                            <tr>
                                <td>${u.id}</td>
                                <td>${u.name}</td>
                                <td>${u.email}</td>
                                <td>${u.username}</td>
                                <td>${u.role}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;    
}

function renderSettings() {
    pageTitle.textContent = "Cài Đặt Hệ Thống";
    app.innerHTML = `
        <div class="panel" style="max-width: 600px">
            <div class="form-group">
                <label>Tên cửa hàng</label>
                <input type="text" class="form-control" value="My Tech Shop">
            </div>
            <div class="form-group">
                <label>Email liên hệ</label>
                <input type="email" class="form-control" value="admin@techshop.com">
            </div>
            <div class="form-group">
                <label>Tiền tệ mặc định</label>
                <select class="form-control">
                    <option value="VND">VND (đ)</option>
                    <option value="USD">USD ($)</option>
                </select>
            </div>
            <button class="btn btn-primary" onclick="alert('Đã lưu cài đặt')">Lưu Thay Đổi</button>
        </div>
    `;
}

function getStatusText(status) {
    const map = {
        'pending': 'Chờ xử lý',
        'shipping': 'Đang giao',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy'
    };
    return map[status] || status;
}

function deleteProduct(id) {
    if(confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        mockData.products = mockData.products.filter(p => p.id !== id);
        renderProducts();
    }
}

function openProductModal() {
    modalTitle.textContent = "Thêm Sản Phẩm Mới";
    modalBody.innerHTML = `
        <div class="form-group">
            <label>Tên sản phẩm</label>
            <input type="text" id="p-name" class="form-control">
        </div>
        <div class="form-group">
            <label>Giá bán</label>
            <input type="number" id="p-price" class="form-control">
        </div>
        <div class="form-group">
            <label>Danh mục</label>
            <select id="p-cat" class="form-control">
                <option>Laptop</option>
                <option>Điện thoại</option>
                <option>Phụ kiện</option>
            </select>
        </div>
    `;
    modalActionBtn.onclick = () => {
        const name = document.getElementById('p-name').value;
        const price = document.getElementById('p-price').value;
        const cat = document.getElementById('p-cat').value;
        if(name && price) {
            mockData.products.push({
                id: mockData.products.length + 1,
                name: name,
                category: cat,
                price: parseInt(price),
                stock: 0
            });
            closeModal();
            renderProducts();
        }
    };
    modalOverlay.classList.add('active');
}

function viewOrder(id) {
    const order = mockData.orders.find(o => o.id === id);
    modalTitle.textContent = `Chi tiết đơn hàng ${id}`;
    modalBody.innerHTML = `
        <p><strong>Khách hàng:</strong> ${order.customer}</p>
        <p><strong>Ngày đặt:</strong> ${order.date}</p>
        <p><strong>Trạng thái:</strong> <span class="status ${order.status}">${getStatusText(order.status)}</span></p>
        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
        <h4>Sản phẩm:</h4>
        <ul style="list-style: none; margin-top: 10px;">
            ${order.items.map(i => `<li style="display:flex; justify-content:space-between; padding: 5px 0"><span>${i.name} x${i.qty}</span></li>`).join('')}
        </ul>
        <div style="margin-top: 15px; text-align: right; font-weight: bold; font-size: 18px;">
            Tổng tiền: ${formatCurrency(order.total)}
        </div>
    `;
    modalActionBtn.textContent = "Đóng";
    modalActionBtn.onclick = closeModal;
    modalOverlay.classList.add('active');
}

function closeModal() {
    modalOverlay.classList.remove('active');
}

window.onload = () => renderDashboard();