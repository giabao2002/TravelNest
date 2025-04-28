<?php include 'layouts/header.php';
include 'config/config.php';

// Lấy tour mới nhất
$newestToursQuery = "SELECT * FROM tours WHERE status = 'active' ORDER BY created_at DESC LIMIT 3";
$newestToursResult = $conn->query($newestToursQuery);
$newestTours = [];
while ($row = $newestToursResult->fetch_assoc()) {
    $newestTours[] = $row;
}

// Lấy tour được đặt nhiều nhất
$popularToursQuery = "
    SELECT t.*, COUNT(b.booking_id) as booking_count, 
    (SELECT COUNT(*) FROM reviews r WHERE r.tour_id = t.tour_id AND r.status = 'active') as review_count,
    (SELECT AVG(rating) FROM reviews r WHERE r.tour_id = t.tour_id AND r.status = 'active') as avg_rating
    FROM tours t
    LEFT JOIN bookings b ON t.tour_id = b.tour_id
    WHERE t.status = 'active'
    GROUP BY t.tour_id
    ORDER BY booking_count DESC
    LIMIT 3
";
$popularToursResult = $conn->query($popularToursQuery);
$popularTours = [];
while ($row = $popularToursResult->fetch_assoc()) {
    $popularTours[] = $row;
}

// Lấy đánh giá khách hàng
$reviewsQuery = "
    SELECT r.*, u.full_name, u.email, t.name as tour_name
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN tours t ON r.tour_id = t.tour_id
    WHERE r.status = 'active'
    ORDER BY r.review_date DESC
    LIMIT 3
";
$reviewsResult = $conn->query($reviewsQuery);
$reviews = [];
while ($row = $reviewsResult->fetch_assoc()) {
    $reviews[] = $row;
}
?>

<!-- Hero Section -->
<section class="hero-section" style="background-image: url('assets/images/banner1.png');">
    <div class="container hero-content text-center">
        <h1 class="hero-title animate">Khám Phá Vẻ Đẹp Việt Nam</h1>
        <p class="hero-subtitle animate delay-100">Trải nghiệm những chuyến du lịch trong nước đáng nhớ với Travel Nest</p>
        <a href="#search" class="btn btn-primary btn-lg animate delay-200">Khám phá ngay</a>
    </div>
</section>

<!-- Newest Tours -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold animate">Tours Mới Nhất</h2>
            <p class="text-muted animate delay-100">Khám phá những tour du lịch mới nhất được giới thiệu tại Travel Nest</p>
        </div>

        <div class="row">
            <?php if (count($newestTours) > 0): ?>
                <?php foreach ($newestTours as $index => $tour): ?>
                    <div class="col-md-4 mb-4 animate delay-<?php echo ($index + 1) * 100; ?>">
                        <div class="tour-card">
                            <div class="tour-card-img-wrapper">
                                <img src="admin/<?php echo $tour['image1']; ?>" class="card-img-top tour-card-img" alt="<?php echo $tour['name']; ?>">
                                <div class="tour-card-tag">
                                    <span class="badge bg-success">Mới</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="tour-duration"><i class="far fa-calendar-alt me-1"></i> <?php echo $tour['duration']; ?></span>
                                    <span class="tour-price"><?php echo number_format($tour['price_adult'], 0, ',', '.'); ?>đ</span>
                                </div>
                                <h5 class="card-title"><?php echo strlen($tour['name']) > 40 ? substr($tour['name'], 0, 37) . '...' : $tour['name']; ?></h5>
                                <div class="tour-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo strlen($tour['location']) > 40 ? substr($tour['location'], 0, 37) . '...' : $tour['location']; ?></span>
                                </div>
                                <p class="card-text small text-muted mt-2">
                                    <?php echo strlen($tour['description']) > 100 ? substr(strip_tags($tour['description']), 0, 97) . '...' : strip_tags($tour['description']); ?>
                                </p>
                                <hr>
                                <div class="d-grid">
                                    <a href="tour-detail.php?id=<?php echo $tour['tour_id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Không có tour mới nào được tìm thấy</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Most Booked Tours -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold animate">Tour Du Lịch Được Đặt Nhiều Nhất</h2>
            <p class="text-muted animate delay-100">Những gói tour được du khách tin tưởng và lựa chọn nhiều nhất</p>
        </div>

        <div class="row">
            <?php if (count($popularTours) > 0): ?>
                <?php foreach ($popularTours as $index => $tour): ?>
                    <div class="col-md-4 animate delay-<?php echo ($index + 1) * 100; ?>">
                        <div class="tour-card">
                            <div class="tour-card-img-wrapper">
                                <img src="admin/<?php echo $tour['image1']; ?>" class="card-img-top tour-card-img" alt="<?php echo $tour['name']; ?>">
                                <div class="tour-card-tag">
                                    <span class="badge bg-primary">Phổ biến</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="tour-rating">
                                        <?php
                                        $rating = isset($tour['avg_rating']) ? $tour['avg_rating'] : 0;
                                        $fullStars = floor($rating);
                                        $halfStar = $rating - $fullStars >= 0.5;

                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $fullStars) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i == $fullStars + 1 && $halfStar) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                        <span class="ms-1"><?php echo number_format($rating, 1); ?> (<?php echo $tour['review_count']; ?>)</span>
                                    </div>
                                    <span class="tour-price"><?php echo number_format($tour['price_adult'], 0, ',', '.'); ?>đ</span>
                                </div>
                                <h5 class="card-title"><?php echo strlen($tour['name']) > 40 ? substr($tour['name'], 0, 37) . '...' : $tour['name']; ?></h5>
                                <div class="tour-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo strlen($tour['location']) > 40 ? substr($tour['location'], 0, 37) . '...' : $tour['location']; ?></span>
                                </div>
                                <div class="tour-duration">
                                    <i class="far fa-calendar-alt"></i>
                                    <span><?php echo $tour['duration']; ?></span>
                                </div>
                                <p class="card-text small text-muted mt-2">
                                    <?php echo strlen($tour['description']) > 100 ? substr(strip_tags($tour['description']), 0, 97) . '...' : strip_tags($tour['description']); ?>
                                </p>
                                <hr>
                                <div class="d-grid">
                                    <a href="tour-detail.php?id=<?php echo $tour['tour_id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Không có tour nào được đặt</p>
                </div>
            <?php endif; ?>
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

