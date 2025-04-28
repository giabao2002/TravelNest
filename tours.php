<?php
include 'config/config.php';
include 'functions/auth_functions.php';

// Xử lý tìm kiếm
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$priceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$departureDate = isset($_GET['departure_date']) ? $_GET['departure_date'] : '';

// Tạo câu truy vấn động
$whereConditions = ["t.status = 'active'"];
$params = [];
$types = '';

if (!empty($searchTerm)) {
    $whereConditions[] = "t.name LIKE ?";
    $params[] = "%$searchTerm%";
    $types .= 's';
}

if (!empty($location)) {
    $whereConditions[] = "t.location LIKE ?";
    $params[] = "%$location%";
    $types .= 's';
}

if (!empty($priceRange)) {
    switch ($priceRange) {
        case '1':
            $whereConditions[] = "t.price_adult < 1000000";
            break;
        case '2':
            $whereConditions[] = "t.price_adult >= 1000000 AND t.price_adult <= 3000000";
            break;
        case '3':
            $whereConditions[] = "t.price_adult > 3000000 AND t.price_adult <= 5000000";
            break;
        case '4':
            $whereConditions[] = "t.price_adult > 5000000 AND t.price_adult <= 10000000";
            break;
        case '5':
            $whereConditions[] = "t.price_adult > 10000000";
            break;
    }
}

if (!empty($departureDate)) {
    $whereConditions[] = "td.departure_date = ? AND td.status = 'available'";
    $params[] = $departureDate;
    $types .= 's';
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Lấy danh sách tour
$query = "
    SELECT DISTINCT t.*, 
           (SELECT COUNT(*) FROM reviews r WHERE r.tour_id = t.tour_id AND r.status = 'active') as review_count,
           (SELECT AVG(rating) FROM reviews r WHERE r.tour_id = t.tour_id AND r.status = 'active') as avg_rating
    FROM tours t
    LEFT JOIN tour_dates td ON t.tour_id = td.tour_id
    $whereClause
    ORDER BY t.created_at DESC
";

$stmt = $conn->prepare($query);

// Bind parameters if they exist
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$toursResult = $stmt->get_result();
$tours = [];

while ($row = $toursResult->fetch_assoc()) {
    $tours[] = $row;
}

// Lấy danh sách địa điểm phổ biến cho bộ lọc
$stmt = $conn->prepare("SELECT DISTINCT location FROM tours WHERE status = 'active' ORDER BY location");
$stmt->execute();
$locationsResult = $stmt->get_result();
$locations = [];

while ($row = $locationsResult->fetch_assoc()) {
    $locations[] = $row['location'];
}

include 'layouts/header.php';
?>

<div class="tours-header py-5 mb-4" style="background-image: url('assets/images/banner5.png');">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="text-white">Tour Du Lịch</h1>
                <p class="text-white lead">Khám phá những điểm đến tuyệt vời trong nước cùng Travel Nest</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <!-- Search and Filter Section -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body">
            <form method="get" action="tours.php">
                <div class="row g-3 align-items-end">
                    <!-- Search Input -->
                    <div class="col-md-3">
                        <label for="search" class="form-label">Tên tour</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Tìm theo tên tour" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    
                    <!-- Location Filter -->
                    <div class="col-md-3">
                        <label for="location" class="form-label">Địa điểm</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Nhập địa điểm" value="<?php echo htmlspecialchars($location); ?>">
                    </div>
                    
                    <!-- Price Range Filter -->
                    <div class="col-md-3">
                        <label for="price_range" class="form-label">Mức giá</label>
                        <select class="form-select" id="price_range" name="price_range">
                            <option value="">Tất cả mức giá</option>
                            <option value="1" <?php if ($priceRange === '1') echo "selected"; ?>>Dưới 1 triệu</option>
                            <option value="2" <?php if ($priceRange === '2') echo "selected"; ?>>1 - 3 triệu</option>
                            <option value="3" <?php if ($priceRange === '3') echo "selected"; ?>>3 - 5 triệu</option>
                            <option value="4" <?php if ($priceRange === '4') echo "selected"; ?>>5 - 10 triệu</option>
                            <option value="5" <?php if ($priceRange === '5') echo "selected"; ?>>Trên 10 triệu</option>
                        </select>
                    </div>
                    
                    <!-- Departure Date Filter -->
                    <div class="col-md-3">
                        <label for="departure_date" class="form-label">Ngày khởi hành</label>
                        <input type="date" class="form-control" id="departure_date" name="departure_date" value="<?php echo $departureDate; ?>">
                    </div>
                    
                    <!-- Search Button -->
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <a href="tours.php" class="btn btn-outline-secondary me-2">Xóa bộ lọc</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">Kết quả tìm kiếm</h2>
            <p class="text-muted">Tìm thấy <?php echo count($tours); ?> tour phù hợp</p>
        </div>
    </div>
    
    <?php if (count($tours) > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($tours as $tour): ?>
                <div class="col">
                    <div class="card tour-card h-100 border-0 shadow-sm">
                        <div class="tour-image-wrapper">
                            <img src="admin/<?php echo $tour['image1']; ?>" class="card-img-top tour-card-img" alt="<?php echo $tour['name']; ?>">
                            <?php if ($tour['review_count'] > 0): ?>
                                <div class="tour-rating">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <span><?php echo number_format($tour['avg_rating'], 1); ?></span>
                                    <small class="text-muted">(<?php echo $tour['review_count']; ?>)</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo strlen($tour['name']) > 50 ? substr($tour['name'], 0, 47) . '...' : $tour['name']; ?></h5>
                            <div class="tour-location mb-2">
                                <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                <span><?php echo strlen($tour['location']) > 50 ? substr($tour['location'], 0, 47) . '...' : $tour['location']; ?></span>
                            </div>
                            <div class="tour-duration mb-2">
                                <i class="far fa-calendar-alt me-1 text-primary"></i>
                                <span><?php echo $tour['duration']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="tour-price">
                                    <span class="price-label">Giá từ</span>
                                    <span class="price-value"><?php echo number_format($tour['price_adult'], 0, ',', '.'); ?>đ</span>
                                </div>
                            </div>
                            <div class="d-grid">
                                <a href="tour-detail.php?id=<?php echo $tour['tour_id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4>Không tìm thấy tour nào phù hợp</h4>
            <p class="text-muted">Vui lòng thử lại với các điều kiện tìm kiếm khác</p>
            <a href="tours.php" class="btn btn-primary mt-3">Xem tất cả tour</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .tours-header {
        background-size: cover;
        background-position: center;
        position: relative;
    }
    
    .tours-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    
    .tours-header .container {
        position: relative;
        z-index: 2;
    }
    
    .tour-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .tour-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    .tour-image-wrapper {
        position: relative;
    }
    
    .tour-card-img {
        height: 200px;
        object-fit: cover;
    }
    
    .tour-rating {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: bold;
    }
    
    .price-label {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .price-value {
        font-weight: bold;
        color: #fd7e14;
        font-size: 1.2rem;
    }
</style>

<?php include 'layouts/footer.php'; ?> 