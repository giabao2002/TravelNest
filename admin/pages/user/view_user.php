<?php
require_once '../../config/config.php';
require_once '../../functions/user_functions.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$userId = (int)$_GET['id'];
$user = getUserById($conn, $userId);

// If user doesn't exist, redirect to users page
if (!$user) {
    header('Location: users.php');
    exit();
}

// Handle user status update (block/unblock)
if (isset($_POST['update_status'])) {
    $newStatus = $_POST['status'];
    
    if (updateUserStatus($conn, $userId, $newStatus)) {
        $statusMessage = "Cập nhật trạng thái người dùng thành công.";
        // Refresh user data
        $user = getUserById($conn, $userId);
    } else {
        $errorMessage = "Không thể cập nhật trạng thái người dùng.";
    }
}

// Get user bookings
$userBookings = getUserBookings($conn, $userId);

// Page title
$pageTitle = 'Chi tiết Người dùng: ' . htmlspecialchars($user['email']);
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
                        <h1 class="m-0">Chi tiết Người dùng</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="users.php">Quản lý Người dùng</a></li>
                            <li class="breadcrumb-item active">Chi tiết Người dùng</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($statusMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $statusMessage; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $errorMessage; ?>
        </div>
        <?php endif; ?>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- User Info Card -->
                    <div class="col-xl-4 col-md-12 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin Người dùng</h6>
                                
                                <!-- Block/Unblock Form -->
                                <form method="POST" action="view_user.php?id=<?php echo $userId; ?>" class="d-inline" 
                                    onsubmit="return confirm('Bạn có chắc chắn muốn <?php echo $user['status'] == 'active' ? 'chặn' : 'bỏ chặn'; ?> người dùng này?');">
                                    <input type="hidden" name="status" value="<?php echo $user['status'] == 'active' ? 'blocked' : 'active'; ?>">
                                    <button type="submit" name="update_status" class="btn <?php echo $user['status'] == 'active' ? 'btn-danger' : 'btn-success'; ?> btn-sm">
                                        <?php if ($user['status'] == 'active'): ?>
                                            <i class="fas fa-ban"></i> Chặn tài khoản
                                        <?php else: ?>
                                            <i class="fas fa-check"></i> Bỏ chặn tài khoản
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <img class="img-profile rounded-circle" src="../../assets/img/user.jpg" 
                                        style="width: 100px; height: 100px;">
                                    <h4 class="mt-2"><?php echo isset($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Người dùng'; ?></h4>
                                    <p>
                                        <?php if ($user['status'] == 'active'): ?>
                                            <span class="badge badge-success">Đang hoạt động</span>
                                        <?php elseif ($user['status'] == 'blocked'): ?>
                                            <span class="badge badge-danger">Đã chặn</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo htmlspecialchars($user['status']); ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="user-details">
                                    <div class="row mb-2">
                                        <div class="col-5 font-weight-bold">Mã người dùng:</div>
                                        <div class="col-7"><?php echo $user['user_id']; ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 font-weight-bold">Email:</div>
                                        <div class="col-7"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 font-weight-bold">Điện thoại:</div>
                                        <div class="col-7"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Chưa cung cấp'; ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 font-weight-bold">Địa chỉ:</div>
                                        <div class="col-7"><?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : 'Chưa cung cấp'; ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 font-weight-bold">Ngày tham gia:</div>
                                        <div class="col-7"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Booking History -->
                    <div class="col-xl-8 col-md-12 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Tour đã đặt</h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($userBookings)): ?>
                                    <p class="text-center">Không tìm thấy lịch sử đặt tour cho người dùng này.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Mã đặt tour</th>
                                                    <th>Tour</th>
                                                    <th>Ngày khởi hành</th>
                                                    <th>Số người</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Trạng thái</th>
                                                    <th>Ngày đặt</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($userBookings as $booking): ?>
                                                    <tr>
                                                        <td><?php echo $booking['booking_id']; ?></td>
                                                        <td>
                                                            <a href="../tour/edit_tour.php?id=<?php echo $booking['tour_id']; ?>">
                                                                <?php echo htmlspecialchars($booking['tour_name']); ?>
                                                            </a>
                                                            <div class="small text-muted"><?php echo htmlspecialchars($booking['location']); ?></div>
                                                        </td>
                                                        <td><?php echo date('d/m/Y', strtotime($booking['departure_date'])); ?></td>
                                                        <td>
                                                            Người lớn: <?php echo $booking['adults']; ?><br>
                                                            Trẻ em: <?php echo $booking['children']; ?>
                                                        </td>
                                                        <td><?php echo number_format($booking['total_price']) . ' VNĐ'; ?></td>
                                                        <td>
                                                            <?php if ($booking['status'] == 'confirmed'): ?>
                                                                <span class="badge badge-success">Đã xác nhận</span>
                                                            <?php elseif ($booking['status'] == 'cancelled'): ?>
                                                                <span class="badge badge-danger">Đã hủy</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary"><?php echo htmlspecialchars($booking['status']); ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../layouts/footer.php'; ?>
</body>
</html>