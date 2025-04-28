<?php include 'layouts/header.php'; ?>

<!-- Contact Header -->
<section class="contact-header" style="background-image: url('assets/images/banner3.png');">
    <div class="container text-center">
        <h1 class="text-white fw-bold">Liên Hệ</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Liên hệ</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-info-content">
                        <h4>Địa Chỉ</h4>
                        <p>123 Đường Láng, Quận Đống Đa<br>Hà Nội, Việt Nam</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-info-content">
                        <h4>Điện Thoại</h4>
                        <p>Hotline: +84 123 456 789<br>Tư vấn: +84 987 654 321</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-info-content">
                        <h4>Email</h4>
                        <p>info@travelnest.vn<br>support@travelnest.vn</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & Map -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="section-title mb-4">
                    <h6 class="text-primary text-uppercase fw-bold">Gửi tin nhắn</h6>
                    <h2 class="fw-bold">Liên hệ với chúng tôi</h2>
                    <p class="text-muted">Hãy điền thông tin vào form dưới đây, chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất</p>
                </div>

                <form action="#" method="post" class="contact-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Họ và tên" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col-md-6">
                            <input type="tel" class="form-control" placeholder="Số điện thoại" required>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select">
                                <option selected disabled>Chủ đề</option>
                                <option>Thông tin tour</option>
                                <option>Đặt tour</option>
                                <option>Góp ý dịch vụ</option>
                                <option>Khác</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" rows="5" placeholder="Nội dung tin nhắn" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.506277022581!2d105.80603877503098!3d21.012419280632855!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab61dbeba2ad%3A0xa75b8941f8f12568!2zMTIzIMSQLiBMw6FuZywgVHJ1bmcgSG_DoCwgxJDhu5FuZyDEkGEsIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1745840162167!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Working Hours -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="section-title mb-4">
                    <h6 class="text-primary text-uppercase fw-bold">Giờ làm việc</h6>
                    <h2 class="fw-bold">Thời gian hoạt động</h2>
                </div>
                <div class="working-hours">
                    <div class="working-day d-flex justify-content-between align-items-center">
                        <span class="day">Thứ Hai - Thứ Sáu:</span>
                        <span class="hours">8:00 - 17:30</span>
                    </div>
                    <div class="working-day d-flex justify-content-between align-items-center">
                        <span class="day">Thứ Bảy:</span>
                        <span class="hours">8:00 - 12:00</span>
                    </div>
                    <div class="working-day d-flex justify-content-between align-items-center">
                        <span class="day">Chủ Nhật:</span>
                        <span class="hours">Đóng cửa</span>
                    </div>
                    <div class="working-day d-flex justify-content-between align-items-center">
                        <span class="day">Hotline hỗ trợ khẩn cấp:</span>
                        <span class="hours">24/7</span>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="mb-1"><strong>Lưu ý:</strong> Lịch làm việc có thể thay đổi vào các dịp lễ, Tết.</p>
                    <p>Vui lòng liên hệ trước khi đến để đảm bảo văn phòng đang mở cửa.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .contact-header {
        padding: 120px 0;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .contact-header:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    .contact-header .container {
        position: relative;
        z-index: 1;
    }

    .contact-info-card {
        display: flex;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .contact-info-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .contact-info-icon {
        width: 60px;
        height: 60px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--bs-primary);
        margin-right: 20px;
    }

    .contact-info-content h4 {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .contact-info-content p {
        margin-bottom: 0;
        color: #6c757d;
    }

    .contact-form .form-control,
    .contact-form .form-select {
        padding: 12px 15px;
        border-radius: 5px;
        border: 1px solid #ced4da;
        margin-bottom: 15px;
    }

    .contact-form .form-control:focus,
    .contact-form .form-select:focus {
        box-shadow: none;
        border-color: var(--bs-primary);
    }

    .map-container {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        height: 100%;
    }

    .working-hours {
        margin-bottom: 20px;
    }

    .working-day {
        padding: 15px 0;
        border-bottom: 1px dashed #dee2e6;
    }

    .working-day:last-child {
        border-bottom: none;
    }

    .day {
        font-weight: 600;
    }

    .hours {
        color: var(--bs-primary);
        font-weight: 600;
    }

    .office-image {
        margin-bottom: 15px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .accordion-item {
        margin-bottom: 15px;
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .accordion-button {
        padding: 20px;
        font-weight: 600;
        background-color: white;
    }

    .accordion-button:not(.collapsed) {
        background-color: var(--bs-primary);
        color: white;
    }

    .accordion-button:focus {
        box-shadow: none;
    }

    .accordion-body {
        padding: 20px;
        background-color: white;
    }

    .newsletter-section {
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .newsletter-section:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
    }

    .newsletter-content {
        position: relative;
        z-index: 1;
        padding: 40px;
    }

    .newsletter-form {
        max-width: 500px;
        margin: 0 auto;
    }

    @media (max-width: 767px) {
        .contact-info-card {
            margin-bottom: 20px;
        }
    }
</style>

<?php include 'layouts/footer.php'; ?>