<?php
require_once '../../../config/config.php';
require_once '../../../functions/auth_functions.php';

// Xác thực admin
redirectIfNotAdmin();

// Xử lý filter cho phần thống kê doanh thu
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'month';
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$quarter = isset($_GET['quarter']) ? (int)$_GET['quarter'] : ceil(date('m') / 3);
$location = isset($_GET['location']) ? $_GET['location'] : '';

// Lấy danh sách năm có trong hệ thống
$stmt = $conn->prepare("SELECT DISTINCT YEAR(booking_date) as year FROM bookings ORDER BY year DESC");
$stmt->execute();
$yearsResult = $stmt->get_result();
$years = [];
while ($row = $yearsResult->fetch_assoc()) {
    $years[] = $row['year'];
}

// Nếu không có năm nào, sử dụng năm hiện tại
if (empty($years)) {
    $years[] = date('Y');
}

// Lấy danh sách địa điểm tour
$stmt = $conn->prepare("SELECT DISTINCT location FROM tours ORDER BY location");
$stmt->execute();
$locationsResult = $stmt->get_result();
$locations = [];
while ($row = $locationsResult->fetch_assoc()) {
    $locations[] = $row['location'];
}

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

// Xử lý query thống kê doanh thu
$whereClause = "WHERE b.status IN ('confirmed', 'completed')";
$chartTitle = "";
$timeFilter = "";
$locationFilter = "";

if ($filter === 'day') {
    // Thống kê theo ngày trong tháng
    $timeFilter = "AND MONTH(b.booking_date) = ? AND YEAR(b.booking_date) = ?";
    $chartTitle = "Doanh thu theo ngày trong tháng " . $month . "/" . $year;
    $groupBy = "DAY(b.booking_date)";
    $orderBy = "day";
    $selectTime = "DAY(b.booking_date) as day";
    $labelFormat = "'Ngày ' + value";
} elseif ($filter === 'month') {
    // Thống kê theo tháng trong năm
    $timeFilter = "AND YEAR(b.booking_date) = ?";
    $chartTitle = "Doanh thu theo tháng trong năm " . $year;
    $groupBy = "MONTH(b.booking_date)";
    $orderBy = "month";
    $selectTime = "MONTH(b.booking_date) as month";
    $labelFormat = "'Tháng ' + value";
} elseif ($filter === 'quarter') {
    // Thống kê theo quý trong năm
    $timeFilter = "AND YEAR(b.booking_date) = ?";
    $chartTitle = "Doanh thu theo quý trong năm " . $year;
    $groupBy = "QUARTER(b.booking_date)";
    $orderBy = "quarter";
    $selectTime = "QUARTER(b.booking_date) as quarter";
    $labelFormat = "'Quý ' + value";
} elseif ($filter === 'year') {
    // Thống kê theo năm - không cần timeFilter vì đã filter theo năm
    $timeFilter = "";
    $chartTitle = "Doanh thu theo năm";
    $groupBy = "YEAR(b.booking_date)";
    $orderBy = "year";
    $selectTime = "YEAR(b.booking_date) as year";
    $labelFormat = "value";
}

if (!empty($location)) {
    $locationFilter = "AND t.location = ?";
    $chartTitle .= " - Địa điểm: " . $location;
}

$query = "
    SELECT $selectTime, SUM(b.total_price) as total_revenue, COUNT(b.booking_id) as booking_count
    FROM bookings b
    INNER JOIN tours t ON b.tour_id = t.tour_id
    $whereClause $timeFilter $locationFilter
    GROUP BY $groupBy
    ORDER BY $orderBy
";

$stmt = $conn->prepare($query);

// Bind parameters
if ($filter === 'day') {
    if (empty($location)) {
        $stmt->bind_param("ii", $month, $year);
    } else {
        $stmt->bind_param("iis", $month, $year, $location);
    }
} elseif ($filter === 'month' || $filter === 'quarter') {
    if (empty($location)) {
        $stmt->bind_param("i", $year);
    } else {
        $stmt->bind_param("is", $year, $location);
    }
} else { // year filter
    if (!empty($location)) {
        $stmt->bind_param("s", $location);
    }
}

