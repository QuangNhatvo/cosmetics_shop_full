<style>
    .custom-footer {
        background-color: #1a1a1a;
        color: #b0b0b0;
        padding: 60px 0 30px;
        margin-top: 50px;
        font-size: 14px;
    }

    .footer-heading {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 25px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .footer-links { list-style: none; padding: 0; }
    .footer-links li { margin-bottom: 12px; }
    .footer-links a {
        color: #b0b0b0;
        text-decoration: none;
        transition: 0.3s;
    }
    .footer-links a:hover { color: #ff6b6b; padding-left: 5px; }

    .footer-contact-info p { margin-bottom: 10px; }

    .social-icons-container { display: flex; gap: 15px; margin-top: 20px; }
    .social-icon {
        width: 35px; height: 35px;
        background: #333;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        transition: 0.3s;
        color: white;
    }
    .social-icon:hover { background: #ff6b6b; transform: translateY(-3px); }

    .newsletter-form p { color: white; margin-bottom: 15px; font-weight: 600; }
    .newsletter-form input {
        background: #333; border: none; color: white;
        padding: 10px; font-size: 13px;
    }
    .newsletter-form input:focus { background: #444; color: white; box-shadow: none; }
    
    .btn-submit {
        background: #ff6b6b; color: white; font-weight: 600; border: none;
    }
    .btn-submit:hover { background: #e65b5b; color: white; }

    .copyright { margin-top: 30px; font-size: 12px; color: #666; }
    
    @media (min-width: 992px) {
        .vertical-divider { border-left: 1px solid #333; padding-left: 30px; }
    }
</style>

<footer class="custom-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-3 col-md-6 footer-col">
                <div class="mb-3">
                    <i class="fas fa-gem text-white fa-2x"></i> 
                    <span class="text-white fw-bold fs-4 ms-2">COSMETICS</span>
                </div>
                <div class="footer-contact-info">
                    <p><i class="fas fa-phone-alt me-2"></i> 077.xxx.xxxx</p>
                    <p><i class="fas fa-quote-left me-2"></i> Nâng tầm vẻ đẹp Việt</p>
                    <p><i class="fas fa-envelope me-2"></i> contact@myshop.com</p>
                </div>
                <div class="social-icons-container">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 footer-col vertical-divider">
                <h3 class="footer-heading">Về chúng tôi</h3>
                <ul class="footer-links">
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Liên hệ</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Điều khoản sử dụng</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6 footer-col vertical-divider">
                <h3 class="footer-heading">Thương hiệu nổi bật</h3>
                <div class="row">
                    <div class="col-6">
                        <ul class="footer-links">
                            <li><a href="#">La Roche-Posay</a></li>
                            <li><a href="#">L'Oréal Paris</a></li>
                            <li><a href="#">Thorakao</a></li>
                            <li><a href="#">Cocoon Vietnam</a></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul class="footer-links">
                            <li><a href="#">Estée Lauder</a></li>
                            <li><a href="#">Decumar</a></li>
                            <li><a href="#">Shiseido</a></li>
                            <li><a href="#">Innisfree</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 footer-col vertical-divider">
                <form class="newsletter-form" id="newsletterForm">
                    <p>Đăng ký nhận tin khuyến mãi</p>
                    <div class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Email của bạn..." required>
                        <button type="submit" class="btn btn-submit">Gửi</button>
                    </div>
                    <div class="copyright">
                        © 2025 Cosmetics Shop.<br>All rights reserved.
                    </div>
                </form>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>