<?php include 'layouts/header.php'; ?>

<!-- About Header -->
<section class="about-header" style="background-image: url('assets/images/banner2.png');">
    <div class="container text-center">
        <h1 class="text-white fw-bold">Giới Thiệu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Giới thiệu</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Company Overview -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image-wrapper">
                    <img src="assets/images/banner4.png" alt="Travel Nest Overview" class="img-fluid rounded-3 shadow">
                    <div class="experience-badge">
                        <span class="experience-years">5+</span>
                        <span class="experience-text">Năm Kinh Nghiệm</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-title mb-4">
                    <h6 class="text-primary text-uppercase fw-bold">Về chúng tôi</h6>
                    <h2 class="fw-bold">Công ty Du lịch Travel Nest</h2>
                </div>
                <p class="mb-4">Travel Nest được thành lập vào năm 2018, với sứ mệnh mang đến những trải nghiệm du lịch tuyệt vời nhất cho khách hàng Việt Nam. Chúng tôi tự hào là đơn vị tiên phong trong việc cung cấp các dịch vụ du lịch chất lượng cao với giá thành hợp lý.</p>
                <p class="mb-4">Với đội ngũ nhân viên giàu kinh nghiệm và am hiểu sâu sắc về các điểm đến du lịch trong nước, Travel Nest cam kết mang đến cho khách hàng những chuyến đi an toàn, thú vị và đáng nhớ.</p>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="about-feature d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-3 fa-2x"></i>
                            <div>
                                <h5 class="mb-1 fs-5">Chuyên nghiệp</h5>
                                <p class="mb-0 text-muted small">Đội ngũ nhân viên được đào tạo bài bản</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="about-feature d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-3 fa-2x"></i>
                            <div>
                                <h5 class="mb-1 fs-5">Uy tín</h5>
                                <p class="mb-0 text-muted small">Cam kết chất lượng dịch vụ tốt nhất</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="about-feature d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-3 fa-2x"></i>
                            <div>
                                <h5 class="mb-1 fs-5">Đa dạng</h5>
                                <p class="mb-0 text-muted small">Nhiều lựa chọn tour phù hợp mọi nhu cầu</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="about-feature d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-3 fa-2x"></i>
                            <div>
                                <h5 class="mb-1 fs-5">Tận tâm</h5>
                                <p class="mb-0 text-muted small">Luôn sẵn sàng hỗ trợ khách hàng 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="contact.php" class="btn btn-primary">Liên hệ ngay</a>
            </div>
        </div>
    </div>
</section>


<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h6 class="text-primary text-uppercase fw-bold">Câu hỏi thường gặp</h6>
            <h2 class="fw-bold">Giải đáp thắc mắc</h2>
            <p class="text-muted">Những câu hỏi khách hàng thường hỏi khi liên hệ với chúng tôi</p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Làm thế nào để đặt tour du lịch?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Bạn có thể đặt tour du lịch trực tiếp trên website của chúng tôi bằng cách chọn tour phù hợp, điền thông tin cá nhân và thanh toán. Ngoài ra, bạn cũng có thể liên hệ với chúng tôi qua số điện thoại hoặc email để được tư vấn và đặt tour.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Chính sách hủy tour như thế nào?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chính sách hủy tour của chúng tôi như sau:<br>
                                - Hủy trước 30 ngày: Hoàn trả 90% tổng giá trị tour<br>
                                - Hủy từ 15-29 ngày: Hoàn trả 70% tổng giá trị tour<br>
                                - Hủy từ 7-14 ngày: Hoàn trả 50% tổng giá trị tour<br>
                                - Hủy từ 3-6 ngày: Hoàn trả 30% tổng giá trị tour<br>
                                - Hủy dưới 3 ngày: Không hoàn trả
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Có những phương thức thanh toán nào?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi chấp nhận nhiều phương thức thanh toán khác nhau bao gồm: chuyển khoản ngân hàng, thanh toán bằng thẻ tín dụng/ghi nợ (Visa, Mastercard, JCB), ví điện tử (MoMo, ZaloPay, VNPay) và thanh toán tiền mặt tại văn phòng của chúng tôi.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Tôi có thể yêu cầu thiết kế tour riêng không?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Vâng, chúng tôi cung cấp dịch vụ thiết kế tour theo yêu cầu riêng của khách hàng. Bạn có thể liên hệ với đội ngũ tư vấn của chúng tôi để chia sẻ nhu cầu, sở thích và ngân sách của bạn. Chúng tôi sẽ tạo ra một hành trình phù hợp nhất cho bạn và những người đồng hành.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Tôi cần chuẩn bị những gì trước khi đi tour?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Tùy thuộc vào tour mà bạn đã đặt, chúng tôi sẽ gửi cho bạn một danh sách chi tiết những vật dụng cần mang theo. Nhìn chung, bạn nên chuẩn bị giấy tờ tùy thân, quần áo phù hợp với thời tiết, thuốc men cá nhân, và các vật dụng cá nhân cần thiết khác. Nếu có bất kỳ yêu cầu đặc biệt nào, đừng ngần ngại liên hệ với chúng tôi trước chuyến đi.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 cta-section" style="background-image: url('assets/images/cta-bg.jpg');">
    <div class="container text-center">
        <h2 class="text-white mb-4">Sẵn sàng khám phá Việt Nam cùng Travel Nest?</h2>
        <p class="text-white mb-4">Đội ngũ chuyên viên tư vấn của chúng tôi luôn sẵn sàng hỗ trợ bạn lên kế hoạch cho chuyến đi hoàn hảo</p>
        <a href="tours.php" class="btn btn-primary me-2">Xem các tour</a>
        <a href="contact.php" class="btn btn-outline-light">Liên hệ ngay</a>
    </div>
