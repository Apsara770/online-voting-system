<?php
require_once '../includes/auth.php'; // Ensure authentication is handled
require_once '../includes/db.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Handle case where user is not found, redirect to login perhaps
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard | Online Voting System</title>
    <link rel="stylesheet" href="../assets/css/myglobal.css"> <link rel="stylesheet" href="css/myvoter-style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="dashboard-wrapper">
        <header class="dashboard-header">
            <div class="container">
                <div class="header-left">
                    <span class="logo-text">VoteSmart</span>
        
                </div>
                <div class="header-right">
                    <span class="welcome-message">Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</span>
                    <a href="../index.php" class="btn btn-logout">Logout</a>
                </div>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="container">
                <section class="quick-actions">
                    <h2>Quick Actions</h2>
                    <div class="action-grid">
                        <a href="vote.php" class="action-card btn-primary">
                            <i class="fas fa-vote-yea"></i>
                            <h3>Vote in Elections</h3>
                            <p>Participate in ongoing elections.</p>
                        </a>
                        <a href="history.php" class="action-card btn-secondary">
                            <i class="fas fa-history"></i>
                            <h3>View Voting History</h3>
                            <p>Review your past voting records.</p>
                        </a>
                        </div>
                </section>

  
            </div>
        </main>

        <footer class="dashboard-footer">
            <div class="container">
                <p>&copy; <?php echo date("Y"); ?> VoteSmart. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>