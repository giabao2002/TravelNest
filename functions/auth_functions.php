<?php
session_start();

// Đăng ký người dùng mới
function registerUser($conn, $fullName, $email, $password, $phone, $address = '') {
    // Kiểm tra email đã tồn tại chưa
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    
    if ($result->num_rows > 0) {
        return [
            'success' => false,
            'message' => 'Email đã được sử dụng. Vui lòng chọn email khác.'
        ];
    }
    
    // Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Thêm người dùng mới vào database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'customer')");
    $stmt->bind_param("sssss", $fullName, $email, $hashedPassword, $phone, $address);
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Đăng ký thất bại: ' . $conn->error
        ];
    }
}

// Đăng nhập người dùng
function loginUser($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT user_id, full_name, email, password, role, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra trạng thái tài khoản
        if ($user['status'] === 'blocked') {
            return [
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'
            ];
        }
        
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Tạo session người dùng
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            return [
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'role' => $user['role']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Mật khẩu không chính xác.'
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => 'Email không tồn tại.'
        ];
    }
}

// Đăng xuất người dùng
function logoutUser() {
    // Xóa tất cả dữ liệu session
    session_unset();
    session_destroy();
    
    // Chuyển hướng về trang chủ
    header('Location: index.php');
    exit();
}

// Kiểm tra người dùng đã đăng nhập chưa
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kiểm tra người dùng có phải admin không
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Chuyển hướng nếu chưa đăng nhập
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Chuyển hướng nếu không phải admin
function redirectIfNotAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}
