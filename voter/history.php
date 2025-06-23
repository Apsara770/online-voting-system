<?php
require_once '../includes/auth.php'; // Ensure authentication is handled
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch voting history (without created_at column)
$stmt = $conn->prepare("
    SELECT e.title AS election_title, e.description, c.name AS candidate_name
    FROM votes v
    JOIN elections e ON v.election_id = e.id
    JOIN candidates c ON v.candidate_id = c.id
    WHERE v.voter_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history = $stmt->get_result();

// Fetch user name for header
$user_name = '';
$stmt_user = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
if ($user_data) {
    $user_name = $user_data['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting History | Online Voting System</title>
    <link rel="stylesheet" href="../assets/css/myglobal.css">
    <link rel="stylesheet" href="css/myvoter-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
</head>
<body>
    <div class="dashboard-wrapper">
        <header class="dashboard-header">
            <div class="container">
                <div class="header-left">
                    <a href="dashboard.php" class="header-logo-link">
                        <img src="../assets/images/logo.png" alt="Voting System Logo">
                    </a>
                       <span>VoteSmart</span>
                </div>
                <div class="header-right">
                    <span class="welcome-message">Welcome, <?php echo htmlspecialchars($user_name); ?> 👋</span>
                    <a href="../index.php" class="btn btn-logout"> Logout</a>
                </div>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="container">
                <section class="history-section">
                    <h2>Your Voting History 📜</h2>

                    <?php if ($history->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table   class="data-table history-table" >
                                <thead>
                                    <tr>
                                        <th>Election Title</th>
                                        <th>Voted For</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $history->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['election_title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="no-history-message">
                            <p><i class="fas fa-info-circle"></i> You haven't voted in any election yet.</p>
                            <p>Go to the <a href="vote.php">Vote in Elections</a> page to cast your first vote!</p>
                        </div>
                    <?php endif; ?>
                    <br>
                      <a href="dashboard.php" class="btn btn-outline">← Back</a>
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
