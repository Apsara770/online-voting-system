<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch admin name
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$admin_name = $admin ? $admin['name'] : 'Admin';

// Fetch all elections for dropdown
$elections = $conn->query("SELECT id, title FROM elections ORDER BY title");

// Set selected election
$selected_election_id = isset($_GET['election_id']) ? intval($_GET['election_id']) : null;

// Fetch vote results for selected election
$results = [];
$chart_labels = [];
$chart_votes = [];

if ($selected_election_id) {
    $stmt = $conn->prepare("
        SELECT c.name AS candidate_name, COUNT(v.id) AS vote_count
        FROM candidates c
        LEFT JOIN votes v ON c.id = v.candidate_id
        WHERE c.election_id = ?
        GROUP BY c.id
        ORDER BY vote_count DESC
    ");
    $stmt->bind_param("i", $selected_election_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $results[] = $row;
        $chart_labels[] = $row['candidate_name'];
        $chart_votes[] = $row['vote_count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Results | Admin</title>
    <link rel="stylesheet" href="../assets/css/myglobal.css">
    <link rel="stylesheet" href="css/myadmin-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-logo-link">
                <img src="../assets/images/logo.png" alt="Logo">
                <span>VoteSmart</span>
            </a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-elections.php"><i class="fas fa-list-alt"></i> Elections</a></li>
                <li><a href="manage-candidates.php"><i class="fas fa-users"></i> Candidates</a></li>
                <li><a href="voters.php"><i class="fas fa-user-friends"></i> Voters</a></li>
                <li><a href="results.php" class="active"><i class="fas fa-chart-bar"></i> Results</a></li>
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

        <main class="admin-content-area container">
            <h2>Election Results 📊</h2>

            <!-- Dropdown to select election -->
            <form method="GET" style="margin-bottom: 20px;">
                <label for="election_id">Select Election:</label>
                <select name="election_id" id="election_id" required onchange="this.form.submit()">
                    <option value="">-- Choose Election --</option>
                    <?php while ($row = $elections->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $selected_election_id == $row['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>

            <?php if ($selected_election_id): ?>
                <?php if (count($results) > 0): ?>
                    <canvas id="resultsChart" style="max-width: 600px; margin: 20px auto;"></canvas>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $r): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($r['candidate_name']); ?></td>
                                    <td><?php echo $r['vote_count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No votes recorded for this election.</p>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
    const ctx = document.getElementById('resultsChart');
    <?php if (!empty($chart_labels)): ?>
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Votes',
                data: <?php echo json_encode($chart_votes); ?>,
                backgroundColor: '#f2a900'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Vote Count Per Candidate'
                }
            }
        }
    });
    <?php endif; ?>
</script>
</body>
</html>
