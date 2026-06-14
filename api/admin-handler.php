<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action == 'add_package') {
    $name = $_POST['name'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $activities = $_POST['activities'] ?? '';

    if (empty($name) || empty($destination) || empty($duration) || empty($price)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit();
    }

    $query = "INSERT INTO packages (name, destination, duration, price, description, activities) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssidss', $name, $destination, $duration, $price, $description, $activities);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Package added successfully', 'package_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add package']);
    }
    exit();

} elseif ($action == 'update_package') {
    $package_id = $_POST['package_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($package_id)) {
        echo json_encode(['success' => false, 'error' => 'Package ID required']);
        exit();
    }

    $query = "UPDATE packages SET name = ?, price = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssii', $name, $price, $description, $package_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Package updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update package']);
    }
    exit();

} elseif ($action == 'delete_package') {
    $package_id = $_POST['package_id'] ?? '';

    if (empty($package_id)) {
        echo json_encode(['success' => false, 'error' => 'Package ID required']);
        exit();
    }

    $query = "DELETE FROM packages WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $package_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Package deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete package']);
    }
    exit();

} elseif ($action == 'add_expense') {
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');

    if (empty($category) || empty($amount)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit();
    }

    $query = "INSERT INTO expenses (category, description, amount, date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssds', $category, $description, $amount, $date);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Expense added successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add expense']);
    }
    exit();

} elseif ($action == 'update_booking_status') {
    $booking_id = $_POST['booking_id'] ?? '';
    $status = $_POST['status'] ?? '';

    if (empty($booking_id) || empty($status)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit();
    }

    $query = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $booking_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking status updated']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update booking']);
    }
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
?>
