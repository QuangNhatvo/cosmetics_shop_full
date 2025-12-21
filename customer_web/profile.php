<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='../login.php';</script>";
    exit();
}

include '../includes/db.php'; 

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM users WHERE user_id = $user_id";
$result_user = $conn->query($sql_user);
if($result_user && $result_user->num_rows > 0){
    $user = $result_user->fetch_assoc();
} else {
    $user = ['name' => 'Khách hàng', 'email' => '', 'phone' => '', 'address' => ''];
}

include '../includes/header.php'; 
?>

<style>
    :root {
        --bg-pink-pastel: #fceef2;      
        --btn-green-pastel: #c2eac4;   
        --btn-green-hover: #a8dcb0;     
        --text-dark: #333;
        --border-radius-card: 20px;    
    }

    body {
        background-color: #f8f9fa; 
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    .breadcrumb-area {
        margin: 20px 0;
        font-size: 14px;
        color: #666;
    }

    .profile-section {
        background-color: var(--bg-pink-pastel);
        border-radius: var(--border-radius-card);
        padding: 30px;
        margin-bottom: 25px;
        border: 1px solid #f3dce2;
    }


    .user-welcome-card {
        text-align: center;
        padding: 40px 20px;
    }

    .user-avatar-circle {
        width: 80px;
        height: 80px;
        border: 2px solid #333;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 40px;
        color: #333;
        background: transparent;
    }

    .user-greeting {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }


    .info-header {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 20px;
        color: var(--text-dark);
    }

    .custom-form-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .custom-label {
        font-weight: 600;
        width: 140px;
        font-size: 14px;
        color: #333;
    }

    .custom-input {
        flex: 1;
        border: 1px solid transparent;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        background-color: #fff;
        color: #555;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }

    .custom-input:not([readonly]) {
        border-color: #aaa;
        background-color: #fff;
    }
    
    .custom-input:focus {
        outline: none;
        border-color: #ff6b6b;
        box-shadow: 0 0 5px rgba(255, 107, 107, 0.2);
    }

    .btn-mint {
        background-color: var(--btn-green-pastel);
        border: none;
        border-radius: 50px; 
        padding: 10px 30px;
        font-weight: 700;
        color: #333;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
        margin-top: 10px;
    }

    .btn-mint:hover {
        background-color: var(--btn-green-hover);
        transform: translateY(-2px);
    }

    .history-card {
        background-color: #fff;
        border-radius: var(--border-radius-card);
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }

    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .st-pending { background: #fff3cd; color: #856404; }
    .st-confirmed { background: #cce5ff; color: #004085; }
    .st-shipped { background: #d1ecf1; color: #0c5460; }
    .st-delivered { background: #d4edda; color: #155724; }
    .st-cancelled { background: #f8d7da; color: #721c24; }
    @media (max-width: 768px) {
        .custom-form-group { flex-direction: column; align-items: flex-start; }
        .custom-label { margin-bottom: 5px; }
        .custom-input { width: 100%; }
    }
</style>

<div class="container" style="max-width: 900px; padding-bottom: 50px;">
    <div class="breadcrumb-area">
        Trang chủ / Tài khoản cá nhân
    </div>

    <div class="profile-section user-welcome-card">
        <div class="user-avatar-circle">
            <i class="far fa-user"></i> </div>
        <h3 class="user-greeting">Hi, <?php echo htmlspecialchars($user['name']); ?></h3>
    </div>

    <div class="profile-section">
        <div class="info-header">Thông tin của bạn:</div>
        
        <form id="profileForm">
            <div class="custom-form-group">
                <label class="custom-label">Email:</label>
                <input type="email" class="custom-input" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background-color: #f2f2f2; cursor: not-allowed;">
            </div>

            <div class="custom-form-group">
                <label class="custom-label">Số điện thoại:</label>
                <input type="tel" class="custom-input" name="phone" id="inputPhone" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
            </div>

            <div class="custom-form-group">
                <label class="custom-label">Họ và tên:</label>
                <input type="text" class="custom-input" name="name" id="inputName" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
            </div>

            <div class="custom-form-group">
                <label class="custom-label">Địa chỉ:</label>
                <input type="text" class="custom-input" name="address" id="inputAddress" value="<?php echo htmlspecialchars($user['address']); ?>" readonly>
            </div>

            <div class="mt-3">
                <button type="button" class="btn-mint" id="btnEdit" onclick="toggleEdit()">
                    Cập nhật
                </button>
                
                <div id="saveGroup" style="display: none;">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Lưu thay đổi</button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 ms-2" onclick="cancelEdit()">Hủy</button>
                </div>
            </div>
        </form>
    </div>

    <div class="history-card mt-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-box-open me-2"></i>Lịch sử đơn hàng</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody id="order-history-body">
                    <tr><td colspan="5" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    let originalData = {};

    function toggleEdit() {
        originalData = {
            name: document.getElementById('inputName').value,
            phone: document.getElementById('inputPhone').value,
            address: document.getElementById('inputAddress').value
        };

        const inputs = ['inputName', 'inputPhone', 'inputAddress'];
        inputs.forEach(id => document.getElementById(id).removeAttribute('readonly'));
        
        document.getElementById('inputName').focus();

        document.getElementById('btnEdit').style.display = 'none';
        document.getElementById('saveGroup').style.display = 'inline-block';
    }

    function cancelEdit() {
        document.getElementById('inputName').value = originalData.name;
        document.getElementById('inputPhone').value = originalData.phone;
        document.getElementById('inputAddress').value = originalData.address;

        const inputs = ['inputName', 'inputPhone', 'inputAddress'];
        inputs.forEach(id => document.getElementById(id).setAttribute('readonly', true));

        document.getElementById('btnEdit').style.display = 'inline-block';
        document.getElementById('saveGroup').style.display = 'none';
    }

    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = {
            action: 'update_info',
            name: document.getElementById('inputName').value,
            phone: document.getElementById('inputPhone').value,
            address: document.getElementById('inputAddress').value
        };

        fetch('api/user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                alert("Cập nhật thành công!");
                const inputs = ['inputName', 'inputPhone', 'inputAddress'];
                inputs.forEach(id => document.getElementById(id).setAttribute('readonly', true));
                document.getElementById('btnEdit').style.display = 'inline-block';
                document.getElementById('saveGroup').style.display = 'none';
                
                document.querySelector('.user-greeting').innerText = "Hi, " + data.name;
            } else {
                alert("Lỗi: " + res.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi kết nối server");
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        fetch('api/order.php?action=list')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('order-history-body');
                tbody.innerHTML = '';
                
                if(data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(order => {
                        let badgeClass = 'bg-secondary';
                        let statusText = order.status;

                        if(order.status === 'pending') { badgeClass = 'st-pending'; statusText = 'Chờ xử lý'; }
                        else if(order.status === 'confirmed') { badgeClass = 'st-confirmed'; statusText = 'Đã xác nhận'; }
                        else if(order.status === 'shipped') { badgeClass = 'st-shipped'; statusText = 'Đang giao'; }
                        else if(order.status === 'delivered') { badgeClass = 'st-delivered'; statusText = 'Hoàn thành'; }
                        else if(order.status === 'cancelled') { badgeClass = 'st-cancelled'; statusText = 'Đã hủy'; }

                        const html = `
                            <tr>
                                <td><b>#${order.order_id}</b></td>
                                <td>${order.order_date}</td>
                                <td class="text-danger fw-bold">${parseInt(order.total_amount).toLocaleString()}đ</td>
                                <td><span class="status-badge ${badgeClass}">${statusText}</span></td>
                                <td>
                                    <a href="order_details.php?id=${order.order_id}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += html;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Bạn chưa có đơn hàng nào.</td></tr>';
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('order-history-body').innerHTML = '<tr><td colspan="5" class="text-danger text-center">Lỗi tải dữ liệu.</td></tr>';
            });
    });
</script>

<?php include '../includes/footer.php'; ?>