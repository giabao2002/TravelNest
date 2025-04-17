document.addEventListener('DOMContentLoaded', function() {
    // Các biểu thức chính quy để validate
    const patterns = {
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
        password: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/,
        phone: /^(0|\+84)[0-9]{9,10}$/,
        fullName: /^[A-Za-zÀ-ỹ\s]{3,50}$/
    };

    // Form đăng ký
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        // Các trường input cần validate
        const fullName = document.getElementById('fullName');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const address = document.getElementById('address');
        
        // Validate Full Name
        fullName.addEventListener('blur', function() {
            validateField(this, patterns.fullName, 'Họ tên phải từ 3-50 ký tự và không chứa ký tự đặc biệt hoặc số');
        });
        
        // Validate Email
        email.addEventListener('blur', function() {
            validateField(this, patterns.email, 'Email không hợp lệ. Vui lòng nhập đúng định dạng email');
        });
        
        // Validate Phone
        phone.addEventListener('blur', function() {
            validateField(this, patterns.phone, 'Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam hợp lệ');
        });
        
        // Validate Password
        password.addEventListener('blur', function() {
            validateField(this, patterns.password, 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ và số');
        });
        
        // Validate Confirm Password
        confirmPassword.addEventListener('blur', function() {
            if (this.value !== password.value) {
                showError(this, 'Mật khẩu xác nhận không khớp');
            } else {
                showSuccess(this);
            }
        });
        
        // Khi submit form
        registerForm.addEventListener('submit', function(e) {
            // Kiểm tra lại tất cả trường
            let isValid = true;
            
            if (!validateField(fullName, patterns.fullName, 'Họ tên phải từ 3-50 ký tự và không chứa ký tự đặc biệt hoặc số')) {
                isValid = false;
            }
            
            if (!validateField(email, patterns.email, 'Email không hợp lệ. Vui lòng nhập đúng định dạng email')) {
                isValid = false;
            }
            
            if (!validateField(phone, patterns.phone, 'Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam hợp lệ')) {
                isValid = false;
            }
            
            if (!validateField(password, patterns.password, 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ và số')) {
                isValid = false;
            }
            
            if (confirmPassword.value !== password.value) {
                showError(confirmPassword, 'Mật khẩu xác nhận không khớp');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Form đăng nhập
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const loginEmail = document.getElementById('loginEmail');
        const loginPassword = document.getElementById('loginPassword');
        
        // Validate Email
        loginEmail.addEventListener('blur', function() {
            validateField(this, patterns.email, 'Email không hợp lệ');
        });
        
        // Khi submit form đăng nhập
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            if (!validateField(loginEmail, patterns.email, 'Email không hợp lệ')) {
                isValid = false;
            }
            
            if (loginPassword.value.trim() === '') {
                showError(loginPassword, 'Vui lòng nhập mật khẩu');
                isValid = false;
            } else {
                showSuccess(loginPassword);
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Hàm kiểm tra trường dữ liệu
    function validateField(field, pattern, errorMessage) {
        if (field.value.trim() === '') {
            showError(field, 'Trường này không được để trống');
            return false;
        } else if (!pattern.test(field.value)) {
            showError(field, errorMessage);
            return false;
        } else {
            showSuccess(field);
            return true;
        }
    }
    
    // Hiển thị lỗi
    function showError(field, message) {
        // Xóa class success nếu có
        field.classList.remove('is-valid');
        // Thêm class error
        field.classList.add('is-invalid');
        
        // Tìm hoặc tạo feedback div
        let feedback = field.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.insertBefore(feedback, field.nextSibling);
        }
        
        feedback.textContent = message;
    }
    
    // Hiển thị thành công
    function showSuccess(field) {
        // Xóa class error nếu có
        field.classList.remove('is-invalid');
        // Thêm class success
        field.classList.add('is-valid');
        
        // Xóa feedback nếu có
        let feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = '';
        }
    }
});

// Hàm để hiển thị mật khẩu
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
