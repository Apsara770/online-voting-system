<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Only allow admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];
$message = "";

// Fetch admin name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin ? $admin['name'] : 'Admin';

// Delete voter
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $delete_id AND role = 'voter'");
    header("Location: voters.php");
    exit;
}

// Fetch all voters
$voters = $conn->query("SELECT * FROM users WHERE role = 'voter' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Voters | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/myglobal.css">
    <link rel="stylesheet" href="css/myadmin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
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
                <li><a href="voters.php" class="active"><i class="fas fa-user-friends"></i> Voters</a></li>
                <li><a href="results.php"><i class="fas fa-chart-bar"></i> Results</a></li>
            </ul>
        </nav>
    </aside>

    <div class="admin-main-content">
        <header class="admin-main-header">
            <div class="container">
                <span class="welcome-message">Welcome, <?php echo htmlspecialchars($admin_name); ?> 👋</span>
                <a href="../logout.php" class="btn btn-logout">Logout</a>
                <button class="toggle-sidebar-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </header>

        <main class="admin-content-area container">
            <h2>Registered Voters 🧑‍💼</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($voter = $voters->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voter['name']); ?></td>
                            <td><?php echo htmlspecialchars($voter['email']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($voter['created_at'] ?? 'now')); ?></td>
                            <td>
                                <a href="?delete=<?php echo $voter['id']; ?>" onclick="return confirm('Delete this voter?')" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>

<script>
    document.getElementById('toggleSidebar').addEventListener('click', function () {
        document.querySelector('.admin-wrapper').classList.toggle('sidebar-collapsed');
    });
</script>
</body>
</html>
