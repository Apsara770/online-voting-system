<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm)) {
        if ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            // Check if email already exists
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Email is already registered.";
            } else {
                // Hash password and insert new voter
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'voter')");
                $stmt->bind_param("sss", $name, $email, $hashed);
                if ($stmt->execute()) {
                    $success = "Registered successfully. You can now login.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Online Voting System</title>
    <link rel="stylesheet" href="assets/css/myglobal.css">
    <link rel="stylesheet" href="assets/css/myregister.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="register-wrapper">
        <div class="register-container">
            <h2 class="form-title">Voter Registration</h2>

            <?php if ($error): ?>
                <p class="message error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p class="message success-message"><i class="fas fa-check-circle"></i> <?php echo $success; ?></p>
            <?php endif; ?>

            <form method="POST" class="register-form">
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your@example.com" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full-width">Register Account</button>
                <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </div>
</body>
</html>