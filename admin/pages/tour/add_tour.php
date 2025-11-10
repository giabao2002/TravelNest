<?php
require_once '../../../admin/config/config.php';
require_once '../../functions/tour_functions.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validate required fields
    if (empty($_POST['name'])) {
        $errors[] = "Tên tour không được để trống";
    }
    
    if (empty($_POST['description'])) {
        $errors[] = "Mô tả tour không được để trống";
    }
    
    if (empty($_POST['location'])) {
        $errors[] = "Địa điểm không được để trống";
    }
    
    if (empty($_POST['duration'])) {
        $errors[] = "Thời gian không được để trống";
    }
    
    if (empty($_POST['price_adult']) || !is_numeric($_POST['price_adult']) || $_POST['price_adult'] < 0) {
        $errors[] = "Giá vé người lớn không hợp lệ";
    }
    
    if (empty($_POST['price_child']) || !is_numeric($_POST['price_child']) || $_POST['price_child'] < 0) {
        $errors[] = "Giá vé trẻ em không hợp lệ";
    }
    
    // Process if no validation errors
    if (empty($errors)) {
        // Set status value
        $status = isset($_POST['status']) && $_POST['status'] == 'active' ? 'active' : 'inactive';
        
        // Upload images
        $image1Path = '';
        $image2Path = '';
        $image3Path = '';
        
        // Image 1 (Required)
        if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
            $image1Path = uploadTourImage($_FILES['image1'], 'uploads/tours/');
            if (!$image1Path) {
                $errors[] = "Không thể tải lên hình ảnh 1";
            }
        } else {
            $errors[] = "Hình ảnh 1 là bắt buộc";
        }
        
        // Image 2
        if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
            $image2Path = uploadTourImage($_FILES['image2'], 'uploads/tours/');
            if (!$image2Path) {
                $errors[] = "Không thể tải lên hình ảnh 2";
            }
        }
        
        // Image 3
        if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
            $image3Path = uploadTourImage($_FILES['image3'], 'uploads/tours/');
            if (!$image3Path) {
                $errors[] = "Không thể tải lên hình ảnh 3";
            }
        }
        
        // Use image1 as fallback for missing images
        if (!empty($image1Path)) {
            if (empty($image2Path)) {
                $image2Path = $image1Path;
            }
            if (empty($image3Path)) {
                $image3Path = $image1Path;
            }
        }
        
        // Prepare tour data
        $tourData = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'location' => $_POST['location'],
            'duration' => $_POST['duration'],
            'price_adult' => $_POST['price_adult'],
            'price_child' => $_POST['price_child'],
            'image1' => $image1Path,
            'image2' => $image2Path,
            'image3' => $image3Path,
            'link_map' => $_POST['link_map'],
            'status' => $status
        ];
        
        // Prepare tour dates
        $tourDates = [];
        if (isset($_POST['departure_dates']) && isset($_POST['available_seats'])) {
            for ($i = 0; $i < count($_POST['departure_dates']); $i++) {
                if (!empty($_POST['departure_dates'][$i]) && !empty($_POST['available_seats'][$i])) {
                    $tourDates[] = [
                        'departure_date' => $_POST['departure_dates'][$i],
                        'available_seats' => (int)$_POST['available_seats'][$i]
                    ];
                }
            }
        }
        
        // Add tour to database
        $tourId = addTour($conn, $tourData, $tourDates);
        
        if ($tourId) {
            // Redirect to tour list with success message
            header('Location: tours.php?msg=added');
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm tour. Vui lòng thử lại sau";
        }
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
                        <h1 class="m-0">Thêm Tour mới</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="tours.php">Quản lý Tour</a></li>
                            <li class="breadcrumb-item active">Thêm Tour mới</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Display validation errors -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Tour Form -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Main Info Card -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên Tour <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description" rows="6" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="location" class="form-label">Địa điểm <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="location" name="location" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required>
                                            <div class="form-text">Ví dụ: Hà Nội, Đà Nẵng, Huế, ...</div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="duration" class="form-label">Thời gian <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="duration" name="duration" value="<?php echo isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : ''; ?>" required>
                                            <div class="form-text">Ví dụ: 3 ngày 2 đêm, 5 ngày 4 đêm, ...</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="link_map" class="form-label">Link bản đồ (Google Maps iframe)</label>
                                        <textarea class="form-control" id="link_map" name="link_map" rows="3" placeholder="Dán link iframe từ Google Maps ở đây"><?php echo isset($_POST['link_map']) ? htmlspecialchars($_POST['link_map']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="price_adult" class="form-label">Giá vé người lớn (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="price_adult" name="price_adult" value="<?php echo isset($_POST['price_adult']) ? (int)$_POST['price_adult'] : ''; ?>" min="0" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="price_child" class="form-label">Giá vé trẻ em (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="price_child" name="price_child" value="<?php echo isset($_POST['price_child']) ? (int)$_POST['price_child'] : ''; ?>" min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tour Dates Card -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Lịch khởi hành</h5>
                                    <button type="button" class="btn btn-sm btn-primary" id="addDateBtn">
                                        <i class="fas fa-plus"></i> Thêm lịch
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="tour-dates-container">
                                        <div class="tour-date-item mb-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Ngày khởi hành</label>
                                                    <input type="date" class="form-control" name="departure_dates[]">
                                                </div>
                                                <div class="col-md-5 mb-2">
                                                    <label class="form-label">Số chỗ trống</label>
                                                    <input type="number" class="form-control" name="available_seats[]" min="1" value="20">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end mb-2">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-date">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center" id="no-dates-message" style="display: none;">
                                        <p class="text-muted">Chưa có lịch khởi hành nào. Nhấn "Thêm lịch" để thêm.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sidebar: Status & Images -->
                        <div class="col-md-4">
                            <!-- Status Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Trạng thái</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="active" checked>
                                        <label class="form-check-label" for="status">
                                            Kích hoạt tour
                                        </label>
                                    </div>
                                    <div class="form-text">Tour sẽ được hiển thị trên trang web nếu được kích hoạt.</div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-1"></i> Lưu Tour
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Images Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Hình ảnh</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="image1" class="form-label">Hình ảnh 1 (Ảnh chính) <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="image1" name="image1" accept="image/*" required>
                                        <div class="form-text">Hình ảnh 1 sẽ được sử dụng làm ảnh chính hiển thị đầu tiên. Nếu không chọn đủ hình ảnh, hình ảnh này sẽ được sử dụng cho các vị trí còn lại.</div>
                                        
                                        <div class="mt-2 image-preview-container">
                                            <img id="image1-preview" src="../../assets/img/no_image.png" alt="Preview" class="img-thumbnail">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="image2" class="form-label">Hình ảnh 2</label>
                                        <input type="file" class="form-control" id="image2" name="image2" accept="image/*">
                                        <div class="mt-2 image-preview-container">
                                            <img id="image2-preview" src="../../assets/img/no_image.png" alt="Preview" class="img-thumbnail">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="image3" class="form-label">Hình ảnh 3</label>
                                        <input type="file" class="form-control" id="image3" name="image3" accept="image/*">
                                        <div class="mt-2 image-preview-container">
                                            <img id="image3-preview" src="../../assets/img/no_image.png" alt="Preview" class="img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../layouts/footer.php'; ?>
    
    <script>
        $(document).ready(function() {
            // Image preview for image1
            $("#image1").change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $("#image1-preview").attr("src", e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Image preview for image2
            $("#image2").change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $("#image2-preview").attr("src", e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Image preview for image3
            $("#image3").change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $("#image3-preview").attr("src", e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Add new date field
            $("#addDateBtn").click(function() {
                $("#no-dates-message").hide();
                
                const dateItem = `
                    <div class="tour-date-item mb-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Ngày khởi hành</label>
                                <input type="date" class="form-control" name="departure_dates[]">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label">Số chỗ trống</label>
                                <input type="number" class="form-control" name="available_seats[]" min="1" value="20">
                            </div>
                            <div class="col-md-1 d-flex align-items-end mb-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-date">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                $("#tour-dates-container").append(dateItem);
                checkEmptyDates();
            });
            
            // Remove date field
            $(document).on("click", ".remove-date", function() {
                $(this).closest(".tour-date-item").remove();
                checkEmptyDates();
            });
            
            // Check if there are no date fields
            function checkEmptyDates() {
                if ($("#tour-dates-container .tour-date-item").length === 0) {
                    $("#no-dates-message").show();
                } else {
                    $("#no-dates-message").hide();
                }
            }
            
            // Initialize
            checkEmptyDates();
        });
    </script>
</body>
</html> 