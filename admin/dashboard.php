<?php
require_once '../includes/auth.php'; // Make sure admin is logged in
require_once '../includes/db.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect non-admins
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch admin name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin ? $admin['name'] : 'Admin';

// Get dashboard stats
$total_elections = $conn->query("SELECT COUNT(*) AS total FROM elections")->fetch_assoc()['total'];
$total_candidates = $conn->query("SELECT COUNT(*) AS total FROM candidates")->fetch_assoc()['total'];
$total_voters = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'voter'")->fetch_assoc()['total'];
$total_votes = $conn->query("SELECT COUNT(*) AS total FROM votes")->fetch_assoc()['total'];

// Get recent activity (without using created_at)
$recent_votes_query = $conn->prepare("
    SELECT e.title AS election_title, c.name AS candidate_name, u.name AS voter_name
    FROM votes v
    JOIN elections e ON v.election_id = e.id
    JOIN candidates c ON v.candidate_id = c.id
    JOIN users u ON v.voter_id = u.id
    LIMIT 5
");
$recent_votes_query->execute();
$recent_votes = $recent_votes_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Online Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/myglobal.css">
        <link rel="stylesheet" href="../assets/css/mylogin.css">
    <link rel="stylesheet" href="css/myadmin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo-link">
                    <img src="../assets/images/logo.png" alt="Logo">
                    <span>VoteSmart </span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="manage-elections.php"><i class="fas fa-list-alt"></i> Elections</a></li>
                    <li><a href="manage-candidates.php"><i class="fas fa-users"></i> Candidates</a></li>
                    <li><a href="voters.php"><i class="fas fa-user-friends"></i> Voters</a></li>
                    <li><a href="results.php"><i class="fas fa-chart-bar"></i> Results</a></li>
                 
                </ul>
            </nav>
        </aside>

        <div class="admin-main-content">
            <header class="admin-main-header">
                <div class="container">
                    <span class="welcome-message">Welcome, <?php echo htmlspecialchars($admin_name); ?> 👋</span>
                    <a href="../index.php" class="btn btn-logout">Logout</a>
                    <button class="toggle-sidebar-btn" id="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </header>

            <main class="admin-content-area">
                <div class="container">
                    <h1>Dashboard Overview 📊</h1>

                    <div class="dashboard-stats">
                        <div class="stat-card">
                            <i class="fas fa-calendar-check "></i>
                            <h3>Total Elections</h3>
                            <p class="stat-number"><?php echo $total_elections; ?></p>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-user-tie "></i>
                            <h3>Total Candidates</h3>
                            <p class="stat-number"><?php echo $total_candidates; ?></p>
                        </div>
        
                        <div class="stat-card">
                            <i class="fas fa-users "></i>
                            <h3>Total Voters</h3>
                            <p class="stat-number"><?php echo $total_voters; ?></p>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-vote-yea"></i>
                            <h3>Total Votes Cast</h3>
                            <p class="stat-number"><?php echo $total_votes; ?></p>
                        </div>
                    </div>

                    <section class="recent-activity-section">
                        <h2>Recent Voting Activity</h2>
                        <?php if ($recent_votes->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="data-table recent-votes-table">
                                    <thead>
                                        <tr>
                                            <th>Election</th>
                                            <th>Candidate</th>
                                            <th>Voter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($vote = $recent_votes->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($vote['election_title']); ?></td>
                                                <td><?php echo htmlspecialchars($vote['candidate_name']); ?></td>
                                                <td><?php echo htmlspecialchars($vote['voter_name']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="no-records-message"><i class="fas fa-info-circle"></i> No recent voting activity to display.</p>
                        <?php endif; ?>
                    </section>
                </div>
            </main>

            <footer class="admin-main-footer">
                <div class="container">
                    <p>&copy; <?php echo date("Y"); ?> VoteSmart Admin. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.querySelector('.admin-wrapper').classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>
