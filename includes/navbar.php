<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="container">
        <div class="logo">EboStay</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="#packages">Packages</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php" class="btn-nav">Dashboard</a></li>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <li><a href="admin-panel.php" class="btn-nav admin">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="btn-nav logout">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-nav">Login</a></li>
                <li><a href="signup.php" class="btn-nav signup">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>