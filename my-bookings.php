<?php
include 'config/config.php';
include 'functions/auth_functions.php';

// Chuyển hướng nếu chưa đăng nhập
redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$success = $error = '';

// Xử lý hủy đơn đặt tour
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $bookingId = $_GET['cancel'];
    
    // Kiểm tra đơn đặt tour có tồn tại và thuộc về người dùng hiện tại
    $stmt = $conn->prepare("
        SELECT b.*, td.available_seats, td.date_id
        FROM bookings b
        JOIN tour_dates td ON b.date_id = td.date_id
        WHERE b.booking_id = ? AND b.user_id = ? AND b.status = 'confirmed'
    ");
    $stmt->bind_param("ii", $bookingId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        $dateId = $booking['date_id'];
        $numPeople = $booking['num_adults'] + $booking['num_children'];
        $newAvailableSeats = $booking['available_seats'] + $numPeople;
        
        // Cập nhật trạng thái đơn đặt tour thành đã hủy
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        // Cập nhật lại số chỗ trống
        $stmt = $conn->prepare("UPDATE tour_dates SET available_seats = ? WHERE date_id = ?");
        $stmt->bind_param("ii", $newAvailableSeats, $dateId);
        $stmt->execute();
        
        $success = "Đã hủy đơn đặt tour thành công.";
    } else {
        $error = "Không thể hủy đơn đặt tour này.";
    }
}

// Lấy danh sách đơn đặt tour của người dùng
$stmt = $conn->prepare("
    SELECT b.booking_id, b.booking_date, b.status, b.total_price, 
           b.num_adults, b.num_children, b.notes,
           t.tour_id, t.name as tour_name, t.location, t.image1,
           td.departure_date,
           (SELECT COUNT(*) FROM reviews r WHERE r.booking_id = b.booking_id) as has_review
    FROM bookings b
    INNER JOIN tours t ON b.tour_id = t.tour_id
    INNER JOIN tour_dates td ON b.date_id = td.date_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookings = $stmt->get_result();

include 'layouts/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Tour đã đặt</h2>
                <a href="tours.php" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i>Tìm tour mới
                </a>
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
            
            <?php if ($bookings->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tour</th>
                            <th>Ngày khởi hành</th>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><a href="tour-detail.php?id=<?php echo $booking['tour_id']; ?>"><?php echo $booking['tour_name']; ?></a></td>
                                <td><?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?></td>
                                <td>
                                    <?php echo $booking['num_adults']; ?> người lớn
                                    <?php if ($booking['num_children'] > 0): ?>, <?php echo $booking['num_children']; ?> trẻ em<?php endif; ?>
                                </td>
                                <td><?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ</td>
                                <td>
                                    <?php
                                    $statusClass = $statusText = '';
                                    switch ($booking['status']) {
                                        case 'confirmed':
                                            $statusClass = 'bg-success';
                                            $statusText = 'Đã xác nhận';
                                            break;
                                        case 'completed':
                                            $statusClass = 'bg-primary';
                                            $statusText = 'Đã hoàn thành';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'bg-danger';
                                            $statusText = 'Đã hủy';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info mb-1" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $booking['booking_id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <?php if ($booking['status'] === 'confirmed'): ?>
                                        <a href="?cancel=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt tour này?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($booking['status'] === 'completed' && !$booking['has_review']): ?>
                                        <a href="review.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-warning mb-1">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Modal hiển thị chi tiết đơn đặt tour -->
                            <div class="modal fade" id="detailModal<?php echo $booking['booking_id']; ?>" tabindex="-1" aria-labelledby="detailModalLabel<?php echo $booking['booking_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detailModalLabel<?php echo $booking['booking_id']; ?>">Chi tiết đơn đặt tour</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <img src="admin/<?php echo $booking['image1']; ?>" class="img-fluid rounded" alt="<?php echo $booking['tour_name']; ?>">
                                                </div>
                                                <div class="col-md-8">
                                                    <h4><?php echo $booking['tour_name']; ?></h4>
                                                    <p>
                                                        <i class="fas fa-map-marker-alt me-2"></i><?php echo $booking['location']; ?>
                                                    </p>
                                                    <div class="mb-3">
                                                        <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                        <span class="badge bg-secondary">Mã đơn: #<?php echo $booking['booking_id']; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <h5>Thông tin đơn đặt</h5>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                            <span>Ngày khởi hành:</span>
                                                            <span><?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?></span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                            <span>Số lượng:</span>
                                                            <span><?php echo $booking['num_adults']; ?> người lớn<?php if ($booking['num_children'] > 0): ?>, <?php echo $booking['num_children']; ?> trẻ em<?php endif; ?></span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                            <span>Tổng tiền:</span>
                                                            <span class="fw-bold"><?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                            <span>Ngày đặt:</span>
                                                            <span><?php echo date('d/m/Y H:i', strtotime($booking['booking_date'])); ?></span>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>Ghi chú</h5>
                                                    <div class="p-3 bg-light rounded">
                                                        <?php echo empty($booking['notes']) ? 'Không có ghi chú' : $booking['notes']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <?php if ($booking['status'] === 'confirmed'): ?>
                                                <a href="?cancel=<?php echo $booking['booking_id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt tour này?')">
                                                    <i class="fas fa-times me-2"></i>Hủy đơn
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($booking['status'] === 'completed' && !$booking['has_review']): ?>
                                                <a href="review.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-warning">
                                                    <i class="fas fa-star me-2"></i>Đánh giá
                                                </a>
                                            <?php endif; ?>
                                            
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                    <h4>Bạn chưa đặt tour nào</h4>
                    <p class="text-muted">Khám phá các tour du lịch hấp dẫn và đặt tour ngay hôm nay!</p>
                    <a href="tours.php" class="btn btn-primary mt-3">Tìm tour ngay</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>

<script>
    // Tự động ẩn thông báo sau 3 giây
    setTimeout(function() {
        $('.alert').alert('close');
    }, 3000);
</script> 