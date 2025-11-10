<?php
// Tour management functions

/**
 * Get all tours with pagination
 * 
 * @param object $conn Database connection
 * @param int $currentPage Current page number
 * @param int $itemsPerPage Items per page
 * @param string $search Search term
 * @param string $status Status filter
 * @return array Array of tours
 */
function getTours($conn, $currentPage = 1, $itemsPerPage = 10, $search = '', $status = 'all') {
    $tours = [];
    
    // Calculate offset
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    // Base query
    $sql = "SELECT t.* 
            FROM tours t 
            WHERE 1=1";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $searchTerm = "%$search%";
        $sql .= " AND (t.name LIKE ? OR t.location LIKE ?)";
    }
    
    // Add status filter if not 'all'
    if ($status != 'all') {
        $sql .= " AND t.status = ?";
    }
    
    // Add ordering and limit
    $sql .= " ORDER BY t.created_at DESC LIMIT ?, ?";
    
    // Prepare statement with appropriate bind parameters
    $stmt = $conn->prepare($sql);
    
    // Bind parameters based on conditions
    if (!empty($search) && $status != 'all') {
        $stmt->bind_param("sssii", $searchTerm, $searchTerm, $status, $offset, $itemsPerPage);
    } elseif (!empty($search)) {
        $stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $itemsPerPage);
    } elseif ($status != 'all') {
        $stmt->bind_param("sii", $status, $offset, $itemsPerPage);
    } else {
        $stmt->bind_param("ii", $offset, $itemsPerPage);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Get tour dates for each tour
            $row['dates'] = getTourDates($conn, $row['tour_id']);
            $tours[] = $row;
        }
    }
    
    return $tours;
}

/**
 * Get total count of tours for pagination
 * 
 * @param object $conn Database connection
 * @param string $search Search term
 * @param string $status Status filter
 * @return int Total number of tours
 */
function getTotalTourCount($conn, $search = '', $status = 'all') {
    // Base count query
    $sql = "SELECT COUNT(*) as total FROM tours WHERE 1=1";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $searchTerm = "%$search%";
        $sql .= " AND (name LIKE ? OR location LIKE ?)";
    }
    
    // Add status filter if not 'all'
    if ($status != 'all') {
        $sql .= " AND status = ?";
    }
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    
    // Bind parameters based on conditions
    if (!empty($search) && $status != 'all') {
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $status);
    } elseif (!empty($search)) {
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
    } elseif ($status != 'all') {
        $stmt->bind_param("s", $status);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['total'];
    }
    
    return 0;
}

/**
 * Get tour by ID
 * 
 * @param object $conn Database connection
 * @param int $tourId Tour ID
 * @return array|null Tour details or null if not found
 */
function getTourById($conn, $tourId) {
    $sql = "SELECT * FROM tours WHERE tour_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tourId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $tour = $result->fetch_assoc();
        
        // Get tour dates
        $tour['dates'] = getTourDates($conn, $tourId);
        
        return $tour;
    }
    
    return null;
}

/**
 * Get tour dates
 * 
 * @param object $conn Database connection
 * @param int $tourId Tour ID
 * @return array Array of tour dates
 */
function getTourDates($conn, $tourId) {
    $dates = [];
    
    $sql = "SELECT * FROM tour_dates WHERE tour_id = ? ORDER BY departure_date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tourId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dates[] = $row;
        }
    }
    
    return $dates;
}

/**
 * Add new tour
 * 
 * @param object $conn Database connection
 * @param array $tourData Tour data
 * @param array $dates Tour dates
 * @return int|bool New tour ID or false on failure
 */
function addTour($conn, $tourData, $dates = []) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert tour data
        $sql = "INSERT INTO tours (name, description, location, duration, price_adult, price_child, image1, image2, image3, link_map, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param(
            "ssssddsssss", 
            $tourData['name'], 
            $tourData['description'], 
            $tourData['location'], 
            $tourData['duration'], 
            $tourData['price_adult'], 
            $tourData['price_child'], 
            $tourData['image1'],
            $tourData['image2'],
            $tourData['image3'],
            $tourData['link_map'],
            $tourData['status']
        );
        $stmt->execute();
        
        // Get the new tour ID
        $tourId = $conn->insert_id;
        
        // Insert tour dates
        if (!empty($dates)) {
            foreach ($dates as $date) {
                $sql = "INSERT INTO tour_dates (tour_id, departure_date, available_seats) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $tourId, $date['departure_date'], $date['available_seats']);
                $stmt->execute();
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        return $tourId;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return false;
    }
}

