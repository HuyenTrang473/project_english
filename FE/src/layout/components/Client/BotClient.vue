// ...existing code...
<template>
    <footer class="footer-dark mt-5">
        <div class="container py-5">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <h4 class="fw-bold mb-3 logo">
                        <i class="fa-solid fa-graduation-cap me-2"></i>
                        ENG LEARN
                    </h4>
                    <p class="lead small mb-3 text-white">Phần mềm học tiếng Anh, luyện thi IELTS.</p>

                    <div>
                        <div class="d-flex align-items-center mb-2 text-white">
                            <i class="fa-solid fa-map-marker-alt me-3"></i>
                            <span>Địa chỉ</span>
                        </div>
                        <div class="d-flex align-items-center mb-2 text-white">
                            <i class="fa-solid fa-phone me-3"></i>
                            <span>Số điện thoại</span>
                        </div>
                        <div class="d-flex align-items-center mb-2 text-white">
                            <i class="fa-solid fa-envelope me-3"></i>
                            <span>dangmaihuyentrang@gmail.com</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <h5 class="fw-semibold mb-3 text-white">Học tiếng Anh</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Reading</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Writing</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Speaking</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Listening</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Tư vấn</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <h5 class="fw-semibold mb-3 text-white">Test IELTS</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">IELTS Online Test</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">IELTS Reading Practice</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">IELTS Writing Practice</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">IELTS Listening Practice</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h5 class="fw-semibold mb-3 text-white">Liên hệ & Mạng xã hội</h5>
                    <p class="small text-white mb-3">Theo dõi để nhận tài liệu và thông báo khóa học mới nhất.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-btn" aria-label="facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-btn" aria-label="instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-btn" aria-label="youtube"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" class="social-btn" aria-label="linkedin"><i
                                class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top border-dark">
            <div class="container py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 small text-white-50">&copy; 2025 ENG LEARN. Tất cả quyền được bảo lưu.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="footer-link me-3">Chính sách bảo mật</a>
                        <a href="#" class="footer-link me-3">Điều khoản sử dụng</a>
                        <a href="#" class="footer-link">Sơ đồ trang</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
export default {

}
</script>

<style>
.footer-dark {
    background-color: #0b0b0b;
    /* nền đen */
    color: #ffffff;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.35);
    font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

/* border phần dưới */
.border-dark {
    border-color: rgba(255, 255, 255, 0.06) !important;
}

/* tiêu đề, logo */
.footer-dark .logo {
    font-size: 1.15rem;
    letter-spacing: 0.4px;
    color: #ffffff;
}

/* link footer */
.footer-dark .footer-link {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color .15s ease, transform .08s ease;
}

.footer-dark .footer-link:hover {
    color: #ffffff;
    text-decoration: none;
    transform: translateY(-1px);
}

/* icon và social buttons */
.footer-dark i {
    color: rgba(255, 255, 255, 0.9);
}

.footer-dark .social-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
    text-decoration: none;
    transition: background .15s ease, transform .08s ease;
}

.footer-dark .social-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-2px);
}

/* văn bản phụ */
.text-white-50 {
    color: rgba(255, 255, 255, 0.65) !important;
}

/* responsive nhỏ gọn */
@media (max-width: 575.98px) {
    .footer-dark {
        padding-bottom: 0;
    }

    .footer-dark .logo {
        font-size: 1rem;
    }
}
</style>