<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

redirectToLogin();

if (!isAdmin()) {
    header('Location: ../pages/dashboard.php');
    exit();
}

$stats_query = "SELECT COUNT(*) as total_bookings, SUM(total_cost) as total_revenue FROM bookings";
$stats = $conn->query($stats_query)->fetch_assoc();

$users_query = "SELECT COUNT(*) as total_users FROM users WHERE user_type = 'customer'";
$users = $conn->query($users_query)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - EboStay</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="admin-panel">
        <div class="admin-container">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="admin-logo">EboStay Admin</div>
                <nav class="admin-nav">
                    <a href="admin-panel.php" class="admin-nav-item active">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a href="admin-panel.php?section=packages" class="admin-nav-item">
                        <i class="fas fa-box"></i> Manage Packages
                    </a>
                    <a href="admin-panel.php?section=bookings" class="admin-nav-item">
                        <i class="fas fa-calendar"></i> Bookings
                    </a>
                    <a href="admin-panel.php?section=expenses" class="admin-nav-item">
                        <i class="fas fa-chart-bar"></i> Expenses
                    </a>
                    <a href="admin-panel.php?section=ai-assistant" class="admin-nav-item">
                        <i class="fas fa-robot"></i> AI Assistant
                    </a>
                    <a href="admin-panel.php?section=customers" class="admin-nav-item">
                        <i class="fas fa-users"></i> Customers
                    </a>
                    <a href="logout.php" class="admin-nav-item logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="admin-content">
                <div class="admin-header">
                    <h1>Admin Dashboard</h1>
                    <p>Welcome, <?php echo $_SESSION['user_name']; ?></p>
                </div>

                <?php
                $section = $_GET['section'] ?? 'dashboard';

                if ($section == 'dashboard') {
                    ?>
                    <!-- Stats -->
                    <div class="admin-stats">
                        <div class="stat-box">
                            <i class="fas fa-book"></i>
                            <div>
                                <h3><?php echo $stats['total_bookings']; ?></h3>
                                <p>Total Bookings</p>
                            </div>
                        </div>
                        <div class="stat-box">
                            <i class="fas fa-rupee-sign"></i>
                            <div>
                                <h3>₹<?php echo $stats['total_revenue']; ?></h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                        <div class="stat-box">
                            <i class="fas fa-users"></i>
                            <div>
                                <h3><?php echo $users['total_users']; ?></h3>
                                <p>Total Customers</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="admin-section">
                        <h2>Recent Bookings</h2>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_query = "SELECT b.*, u.full_name, p.name FROM bookings b JOIN users u ON b.user_id = u.id JOIN packages p ON b.package_id = p.id LIMIT 5";
                                $recent = $conn->query($recent_query);
                                while ($booking = $recent->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $booking['full_name']; ?></td>
                                        <td><?php echo $booking['name']; ?></td>
                                        <td>₹<?php echo $booking['total_cost']; ?></td>
                                        <td><span class="status-badge <?php echo strtolower($booking['status']); ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                        <td><?php echo date('d M Y', strtotime($booking['created_at'])); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } elseif ($section == 'packages') {
                    ?>
                    <div class="admin-section">
                        <div class="section-header">
                            <h2>Manage Packages</h2>
                            <button class="btn btn-primary" onclick="showAddPackageForm()">+ Add Package</button>
                        </div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Destination</th>
                                    <th>Duration</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $packages = $conn->query("SELECT * FROM packages");
                                while ($pkg = $packages->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $pkg['name']; ?></td>
                                        <td><?php echo $pkg['destination']; ?></td>
                                        <td><?php echo $pkg['duration']; ?> days</td>
                                        <td>₹<?php echo $pkg['price']; ?></td>
                                        <td>
                                            <button class="btn-small edit">Edit</button>
                                            <button class="btn-small delete">Delete</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } elseif ($section == 'bookings') {
                    ?>
                    <div class="admin-section">
                        <h2>All Bookings</h2>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $all_bookings = $conn->query("SELECT b.*, u.full_name, p.name FROM bookings b JOIN users u ON b.user_id = u.id JOIN packages p ON b.package_id = p.id");
                                while ($booking = $all_bookings->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $booking['full_name']; ?></td>
                                        <td><?php echo $booking['name']; ?></td>
                                        <td><?php echo date('d M Y', strtotime($booking['check_in'])); ?></td>
                                        <td><?php echo date('d M Y', strtotime($booking['check_out'])); ?></td>
                                        <td><span class="status-badge <?php echo strtolower($booking['status']); ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                        <td><button class="btn-small">Manage</button></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } elseif ($section == 'expenses') {
                    ?>
                    <div class="admin-section">
                        <div class="section-header">
                            <h2>Manage Expenses</h2>
                            <button class="btn btn-primary" onclick="showAddExpenseForm()">+ Add Expense</button>
                        </div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $expenses = $conn->query("SELECT * FROM expenses ORDER BY date DESC LIMIT 10");
                                if ($expenses->num_rows > 0) {
                                    while ($expense = $expenses->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?php echo $expense['category']; ?></td>
                                            <td><?php echo $expense['description']; ?></td>
                                            <td>₹<?php echo $expense['amount']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($expense['date'])); ?></td>
                                            <td>
                                                <button class="btn-small edit">Edit</button>
                                                <button class="btn-small delete">Delete</button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No expenses recorded</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } elseif ($section == 'ai-assistant') {
                    ?>
                    <div class="admin-section">
                        <h2>AI Assistant - Gemini Integration</h2>
                        <div class="ai-box">
                            <form id="aiForm" class="ai-form">
                                <div class="form-group">
                                    <label>Ask the AI Assistant</label>
                                    <textarea id="aiPrompt" placeholder="e.g., Help me optimize tour pricing or suggest tour combinations..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Get AI Suggestions</button>
                            </form>
                            <div id="aiResponse" class="ai-response" style="display:none;">
                                <h3>AI Response</h3>
                                <div id="responseContent"></div>
                            </div>
                        </div>
                    </div>
                    <?php
                } elseif ($section == 'customers') {
                    ?>
                    <div class="admin-section">
                        <h2>All Customers</h2>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Joined</th>
                                    <th>Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $customers = $conn->query("SELECT u.*, COUNT(b.id) as booking_count FROM users u LEFT JOIN bookings b ON u.id = b.user_id WHERE u.user_type = 'customer' GROUP BY u.id");
                                while ($customer = $customers->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $customer['full_name']; ?></td>
                                        <td><?php echo $customer['email']; ?></td>
                                        <td><?php echo $customer['phone'] ?? 'N/A'; ?></td>
                                        <td><?php echo date('d M Y', strtotime($customer['created_at'])); ?></td>
                                        <td><?php echo $customer['booking_count']; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                }
                ?>
            </main>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>