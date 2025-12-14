let mockData = typeof phpData !== 'undefined' ? phpData : {};
let currentProducts = []; 
let categories = [];      

const app = document.getElementById('app');
const pageTitle = document.getElementById('page-title');
const modalOverlay = document.getElementById('modal-overlay');
const modalBody = document.getElementById('modal-body');
const modalTitle = document.getElementById('modal-title');
const modalActionBtn = document.getElementById('modal-action-btn');

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function getImageUrl(url) {
    if (!url) return 'https://via.placeholder.com/50';
    if (url.startsWith('http')) return url;
    let cleanUrl = url.replace(/^(\.\.\/|\/)+/, '');
    return '../' + cleanUrl; 
}

function navigate(page) {
    document.querySelectorAll('.menu a').forEach(el => el.classList.remove('active'));
    const navItem = document.getElementById(`nav-${page}`);
    if(navItem) navItem.classList.add('active');

    switch(page) {
        case 'dashboard': renderDashboard(); break;
        case 'products': renderProducts(); break;
        case 'orders': renderOrders(); break;
        case 'customers': renderCustomers(); break;
        case 'users': renderUsers(); break;
        case 'settings': renderSettings(); break;
        case 'inventory': renderInventory(); break;
    }
}

function renderDashboard() {
    pageTitle.textContent = "Tổng Quan";
    const orders = mockData.orders || [];
    const products = mockData.products || [];
    const customers = mockData.customers || [];

    const totalRev = orders.reduce((sum, order) => sum + (order.status !== 'cancelled' ? order.total : 0), 0);
    const newOrders = orders.filter(o => o.status === 'pending').length;

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
                    <h3>${products.length}</h3>
                    <p>Sản phẩm</p>
                </div>
                <div class="card-icon"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>${customers.length}</h3>
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
                        ${orders.slice(0, 5).map(order => `
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
    
    fetch('api/product.php')
        .then(res => res.json())
        .then(resData => {
            if(resData.status === 'success') {
                currentProducts = resData.data; 
                displayProductTable(currentProducts);
            } else {
                alert('Lỗi tải sản phẩm: ' + resData.message);
            }
        })
        .catch(err => console.error(err));
}

function displayProductTable(products) {
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
                            <th>Hình</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${products.map(p => `
                            <tr>
                                <td>${p.product_id}</td>
                                <td>
                                    <img src="${getImageUrl(p.image_url)}" width="50" height="50" style="object-fit:cover; border-radius:4px; border: 1px solid #ddd;">
                                </td>
                                <td>${p.name}</td>
                                <td>${p.category_name || '-'}</td>
                                <td>${formatCurrency(p.price)}</td>
                                <td>${p.stock}</td>
                                <td><span class="status ${p.status === 'active' ? 'completed' : 'cancelled'}">${p.status === 'active' ? 'Đang bán' : 'Ngừng bán'}</span></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" title="Sửa" onclick="editProduct(${p.product_id})"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" title="Xóa" onclick="deleteProduct(${p.product_id})"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function editProduct(id) {
    const product = currentProducts.find(p => p.product_id == id);
    if (product) {
        openProductModal(product); 
    } else {
        alert("Không tìm thấy dữ liệu sản phẩm này!");
    }
}

function openProductModal(product = null) {
    const isEdit = product !== null;
    modalTitle.textContent = isEdit ? "Cập Nhật Sản Phẩm" : "Thêm Sản Phẩm Mới";
    modalActionBtn.style.display = 'inline-block'; 

    let catOptions = `<option value="">-- Chọn danh mục --</option>`;
    categories.forEach(c => {
        const selected = (product && product.category_id == c.category_id) ? 'selected' : '';
        catOptions += `<option value="${c.category_id}" ${selected}>${c.name}</option>`;
    });

    modalBody.innerHTML = `
        <div class="form-group">
            <label>Tên sản phẩm (*)</label>
            <input type="text" id="p-name" class="form-control" value="${product ? product.name : ''}">
        </div>
        
        <div class="form-group">
            <label>Danh mục (*)</label>
            <select id="p-cat" class="form-control">
                ${catOptions}
            </select>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label>Giá bán (*)</label>
                <input type="number" id="p-price" class="form-control" value="${product ? product.price : ''}">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Giảm giá (Số tiền)</label>
                <input type="number" id="p-discount" class="form-control" value="${product ? product.discount : '0'}">
            </div>
        </div>

        <div style="display: flex; gap: 15px;">
             <div class="form-group" style="flex: 1;">
                <label>Số lượng</label>
                <input type="number" id="p-stock" class="form-control" value="${product ? product.stock : '0'}">
            </div>
             <div class="form-group" style="flex: 1;">
                <label>Trạng thái</label>
                <select id="p-status" class="form-control">
                    <option value="active" ${product && product.status === 'active' ? 'selected' : ''}>Đang bán (Active)</option>
                    <option value="inactive" ${product && product.status === 'inactive' ? 'selected' : ''}>Ngừng kinh doanh (Inactive)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Mô tả chi tiết</label>
            <textarea id="p-desc" class="form-control" rows="3">${product ? product.description : ''}</textarea>
        </div>

        <div class="form-group">
            <label>Link Hình ảnh (Nhập: uploads/ten_anh.jpg)</label>
            <input type="text" id="p-img" class="form-control" value="${product ? product.image_url : ''}" placeholder="uploads/...">
            ${product && product.image_url ? 
                `<br><img src="${getImageUrl(product.image_url)}" height="60" style="border:1px solid #ccc"> <small>Ảnh hiện tại</small>` 
                : ''}
        </div>
    `;

    modalActionBtn.textContent = isEdit ? "Cập nhật" : "Tạo mới";
    modalActionBtn.onclick = () => saveProduct(isEdit ? product.product_id : null);
    
    modalOverlay.classList.add('active');
}

function saveProduct(id) {
    const name = document.getElementById('p-name').value;
    const catId = document.getElementById('p-cat').value;
    const price = document.getElementById('p-price').value;
    const discount = document.getElementById('p-discount').value;
    const stock = document.getElementById('p-stock').value;
    const desc = document.getElementById('p-desc').value;
    const img = document.getElementById('p-img').value;
    const status = document.getElementById('p-status').value;

    if (!name || !price || !catId) {
        alert("Vui lòng nhập tên, giá và chọn danh mục!");
        return;
    }

    const formData = new FormData();
    if (id) {
        formData.append('action', 'update');
        formData.append('product_id', id);
    } else {
        formData.append('action', 'create');
    }

    formData.append('name', name);
    formData.append('category_id', catId);
    formData.append('price', price);
    formData.append('discount', discount);
    formData.append('stock', stock);
    formData.append('description', desc);
    formData.append('image_url', img);
    formData.append('status', status);

    fetch('api/product.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert(data.message);
            closeModal();
            renderProducts(); 
        } else {
            alert("Lỗi: " + data.message);
        }
    })
    .catch(err => console.error(err));
}

function deleteProduct(id) {
    if(confirm('Bạn có muốn xóa sản phẩm này? Nếu sản phẩm đã từng bán, hệ thống sẽ chỉ ẩn nó đi.')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('product_id', id);

        fetch('api/product.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.message);
                renderProducts();
            } else {
                alert("KHÔNG THỂ XÓA: " + data.message);
            }
        });
    }
}

