<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="d-flex align-items-center text-decoration-none">
            <i class="fas fa-globe-asia me-2"></i>
            <span>Travel Nest</span>
        </a>
        <button class="btn-close d-md-none" id="closeSidebar"></button>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tổng quan</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tours.php' ? 'active' : ''; ?>" href="../pages/tour/tours.php">
                <i class="fas fa-map-marked-alt"></i>
                <span>Quản lý Tour</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>" href="../pages/booking/bookings.php">
                <i class="fas fa-calendar-check"></i>
                <span>Quản lý Đặt tour</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="../pages/user/users.php">
                <i class="fas fa-users"></i>
                <span>Quản lý Người dùng</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>" href="../pages/review/reviews.php">
                <i class="fas fa-star"></i>
                <span>Quản lý Đánh giá</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'statistics.php' ? 'active' : ''; ?>" href="../pages/statistics/statistics.php">
                <i class="fas fa-chart-bar"></i>
                <span>Thống kê doanh thu</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</div>