</section>

<style>
    .about-header {
        padding: 120px 0;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    
    .about-header:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    
    .about-header .container {
        position: relative;
        z-index: 1;
    }
    
    .about-image-wrapper {
        position: relative;
    }
    
    .experience-badge {
        position: absolute;
        right: -20px;
        bottom: 30px;
        background: var(--bs-primary);
        color: white;
        padding: 15px 25px;
        border-radius: 5px;
        text-align: center;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .experience-years {
        display: block;
        font-size: 2.5rem;
        font-weight: bold;
        line-height: 1;
    }
    
    .experience-text {
        display: block;
        font-size: 1rem;
    }
    
    .timeline {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .timeline:after {
        content: '';
        position: absolute;
        width: 6px;
        background-color: var(--bs-primary);
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -3px;
        border-radius: 3px;
    }
    
    .timeline-item {
        padding: 10px 40px;
        position: relative;
        width: 50%;
        left: 0;
        margin-bottom: 60px;
    }
    
    .timeline-item.right {
        left: 50%;
    }
    
    .timeline-badge {
        position: absolute;
        width: 60px;
        height: 60px;
        right: -30px;
        background-color: white;
        border: 4px solid var(--bs-primary);
        top: 15px;
        border-radius: 50%;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .timeline-item.right .timeline-badge {
        left: -30px;
    }
    
    .timeline-badge span {
        font-weight: bold;
        color: var(--bs-primary);
    }
    
    .timeline-content {
        padding: 20px 30px;
        background-color: white;
        position: relative;
        border-radius: 6px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .timeline-content h4 {
        margin-top: 0;
        color: var(--bs-primary);
    }
    
    .team-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: white;
    }
    
    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .team-img-wrapper {
        position: relative;
        overflow: hidden;
    }
    
    .team-social {
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        transition: bottom 0.3s ease;
    }
    
    .team-img-wrapper:hover .team-social {
        bottom: 20px;
    }
    
    .team-social a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        margin: 0 5px;
        color: var(--bs-primary);
        transition: all 0.3s ease;
    }
    
    .team-social a:hover {
        background: var(--bs-primary);
        color: white;
    }
    
    .vision-card, .mission-card {
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        background-color: white;
    }
    
    .vision-icon, .mission-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .vision-icon {
        background-color: rgba(13, 110, 253, 0.1);
        color: var(--bs-primary);
    }
    
    .mission-icon {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--bs-danger);
    }
    
    .cta-section {
        background-size: cover;
        background-position: center;
        position: relative;
    }
    
    .cta-section:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
    }
    
    .cta-section .container {
        position: relative;
        z-index: 1;
    }
    
    @media (max-width: 767px) {
        .timeline:after {
            left: 31px;
        }
        
        .timeline-item {
            width: 100%;
            padding-left: 70px;
            padding-right: 25px;
        }
        
        .timeline-item.right {
            left: 0;
        }
        
        .timeline-badge {
            left: 0;
            right: auto;
        }
        
        .timeline-item.right .timeline-badge {
            left: 0;
        }
    }
</style>

<?php include 'layouts/footer.php'; ?> 