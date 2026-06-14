<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $check_query = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (full_name, email, phone, password, user_type) VALUES (?, ?, ?, ?, 'customer')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssss', $full_name, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                $success = 'Account created successfully! Please login.';
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_type'] = 'customer';
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Error creating account';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - EboStay</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-form">
                <h1>Create Account</h1>
                <p>Join EboStay and start exploring</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone">
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </form>

                <p class="auth-footer">
                    Already have an account? <a href="login.php">Sign in here</a>
                </p>
            </div>
            <div class="auth-image">
                <img src="https://via.placeholder.com/400x500?text=Sign+Up" alt="Sign Up">
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>