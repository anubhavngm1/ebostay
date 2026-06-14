<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];
                
                if ($user['user_type'] == 'admin') {
                    header('Location: admin-panel.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'Email not found';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EboStay</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-form">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>

                <p class="auth-footer">
                    Don't have an account? <a href="signup.php">Sign up here</a>
                </p>
            </div>
            <div class="auth-image">
                <img src="https://via.placeholder.com/400x500?text=Login" alt="Login">
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>