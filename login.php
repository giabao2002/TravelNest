<?php
require_once 'config/config.php';
require_once 'functions/auth_functions.php';

// Nếu đã đăng nhập thì chuyển hướng về trang chủ
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$message = '';
$messageType = '';

// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy và làm sạch dữ liệu đầu vào
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    
    // Validate server-side
    $errors = [];
    
    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    }
    
    // Validate mật khẩu
    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu';
    }
    
    // Nếu không có lỗi, thực hiện đăng nhập
    if (empty($errors)) {
        $result = loginUser($conn, $email, $password);
        
        if ($result['success']) {
            // Chuyển hướng dựa vào vai trò
            if ($result['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    }
}

include 'layouts/header.php';
?>
<!-- Login Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Đăng Nhập</h2>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="loginEmail" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mật khẩu -->
                            <div class="mb-4">
                                <label for="loginPassword" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                           id="loginPassword" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('loginPassword', 'toggleLoginIcon')">
                                        <i id="toggleLoginIcon" class="fas fa-eye"></i>
                                    </button>
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Remember Me & Forgot Password -->
                            <div class="d-flex justify-content-between mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="rememberMe" name="rememberMe">
                                    <label class="form-check-label" for="rememberMe">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a href="#" class="text-primary">Quên mật khẩu?</a>
                            </div>
                            
                            <!-- Submit button -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Đăng Nhập</button>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-0">Chưa có tài khoản? <a href="register.php" class="text-primary fw-bold">Đăng ký ngay</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JS for form validation -->
<script src="assets/js/auth.js"></script>

<?php include 'layouts/footer.php'; ?>
