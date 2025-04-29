<?php
require_once '../../../config/config.php';
require_once '../../../functions/auth_functions.php';

// Xác thực admin
redirectIfNotAdmin();

// Xử lý xóa đánh giá
if (isset($_POST['delete_review']) && isset($_POST['review_id'])) {
    $reviewId = $_POST['review_id'];
    $stmt = $conn->prepare("UPDATE reviews SET status = 'deleted' WHERE review_id = ?");
    $stmt->bind_param("i", $reviewId);
    
    if ($stmt->execute()) {
        $success = "Đã xóa đánh giá thành công.";
    } else {
        $error = "Có lỗi xảy ra: " . $conn->error;
    }
}

// Xử lý khôi phục đánh giá
if (isset($_POST['restore_review']) && isset($_POST['review_id'])) {
    $reviewId = $_POST['review_id'];
    $stmt = $conn->prepare("UPDATE reviews SET status = 'active' WHERE review_id = ?");
    $stmt->bind_param("i", $reviewId);
    
    if ($stmt->execute()) {
        $success = "Đã khôi phục đánh giá thành công.";
    } else {
        $error = "Có lỗi xảy ra: " . $conn->error;
    }
}

// Lấy danh sách đánh giá
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$whereClause = '';

if ($filter === 'active') {
    $whereClause = "WHERE r.status = 'active'";
} elseif ($filter === 'deleted') {
    $whereClause = "WHERE r.status = 'deleted'";
} elseif ($filter === 'high_rating') {
    $whereClause = "WHERE r.rating >= 4 AND r.status = 'active'";
} elseif ($filter === 'low_rating') {
    $whereClause = "WHERE r.rating < 3 AND r.status = 'active'";
}

$stmt = $conn->prepare("
    SELECT r.review_id, r.rating, r.comment, r.review_date, r.status,
           u.user_id, u.full_name, u.email,
           t.tour_id, t.name as tour_name,
           b.booking_id
    FROM reviews r
    INNER JOIN users u ON r.user_id = u.user_id
    INNER JOIN tours t ON r.tour_id = t.tour_id
    INNER JOIN bookings b ON r.booking_id = b.booking_id
    $whereClause
    ORDER BY r.review_date DESC
");
$stmt->execute();
$reviews = $stmt->get_result();
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
                        <h1 class="m-0">Quản lý đánh giá</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Đánh giá</li>
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
                            <h3 class="card-title mb-0">Danh sách đánh giá</h3>
                            <div class="btn-group">
                                <a href="?filter=all" class="btn btn-<?php echo $filter === 'all' ? 'primary' : 'outline-primary'; ?>">Tất cả</a>
                                <a href="?filter=active" class="btn btn-<?php echo $filter === 'active' ? 'primary' : 'outline-primary'; ?>">Đang hiển thị</a>
                                <a href="?filter=deleted" class="btn btn-<?php echo $filter === 'deleted' ? 'primary' : 'outline-primary'; ?>">Đã ẩn</a>
                                <a href="?filter=high_rating" class="btn btn-<?php echo $filter === 'high_rating' ? 'primary' : 'outline-primary'; ?>">Đánh giá cao</a>
                                <a href="?filter=low_rating" class="btn btn-<?php echo $filter === 'low_rating' ? 'primary' : 'outline-primary'; ?>">Đánh giá thấp</a>
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
                                        <th>Đánh giá</th>
                                        <th>Bình luận</th>
                                        <th>Ngày đánh giá</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($reviews->num_rows > 0): ?>
                                        <?php while ($review = $reviews->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?php echo $review['review_id']; ?></td>
                                                <td>
                                                    <?php echo $review['full_name']; ?><br>
                                                    <small class="text-muted"><?php echo $review['email']; ?></small>
                                                </td>
                                                <td><?php echo $review['tour_name']; ?></td>
                                                <td>
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <?php if ($i <= $review['rating']): ?>
                                                            <i class="fas fa-star text-warning"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star text-warning"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </td>
                                                <td>
                                                    <?php echo (strlen($review['comment']) > 50) ? substr($review['comment'], 0, 50) . '...' : $review['comment']; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($review['review_date'])); ?></td>
                                                <td>
                                                    <?php if ($review['status'] === 'active'): ?>
                                                        <span class="badge bg-success">Đang hiển thị</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Đã ẩn</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $review['review_id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($review['status'] === 'active'): ?>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn ẩn đánh giá này?');">
                                                            <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                                            <button type="submit" name="delete_review" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục đánh giá này?');">
                                                            <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                                            <button type="submit" name="restore_review" class="btn btn-sm btn-success">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            
                                            <!-- Detail Modal -->
                                            <div class="modal fade" id="detailModal<?php echo $review['review_id']; ?>" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="detailModalLabel">Chi tiết đánh giá #<?php echo $review['review_id']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-4">
                                                                <h6 class="fw-bold">Thông tin khách hàng</h6>
                                                                <p><strong>Họ tên:</strong> <?php echo $review['full_name']; ?></p>
                                                                <p><strong>Email:</strong> <?php echo $review['email']; ?></p>
                                                            </div>
                                                            <div class="mb-4">
                                                                <h6 class="fw-bold">Thông tin tour</h6>
                                                                <p><strong>Tour:</strong> <?php echo $review['tour_name']; ?></p>
                                                                <p><strong>Mã đơn đặt tour:</strong> #<?php echo $review['booking_id']; ?></p>
                                                            </div>
                                                            <div class="mb-4">
                                                                <h6 class="fw-bold">Nội dung đánh giá</h6>
                                                                <p>
                                                                    <strong>Đánh giá:</strong>
                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                        <?php if ($i <= $review['rating']): ?>
                                                                            <i class="fas fa-star text-warning"></i>
                                                                        <?php else: ?>
                                                                            <i class="far fa-star text-warning"></i>
                                                                        <?php endif; ?>
                                                                    <?php endfor; ?>
                                                                </p>
                                                                <p><strong>Bình luận:</strong></p>
                                                                <div class="p-3 bg-light rounded">
                                                                    <?php echo $review['comment']; ?>
                                                                </div>
                                                                <p class="mt-2"><strong>Ngày đánh giá:</strong> <?php echo date('d/m/Y H:i', strtotime($review['review_date'])); ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <?php if ($review['status'] === 'active'): ?>
                                                                <form method="post">
                                                                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                                                    <button type="submit" name="delete_review" class="btn btn-danger">Ẩn đánh giá</button>
                                                                </form>
                                                            <?php else: ?>
                                                                <form method="post">
                                                                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                                                    <button type="submit" name="restore_review" class="btn btn-success">Khôi phục</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-3">Không có đánh giá nào.</td>
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