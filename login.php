<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } elseif ($user['role'] === 'voter') {
                header("Location: voter/dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Online Voting System</title>
    <link rel="stylesheet" href="assets/css/mylogin.css">
      <link rel="stylesheet" href="assets/css/myglobal.css">
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <h2 class="form-title">Login to VoteSmart</h2>
            <?php if ($error): ?>
                <p class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" class="login-form">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your@example.com" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full-width">Login</button>
            </form>
            <p class="signup-link">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>