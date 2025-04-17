<?php include 'layouts/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section" style="background-image: url('assets/images/hero-bg.jpg');">
    <div class="container hero-content text-center">
        <h1 class="hero-title animate">Khám Phá Vẻ Đẹp Việt Nam</h1>
        <p class="hero-subtitle animate delay-100">Trải nghiệm những chuyến du lịch trong nước đáng nhớ với Travel Nest</p>
        <a href="#search" class="btn btn-primary btn-lg animate delay-200">Khám phá ngay</a>
    </div>
</section>

<!-- Search Section -->
<section id="search" class="py-5">
    <div class="container">
        <div class="search-box animate">
            <h3 class="text-center mb-4">Tìm Tour Du Lịch</h3>
            <form id="search-form">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="search-location" placeholder="Địa điểm">
                            <label for="search-location">Địa điểm</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control datepicker" id="search-date" placeholder="Ngày khởi hành">
                            <label for="search-date">Ngày khởi hành</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Tìm kiếm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Popular Destinations -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold animate">Điểm Đến Phổ Biến</h2>
            <p class="text-muted animate delay-100">Khám phá những địa điểm du lịch được yêu thích nhất tại Việt Nam</p>
        </div>
        
        <div class="row">
            <!-- Destination 1 -->
            <div class="col-md-4 mb-4 animate delay-100">
                <div class="card border-0 shadow-sm h-100">
                    <img src="https://source.unsplash.com/600x400/?halong-bay" class="card-img-top" alt="Hạ Long">
                    <div class="card-body text-center">
                        <h4 class="card-title">Vịnh Hạ Long</h4>
                        <p class="card-text text-muted">Di sản thiên nhiên thế giới với hàng nghìn đảo đá vôi tuyệt đẹp</p>
                        <a href="tours.php?location=Ha%20Long" class="btn btn-outline-primary">Xem tours</a>
                    </div>
                </div>
            </div>
            
            <!-- Destination 2 -->
            <div class="col-md-4 mb-4 animate delay-200">
                <div class="card border-0 shadow-sm h-100">
                    <img src="https://source.unsplash.com/600x400/?danang" class="card-img-top" alt="Đà Nẵng">
                    <div class="card-body text-center">
                        <h4 class="card-title">Đà Nẵng</h4>
                        <p class="card-text text-muted">Thành phố biển năng động với cầu Rồng và bãi biển Mỹ Khê tuyệt đẹp</p>
                        <a href="tours.php?location=Da%20Nang" class="btn btn-outline-primary">Xem tours</a>
                    </div>
                </div>
            </div>
            
            <!-- Destination 3 -->
            <div class="col-md-4 mb-4 animate delay-300">
                <div class="card border-0 shadow-sm h-100">
                    <img src="https://source.unsplash.com/600x400/?phuquoc" class="card-img-top" alt="Phú Quốc">
                    <div class="card-body text-center">
                        <h4 class="card-title">Phú Quốc</h4>
                        <p class="card-text text-muted">Đảo ngọc với bãi biển cát trắng, nước biển trong xanh và nhiều khu nghỉ dưỡng cao cấp</p>
                        <a href="tours.php?location=Phu%20Quoc" class="btn btn-outline-primary">Xem tours</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Tours -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold animate">Tour Du Lịch Nổi Bật</h2>
            <p class="text-muted animate delay-100">Những gói tour được du khách đánh giá cao và lựa chọn nhiều nhất</p>
        </div>
        
        <div class="row">
            <!-- Tour 1 -->
            <div class="col-md-4 animate delay-100">
                <div class="tour-card">
                    <img src="assets/images/tour1.jpg" class="card-img-top tour-card-img" alt="Tour Hạ Long">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">Phổ biến</span>
                            <span class="tour-price">2.590.000đ</span>
                        </div>
                        <h5 class="card-title">Tour Vịnh Hạ Long 2N1Đ</h5>
                        <div class="tour-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Hạ Long, Quảng Ninh</span>
                        </div>
                        <div class="tour-duration">
                            <i class="far fa-calendar-alt"></i>
                            <span>2 ngày 1 đêm</span>
                        </div>
                        <hr>
                        <div class="d-grid">
                            <a href="tour-detail.php?id=1" class="btn btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tour 2 -->
            <div class="col-md-4 animate delay-200">
                <div class="tour-card">
                    <img src="assets/images/tour2.jpg" class="card-img-top tour-card-img" alt="Tour Đà Nẵng">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-danger">Giảm giá</span>
                            <span class="tour-price">3.490.000đ</span>
                        </div>
                        <h5 class="card-title">Tour Đà Nẵng - Hội An 3N2Đ</h5>
                        <div class="tour-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Đà Nẵng - Hội An</span>
                        </div>
                        <div class="tour-duration">
                            <i class="far fa-calendar-alt"></i>
                            <span>3 ngày 2 đêm</span>
                        </div>
                        <hr>
                        <div class="d-grid">
                            <a href="tour-detail.php?id=2" class="btn btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tour 3 -->
            <div class="col-md-4 animate delay-300">
                <div class="tour-card">
                    <img src="assets/images/tour3.jpg" class="card-img-top tour-card-img" alt="Tour Phú Quốc">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success">Mới</span>
                            <span class="tour-price">4.990.000đ</span>
                        </div>
                        <h5 class="card-title">Tour Phú Quốc 4N3Đ</h5>
                        <div class="tour-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Phú Quốc, Kiên Giang</span>
                        </div>
                        <div class="tour-duration">
                            <i class="far fa-calendar-alt"></i>
                            <span>4 ngày 3 đêm</span>
                        </div>
                        <hr>
                        <div class="d-grid">
                            <a href="tour-detail.php?id=3" class="btn btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 animate">
            <a href="tours.php" class="btn btn-outline-primary btn-lg">Xem tất cả tour</a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold animate">Tại Sao Chọn Travel Nest?</h2>
            <p class="text-muted animate delay-100">Chúng tôi cam kết mang đến cho bạn những trải nghiệm du lịch tuyệt vời nhất</p>
        </div>
        
        <div class="row">
            <!-- Feature 1 -->
            <div class="col-md-3 col-sm-6 mb-4 animate delay-100">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h4 class="fs-5 fw-bold">Chất Lượng</h4>
                    <p class="text-muted small">Dịch vụ du lịch chất lượng cao với đội ngũ hướng dẫn viên chuyên nghiệp</p>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="col-md-3 col-sm-6 mb-4 animate delay-200">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h4 class="fs-5 fw-bold">Giá Cả</h4>
                    <p class="text-muted small">Mức giá cạnh tranh với nhiều ưu đãi hấp dẫn cho khách hàng thân thiết</p>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="col-md-3 col-sm-6 mb-4 animate delay-300">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="fs-5 fw-bold">An Toàn</h4>
                    <p class="text-muted small">Đảm bảo an toàn tuyệt đối cho du khách trong suốt hành trình</p>
                </div>
            </div>
            
            <!-- Feature 4 -->
            <div class="col-md-3 col-sm-6 mb-4 animate delay-400">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="fs-5 fw-bold">Hỗ Trợ 24/7</h4>
                    <p class="text-muted small">Đội ngũ nhân viên sẵn sàng hỗ trợ bạn mọi lúc, mọi nơi</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5 bg-primary text-white stats-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4">
                <h2 class="fw-bold counter" data-target="1500">0</h2>
                <p>Tour đã tổ chức</p>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <h2 class="fw-bold counter" data-target="12000">0</h2>
                <p>Khách hàng hài lòng</p>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <h2 class="fw-bold counter" data-target="50">0</h2>
                <p>Điểm đến</p>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <h2 class="fw-bold counter" data-target="25">0</h2>
                <p>Giải thưởng</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold animate">Khách Hàng Nói Gì?</h2>
            <p class="text-muted animate delay-100">Cảm nhận của khách hàng sau khi trải nghiệm dịch vụ của chúng tôi</p>
        </div>
        
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <!-- Testimonial Group 1 -->
                <div class="carousel-item active">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card h-100">
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="testimonial-text">"Tour Hạ Long thật tuyệt vời! Cảnh đẹp, đồ ăn ngon, hướng dẫn viên nhiệt tình. Chắc chắn sẽ quay lại lần nữa!"</p>
                                <div class="d-flex align-items-center mt-3">
                                    <img src="https://randomuser.me/api/portraits/women/12.jpg" class="testimonial-img" alt="Khách hàng">
                                    <div>
                                        <p class="testimonial-name">Nguyễn Thị Hương</p>
                                        <small class="text-muted">Tour Vịnh Hạ Long</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card h-100">
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="testimonial-text">"Chuyến đi Đà Nẵng - Hội An đáng nhớ với gia đình tôi. Lịch trình hợp lý, khách sạn tốt, và dịch vụ chuyên nghiệp!"</p>
                                <div class="d-flex align-items-center mt-3">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="testimonial-img" alt="Khách hàng">
                                    <div>
                                        <p class="testimonial-name">Trần Văn Minh</p>
                                        <small class="text-muted">Tour Đà Nẵng - Hội An</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card h-100">
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <p class="testimonial-text">"Phú Quốc thật sự là thiên đường! Biển xanh, cát trắng, và chương trình tour rất chi tiết. Travel Nest không làm tôi thất vọng!"</p>
                                <div class="d-flex align-items-center mt-3">
                                    <img src="https://randomuser.me/api/portraits/women/28.jpg" class="testimonial-img" alt="Khách hàng">
                                    <div>
                                        <p class="testimonial-name">Lê Thu Thảo</p>
                                        <small class="text-muted">Tour Phú Quốc</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial Group 2 -->
                <div class="carousel-item">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card h-100">
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p class="testimonial-text">"Chuyến đi Sapa tuyệt vời với cảnh đẹp hùng vĩ và người dân thân thiện. Cảm ơn Travel Nest đã tổ chức chuyến đi hoàn hảo!"</p>
                                <div class="d-flex align-items-center mt-3">
                                    <img src="https://randomuser.me/api/portraits/men/15.jpg" class="testimonial-img" alt="Khách hàng">
                                    <div>
                                        <p class="testimonial-name">Phạm Tuấn Anh</p>
                                        <small class="text-muted">Tour Sapa</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card h-100">
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="testimonial-text">"Tour Đà Lạt rất thú vị với nhiều địa điểm tham quan và không khí mát mẻ. Nhân viên nhiệt tình, chu đáo!"</p>
                                <div class="d-flex align-items-center mt-3">
                                    <img src="https://randomuser.me/api/portraits/women/22.jpg" class="testimonial-img" alt="Khách hàng">
                                    <div>
                                        <p class="testimonial-name">Hoàng Ngọc Mai</p>
                                        <small class="text-muted">Tour Đà Lạt</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card h-100">
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="testimonial-text">"Chuyến du lịch Huế - Hội An thật sự đáng nhớ. Được tìm hiểu về lịch sử, văn hóa và thưởng thức ẩm thực tuyệt vời!"</p>
                                <div class="d-flex align-items-center mt-3">
                                    <img src="https://randomuser.me/api/portraits/men/62.jpg" class="testimonial-img" alt="Khách hàng">
                                    <div>
                                        <p class="testimonial-name">Đỗ Quang Hải</p>
                                        <small class="text-muted">Tour Huế - Hội An</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            
            <div class="carousel-indicators" style="bottom: -50px;">
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active" style="background-color: #FF6B6B;" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1" style="background-color: #FF6B6B;" aria-label="Slide 2"></button>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="container">
    <div class="cta-section animate">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start mb-4 mb-lg-0">
                <h2 class="fw-bold mb-3">Sẵn sàng cho chuyến du lịch tiếp theo?</h2>
                <p class="mb-0">Đăng ký ngay hôm nay để nhận ưu đãi đặc biệt cho tour du lịch trong nước!</p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <a href="register.php" class="btn btn-light btn-lg">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</section>

<!-- Back to Top Button -->
<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button">
    <i class="fas fa-arrow-up"></i>
</a>

<style>
    #back-to-top {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        text-align: center;
        line-height: 45px;
        z-index: 99;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
    }
    
    #back-to-top.show {
        opacity: 1;
        visibility: visible;
    }
</style>

<?php include 'layouts/footer.php'; ?>
