    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row">
                <!-- About Company -->
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3">Travel Nest</h5>
                    <p class="small">Chúng tôi cung cấp các tour du lịch trong nước với chất lượng tốt nhất, giá cả phải chăng và dịch vụ chuyên nghiệp.</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3">Liên kết nhanh</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-decoration-none text-white-50 hover-white">Trang chủ</a></li>
                        <li class="mb-2"><a href="tours.php" class="text-decoration-none text-white-50 hover-white">Tour du lịch</a></li>
                        <li class="mb-2"><a href="about.php" class="text-decoration-none text-white-50 hover-white">Giới thiệu</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-decoration-none text-white-50 hover-white">Liên hệ</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3">Thông tin liên hệ</h5>
                    <p class="small mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Đường ABC, Quận XYZ, TP. HCM</p>
                    <p class="small mb-2"><i class="fas fa-phone me-2"></i> (84) 123 456 789</p>
                    <p class="small mb-2"><i class="fas fa-envelope me-2"></i> info@travelnest.com</p>
                </div>
                
                <!-- Newsletter -->
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3">Đăng ký nhận tin</h5>
                    <p class="small">Nhận thông tin về các tour mới và ưu đãi hấp dẫn</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email của bạn">
                        <button class="btn btn-primary" type="button">Đăng ký</button>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <!-- Copyright -->
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small mb-0">&copy; 2025 Travel Nest. Tất cả quyền được bảo lưu.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-white-50 text-decoration-none small">Điều khoản sử dụng</a></li>
                        <li class="list-inline-item ms-3"><a href="#" class="text-white-50 text-decoration-none small">Chính sách bảo mật</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.animate');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                    }
                });
            }, { threshold: 0.1 });
            
            animatedElements.forEach(element => {
                observer.observe(element);
            });
        });
    </script>
</body>
</html>
