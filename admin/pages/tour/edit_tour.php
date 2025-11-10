<?php
require_once '../../../admin/config/config.php';
require_once '../../functions/tour_functions.php';

// Get tour ID from URL parameter
$tourId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if tour ID is invalid
if ($tourId <= 0) {
    header('Location: tours.php');
    exit;
}

// Get tour data
$tour = getTourById($conn, $tourId);

// Redirect if tour not found
if (!$tour) {
    header('Location: tours.php?error=tour_not_found');
    exit;
}

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
        
        // Initialize image paths with current values
        $image1Path = $tour['image1'];
        $image2Path = $tour['image2'];
        $image3Path = $tour['image3'];
        
        // Upload image1 if provided (required)
        if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage1 = uploadTourImage($_FILES['image1'], 'uploads/tours/');
            if ($uploadedImage1) {
                // Delete old image if it exists
                if (!empty($image1Path) && file_exists('../../' . $image1Path)) {
                    @unlink('../../' . $image1Path);
                }
                $image1Path = $uploadedImage1;
            } else {
                $errors[] = "Không thể tải lên hình ảnh 1";
            }
        }
        
        // Upload image2 if provided
        if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage2 = uploadTourImage($_FILES['image2'], 'uploads/tours/');
            if ($uploadedImage2) {
                // Delete old image if it exists
                if (!empty($image2Path) && file_exists('../../' . $image2Path) && $image2Path != $image1Path) {
                    @unlink('../../' . $image2Path);
                }
                $image2Path = $uploadedImage2;
            } else {
                $errors[] = "Không thể tải lên hình ảnh 2";
            }
        }
        
        // Upload image3 if provided
        if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage3 = uploadTourImage($_FILES['image3'], 'uploads/tours/');
            if ($uploadedImage3) {
                // Delete old image if it exists
                if (!empty($image3Path) && file_exists('../../' . $image3Path) && $image3Path != $image1Path) {
                    @unlink('../../' . $image3Path);
                }
                $image3Path = $uploadedImage3;
            } else {
                $errors[] = "Không thể tải lên hình ảnh 3";
            }
        }
        
        // Use image1 as fallback if any image is empty
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
        
        // Update tour in database
        if (updateTour($conn, $tourId, $tourData)) {
            // Process tour dates
            // Delete removed dates
            if (isset($_POST['delete_date_ids']) && !empty($_POST['delete_date_ids'])) {
                $deleteIds = explode(',', $_POST['delete_date_ids']);
                foreach ($deleteIds as $dateId) {
                    if (!empty($dateId)) {
                        deleteTourDate($conn, (int)$dateId);
                    }
                }
            }
            
            // Update existing dates
            if (isset($_POST['existing_date_ids']) && isset($_POST['existing_departure_dates']) && isset($_POST['existing_available_seats']) && isset($_POST['existing_date_status'])) {
                for ($i = 0; $i < count($_POST['existing_date_ids']); $i++) {
                    if (!empty($_POST['existing_date_ids'][$i]) && !empty($_POST['existing_departure_dates'][$i])) {
                        $dateId = (int)$_POST['existing_date_ids'][$i];
                        $departureDate = $_POST['existing_departure_dates'][$i];
                        $availableSeats = (int)$_POST['existing_available_seats'][$i];
                        $status = $_POST['existing_date_status'][$i];
                        
                        updateTourDate($conn, $dateId, $departureDate, $availableSeats, $status);
                    }
                }
            }
            
            // Add new dates
            if (isset($_POST['new_departure_dates']) && isset($_POST['new_available_seats'])) {
                for ($i = 0; $i < count($_POST['new_departure_dates']); $i++) {
                    if (!empty($_POST['new_departure_dates'][$i])) {
                        $departureDate = $_POST['new_departure_dates'][$i];
                        $availableSeats = (int)$_POST['new_available_seats'][$i];
                        
                        addTourDate($conn, $tourId, $departureDate, $availableSeats);
                    }
                }
            }
            
            // Redirect to tour list with success message
            header('Location: tours.php?msg=updated');
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật tour. Vui lòng thử lại sau";
        }
    }
    
    // If there are errors, re-fetch the tour data to display current values
    $tour = getTourById($conn, $tourId);
}

