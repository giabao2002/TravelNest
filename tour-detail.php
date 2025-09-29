<?php
include 'config/config.php';
include 'functions/auth_functions.php';

// Kiểm tra id tour
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tours.php');
    exit();
}

$tourId = $_GET['id'];
$success = $error = '';

// Lấy thông tin tour
$stmt = $conn->prepare("
    SELECT t.*, 
           (SELECT COUNT(*) FROM reviews r WHERE r.tour_id = t.tour_id AND r.status = 'active') as review_count,
           (SELECT AVG(rating) FROM reviews r WHERE r.tour_id = t.tour_id AND r.status = 'active') as avg_rating
    FROM tours t 
    WHERE t.tour_id = ? AND t.status = 'active'
");
$stmt->bind_param("i", $tourId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: tours.php');
    exit();
}

$tour = $result->fetch_assoc();

// Lấy các ngày khởi hành có sẵn
$stmt = $conn->prepare("
    SELECT date_id, departure_date, available_seats
    FROM tour_dates
    WHERE tour_id = ? AND status = 'available' AND departure_date >= CURDATE()
    ORDER BY departure_date
");
$stmt->bind_param("i", $tourId);
$stmt->execute();
$datesResult = $stmt->get_result();
$availableDates = [];

while ($date = $datesResult->fetch_assoc()) {
    $availableDates[] = $date;
}

// Lấy đánh giá của tour
$stmt = $conn->prepare("
    SELECT r.review_id, r.rating, r.comment, r.review_date,
           u.full_name
    FROM reviews r
    INNER JOIN users u ON r.user_id = u.user_id
    WHERE r.tour_id = ? AND r.status = 'active'
    ORDER BY r.review_date DESC
    LIMIT 10
");
$stmt->bind_param("i", $tourId);
$stmt->execute();
$reviewsResult = $stmt->get_result();
$reviews = [];

while ($review = $reviewsResult->fetch_assoc()) {
    $reviews[] = $review;
}

// Xử lý đặt tour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_tour'])) {
    // Kiểm tra đăng nhập
    if (!isLoggedIn()) {
        $error = "Vui lòng đăng nhập để đặt tour.";
    } else {
        $dateId = $_POST['date_id'] ?? 0;
        $numAdults = isset($_POST['num_adults']) ? (int)$_POST['num_adults'] : 0;
        $numChildren = isset($_POST['num_children']) ? (int)$_POST['num_children'] : 0;
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        // Validate
        if (empty($dateId) || !is_numeric($dateId)) {
            $error = "Vui lòng chọn ngày khởi hành.";
        } elseif ($numAdults < 1) {
            $error = "Số lượng người lớn phải ít nhất là 1.";
        } else {
            // Kiểm tra ngày khởi hành hợp lệ
            $stmt = $conn->prepare("SELECT available_seats, departure_date FROM tour_dates WHERE date_id = ? AND tour_id = ? AND status = 'available'");
            $stmt->bind_param("ii", $dateId, $tourId);
            $stmt->execute();
            $dateResult = $stmt->get_result();

            if ($dateResult->num_rows === 0) {
                $error = "Ngày khởi hành không hợp lệ.";
            } else {
                $dateData = $dateResult->fetch_assoc();
                $availableSeats = $dateData['available_seats'];
                $departureDate = $dateData['departure_date'];

                if ($numAdults + $numChildren > $availableSeats) {
                    $error = "Số lượng chỗ trống không đủ. Chỉ còn {$availableSeats} chỗ.";
                } else {
                    // Tính tổng tiền
                    $totalPrice = ($numAdults * $tour['price_adult']) + ($numChildren * $tour['price_child']);

                    // Lưu thông tin đặt tour vào session thay vì lưu vào database
                    $_SESSION['booking_data'] = [
                        'user_id' => $_SESSION['user_id'],
                        'tour_id' => $tourId,
                        'date_id' => $dateId,
                        'departure_date' => $departureDate,
                        'num_adults' => $numAdults,
                        'num_children' => $numChildren,
                        'total_price' => $totalPrice,
                        'notes' => $notes,
                        'available_seats' => $availableSeats,
                        'tour_name' => $tour['name'],
                        'tour_location' => $tour['location'],
                        'tour_image' => $tour['image1']
                    ];

                    // Chuyển hướng đến trang thanh toán
                    header("Location: checkout.php");
                    exit();
                }
            }
        }
    }
}

include 'layouts/header.php';
?>

<div class="tour-header py-4" style="background-image: url('admin/<?php echo $tour['image1']; ?>');">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="tours.php" class="text-white">Tours</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $tour['name']; ?></li>
                    </ol>
                </nav>
                <h1 class="text-white mt-2"><?php echo $tour['name']; ?></h1>
                <div class="d-flex align-items-center mt-2 text-white">
                    <div class="me-3">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?php echo $tour['location']; ?>
                    </div>
                    <div class="me-3">
                        <i class="far fa-clock me-1"></i>
                        <?php echo $tour['duration']; ?>
                    </div>
                    <?php if ($tour['review_count'] > 0): ?>
                        <div>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= round($tour['avg_rating'])): ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-warning"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span class="text-white">(<?php echo $tour['review_count']; ?> đánh giá)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Tour Content -->
        <div class="col-lg-8">
            <!-- Image Gallery -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div id="tourCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#tourCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#tourCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#tourCarousel" data-bs-slide-to="2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="admin/<?php echo $tour['image1']; ?>" class="d-block w-100 tour-carousel-img" alt="<?php echo $tour['name']; ?>">
                            </div>
                            <div class="carousel-item">
                                <img src="admin/<?php echo $tour['image2']; ?>" class="d-block w-100 tour-carousel-img" alt="<?php echo $tour['name']; ?>">
                            </div>
                            <div class="carousel-item">
                                <img src="admin/<?php echo $tour['image3']; ?>" class="d-block w-100 tour-carousel-img" alt="<?php echo $tour['name']; ?>">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#tourCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#tourCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tour Description -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="card-title mb-0">Mô tả tour</h4>
                </div>
                <div class="card-body">
                    <div class="tour-description">
                        <?php
                        // Chuyển đổi xuống dòng thành HTML
                        echo nl2br($tour['description']);
                        ?>
                    </div>
                </div>
            </div>

            <!-- Tour Reviews -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Đánh giá từ khách hàng</h4>
                    <span class="badge bg-primary rounded-pill"><?php echo count($reviews); ?></span>
                </div>
                <div class="card-body">
                    <?php if (count($reviews) > 0): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <img src="admin/assets/img/user.jpg" class="testimonial-img-small rounded-circle mb-3" alt="Khách hàng">
                                        <h5 class="mb-0"><?php echo $review['full_name']; ?></h5>
                                        <div class="text-muted small">
                                            <i class="far fa-clock me-1"></i>
                                            <?php echo date('d/m/Y', strtotime($review['review_date'])); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $review['rating']): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-warning"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="mb-0"><?php echo $review['comment']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                            <p>Chưa có đánh giá nào cho tour này.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="card-title mb-0">Đặt tour</h4>
                </div>
                <div class="card-body">
                    <div class="price-info mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Người lớn (trên 15 tuổi):</span>
                            <span class="text-primary fw-bold"><?php echo number_format($tour['price_adult'], 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Trẻ em (từ 6-15 tuổi):</span>
                            <span class="text-primary fw-bold"><?php echo number_format($tour['price_child'], 0, ',', '.'); ?>đ</span>
                        </div>
                        <!-- <div class="d-flex justify-content-between mb-2">
                            <span>Em bé (dưới 6 tuổi):</span>
                            <span class="text-primary fw-bold">Miễn phí</span>
                        </div> -->
                    </div>

                    <form method="post" action="tour-detail.php?id=<?php echo $tourId; ?>" id="booking-form">
                        <!-- Departure Date -->
                        <div class="mb-3">
                            <label for="date_id" class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                            <select class="form-select" id="date_id" name="date_id" required>
                                <option value="">Chọn ngày khởi hành</option>
                                <?php foreach ($availableDates as $date): ?>
                                    <option value="<?php echo $date['date_id']; ?>" data-seats="<?php echo $date['available_seats']; ?>">
                                        <?php echo date('d/m/Y', strtotime($date['departure_date'])); ?> (còn <?php echo $date['available_seats']; ?> chỗ)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($availableDates)): ?>
                                <div class="form-text text-danger">Không có ngày khởi hành nào có sẵn</div>
                            <?php endif; ?>
                        </div>

                        <!-- Number of People -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="num_adults" class="form-label">Người lớn <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementValue('num_adults')">-</button>
                                    <input type="number" class="form-control text-center" id="num_adults" name="num_adults" value="1" min="1" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementValue('num_adults')">+</button>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="num_children" class="form-label">Trẻ em</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementValue('num_children')">-</button>
                                    <input type="number" class="form-control text-center" id="num_children" name="num_children" value="0" min="0">
                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementValue('num_children')">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Yêu cầu đặc biệt, thông tin liên hệ,..."></textarea>
                        </div>

                        <!-- Total Price -->
                        <div class="total-price p-3 bg-light rounded mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Tổng tiền:</span>
                                <span class="text-primary fw-bold fs-5" id="total-price">0đ</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" name="book_tour" class="btn btn-primary" <?php if (empty($availableDates)) echo "disabled"; ?>>
                                Đặt tour ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Share and Contact -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Cần hỗ trợ?</h5>
                    <div class="d-grid gap-2">
                        <a href="tel:0123456789" class="btn btn-outline-primary">
                            <i class="fas fa-phone-alt me-2"></i>Gọi ngay: 0123.456.789
                        </a>
                        <a href="mailto:support@travelnest.com" class="btn btn-outline-secondary">
                            <i class="fas fa-envelope me-2"></i>Email: support@travelnest.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tour-header {
        background-size: cover;
        background-position: center;
        position: relative;
        padding: 100px 0 50px;
    }

    .tour-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    .tour-header .container {
        position: relative;
        z-index: 1;
    }

    .tour-carousel-img {
        height: 400px;
        object-fit: cover;
    }

    .review-item:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    .tour-description {
        white-space: pre-line;
    }

    .testimonial-img-small {
        width: 50px;
        height: 50px;
    }

    @media (max-width: 992px) {
        .tour-carousel-img {
            height: 300px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateTotalPrice();

        // Cập nhật tổng tiền khi thay đổi số lượng
        document.getElementById('num_adults').addEventListener('change', updateTotalPrice);
        document.getElementById('num_children').addEventListener('change', updateTotalPrice);

        // Cập nhật số lượng tối đa khi chọn ngày
        document.getElementById('date_id').addEventListener('change', function() {
            updateMaxPeople();
            updateTotalPrice();
        });

        // Khởi tạo ban đầu
        updateMaxPeople();
    });

    function updateMaxPeople() {
        const dateSelect = document.getElementById('date_id');
        const adultInput = document.getElementById('num_adults');
        const childrenInput = document.getElementById('num_children');

        if (dateSelect.value) {
            const selectedOption = dateSelect.options[dateSelect.selectedIndex];
            const availableSeats = parseInt(selectedOption.getAttribute('data-seats'));

            // Cập nhật số lượng tối đa dựa trên số chỗ có sẵn
            const currentAdults = parseInt(adultInput.value) || 0;
            const currentChildren = parseInt(childrenInput.value) || 0;

            // Đảm bảo số lượng người lớn + trẻ em không vượt quá số chỗ có sẵn
            if (currentAdults + currentChildren > availableSeats) {
                // Nếu vượt quá, ưu tiên giữ người lớn
                adultInput.value = Math.min(currentAdults, availableSeats);
                childrenInput.value = Math.max(0, availableSeats - currentAdults);
            }
        }
    }

    function incrementValue(id) {
        const input = document.getElementById(id);
        const currentValue = parseInt(input.value) || 0;
        const dateSelect = document.getElementById('date_id');

        if (dateSelect.value) {
            const selectedOption = dateSelect.options[dateSelect.selectedIndex];
            const availableSeats = parseInt(selectedOption.getAttribute('data-seats'));

            const adultInput = document.getElementById('num_adults');
            const childrenInput = document.getElementById('num_children');

            const currentAdults = parseInt(adultInput.value) || 0;
            const currentChildren = parseInt(childrenInput.value) || 0;

            // Kiểm tra xem việc tăng thêm có vượt quá số chỗ có sẵn không
            if (currentAdults + currentChildren < availableSeats) {
                input.value = currentValue + 1;
                updateTotalPrice();
            } else {
                alert(`Không thể thêm. Tổng số người không được vượt quá ${availableSeats} (số chỗ còn trống).`);
            }
        } else {
            alert('Vui lòng chọn ngày khởi hành trước.');
        }
    }

    function decrementValue(id) {
        const input = document.getElementById(id);
        const currentValue = parseInt(input.value) || 0;
        const min = parseInt(input.getAttribute('min')) || 0;

        if (currentValue > min) {
            input.value = currentValue - 1;
            updateTotalPrice();
        }
    }

    function updateTotalPrice() {
        const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
        const numChildren = parseInt(document.getElementById('num_children').value) || 0;
        const priceAdult = <?php echo $tour['price_adult']; ?>;
        const priceChild = <?php echo $tour['price_child']; ?>;

        const totalPrice = (numAdults * priceAdult) + (numChildren * priceChild);

        document.getElementById('total-price').textContent = totalPrice.toLocaleString('vi-VN') + 'đ';
    }

    // Validate form before submission
    document.getElementById('booking-form').addEventListener('submit', function(event) {
        const dateId = document.getElementById('date_id').value;
        const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
        const numChildren = parseInt(document.getElementById('num_children').value) || 0;

        if (!dateId) {
            alert('Vui lòng chọn ngày khởi hành.');
            event.preventDefault();
            return;
        }

        if (numAdults < 1) {
            alert('Số lượng người lớn phải ít nhất là 1.');
            event.preventDefault();
            return;
        }

        const option = document.querySelector(`#date_id option[value="${dateId}"]`);
        const availableSeats = parseInt(option.getAttribute('data-seats'));

        if (numAdults + numChildren > availableSeats) {
            alert(`Số lượng chỗ trống không đủ. Chỉ còn ${availableSeats} chỗ.`);
            event.preventDefault();
            return;
        }
    });
</script>

<?php include 'layouts/footer.php'; ?>