function renderOrders() {
    pageTitle.textContent = "Quản Lý Đơn Hàng";
    const orders = mockData.orders || [];
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
                        ${orders.map(o => `
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

function viewOrder(id) {
    const order = mockData.orders.find(o => o.id == id);
    if (!order) return;

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
    modalActionBtn.style.display = 'none'; 
    modalOverlay.classList.add('active');
}

function renderCustomers() {
    pageTitle.textContent = "Danh Sách Khách Hàng";
    const customers = mockData.customers || [];
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
                        ${customers.map(c => `
                            <tr>
                                <td>${c.id}</td>
                                <td>${c.name}</td>
                                <td>${c.email}</td>
                                <td>${c.phone || ''}</td>
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
    const users = mockData.users || [];
    app.innerHTML = `
        <div class="panel">
            <div class="panel-header">
                <h3>Danh sách người dùng</h3>
                <button class="btn btn-primary" onclick="openUserModal()"><i class="fas fa-plus"></i> Thêm User</button>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Vai trò</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${users.map(u => `
                            <tr>
                                <td>${u.id}</td>
                                <td>${u.name}</td>
                                <td>${u.email}</td>
                                <td>${u.phone || ''}</td>
                                <td><span class="status ${u.role === 'admin' ? 'shipping' : 'completed'}">${u.role}</span></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick="openUserModal(${u.id})"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id})"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;    
}

function openUserModal(userId = null) {
    const isEdit = userId !== null;
    let user = {};
    if (isEdit) {
        user = mockData.users.find(u => u.id == userId) || {};
        modalTitle.textContent = "Cập Nhật Người Dùng";
    } else {
        modalTitle.textContent = "Thêm Người Dùng Mới";
    }

    modalActionBtn.style.display = 'inline-block';
    
    modalBody.innerHTML = `
        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" id="u-name" class="form-control" value="${user.name || ''}">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" id="u-email" class="form-control" value="${user.email || ''}" ${isEdit ? 'readonly style="background:#eee"' : ''}>
        </div>
        <div class="form-group">
            <label>Mật khẩu ${isEdit ? '(Để trống nếu không đổi)' : ''}</label>
            <input type="password" id="u-pass" class="form-control">
        </div>
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" id="u-phone" class="form-control" value="${user.phone || ''}">
        </div>
        <div class="form-group">
            <label>Địa chỉ</label>
            <input type="text" id="u-address" class="form-control" value="${user.address || ''}">
        </div>
        <div class="form-group">
            <label>Vai trò</label>
            <select id="u-role" class="form-control">
                <option value="customer" ${user.role === 'customer' ? 'selected' : ''}>Khách hàng (Customer)</option>
                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Quản trị (Admin)</option>
                <option value="staff" ${user.role === 'staff' ? 'selected' : ''}>Nhân viên (Staff)</option>
            </select>
        </div>
    `;

    modalActionBtn.textContent = isEdit ? "Cập nhật" : "Tạo mới";
    modalActionBtn.onclick = () => saveUser(userId);
    modalOverlay.classList.add('active');
}