// Handle image deletion via AJAX
if (isset($_POST['delete_image'])) {
    $imageId = (int)$_POST['image_id'];
    $success = deleteTourImage($conn, $imageId);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit;
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
                        <h1 class="m-0">Chỉnh sửa Tour</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../statistics/statistics.php">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="tours.php">Quản lý Tour</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa Tour</li>
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
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($tour['name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description" rows="6" required><?php echo htmlspecialchars($tour['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="location" class="form-label">Địa điểm <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($tour['location']); ?>" required>
                                            <div class="form-text">Ví dụ: Hà Nội, Đà Nẵng, Huế, ...</div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="duration" class="form-label">Thời gian <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($tour['duration']); ?>" required>
                                            <div class="form-text">Ví dụ: 3 ngày 2 đêm, 5 ngày 4 đêm, ...</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="link_map" class="form-label">Link bản đồ (Google Maps iframe)</label>
                                        <textarea class="form-control" id="link_map" name="link_map" rows="3" placeholder="Dán link iframe từ Google Maps ở đây"><?php echo htmlspecialchars($tour['link_map'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="price_adult" class="form-label">Giá vé người lớn (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="price_adult" name="price_adult" value="<?php echo (int)$tour['price_adult']; ?>" min="0" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="price_child" class="form-label">Giá vé trẻ em (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="price_child" name="price_child" value="<?php echo (int)$tour['price_child']; ?>" min="0" required>
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
                                    <!-- Existing dates -->
                                    <?php if (!empty($tour['dates'])): ?>
                                        <h6 class="text-muted mb-3">Lịch hiện tại</h6>
                                        <div id="existing-dates-container">
                                            <?php foreach ($tour['dates'] as $date): ?>
                                                <div class="tour-date-item mb-3" data-date-id="<?php echo $date['date_id']; ?>">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Ngày khởi hành</label>
                                                            <input type="date" class="form-control" name="existing_departure_dates[]" value="<?php echo $date['departure_date']; ?>">
                                                            <input type="hidden" name="existing_date_ids[]" value="<?php echo $date['date_id']; ?>">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label">Số chỗ trống</label>
                                                            <input type="number" class="form-control" name="existing_available_seats[]" min="0" value="<?php echo $date['available_seats']; ?>">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label">Trạng thái</label>
                                                            <select class="form-select" name="existing_date_status[]">
                                                                <option value="available" <?php echo $date['status'] == 'available' ? 'selected' : ''; ?>>Còn chỗ</option>
                                                                <option value="full" <?php echo $date['status'] == 'full' ? 'selected' : ''; ?>>Hết chỗ</option>
                                                                <option value="cancelled" <?php echo $date['status'] == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end mb-2">
                                                            <button type="button" class="btn btn-outline-danger btn-sm remove-existing-date" data-date-id="<?php echo $date['date_id']; ?>">
                                                                <i class="fas fa-times"></i> Xóa
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <hr>
                                    <?php endif; ?>
                                    
                                    <!-- New dates -->
                                    <h6 class="text-muted mb-3">Thêm lịch mới</h6>
                                    <div id="new-dates-container"></div>
                                    
                                    <div class="text-center" id="no-new-dates-message">
                                        <p class="text-muted">Chưa có lịch khởi hành mới nào. Nhấn "Thêm lịch" để thêm.</p>
                                    </div>
                                    
                                    <!-- Hidden input for deleted date IDs -->
                                    <input type="hidden" name="delete_date_ids" id="delete_date_ids" value="">
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
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="active" <?php echo $tour['status'] == 'active' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="status">
                                            Kích hoạt tour
                                        </label>
                                    </div>
                                    <div class="form-text">Tour sẽ được hiển thị trên trang web nếu được kích hoạt.</div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Images Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Hình ảnh</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Image 1 -->
                                    <div class="mb-3">
                                        <label for="image1" class="form-label">Hình ảnh 1 (Ảnh chính) <span class="text-danger">*</span></label>
                                        <div class="mb-2">
                                            <img src="<?php echo !empty($tour['image1']) ? '../../' . $tour['image1'] : '../../assets/img/no_image.png'; ?>" alt="Tour Image" class="img-fluid rounded mb-3" id="image1-preview">
                                        </div>
                                        <input type="file" class="form-control" id="image1" name="image1" accept="image/*">
                                        <div class="form-text">Hình ảnh 1 sẽ được sử dụng làm ảnh chính hiển thị đầu tiên.</div>
                                    </div>
                                    
                                    <!-- Image 2 -->
                                    <div class="mb-3">
                                        <label for="image2" class="form-label">Hình ảnh 2</label>
                                        <div class="mb-2">
                                            <img src="<?php echo !empty($tour['image2']) ? '../../' . $tour['image2'] : '../../assets/img/no_image.png'; ?>" alt="Tour Image" class="img-thumbnail mb-2" id="image2-preview">
                                        </div>
                                        <input type="file" class="form-control" id="image2" name="image2" accept="image/*">
                                    </div>
                                    
                                    <!-- Image 3 -->
                                    <div class="mb-3">
                                        <label for="image3" class="form-label">Hình ảnh 3</label>
                                        <div class="mb-2">
                                            <img src="<?php echo !empty($tour['image3']) ? '../../' . $tour['image3'] : '../../assets/img/no_image.png'; ?>" alt="Tour Image" class="img-thumbnail mb-2" id="image3-preview">
                                        </div>
                                        <input type="file" class="form-control" id="image3" name="image3" accept="image/*">
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
            // Image 1 preview
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
            
            // Image 2 preview
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
            
            // Image 3 preview
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
            
            // Store deleted date IDs
            let deletedDateIds = [];
            
            // Remove existing date
            $(document).on("click", ".remove-existing-date", function() {
                const dateId = $(this).data("date-id");
                const dateItem = $(this).closest(".tour-date-item");
                
                if (confirm("Bạn có chắc chắn muốn xóa lịch khởi hành này?")) {
                    // Add the date ID to the deleted IDs list
                    deletedDateIds.push(dateId);
                    // Update the hidden input with the deleted IDs
                    $("#delete_date_ids").val(deletedDateIds.join(','));
                    
                    // Remove the date item from the DOM
                    dateItem.fadeOut('fast', function() {
                        $(this).remove();
                        if ($("#existing-dates-container .tour-date-item").length === 0) {
                            $("#existing-dates-container").html('<p class="text-muted">Không có lịch khởi hành nào.</p>');
                        }
                    });
                }
            });
            
            // Add new date field
            $("#addDateBtn").click(function() {
                $("#no-new-dates-message").hide();
                
                const dateItem = `
                    <div class="tour-date-item mb-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Ngày khởi hành</label>
                                <input type="date" class="form-control" name="new_departure_dates[]">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label">Số chỗ trống</label>
                                <input type="number" class="form-control" name="new_available_seats[]" min="1" value="20">
                            </div>
                            <div class="col-md-1 d-flex align-items-end mb-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-new-date">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                $("#new-dates-container").append(dateItem);
                checkEmptyNewDates();
            });
            
            // Remove new date field
            $(document).on("click", ".remove-new-date", function() {
                $(this).closest(".tour-date-item").remove();
                checkEmptyNewDates();
            });
            
            // Check if there are no new date fields
            function checkEmptyNewDates() {
                if ($("#new-dates-container .tour-date-item").length === 0) {
                    $("#no-new-dates-message").show();
                } else {
                    $("#no-new-dates-message").hide();
                }
            }
            
            // Initialize
            checkEmptyNewDates();
        });
    </script>
</body>
</html> 