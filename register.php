<?php
require_once 'config/config.php';
require_once 'functions/auth_functions.php';

$message = '';
$messageType = '';

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy và làm sạch dữ liệu đầu vào
    $fullName = trim(htmlspecialchars($_POST['fullName']));
    $email = trim(htmlspecialchars($_POST['email']));
    $phone = trim(htmlspecialchars($_POST['phone']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $address = trim(htmlspecialchars($_POST['address']));
    
    // Validate server-side
    $errors = [];
    
    // Validate họ tên
    if (empty($fullName)) {
        $errors['fullName'] = 'Vui lòng nhập họ tên';
    } elseif (!preg_match('/^[A-Za-zÀ-ỹ\s]{3,50}$/', $fullName)) {
        $errors['fullName'] = 'Họ tên phải từ 3-50 ký tự và không chứa ký tự đặc biệt hoặc số';
    }
    
    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    }
    
    // Validate số điện thoại
    if (empty($phone)) {
        $errors['phone'] = 'Vui lòng nhập số điện thoại';
    } elseif (!preg_match('/^(0|\+84)[0-9]{9,10}$/', $phone)) {
        $errors['phone'] = 'Số điện thoại không hợp lệ';
    }
    
    // Validate mật khẩu
    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu';
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/', $password)) {
        $errors['password'] = 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ và số';
    }
    
    // Validate xác nhận mật khẩu
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Mật khẩu xác nhận không khớp';
    }
    
    // Nếu không có lỗi, thực hiện đăng ký
    if (empty($errors)) {
        $result = registerUser($conn, $fullName, $email, $password, $phone, $address);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            
            // Chuyển hướng đến trang đăng nhập sau 2 giây
            echo '<script>
                setTimeout(function() {
                    window.location.href = "login.php";
                }, 2000);
            </script>';
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    }
}

include 'layouts/header.php';
?>
<!-- Register Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Tạo Tài Khoản Mới</h2>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form id="registerForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                            <!-- Họ tên -->
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['fullName']) ? 'is-invalid' : ''; ?>" 
                                       id="fullName" name="fullName" value="<?php echo isset($fullName) ? $fullName : ''; ?>" required>
                                <?php if (isset($errors['fullName'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['fullName']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Số điện thoại -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                       id="phone" name="phone" value="<?php echo isset($phone) ? $phone : ''; ?>" required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mật khẩu -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                           id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon')">
                                        <i id="toggleIcon" class="fas fa-eye"></i>
                                    </button>
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ và số</div>
                            </div>
                            
                            <!-- Xác nhận mật khẩu -->
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo isset($errors['confirmPassword']) ? 'is-invalid' : ''; ?>" 
                                           id="confirmPassword" name="confirmPassword" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', 'toggleConfirmIcon')">
                                        <i id="toggleConfirmIcon" class="fas fa-eye"></i>
                                    </button>
                                    <?php if (isset($errors['confirmPassword'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['confirmPassword']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Địa chỉ -->
                            <div class="mb-4">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <textarea class="form-control" id="address" name="address" rows="2"><?php echo isset($address) ? $address : ''; ?></textarea>
                            </div>
                            
                            <!-- Submit button -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Đăng Ký</button>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-0">Đã có tài khoản? <a href="login.php" class="text-primary fw-bold">Đăng nhập</a></p>
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
