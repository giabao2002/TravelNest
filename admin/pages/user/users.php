<?php
require_once '../../../admin/config/config.php';
require_once '../../functions/user_functions.php';

// Initialize variables
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get users with pagination
$users = getUsers($conn, $currentPage, $itemsPerPage, $search, $status);
$totalUsers = getTotalUserCount($conn, $search, $status);
$totalPages = ceil($totalUsers / $itemsPerPage);

// Handle user status update (block/unblock)
if (isset($_POST['update_status'])) {
    $userId = $_POST['user_id'];
    $newStatus = $_POST['status'];

    if (updateUserStatus($conn, $userId, $newStatus)) {
        $statusMessage = "Cập nhật trạng thái người dùng thành công.";
        // Refresh the page to show updated data
        header("Location: users.php?page=$currentPage&search=$search&status=$status&message=" . urlencode($statusMessage));
        exit();
    } else {
        $errorMessage = "Không thể cập nhật trạng thái người dùng.";
    }
}
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
                        <h1 class="m-0">Quản lý Người dùng</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Quản lý Người dùng</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $errorMessage; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Search & Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="users.php" class="row">
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Tìm theo email, tên hoặc số điện thoại"
                                    value="<?php echo htmlspecialchars($search); ?>">
                                <select class="form-control" id="status" name="status">
                                    <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>Tất cả</option>
                                    <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Đang hoạt động</option>
                                    <option value="blocked" <?php echo $status == 'blocked' ? 'selected' : ''; ?>>Đã chặn</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-block">Lọc</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách Người dùng</h3>
                        <div class="card-tools">
                            <span class="badge bg-primary"><?php echo $totalUsers; ?> người dùng</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Điện thoại</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Không tìm thấy người dùng nào.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['user_id']; ?></td>
                                                <td><?php echo isset($user['full_name']) ? htmlspecialchars($user['full_name']) : ''; ?></td>
                                                <td><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?></td>
                                                <td><?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?></td>
                                                <td>
                                                    <?php if ($user['status'] == 'active'): ?>
                                                        <span class="badge badge-success">Đang hoạt động</span>
                                                    <?php elseif ($user['status'] == 'blocked'): ?>
                                                        <span class="badge badge-danger">Đã chặn</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($user['status']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <a href="view_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> Chi tiết
                                                    </a>

                                                    <!-- Block/Unblock Form -->
                                                    <form method="POST" action="users.php" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn <?php echo $user['status'] == 'active' ? 'chặn' : 'bỏ chặn'; ?> người dùng này?');">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                        <input type="hidden" name="status" value="<?php echo $user['status'] == 'active' ? 'blocked' : 'active'; ?>">
                                                        <button type="submit" name="update_status" class="btn <?php echo $user['status'] == 'active' ? 'btn-danger' : 'btn-success'; ?> btn-sm">
                                                            <?php if ($user['status'] == 'active'): ?>
                                                                <i class="fas fa-ban"></i> Chặn
                                                            <?php else: ?>
                                                                <i class="fas fa-check"></i> Bỏ chặn
                                                            <?php endif; ?>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center mt-4">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="users.php?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">
                                                Trước
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">Trước</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="users.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($currentPage < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="users.php?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">
                                                Tiếp
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">Tiếp</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../layouts/footer.php'; ?>
</body>

</html>