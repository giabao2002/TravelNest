<?php
include 'config/config.php';
include 'functions/auth_functions.php';

// Chuyển hướng nếu chưa đăng nhập
redirectIfNotLoggedIn();

// Khởi tạo biến
$userId = $_SESSION['user_id'];
$success = $error = '';
$booking = null;
$tour = null;

// Kiểm tra có booking_id hay không
if (!isset($_GET['booking_id'])) {
    header('Location: my-bookings.php');
    exit();
}

$bookingId = $_GET['booking_id'];

// Lấy thông tin đơn đặt tour
$stmt = $conn->prepare("
    SELECT b.*, t.name as tour_name, t.image1, t.location, td.departure_date
    FROM bookings b
    INNER JOIN tours t ON b.tour_id = t.tour_id
    INNER JOIN tour_dates td ON b.date_id = td.date_id
    WHERE b.booking_id = ? AND b.user_id = ? AND b.status = 'completed'
");
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra đơn đặt tour có tồn tại và có phải của người dùng hiện tại không
if ($result->num_rows === 0) {
    header('Location: my-bookings.php');
    exit();
}

$booking = $result->fetch_assoc();

// Kiểm tra đã đánh giá chưa
$stmt = $conn->prepare("SELECT * FROM reviews WHERE booking_id = ?");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$checkReview = $stmt->get_result();

if ($checkReview->num_rows > 0) {
    header('Location: my-bookings.php');
    exit();
}

// Xử lý gửi đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    // Validate
    if ($rating < 1 || $rating > 5) {
        $error = "Vui lòng chọn đánh giá từ 1 đến 5 sao.";
    } elseif (empty($comment)) {
        $error = "Vui lòng nhập nội dung đánh giá.";
    } else {
        // Thêm đánh giá mới
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, tour_id, booking_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $userId, $booking['tour_id'], $bookingId, $rating, $comment);
        
        if ($stmt->execute()) {
            $success = "Cảm ơn bạn đã gửi đánh giá!";
            
            // Chuyển hướng ngay lập tức
            header("Location: my-bookings.php");
            exit();
        } else {
            $error = "Có lỗi xảy ra: " . $conn->error;
        }
    }
}

include 'layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="my-bookings.php" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="mb-0">Đánh giá tour</h2>
            </div>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="admin/<?php echo $booking['image1']; ?>" class="img-fluid rounded" alt="<?php echo $booking['tour_name']; ?>">
                        </div>
                        <div class="col-md-8">
                            <h4 class="card-title"><?php echo $booking['tour_name']; ?></h4>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt me-2"></i><?php echo $booking['location']; ?>
                            </p>
                            <p>
                                <i class="fas fa-calendar-alt me-2"></i>Ngày khởi hành: <?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?>
                            </p>
                            <p>
                                <i class="fas fa-users me-2"></i><?php echo $booking['num_adults']; ?> người lớn, <?php echo $booking['num_children']; ?> trẻ em
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Đánh giá trải nghiệm của bạn</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-4 text-center">
                            <p class="mb-2">Bạn đánh giá tour này bao nhiêu sao?</p>
                            <div class="rating-stars mb-2">
                                <input type="radio" id="star5" name="rating" value="5" <?php if (isset($_POST['rating']) && $_POST['rating'] == 5) echo 'checked'; ?>>
                                <label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" id="star4" name="rating" value="4" <?php if (isset($_POST['rating']) && $_POST['rating'] == 4) echo 'checked'; ?>>
                                <label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" id="star3" name="rating" value="3" <?php if (isset($_POST['rating']) && $_POST['rating'] == 3) echo 'checked'; ?>>
                                <label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" id="star2" name="rating" value="2" <?php if (isset($_POST['rating']) && $_POST['rating'] == 2) echo 'checked'; ?>>
                                <label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" id="star1" name="rating" value="1" <?php if (isset($_POST['rating']) && $_POST['rating'] == 1) echo 'checked'; ?>>
                                <label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                            </div>
                            <div id="rating-text" class="text-muted small"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment" class="form-label">Chia sẻ trải nghiệm của bạn</label>
                            <textarea class="form-control" id="comment" name="comment" rows="5" placeholder="Hãy chia sẻ những trải nghiệm, cảm nhận của bạn về tour du lịch này..."><?php echo isset($_POST['comment']) ? $_POST['comment'] : ''; ?></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>

<style>
    .rating-stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
    }
    
    .rating-stars input {
        display: none;
    }
    
    .rating-stars label {
        font-size: 30px;
        color: #ddd;
        cursor: pointer;
        padding: 0 5px;
    }
    
    .rating-stars input:checked ~ label {
        color: #ffc107;
    }
    
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #ffc107;
    }
</style>

<script>
    // Tự động ẩn thông báo sau 3 giây
    setTimeout(function() {
        $('.alert').alert('close');
    }, 3000);
    
    // Hiển thị text theo số sao đánh giá
    const starInputs = document.querySelectorAll('input[name="rating"]');
    const ratingText = document.getElementById('rating-text');
    
    const ratingDescriptions = {
        1: "Rất không hài lòng",
        2: "Không hài lòng",
        3: "Bình thường",
        4: "Hài lòng",
        5: "Rất hài lòng"
    };
    
    // Hiển thị mô tả ban đầu nếu đã chọn
    const checkedInput = document.querySelector('input[name="rating"]:checked');
    if (checkedInput) {
        ratingText.textContent = ratingDescriptions[checkedInput.value];
    }
    
    // Cập nhật mô tả khi chọn số sao
    starInputs.forEach(input => {
        input.addEventListener('change', function() {
            ratingText.textContent = ratingDescriptions[this.value];
        });
    });
</script> 