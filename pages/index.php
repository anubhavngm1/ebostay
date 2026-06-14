<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EboStay - Modern Travel & Booking Platform</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover Your Next Adventure</h1>
            <p>Explore beautiful destinations with personalized tour packages</p>
            <div class="search-box">
                <input type="text" placeholder="Search destinations..." id="searchInput">
                <button onclick="searchPackages()"><i class="fas fa-search"></i> Search</button>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://via.placeholder.com/600x400?text=Beautiful+Destination" alt="Hero">
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages" id="packages">
        <div class="container">
            <h2>Our Popular Packages</h2>
            <p class="subtitle">Choose from our curated collection of amazing destinations</p>
            
            <div class="filter-section">
                <button class="filter-btn active" onclick="filterPackages('all')">All</button>
                <button class="filter-btn" onclick="filterPackages('beach')">Beach</button>
                <button class="filter-btn" onclick="filterPackages('mountain')">Mountain</button>
                <button class="filter-btn" onclick="filterPackages('city')">City</button>
            </div>

            <div class="packages-grid" id="packagesGrid">
                <?php
                $query = "SELECT * FROM packages LIMIT 6";
                $result = $conn->query($query);
                
                if ($result && $result->num_rows > 0) {
                    while ($package = $result->fetch_assoc()) {
                        ?>
                        <div class="package-card" data-category="beach">
                            <div class="package-image">
                                <img src="<?php echo $package['image_url'] ?: 'https://via.placeholder.com/300x200?text=' . urlencode($package['name']); ?>" alt="<?php echo $package['name']; ?>">
                                <span class="badge">₹<?php echo $package['price']; ?></span>
                            </div>
                            <div class="package-info">
                                <h3><?php echo $package['name']; ?></h3>
                                <p class="destination"><i class="fas fa-map-marker-alt"></i> <?php echo $package['destination']; ?></p>
                                <p class="duration"><i class="fas fa-calendar"></i> <?php echo $package['duration']; ?> Days</p>
                                <p class="description"><?php echo substr($package['description'], 0, 80) . '...'; ?></p>
                                <div class="activities">
                                    <?php 
                                    $activities = explode(',', $package['activities']);
                                    foreach (array_slice($activities, 0, 3) as $activity) {
                                        echo '<span class="activity-tag">' . trim($activity) . '</span>';
                                    }
                                    ?>
                                </div>
                                <button class="btn-book" onclick="bookPackage(<?php echo $package['id']; ?>)">Book Now</button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No packages available yet.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="features">
        <div class="container">
            <h2>Why Choose EboStay?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-globe"></i>
                    <h3>Worldwide Destinations</h3>
                    <p>Explore amazing places across the globe with our carefully curated packages</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h3>Expert Guides</h3>
                    <p>Professional and experienced guides to make your journey unforgettable</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safe & Secure</h3>
                    <p>Your safety and satisfaction are our top priorities</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Round-the-clock customer support for all your needs</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-star"></i>
                    <h3>Best Price Guarantee</h3>
                    <p>We guarantee the best prices for your chosen packages</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-robot"></i>
                    <h3>AI Customization</h3>
                    <p>Customize your tour with our AI-powered travel assistant</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Start Your Journey?</h2>
            <p>Create an account or login to book your dream vacation</p>
            <?php if (!isLoggedIn()): ?>
                <div class="cta-buttons">
                    <a href="signup.php" class="btn btn-primary">Sign Up Now</a>
                    <a href="login.php" class="btn btn-secondary">Already Have Account?</a>
                </div>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>