<!-- Testimonials -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold animate">Khách Hàng Nói Gì?</h2>
            <p class="text-muted animate delay-100">Đánh giá thực tế từ những khách hàng đã trải nghiệm dịch vụ của chúng tôi</p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div id="reviewsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php if (count($reviews) > 0): ?>
                            <?php foreach ($reviews as $index => $review): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <div class="testimonial-card-large p-4 p-md-5">
                                        <div class="row">
                                            <div class="col-md-4 mb-3 mb-md-0">
                                                <div class="text-center">
                                                    <img src="admin/assets/img/user.jpg" class="testimonial-img-large rounded-circle mb-3" alt="Khách hàng">
                                                    <h5 class="mb-1"><?php echo $review['full_name']; ?></h5>
                                                    <p class="text-muted mb-2">Khách hàng</p>
                                                    <div class="testimonial-rating mb-3">
                                                        <?php
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            if ($i <= $review['rating']) {
                                                                echo '<i class="fas fa-star"></i>';
                                                            } else {
                                                                echo '<i class="far fa-star"></i>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="testimonial-content">
                                                    <i class="fas fa-quote-left fa-2x text-primary opacity-25 mb-3"></i>
                                                    <p class="testimonial-text-large">"<?php echo $review['comment']; ?>"</p>
                                                    <p class="text-muted fst-italic mt-3">Đã trải nghiệm: <?php echo $review['tour_name']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="carousel-item active">
                                <div class="testimonial-card-large p-4 p-md-5">
                                    <div class="text-center">
                                        <p>Chưa có đánh giá nào</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (count($reviews) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="#" class="btn btn-outline-primary">Xem tất cả đánh giá</a>
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

    .tour-card {
        padding: 0 10px;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
        height: 100%;
    }

    .tour-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .tour-card-img-wrapper {
        position: relative;
    }

    .tour-card-img {
        height: 200px;
        object-fit: cover;
    }

    .tour-card-tag {
        position: absolute;
        top: 15px;
        right: 15px;
    }

    .tour-rating i {
        color: #FFD700;
        font-size: 14px;
    }

    .testimonial-card-large {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .testimonial-img-large {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }

    .testimonial-text-large {
        font-size: 1.05rem;
        line-height: 1.7;
    }

    @media (max-width: 767px) {
        .testimonial-img-large {
            width: 80px;
            height: 80px;
        }
    }
</style>

<?php include 'layouts/footer.php'; ?>