$stmt->execute();
$revenueResult = $stmt->get_result();
$revenueData = [];

while ($row = $revenueResult->fetch_assoc()) {
    $revenueData[] = $row;
}

// Lấy tổng doanh thu
$stmt = $conn->prepare("
    SELECT SUM(b.total_price) as total_revenue, COUNT(b.booking_id) as total_bookings
    FROM bookings b
    INNER JOIN tours t ON b.tour_id = t.tour_id
    WHERE b.status IN ('confirmed', 'completed') $timeFilter $locationFilter
");

// Bind parameters
if ($filter === 'day') {
    if (empty($location)) {
        $stmt->bind_param("ii", $month, $year);
    } else {
        $stmt->bind_param("iis", $month, $year, $location);
    }
} elseif ($filter === 'month' || $filter === 'quarter') {
    if (empty($location)) {
        $stmt->bind_param("i", $year);
    } else {
        $stmt->bind_param("is", $year, $location);
    }
} else {
    if (!empty($location)) {
        $stmt->bind_param("s", $location);
    }
}

$stmt->execute();
$totalRevenueResult = $stmt->get_result()->fetch_assoc();
$totalRevenue = $totalRevenueResult['total_revenue'] ?? 0;
$totalConfirmedBookings = $totalRevenueResult['total_bookings'] ?? 0;

// Lấy top 5 tour có doanh thu cao nhất
$stmt = $conn->prepare("
    SELECT t.tour_id, t.name, t.location, COUNT(b.booking_id) as booking_count, SUM(b.total_price) as total_revenue
    FROM bookings b
    INNER JOIN tours t ON b.tour_id = t.tour_id
    WHERE b.status IN ('confirmed', 'completed') $timeFilter $locationFilter
    GROUP BY t.tour_id
    ORDER BY total_revenue DESC
    LIMIT 5
");

// Bind parameters
if ($filter === 'day') {
    if (empty($location)) {
        $stmt->bind_param("ii", $month, $year);
    } else {
        $stmt->bind_param("iis", $month, $year, $location);
    }
} elseif ($filter === 'month' || $filter === 'quarter') {
    if (empty($location)) {
        $stmt->bind_param("i", $year);
    } else {
        $stmt->bind_param("is", $year, $location);
    }
} else {
    if (!empty($location)) {
        $stmt->bind_param("s", $location);
    }
}

$stmt->execute();
$topToursResult = $stmt->get_result();
$topTours = [];

while ($row = $topToursResult->fetch_assoc()) {
    $topTours[] = $row;
}

// Chuẩn bị dữ liệu cho biểu đồ
$chartLabels = [];
$chartData = [];

foreach ($revenueData as $item) {
    if ($filter === 'day') {
        $chartLabels[] = $item['day'];
    } elseif ($filter === 'month') {
        $chartLabels[] = $item['month'];
    } elseif ($filter === 'quarter') {
        $chartLabels[] = $item['quarter'];
    } else {
        $chartLabels[] = $item['year'];
    }
    
    $chartData[] = $item['total_revenue'];
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
                        <h1 class="m-0">Tổng quan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
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
                                                <th>Đơn</th>
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
                                                        <td><?php echo $booking['booking_id']; ?></td>
                                                        <td data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $booking['email']; ?>">
                                                            <?php echo $booking['full_name']; ?>
                                                        </td>
                                                        <td><?php echo substr($booking['tour_name'], 0, 16) . '...'; ?></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            switch ($booking['status']) {
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
                                <a href="../booking/bookings.php" class="btn btn-sm btn-primary">Xem tất cả</a>
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
                                                    <?php echo $review['full_name'] ?> đã đánh giá <?php echo substr($review['tour_name'], 0, 30) . '...'; ?>
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
                                                <p class="mb-1"><?php echo substr($review['comment'], 0, 30); ?></p>
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
                                <a href="../review/reviews.php" class="btn btn-sm btn-primary">Xem tất cả</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê doanh thu-->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Thống kê doanh thu</h5>
                            </div>
                            <div class="card-body">
                                <!-- Filter Options -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title">Hãy chọn thời gian để xem dữ liệu</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="get" action="statistics.php" class="row g-3">
                                            <!-- Thời gian -->
                                            <div class="col-md-3">
                                                <label for="filter" class="form-label">Thống kê theo</label>
                                                <select class="form-select" id="filter" name="filter" onchange="updateFilterOptions()">
                                                    <option value="day" <?php if ($filter === 'day') echo "selected"; ?>>Ngày</option>
                                                    <option value="month" <?php if ($filter === 'month') echo "selected"; ?>>Tháng</option>
                                                    <option value="quarter" <?php if ($filter === 'quarter') echo "selected"; ?>>Quý</option>
                                                    <option value="year" <?php if ($filter === 'year') echo "selected"; ?>>Năm</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Năm -->
                                            <div class="col-md-2" id="year-filter">
                                                <label for="year" class="form-label">Năm</label>
                                                <select class="form-select" id="year" name="year">
                                                    <?php foreach ($years as $y): ?>
                                                        <option value="<?php echo $y; ?>" <?php if ($year == $y) echo "selected"; ?>>
                                                            <?php echo $y; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <!-- Tháng -->
                                            <div class="col-md-2 filter-option" id="month-filter" <?php if ($filter !== 'day') echo 'style="display:none;"'; ?>>
                                                <label for="month" class="form-label">Tháng</label>
                                                <select class="form-select" id="month" name="month">
                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                        <option value="<?php echo $i; ?>" <?php if ($month == $i) echo "selected"; ?>>
                                                            Tháng <?php echo $i; ?>
                                                        </option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            
                                            <!-- Địa điểm -->
                                            <div class="col-md-3">
                                                <label for="location" class="form-label">Địa điểm</label>
                                                <select class="form-select" id="location" name="location">
                                                    <option value="">Tất cả địa điểm</option>
                                                    <?php foreach ($locations as $loc): ?>
                                                        <option value="<?php echo $loc; ?>" <?php if ($location === $loc) echo "selected"; ?>>
                                                            <?php echo $loc; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <!-- Submit Button -->
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary">Lọc dữ liệu</button>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <a href="statistics.php" class="btn btn-secondary">Xóa bộ lọc</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Revenue Summary Cards -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="card-subtitle mb-2 text-muted">Tổng doanh thu</h6>
                                                <h2 class="card-title text-primary"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>đ</h2>
                                                <p class="card-text">
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-calendar-check me-1"></i>
                                                        <?php echo $totalConfirmedBookings; ?> đơn đặt tour đã xác nhận
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="card-subtitle mb-2 text-muted">Doanh thu trung bình</h6>
                                                <h2 class="card-title text-success">
                                                    <?php 
                                                    $averageRevenue = $totalConfirmedBookings > 0 ? $totalRevenue / $totalConfirmedBookings : 0;
                                                    echo number_format($averageRevenue, 0, ',', '.'); 
                                                    ?>đ
                                                </h2>
                                                <p class="card-text">
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-chart-line me-1"></i>
                                                        Trung bình mỗi đơn đặt tour
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Revenue Chart -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title"><?php echo $chartTitle; ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="revenueChart" height="300"></canvas>
                                    </div>
                                </div>
                                
                                <!-- Top Tours Table -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Top 5 tour có doanh thu cao nhất</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Tour</th>
                                                        <th>Địa điểm</th>
                                                        <th>Số đơn đặt</th>
                                                        <th>Doanh thu</th>
                                                        <th>Tỷ lệ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (count($topTours) > 0): ?>
                                                        <?php foreach ($topTours as $tour): ?>
                                                            <tr>
                                                                <td><?php echo $tour['name']; ?></td>
                                                                <td><?php echo $tour['location']; ?></td>
                                                                <td><?php echo $tour['booking_count']; ?></td>
                                                                <td><?php echo number_format($tour['total_revenue'], 0, ',', '.'); ?>đ</td>
                                                                <td>
                                                                    <?php
                                                                    $percentage = $totalRevenue > 0 ? ($tour['total_revenue'] / $totalRevenue) * 100 : 0;
                                                                    ?>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span><?php echo number_format($percentage, 1); ?>%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">Không có dữ liệu</td>
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
                </div>
            </div>
        </div>
    </div>

    <?php include '../../layouts/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Cập nhật bộ lọc dựa trên lựa chọn
        function updateFilterOptions() {
            const filter = document.getElementById('filter').value;
            
            // Ẩn hiện các bộ lọc
            document.getElementById('month-filter').style.display = filter === 'day' ? 'block' : 'none';
            // document.getElementById('quarter-filter').style.display = filter === 'quarter' ? 'block' : 'none';
            
            // Ẩn hiện trường năm - không sử dụng disabled để đảm bảo giá trị vẫn được gửi đi
            const yearField = document.getElementById('year');
            yearField.style.display = filter === 'year' ? 'none' : 'block';
            yearField.disabled = false; // Luôn đảm bảo giá trị được gửi đi
        }
        
        // Biểu đồ doanh thu
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const labels = <?php echo json_encode($chartLabels); ?>;
        const data = <?php echo json_encode($chartData); ?>;
        const labelFormat = <?php echo json_encode($labelFormat); ?>;
        
        // Tạo mảng dữ liệu đầy đủ (bao gồm cả các ngày/tháng/quý/năm không có doanh thu)
        let fullLabels = [];
        let fullData = [];

        // Xử lý hiển thị đầy đủ khoảng thời gian
        if ('<?php echo $filter; ?>' === 'day') {
            // Thêm đủ các ngày trong tháng
            const daysInMonth = new Date(<?php echo $year; ?>, <?php echo $month; ?>, 0).getDate();
            for (let i = 1; i <= daysInMonth; i++) {
                fullLabels.push(i);
                const dataIndex = labels.findIndex(label => parseInt(label) === i);
                fullData.push(dataIndex !== -1 ? data[dataIndex] : 0);
            }
        } else if ('<?php echo $filter; ?>' === 'month') {
            // Thêm đủ 12 tháng
            for (let i = 1; i <= 12; i++) {
                fullLabels.push(i);
                const dataIndex = labels.findIndex(label => parseInt(label) === i);
                fullData.push(dataIndex !== -1 ? data[dataIndex] : 0);
            }
        } else if ('<?php echo $filter; ?>' === 'quarter') {
            // Thêm đủ 4 quý, bắt đầu từ Quý 1
            for (let i = 1; i <= 4; i++) {
                fullLabels.push(i);
                const dataIndex = labels.findIndex(label => parseInt(label) === i);
                fullData.push(dataIndex !== -1 ? data[dataIndex] : 0);
            }
        } else {
            // Sử dụng dữ liệu gốc cho năm
            fullLabels = labels.map(label => parseInt(label));
            fullData = data;
        }

        const revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: fullLabels,
                datasets: [{
                    label: 'Doanh thu (đồng)',
                    data: fullData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            callback: function(value, index) {
                                const label = fullLabels[index];
                                // Áp dụng format tùy thuộc vào loại lọc
                                if ('<?php echo $filter; ?>' === 'day') {
                                    return 'Ngày ' + label;
                                } else if ('<?php echo $filter; ?>' === 'month') {
                                    return 'Tháng ' + label;
                                } else if ('<?php echo $filter; ?>' === 'quarter') {
                                    return 'Quý ' + label;
                                } else {
                                    return label;
                                }
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + context.raw.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>