/**
 * Update existing tour
 * 
 * @param object $conn Database connection
 * @param int $tourId Tour ID
 * @param array $tourData Tour data
 * @return bool True on success, false on failure
 */
function updateTour($conn, $tourId, $tourData) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update tour data
        $sql = "UPDATE tours 
                SET name = ?, description = ?, location = ?, duration = ?, 
                    price_adult = ?, price_child = ?, status = ?, image1 = ?, image2 = ?, image3 = ?, link_map = ?
                WHERE tour_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssddsssssi", 
            $tourData['name'], 
            $tourData['description'], 
            $tourData['location'], 
            $tourData['duration'], 
            $tourData['price_adult'], 
            $tourData['price_child'], 
            $tourData['status'],
            $tourData['image1'],
            $tourData['image2'],
            $tourData['image3'],
            $tourData['link_map'],
            $tourId
        );
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return false;
    }
}

/**
 * Delete tour
 * 
 * @param object $conn Database connection
 * @param int $tourId Tour ID
 * @return bool True on success, false on failure
 */
function deleteTour($conn, $tourId) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get tour to retrieve image paths
        $tour = getTourById($conn, $tourId);
        
        if ($tour) {
            // Delete image files if they exist
            $imagesToDelete = ['image1', 'image2', 'image3'];
            foreach ($imagesToDelete as $imageField) {
                if (!empty($tour[$imageField]) && file_exists('../../' . $tour[$imageField])) {
                    @unlink('../../' . $tour[$imageField]);
                }
            }
        }
        
        // Delete tour dates (cascade will handle this, but still good to be explicit)
        $sql = "DELETE FROM tour_dates WHERE tour_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tourId);
        $stmt->execute();
        
        // Delete tour
        $sql = "DELETE FROM tours WHERE tour_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tourId);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return false;
    }
}

/**
 * Delete tour image
 * 
 * @param string $imagePath Path to the image file
 * @return bool True on success, false on failure
 */
function deleteTourImage($imagePath) {
    if (!empty($imagePath) && file_exists('../../' . $imagePath)) {
        return @unlink('../../' . $imagePath);
    }
    return false;
}

/**
 * Add tour date
 * 
 * @param object $conn Database connection
 * @param int $tourId Tour ID
 * @param string $departureDate Departure date (YYYY-MM-DD)
 * @param int $availableSeats Available seats
 * @return bool True on success, false on failure
 */
function addTourDate($conn, $tourId, $departureDate, $availableSeats) {
    $sql = "INSERT INTO tour_dates (tour_id, departure_date, available_seats) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $tourId, $departureDate, $availableSeats);
    return $stmt->execute();
}

/**
 * Update tour date
 * 
 * @param object $conn Database connection
 * @param int $dateId Date ID
 * @param string $departureDate Departure date (YYYY-MM-DD)
 * @param int $availableSeats Available seats
 * @param string $status Status
 * @return bool True on success, false on failure
 */
function updateTourDate($conn, $dateId, $departureDate, $availableSeats, $status) {
    $sql = "UPDATE tour_dates SET departure_date = ?, available_seats = ?, status = ? WHERE date_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $departureDate, $availableSeats, $status, $dateId);
    return $stmt->execute();
}

/**
 * Delete tour date
 * 
 * @param object $conn Database connection
 * @param int $dateId Date ID
 * @return bool True on success, false on failure
 */
function deleteTourDate($conn, $dateId) {
    $sql = "DELETE FROM tour_dates WHERE date_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dateId);
    return $stmt->execute();
}

/**
 * Upload tour image
 * 
 * @param array $file $_FILES array element
 * @param string $targetDir Target directory
 * @return string|bool Uploaded file path on success, false on failure
 */
function uploadTourImage($file, $targetDir = 'uploads/tours/') {
    // Create directory if it doesn't exist
    if (!file_exists('../../' . $targetDir)) {
        mkdir('../../' . $targetDir, 0777, true);
    }
    
    // Check if file is an actual image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return false;
    }
    
    // Generate unique filename (shorter than before)
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = substr(uniqid(), -6) . '.' . $extension;
    $targetFile = $targetDir . $fileName;
    
    // Attempt to upload file
    if (move_uploaded_file($file['tmp_name'], '../../' . $targetFile)) {
        return $targetFile;
    } else {
        return false;
    }
}
