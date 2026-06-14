<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

redirectToLogin();

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    logout();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$bookings_query = "SELECT b.*, p.name, p.destination FROM bookings b JOIN packages p ON b.package_id = p.id WHERE b.user_id = ? ORDER BY b.created_at DESC";
$stmt = $conn->prepare($bookings_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EboStay</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Welcome, <?php echo $user['full_name']; ?>!</h1>
                <p>Manage your bookings and explore new adventures</p>
            </div>

            <div class="dashboard-grid">
                <!-- Sidebar -->
                <aside class="dashboard-sidebar">
                    <div class="user-card">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3><?php echo $user['full_name']; ?></h3>
                        <p><?php echo $user['email']; ?></p>
                    </div>

                    <nav class="sidebar-nav">
                        <a href="dashboard.php" class="nav-item active">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a href="dashboard.php?tab=bookings" class="nav-item">
                            <i class="fas fa-ticket-alt"></i> My Bookings
                        </a>
                        <a href="dashboard.php?tab=customize" class="nav-item">
                            <i class="fas fa-wand-magic-sparkles"></i> Customize Tour
                        </a>
                        <a href="dashboard.php?tab=profile" class="nav-item">
                            <i class="fas fa-user-edit"></i> Profile
                        </a>
                        <a href="pages/logout.php" class="nav-item logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="dashboard-content">
                    <?php
                    $tab = $_GET['tab'] ?? 'overview';

                    if ($tab == 'overview') {
                        ?>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <i class="fas fa-suitcase"></i>
                                <h3><?php echo $bookings->num_rows; ?></h3>
                                <p>Total Bookings</p>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-check-circle"></i>
                                <h3>2</h3>
                                <p>Completed Trips</p>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-hourglass-half"></i>
                                <h3>1</h3>
                                <p>Pending Bookings</p>
                            </div>
                        </div>

                        <h2>Recent Bookings</h2>
                        <?php
                    } elseif ($tab == 'bookings') {
                        ?>
                        <h2>My Bookings</h2>
                        <?php
                    } elseif ($tab == 'customize') {
                        ?>
                        <h2>Customize Your Tour with AI</h2>
                        <div class="customize-section">
                            <form id="customizeForm" class="customize-form">
                                <div class="form-group">
                                    <label>Select Package</label>
                                    <select id="packageSelect" required>
                                        <option value="">Choose a package...</option>
                                        <?php
                                        $pkg_query = "SELECT id, name FROM packages";
                                        $pkg_result = $conn->query($pkg_query);
                                        while ($pkg = $pkg_result->fetch_assoc()) {
                                            echo '<option value="' . $pkg['id'] . '">' . $pkg['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Custom Budget (₹)</label>
                                    <input type="number" id="budget" placeholder="Enter your budget">
                                </div>
                                <div class="form-group">
                                    <label>Special Requirements</label>
                                    <textarea id="requirements" placeholder="Tell us what you're looking for..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Generate AI Suggestions</button>
                            </form>
                            <div id="aiSuggestions" class="ai-suggestions" style="display:none;">
                                <h3>AI-Powered Suggestions</h3>
                                <div id="suggestionsContent"></div>
                            </div>
                        </div>
                        <?php
                    } elseif ($tab == 'profile') {
                        ?>
                        <h2>Profile Settings</h2>
                        <div class="profile-form">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" value="<?php echo $user['full_name']; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" value="<?php echo $user['email']; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" value="<?php echo $user['phone'] ?? ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea><?php echo $user['address'] ?? ''; ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="bookings-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Destination</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($bookings->num_rows > 0) {
                                    $bookings->data_seek(0);
                                    while ($booking = $bookings->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo $booking['name']; ?></td>
                                            <td><?php echo $booking['destination']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($booking['check_in'])); ?></td>
                                            <td><?php echo date('d M Y', strtotime($booking['check_out'])); ?></td>
                                            <td>₹<?php echo $booking['total_cost']; ?></td>
                                            <td><span class="status-badge <?php echo strtolower($booking['status']); ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                            <td><button class="btn-small">View Details</button></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center">No bookings yet. <a href="index.php">Browse packages</a></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>