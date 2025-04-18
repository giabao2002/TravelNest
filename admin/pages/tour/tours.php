<?php
require_once '../../../admin/config/config.php';
require_once '../../functions/tour_functions.php';

// Set default values for filtering and pagination
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get tours with pagination
$tours = getTours($conn, $currentPage, $itemsPerPage, $searchTerm, $statusFilter);
$totalTours = getTotalTourCount($conn, $searchTerm, $statusFilter);

// Calculate total pages
$totalPages = ceil($totalTours / $itemsPerPage);

// Ensure current page is within valid range
if ($currentPage < 1) {
    $currentPage = 1;
} elseif ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}

// Handle tour deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $tourId = (int)$_GET['delete'];
    
    if (deleteTour($conn, $tourId)) {
        header('Location: tours.php?msg=deleted');
        exit;
    } else {
        header('Location: tours.php?error=delete_failed');
        exit;
    }
}

// Define status labels and classes for display
$statusOptions = [
    'active' => [
        'label' => 'Hoạt động',
        'class' => 'bg-success'
    ],
    'inactive' => [
        'label' => 'Không hoạt động',
        'class' => 'bg-danger'
    ]
];
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
                        <h1 class="m-0">Quản lý Tour</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Quản lý Tour</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Messages -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Thêm tour thành công!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Cập nhật tour thành công!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Xóa tour thành công!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error']) && $_GET['error'] == 'delete_failed'): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Không thể xóa tour. Vui lòng thử lại sau!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Filters and Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <form action="" method="GET" class="form-inline">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Tìm kiếm theo tên tour, địa điểm..." name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                                        <select class="form-select" name="status" style="max-width: 150px;">
                                            <option value="all" <?php echo $statusFilter == 'all' ? 'selected' : ''; ?>>Tất cả</option>
                                            <option value="active" <?php echo $statusFilter == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                            <option value="inactive" <?php echo $statusFilter == 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                                        </select>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Tìm kiếm
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-3 text-end">
                                <a href="add_tour.php" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Thêm Tour mới
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tours List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách Tour</h3>
                        <div class="card-tools">
                            <span class="badge bg-primary"><?php echo $totalTours; ?> tour</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($tours)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover m-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th style="width: 100px;">Hình ảnh</th>
                                            <th>Tên Tour</th>
                                            <th>Địa điểm</th>
                                            <th>Thời gian</th>
                                            <th>Giá</th>
                                            <th style="width: 100px;">Trạng thái</th>
                                            <th style="width: 120px;">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tours as $tour): ?>
                                            <tr>
                                                <td><?php echo $tour['tour_id']; ?></td>
                                                <td>
                                                    <div class="tour-thumbnail">
                                                        <img src="<?php echo !empty($tour['image1']) ? '../../' . $tour['image1'] : '../../assets/img/no_image.png'; ?>" alt="<?php echo htmlspecialchars($tour['name']); ?>" class="img-fluid rounded">
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($tour['name']); ?></strong>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-calendar me-1"></i> 
                                                        <?php
                                                        $activeDates = 0;
                                                        if (!empty($tour['dates'])) {
                                                            foreach ($tour['dates'] as $date) {
                                                                if ($date['status'] != 'cancelled') {
                                                                    $activeDates++;
                                                                }
                                                            }
                                                        }
                                                        echo $activeDates . ' lịch khởi hành';
                                                        ?>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($tour['location']); ?></td>
                                                <td><?php echo htmlspecialchars($tour['duration']); ?></td>
                                                <td>
                                                    <div>Người lớn: <?php echo number_format($tour['price_adult'], 0, ',', '.'); ?> đ</div>
                                                    <div>Trẻ em: <?php echo number_format($tour['price_child'], 0, ',', '.'); ?> đ</div>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $statusOptions[$tour['status']]['class']; ?>">
                                                        <?php echo $statusOptions[$tour['status']]['label']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="edit_tour.php?id=<?php echo $tour['tour_id']; ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTourModal-<?php echo $tour['tour_id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Delete Confirmation Modal -->
                                                    <div class="modal fade" id="deleteTourModal-<?php echo $tour['tour_id']; ?>" tabindex="-1" aria-labelledby="deleteTourModalLabel-<?php echo $tour['tour_id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteTourModalLabel-<?php echo $tour['tour_id']; ?>">Xác nhận xóa</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Bạn có chắc chắn muốn xóa tour: <strong><?php echo htmlspecialchars($tour['name']); ?></strong>?</p>
                                                                    <p class="text-danger">Lưu ý: Tất cả thông tin liên quan đến tour này cũng sẽ bị xóa. Hành động này không thể hoàn tác.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                    <a href="tours.php?delete=<?php echo $tour['tour_id']; ?>" class="btn btn-danger">Xóa</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info m-3">
                                <i class="fas fa-info-circle me-2"></i> Không tìm thấy tour nào.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-end">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo $statusFilter != 'all' ? '&status=' . $statusFilter : ''; ?>">&laquo;</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo $statusFilter != 'all' ? '&status=' . $statusFilter : ''; ?>">&lsaquo;</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                // Display a range of page numbers
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                // Always show at least 5 pages if available
                                if ($endPage - $startPage < 4) {
                                    if ($startPage == 1) {
                                        $endPage = min($totalPages, $startPage + 4);
                                    } elseif ($endPage == $totalPages) {
                                        $startPage = max(1, $endPage - 4);
                                    }
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    $activeClass = ($i == $currentPage) ? 'active' : '';
                                    echo '<li class="page-item ' . $activeClass . '">';
                                    echo '<a class="page-link" href="?page=' . $i . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '') . ($statusFilter != 'all' ? '&status=' . $statusFilter : '') . '">' . $i . '</a>';
                                    echo '</li>';
                                }
                                ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo $statusFilter != 'all' ? '&status=' . $statusFilter : ''; ?>">&rsaquo;</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?><?php echo $statusFilter != 'all' ? '&status=' . $statusFilter : ''; ?>">&raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../layouts/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Set a timeout for alert messages
            setTimeout(function() {
                $('.alert-dismissible').alert('close');
            }, 5000);
        });
    </script>
</body>
</html> 