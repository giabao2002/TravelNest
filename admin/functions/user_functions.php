<?php
// User management functions

/**
 * Get all users with pagination
 * 
 * @param object $conn Database connection
 * @param int $currentPage Current page number
 * @param int $itemsPerPage Items per page
 * @param string $search Search term
 * @param string $status Status filter
 * @return array Array of users
 */
function getUsers($conn, $currentPage = 1, $itemsPerPage = 10, $search = '', $status = 'all') {
    $users = [];
    
    // Calculate offset
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    // Base query
    $sql = "SELECT * 
            FROM users 
            WHERE 1=1";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $searchTerm = "%$search%";
        $sql .= " AND (email LIKE ? OR full_name LIKE ? OR phone LIKE ?)";
    }
    
    // Add status filter if not 'all'
    if ($status != 'all') {
        $sql .= " AND status = ?";
    }
    
    // Add ordering and limit
    $sql .= " ORDER BY created_at DESC LIMIT ?, ?";
    
    // Prepare statement with appropriate bind parameters
    $stmt = $conn->prepare($sql);
    
    // Bind parameters based on conditions
    if (!empty($search) && $status != 'all') {
        // 3 search params + 1 status param + 2 pagination params = 6 params
        $stmt->bind_param("ssssii", $searchTerm, $searchTerm, $searchTerm, $status, $offset, $itemsPerPage);
    } elseif (!empty($search)) {
        // 3 search params + 2 pagination params = 5 params
        $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $offset, $itemsPerPage);
    } elseif ($status != 'all') {
        // 1 status param + 2 pagination params = 3 params
        $stmt->bind_param("sii", $status, $offset, $itemsPerPage);
    } else {
        // Just 2 pagination params
        $stmt->bind_param("ii", $offset, $itemsPerPage);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

/**
 * Get total count of users for pagination
 * 
 * @param object $conn Database connection
 * @param string $search Search term
 * @param string $status Status filter
 * @return int Total number of users
 */
function getTotalUserCount($conn, $search = '', $status = 'all') {
    // Base count query
    $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $searchTerm = "%$search%";
        $sql .= " AND (email LIKE ? OR full_name LIKE ? OR phone LIKE ?)";
    }
    
    // Add status filter if not 'all'
    if ($status != 'all') {
        $sql .= " AND status = ?";
    }
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    
    // Bind parameters based on conditions
    if (!empty($search) && $status != 'all') {
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $status);
    } elseif (!empty($search)) {
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
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
 * Get user by ID
 * 
 * @param object $conn Database connection
 * @param int $userId User ID
 * @return array|null User details or null if not found
 */
function getUserById($conn, $userId) {
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Update user status (block/unblock)
 * 
 * @param object $conn Database connection
 * @param int $userId User ID
 * @param string $status New status ('active' or 'blocked')
 * @return bool True on success, false on failure
 */
function updateUserStatus($conn, $userId, $status) {
    $sql = "UPDATE users SET status = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $userId);
    return $stmt->execute();
}

/**
 * Get user booking history
 * 
 * @param object $conn Database connection
 * @param int $userId User ID
 * @return array Array of bookings
 */
function getUserBookings($conn, $userId) {
    $bookings = [];
    
    $sql = "SELECT b.*, t.name as tour_name, t.location, td.departure_date 
            FROM bookings b
            JOIN tour_dates td ON b.date_id = td.date_id
            JOIN tours t ON td.tour_id = t.tour_id
            WHERE b.user_id = ?
            ORDER BY b.booking_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    
    return $bookings;
} 