<?php
require_once '../config/config.php';
include 'layouts/header.php';

// Lấy số lượng thống kê từ database
// Trong thực tế, bạn sẽ truy vấn database để lấy dữ liệu thực

// Tổng số tour
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tours");
$stmt->execute();
$toursResult = $stmt->get_result();
$totalTours = $toursResult->fetch_assoc()['total'];

// Tổng số đơn đặt tour
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings");
$stmt->execute();
$bookingsResult = $stmt->get_result();
$totalBookings = $bookingsResult->fetch_assoc()['total'];

// Tổng số người dùng
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$stmt->execute();
$usersResult = $stmt->get_result();
$totalUsers = $usersResult->fetch_assoc()['total'];

// Tổng số đánh giá
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM reviews");
$stmt->execute();
$reviewsResult = $stmt->get_result();
$totalReviews = $reviewsResult->fetch_assoc()['total'];

// Lấy các đơn đặt tour gần đây
$stmt = $conn->prepare("
    SELECT b.booking_id, b.booking_date, b.status, b.total_price,
           u.full_name, u.email,
           t.name as tour_name
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.user_id
    INNER JOIN tours t ON b.tour_id = t.tour_id
    ORDER BY b.booking_date DESC
    LIMIT 5
");
$stmt->execute();
$recentBookings = $stmt->get_result();

// Lấy các đánh giá gần đây
$stmt = $conn->prepare("
    SELECT r.review_id, r.review_date, r.rating, r.comment,
           u.full_name,
           t.name as tour_name
    FROM reviews r
    INNER JOIN users u ON r.user_id = u.user_id
    INNER JOIN tours t ON r.tour_id = t.tour_id
    ORDER BY r.review_date DESC
    LIMIT 5
");
$stmt->execute();
$recentReviews = $stmt->get_result();
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tổng quan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Tổng quan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row">
            <!-- Tours Stats -->
            <div class="col-md-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon bg-primary-light">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="stats-text">Tổng số Tour</div>
                    <div class="stats-number counter-value" data-target="<?php echo $totalTours; ?>">0</div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Bookings Stats -->
            <div class="col-md-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon bg-success-light">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stats-text">Đơn đặt tour</div>
                    <div class="stats-number counter-value" data-target="<?php echo $totalBookings; ?>">0</div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Users Stats -->
            <div class="col-md-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon bg-warning-light">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-text">Khách hàng</div>
                    <div class="stats-number counter-value" data-target="<?php echo $totalUsers; ?>">0</div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 80%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Stats -->
            <div class="col-md-6 col-lg-3">
                <div class="stats-card">
                    <div class="stats-icon bg-info-light">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stats-text">Đánh giá</div>
                    <div class="stats-number counter-value" data-target="<?php echo $totalReviews; ?>">0</div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="row">
            <!-- Recent Bookings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Đơn đặt tour gần đây</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Tour</th>
                                        <th>Trạng thái</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recentBookings->num_rows > 0): ?>
                                        <?php while ($booking = $recentBookings->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?php echo $booking['booking_id']; ?></td>
                                                <td data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $booking['email']; ?>">
                                                    <?php echo $booking['full_name']; ?>
                                                </td>
                                                <td><?php echo $booking['tour_name']; ?></td>
                                                <td>
                                                    <?php
                                                        $statusClass = '';
                                                        switch ($booking['status']) {
                                                            case 'pending':
                                                                $statusClass = 'status-pending';
                                                                $statusText = 'Chờ xác nhận';
                                                                break;
                                                            case 'confirmed':
                                                                $statusClass = 'status-confirmed';
                                                                $statusText = 'Đã xác nhận';
                                                                break;
                                                            case 'completed':
                                                                $statusClass = 'status-completed';
                                                                $statusText = 'Hoàn thành';
                                                                break;
                                                            case 'cancelled':
                                                                $statusClass = 'status-cancelled';
                                                                $statusText = 'Đã hủy';
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="status-badge <?php echo $statusClass; ?>">
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-3">Không có đơn đặt tour nào.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="bookings.php" class="btn btn-sm btn-primary">Xem tất cả</a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Reviews -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Đánh giá gần đây</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($recentReviews->num_rows > 0): ?>
                            <?php while ($review = $recentReviews->fetch_assoc()): ?>
                                <div class="activity-item">
                                    <div class="activity-icon bg-warning-light">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">
                                            <?php echo $review['full_name']; ?> đã đánh giá <?php echo $review['tour_name']; ?>
                                            <div class="float-end">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= $review['rating']): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-warning"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p class="mb-1"><?php echo substr($review['comment'], 0, 100); ?>...</p>
                                        <div class="activity-time">
                                            <?php echo date('d/m/Y H:i', strtotime($review['review_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center py-3">Không có đánh giá nào.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="reviews.php" class="btn btn-sm btn-primary">Xem tất cả</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin JS -->
<script src="assets/js/admin.js"></script>

<?php include 'layouts/footer.php'; ?>
