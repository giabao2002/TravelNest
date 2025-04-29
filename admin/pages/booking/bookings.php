<?php
require_once '../../../config/config.php';
require_once '../../../functions/auth_functions.php';

// Xác thực admin
redirectIfNotAdmin();

// Xử lý hủy đơn đặt tour
if (isset($_POST['cancel_booking']) && isset($_POST['booking_id'])) {
    $bookingId = $_POST['booking_id'];
    
    // Get the booking details first to update available seats
    $stmt = $conn->prepare("
        SELECT b.tour_id, b.date_id, b.num_adults, b.num_children, b.status
        FROM bookings b
        WHERE b.booking_id = ?
    ");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    // Only proceed if the booking status is 'confirmed'
    if ($booking && $booking['status'] == 'confirmed') {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Update booking status to cancelled
            $updateStmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?");
            $updateStmt->bind_param("i", $bookingId);
            $updateStmt->execute();
            
            // Increase available seats in tour_dates
            $totalPeople = $booking['num_adults'] + $booking['num_children'];
            $updateSeatsStmt = $conn->prepare("
                UPDATE tour_dates 
                SET available_seats = available_seats + ? 
                WHERE date_id = ?
            ");
            $updateSeatsStmt->bind_param("ii", $totalPeople, $booking['date_id']);
            $updateSeatsStmt->execute();
            
            // Commit transaction
            $conn->commit();
            $success = "Đã hủy đơn đặt tour thành công và cập nhật số chỗ trống.";
        } catch (Exception $e) {
            // Rollback in case of error
            $conn->rollback();
            $error = "Có lỗi xảy ra: " . $e->getMessage();
        }
    } else {
        $error = "Không thể hủy đơn đặt tour. Chỉ đơn đã xác nhận mới có thể bị hủy.";
    }
}

// Xử lý xác nhận đơn đặt tour
if (isset($_POST['confirm_booking']) && isset($_POST['booking_id'])) {
    $bookingId = $_POST['booking_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE booking_id = ?");
    $stmt->bind_param("i", $bookingId);
    
    if ($stmt->execute()) {
        $success = "Đã xác nhận đơn đặt tour thành công.";
    } else {
        $error = "Có lỗi xảy ra: " . $conn->error;
    }
}

// Lấy danh sách đơn đặt tour
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$whereClause = '';

if ($filter === 'confirmed') {
    $whereClause = "WHERE b.status = 'confirmed'";
} elseif ($filter === 'completed') {
    $whereClause = "WHERE b.status = 'completed'";
} elseif ($filter === 'cancelled') {
    $whereClause = "WHERE b.status = 'cancelled'";
}

$stmt = $conn->prepare("
    SELECT b.booking_id, b.booking_date, b.status, b.total_price, 
           b.num_adults, b.num_children, b.notes,
           u.user_id, u.full_name, u.email, u.phone,
           t.tour_id, t.name as tour_name, t.location,
           td.departure_date
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.user_id
    INNER JOIN tours t ON b.tour_id = t.tour_id
    INNER JOIN tour_dates td ON b.date_id = td.date_id
    $whereClause
    ORDER BY b.booking_date DESC
");
$stmt->execute();
$bookings = $stmt->get_result();
?>

<?php include '../../layouts/header.php'; ?>

<body>
    <?php include '../../layouts/navbar.php'; ?>
    <?php include '../../layouts/sidebar.php'; ?>

    <div class="content-wrapper" id="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Quản lý đơn đặt tour</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Đơn đặt tour</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Danh sách đơn đặt tour</h3>
                            <div class="btn-group">
                                <a href="?filter=all" class="btn btn-<?php echo $filter === 'all' ? 'primary' : 'outline-primary'; ?>">Tất cả</a>
                                <a href="?filter=confirmed" class="btn btn-<?php echo $filter === 'confirmed' ? 'primary' : 'outline-primary'; ?>">Đã xác nhận</a>
                                <a href="?filter=completed" class="btn btn-<?php echo $filter === 'completed' ? 'primary' : 'outline-primary'; ?>">Hoàn thành</a>
                                <a href="?filter=cancelled" class="btn btn-<?php echo $filter === 'cancelled' ? 'primary' : 'outline-primary'; ?>">Đã hủy</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Tour</th>
                                        <th>Ngày khởi hành</th>
                                        <th>Số lượng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đặt</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($bookings->num_rows > 0): ?>
                                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?php echo $booking['booking_id']; ?></td>
                                                <td>
                                                    <?php echo $booking['full_name']; ?><br>
                                                    <small class="text-muted"><?php echo $booking['email']; ?></small>
                                                </td>
                                                <!-- Giới hạn 50 ký tự, nếu dài hơn thì thay bằng ... -->
                                                <td><?php echo substr($booking['tour_name'], 0, 40) . '...'; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?></td>
                                                <td>
                                                    <?php echo $booking['num_adults']; ?> người lớn<br>
                                                    <?php echo $booking['num_children']; ?> trẻ em
                                                </td>
                                                <td><?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ</td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($booking['status']) {
                                                        case 'confirmed':
                                                            $statusClass = 'bg-success';
                                                            $statusText = 'Đã xác nhận';
                                                            break;
                                                        case 'completed':
                                                            $statusClass = 'bg-primary';
                                                            $statusText = 'Hoàn thành';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'bg-danger';
                                                            $statusText = 'Đã hủy';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($booking['booking_date'])); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $booking['booking_id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($booking['status'] === 'confirmed'): ?>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn đặt tour này?');">
                                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                            <button type="submit" name="cancel_booking" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            
                                            <!-- Detail Modal -->
                                            <div class="modal fade" id="detailModal<?php echo $booking['booking_id']; ?>" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="detailModalLabel">Chi tiết đơn đặt tour #<?php echo $booking['booking_id']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6 class="fw-bold">Thông tin khách hàng</h6>
                                                                    <p><strong>Họ tên:</strong> <?php echo $booking['full_name']; ?></p>
                                                                    <p><strong>Email:</strong> <?php echo $booking['email']; ?></p>
                                                                    <p><strong>Số điện thoại:</strong> <?php echo $booking['phone']; ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6 class="fw-bold">Thông tin tour</h6>
                                                                    <p><strong>Tour:</strong> <?php echo $booking['tour_name']; ?></p>
                                                                    <p><strong>Địa điểm:</strong> <?php echo $booking['location']; ?></p>
                                                                    <p><strong>Ngày khởi hành:</strong> <?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?></p>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6 class="fw-bold">Chi tiết đặt tour</h6>
                                                                    <p><strong>Số lượng:</strong> <?php echo $booking['num_adults']; ?> người lớn, <?php echo $booking['num_children']; ?> trẻ em</p>
                                                                    <p><strong>Tổng tiền:</strong> <?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ</p>
                                                                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($booking['booking_date'])); ?></p>
                                                                    <p><strong>Trạng thái:</strong> <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6 class="fw-bold">Ghi chú</h6>
                                                                    <p><?php echo empty($booking['notes']) ? 'Không có ghi chú' : $booking['notes']; ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <?php if ($booking['status'] === 'confirmed'): ?>
                                                                <form method="post" class="d-inline">
                                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                                    <button type="submit" name="cancel_booking" class="btn btn-danger">Hủy đơn</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-3">Không có đơn đặt tour nào.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../layouts/footer.php'; ?>

    <script>
        // Tự động ẩn thông báo sau 3 giây
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    </script>
</body>
</html> 