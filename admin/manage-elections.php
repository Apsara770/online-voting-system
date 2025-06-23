<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];
$message = "";
$editing = false;
$edit_election = null;

// Fetch admin name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin ? $admin['name'] : 'Admin';

// Delete election
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM elections WHERE id = $delete_id");
    header("Location: manage-elections.php");
    exit;
}

// Edit election (populate form)
if (isset($_GET['edit'])) {
    $editing = true;
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM elections WHERE id = $edit_id");
    $edit_election = $result->fetch_assoc();
}

// Update election
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare("UPDATE elections SET title=?, description=?, start_time=?, end_time=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $description, $start_time, $end_time, $id);
    if ($stmt->execute()) {
        $message = "✅ Election updated successfully!";
    } else {
        $message = "❌ Failed to update election.";
    }
    header("Location: manage-elections.php");
    exit;
}

// Add election
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_id']) && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare("INSERT INTO elections (title, description, start_time, end_time, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->bind_param("ssss", $title, $description, $start_time, $end_time);
    if ($stmt->execute()) {
        $message = "✅ Election added successfully!";
    } else {
        $message = "❌ Failed to add election.";
    }
    header("Location: manage-elections.php");
    exit;
}

// Fetch all elections
$elections = $conn->query("SELECT * FROM elections ORDER BY start_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Elections | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/myglobal.css">
    <link rel="stylesheet" href="css/myadmin-style.css">
     <link rel="stylesheet" href="css/elections-manager.css">
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
                <li><a href="manage-elections.php" class="active"><i class="fas fa-list-alt"></i> Elections</a></li>
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

        <main class="admin-content-area container">
            <?php if ($message): ?>
                <div class="alert-message"><?php echo $message; ?></div>
            <?php endif; ?>

            <section class="add-election-form">
                <h2><?php echo $editing ? "Edit Election ✏️" : "Add New Election 🗳️"; ?></h2>
                <form method="POST">
                    <?php if ($editing): ?>
                        <input type="hidden" name="update_id" value="<?php echo $edit_election['id']; ?>">
                    <?php endif; ?>

                    <label>Title:</label>
                    <input type="text" name="title" required value="<?php echo $editing ? htmlspecialchars($edit_election['title']) : ''; ?>">

                    <label>Description:</label>
                    <textarea name="description" required><?php echo $editing ? htmlspecialchars($edit_election['description']) : ''; ?></textarea>

                    <label>Start Time:</label>
                    <input type="datetime-local" name="start_time" required value="<?php echo $editing ? date('Y-m-d\TH:i', strtotime($edit_election['start_time'])) : ''; ?>">

                    <label>End Time:</label>
                    <input type="datetime-local" name="end_time" required value="<?php echo $editing ? date('Y-m-d\TH:i', strtotime($edit_election['end_time'])) : ''; ?>">

                    <button type="submit" class="btn btn-primary">
                        <?php echo $editing ? "Update Election" : "Add Election"; ?>
                    </button>
                    <?php if ($editing): ?>
                        <a href="manage-elections.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </section>

            <section class="election-list">
                <h2>Existing Elections 📋</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($election = $elections->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($election['title']); ?></td>
                            <td><?php echo htmlspecialchars($election['description']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($election['start_time'])); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($election['end_time'])); ?></td>
                            <td><?php echo ucfirst($election['status']); ?></td>
                            <td>
                                <a href="?edit=<?php echo $election['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete=<?php echo $election['id']; ?>" onclick="return confirm('Are you sure you want to delete this election?')" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
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