function saveUser(userId) {
    const name = document.getElementById('u-name').value;
    const email = document.getElementById('u-email').value;
    const pass = document.getElementById('u-pass').value;
    const phone = document.getElementById('u-phone').value;
    const addr = document.getElementById('u-address').value;
    const role = document.getElementById('u-role').value;

    const formData = new FormData();
    if (userId) {
        formData.append('action', 'update');
        formData.append('user_id', userId);
    } else {
        formData.append('action', 'create');
        formData.append('email', email);
    }
    formData.append('name', name);
    formData.append('password', pass);
    formData.append('phone', phone);
    formData.append('address', addr);
    formData.append('role', role);

    fetch('api/user.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert(data.message);
            closeModal();
            location.reload(); 
        } else {
            alert(data.message);
        }
    });
}

function deleteUser(id) {
    if(confirm('Bạn có chắc muốn xóa?')) {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('user_id', id);
        fetch('api/user.php', { method: 'POST', body: fd })
        .then(res=>res.json())
        .then(d => { alert(d.message); location.reload(); });
    }
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

function closeModal() {
    modalOverlay.classList.remove('active');
}

function renderSettings() {
    pageTitle.textContent = "Cài Đặt Hệ Thống";
    app.innerHTML = `<div class="panel"><p>Chức năng đang phát triển...</p></div>`;
}

function fetchCategories() {
    fetch('api/category.php')
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                categories = data.data;
            }
        })
        .catch(err => console.error("Lỗi lấy danh mục:", err));
}

function renderInventory() {
    pageTitle.textContent = "Quản Lý Kho Hàng";
    
    fetch('api/inventory.php')
        .then(res => res.json())
        .then(resData => {
            if(resData.status === 'success') {
                displayInventoryTable(resData.data);
            } else {
                alert('Lỗi tải kho: ' + resData.message);
            }
        })
        .catch(err => console.error(err));
}

function displayInventoryTable(items) {
    app.innerHTML = `
        <div class="panel">
            <div class="panel-header">
                <h3>Tình trạng tồn kho</h3>
                <button class="btn btn-secondary" onclick="renderInventory()"><i class="fas fa-sync"></i> Làm mới</button>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Tồn kho hiện tại</th>
                            <th>Cập nhật lần cuối</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(item => `
                            <tr>
                                <td>${item.product_id}</td>
                                <td>
                                    <img src="${getImageUrl(item.image_url)}" width="40" height="40" style="object-fit:cover; border-radius:4px;">
                                </td>
                                <td><strong>${item.name}</strong></td>
                                <td>
                                    <span class="status ${item.stock < 10 ? 'cancelled' : 'completed'}" style="font-size:14px;">
                                        ${item.stock}
                                    </span>
                                </td>
                                <td>${item.last_updated || 'Chưa cập nhật'}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="openImportModal(${item.product_id}, '${item.name}')">
                                        <i class="fas fa-plus-circle"></i> Nhập hàng
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function openImportModal(id, name) {
    modalTitle.textContent = "Nhập Kho: " + name;
    modalActionBtn.style.display = 'inline-block';
    
    modalBody.innerHTML = `
        <div class="form-group">
            <label>Số lượng muốn nhập thêm (*)</label>
            <input type="number" id="inv-qty" class="form-control" placeholder="Nhập số lượng..." min="1">
            <small style="color: #666;">Số lượng này sẽ được cộng thêm vào kho hiện tại.</small>
        </div>
    `;

    modalActionBtn.textContent = "Xác nhận nhập";
    modalActionBtn.onclick = () => saveInventoryImport(id);
    
    modalOverlay.classList.add('active');
}

function saveInventoryImport(id) {
    const qty = document.getElementById('inv-qty').value;
    
    if (!qty || qty <= 0) {
        alert("Vui lòng nhập số lượng hợp lệ!");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'import');
    formData.append('product_id', id);
    formData.append('quantity', qty);

    fetch('api/inventory.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert(data.message);
            closeModal();
            renderInventory(); 
        } else {
            alert("Lỗi: " + data.message);
        }
    })
    .catch(err => console.error(err));
}

window.onload = () => {
    fetchCategories(); 
    renderDashboard();
};