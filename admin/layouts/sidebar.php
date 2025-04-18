<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="../statistics/statistics.php" class="d-flex align-items-center text-decoration-none">
            <i class="fas fa-globe-asia me-2"></i>
            <span>Travel Nest</span>
        </a>
        <button class="btn-close d-md-none" id="closeSidebar"></button>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'statistics.php' ? 'active' : ''; ?>" href="../statistics/statistics.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tổng quan</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['tours.php', 'add_tour.php', 'edit_tour.php']) ? 'active' : ''; ?>" href="../tour/tours.php">
                <i class="fas fa-map-marked-alt"></i>
                <span>Quản lý Tour</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>" href="../booking/bookings.php">
                <i class="fas fa-calendar-check"></i>
                <span>Quản lý Đặt tour</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="../user/users.php">
                <i class="fas fa-users"></i>
                <span>Quản lý Người dùng</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>" href="../review/reviews.php">
                <i class="fas fa-star"></i>
                <span>Quản lý Đánh giá</span>
            </a>
        </li>
    </ul>
</div>