<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

if ($action == 'create_booking') {
    $package_id = $_POST['package_id'] ?? '';
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $number_of_people = $_POST['number_of_people'] ?? 1;
    $special_requests = $_POST['special_requests'] ?? '';

    if (empty($package_id) || empty($check_in) || empty($check_out)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit();
    }

    // Get package price
    $pkg_query = "SELECT price FROM packages WHERE id = ?";
    $stmt = $conn->prepare($pkg_query);
    $stmt->bind_param('i', $package_id);
    $stmt->execute();
    $package = $stmt->get_result()->fetch_assoc();

    if (!$package) {
        echo json_encode(['success' => false, 'error' => 'Package not found']);
        exit();
    }

    $total_cost = $package['price'] * $number_of_people;
    $booking_date = date('Y-m-d');

    $query = "INSERT INTO bookings (user_id, package_id, booking_date, check_in, check_out, number_of_people, total_cost, special_requests, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iisssids', $user_id, $package_id, $booking_date, $check_in, $check_out, $number_of_people, $total_cost, $special_requests);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking created successfully', 'booking_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create booking']);
    }
    exit();

} elseif ($action == 'cancel_booking') {
    $booking_id = $_POST['booking_id'] ?? '';

    // Verify user owns this booking
    $verify_query = "SELECT user_id FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();

    if (!$booking || $booking['user_id'] != $user_id) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }

    $query = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $booking_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to cancel booking']);
    }
    exit();

} elseif ($action == 'update_profile') {
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $query = "UPDATE users SET phone = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $phone, $address, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
    }
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
?>
