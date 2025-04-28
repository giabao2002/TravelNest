<?php
include 'config/config.php';
include 'functions/auth_functions.php';

// Chuyển hướng nếu chưa đăng nhập
redirectIfNotLoggedIn();

// Kiểm tra xem có dữ liệu đặt tour trong session không
if (!isset($_SESSION['booking_data'])) {
    header('Location: tours.php');
    exit();
}

$userId = $_SESSION['user_id'];
$bookingData = $_SESSION['booking_data'];
$success = $error = '';

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Lưu thông tin đặt tour vào database
        $stmt = $conn->prepare("
            INSERT INTO bookings (user_id, tour_id, date_id, num_adults, num_children, total_price, notes, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')
        ");
        $stmt->bind_param(
            "iiiiids", 
            $bookingData['user_id'], 
            $bookingData['tour_id'], 
            $bookingData['date_id'], 
            $bookingData['num_adults'], 
            $bookingData['num_children'], 
            $bookingData['total_price'], 
            $bookingData['notes']
        );
        
        if ($stmt->execute()) {
            $bookingId = $conn->insert_id;
            
            // Cập nhật số chỗ trống
            $totalPeople = $bookingData['num_adults'] + $bookingData['num_children'];
            $newAvailableSeats = $bookingData['available_seats'] - $totalPeople;
            $updateStmt = $conn->prepare("UPDATE tour_dates SET available_seats = ? WHERE date_id = ?");
            $updateStmt->bind_param("ii", $newAvailableSeats, $bookingData['date_id']);
            $updateStmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            // Xóa dữ liệu đặt tour khỏi session
            unset($_SESSION['booking_data']);
            
            // Chuyển hướng đến trang thành công
            header("Location: booking-success.php?booking_id=" . $bookingId);
            exit();
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $conn->rollback();
        $error = "Có lỗi xảy ra khi xử lý thanh toán: " . $e->getMessage();
    }
}

include 'layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center mb-4">
                <a href="tour-detail.php?id=<?php echo $bookingData['tour_id']; ?>" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="mb-0">Thanh toán đơn đặt tour</h2>
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
            
            <div class="row">
                <!-- Thông tin đơn đặt tour -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h4 class="card-title mb-0">Thông tin đơn đặt tour</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <img src="admin/<?php echo $bookingData['tour_image']; ?>" class="img-fluid rounded" alt="<?php echo $bookingData['tour_name']; ?>">
                                </div>
                                <div class="col-md-8">
                                    <h5 class="card-title"><?php echo $bookingData['tour_name']; ?></h5>
                                    <p class="text-muted">
                                        <i class="fas fa-map-marker-alt me-2"></i><?php echo $bookingData['tour_location']; ?>
                                    </p>
                                    <p>
                                        <i class="fas fa-calendar-alt me-2"></i>Ngày khởi hành: <?php echo date('d/m/Y', strtotime($bookingData['departure_date'])); ?>
                                    </p>
                                    <p>
                                        <i class="fas fa-users me-2"></i><?php echo $bookingData['num_adults']; ?> người lớn, <?php echo $bookingData['num_children']; ?> trẻ em
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Trạng thái:</span>
                                        <span class="badge bg-warning">Chờ thanh toán</span>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">Thông tin khách hàng</h5>
                                    <p><strong>Họ tên:</strong> <?php echo $user['full_name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                                    <p><strong>Số điện thoại:</strong> <?php echo $user['phone']; ?></p>
                                    <p><strong>Địa chỉ:</strong> <?php echo empty($user['address']) ? 'Chưa cung cấp' : $user['address']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">Ghi chú</h5>
                                    <p><?php echo empty($bookingData['notes']) ? 'Không có ghi chú' : $bookingData['notes']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Thông tin thanh toán -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h4 class="card-title mb-0">Thông tin thanh toán</h4>
                        </div>
                        <div class="card-body">
                            <div class="price-details mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Người lớn (<?php echo $bookingData['num_adults']; ?> x <?php echo number_format($bookingData['total_price'] / ($bookingData['num_adults'] + max(1, $bookingData['num_children'])), 0, ',', '.'); ?>đ)</span>
                                    <span><?php echo number_format($bookingData['total_price'] * $bookingData['num_adults'] / ($bookingData['num_adults'] + max(1, $bookingData['num_children'])), 0, ',', '.'); ?>đ</span>
                                </div>
                                <?php if ($bookingData['num_children'] > 0): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Trẻ em (<?php echo $bookingData['num_children']; ?> x <?php echo number_format($bookingData['total_price'] / (($bookingData['num_adults'] + $bookingData['num_children']) * 2), 0, ',', '.'); ?>đ)</span>
                                    <span><?php echo number_format($bookingData['total_price'] * $bookingData['num_children'] / ($bookingData['num_adults'] + $bookingData['num_children'] * 2), 0, ',', '.'); ?>đ</span>
                                </div>
                                <?php endif; ?>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Tổng cộng:</span>
                                    <span class="text-primary"><?php echo number_format($bookingData['total_price'], 0, ',', '.'); ?>đ</span>
                                </div>
                            </div>
                            
                            <div class="payment-methods mb-4">
                                <h5 class="mb-3">Phương thức thanh toán</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_qr" checked>
                                    <label class="form-check-label" for="payment_qr">
                                        Thanh toán qua mã QR
                                    </label>
                                </div>
                            </div>
                            
                            <div class="payment-qr text-center mb-4">
                                <div class="qr-code p-3 border rounded mb-2">
                                    <img src="assets/images/qr-code.png" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                                </div>
                                <p class="text-muted small">Quét mã QR để thanh toán</p>
                            </div>
                            
                            <form method="post">
                                <div class="d-grid">
                                    <button type="submit" name="confirm_payment" class="btn btn-primary">
                                        <i class="fas fa-check-circle me-2"></i>Xác nhận đã thanh toán
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Hỗ trợ -->
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
    </div>
</div>

<?php include 'layouts/footer.php'; ?> 