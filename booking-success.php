<?php
include 'config/config.php';
include 'functions/auth_functions.php';

// Chuyển hướng nếu chưa đăng nhập
redirectIfNotLoggedIn();

// Kiểm tra booking_id
if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    header('Location: my-bookings.php');
    exit();
}

$bookingId = $_GET['booking_id'];
$userId = $_SESSION['user_id'];

// Lấy thông tin đơn đặt tour
$stmt = $conn->prepare("
    SELECT b.*, t.name as tour_name, t.location, t.image1, td.departure_date
    FROM bookings b
    INNER JOIN tours t ON b.tour_id = t.tour_id
    INNER JOIN tour_dates td ON b.date_id = td.date_id
    WHERE b.booking_id = ? AND b.user_id = ? AND b.status = 'confirmed'
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

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

include 'layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-3">Đặt tour thành công!</h2>
                    <p class="lead text-muted mb-4">Cảm ơn bạn đã đặt tour với Travel Nest. Chúng tôi đã gửi thông tin chi tiết qua email của bạn.</p>
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fa-2x"></i>
                            <div>
                                <h5 class="alert-heading">Mã đơn đặt tour: #<?php echo $booking['booking_id']; ?></h5>
                                <p class="mb-0">Vui lòng lưu lại mã đơn này để thuận tiện tra cứu.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Thông tin tour</h5>
                                    <p><strong><?php echo $booking['tour_name']; ?></strong></p>
                                    <p><i class="fas fa-map-marker-alt me-2"></i><?php echo $booking['location']; ?></p>
                                    <p><i class="fas fa-calendar-alt me-2"></i>Ngày khởi hành: <?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?></p>
                                    <p><i class="fas fa-users me-2"></i><?php echo $booking['num_adults']; ?> người lớn, <?php echo $booking['num_children']; ?> trẻ em</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Thông tin thanh toán</h5>
                                    <p><strong>Tổng tiền:</strong> <?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ</p>
                                    <p><strong>Trạng thái:</strong> <span class="badge bg-success">Đã xác nhận</span></p>
                                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($booking['booking_date'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                        <a href="my-bookings.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-list-alt me-2"></i>Xem đơn đặt tour
                        </a>
                        <a href="index.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Trang chủ
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Hỗ trợ -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Bạn cần hỗ trợ?</h5>
                    <p>Nếu bạn có bất kỳ câu hỏi nào về đơn đặt tour, vui lòng liên hệ với chúng tôi.</p>
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <a href="tel:0123456789" class="btn btn-outline-dark">
                            <i class="fas fa-phone-alt me-2"></i>0123.456.789
                        </a>
                        <a href="mailto:support@travelnest.com" class="btn btn-outline-dark">
                            <i class="fas fa-envelope me-2"></i>support@travelnest.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?> 