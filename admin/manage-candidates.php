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

// Add new candidate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['election_id'], $_POST['name'], $_POST['bio'])) {
    $election_id = intval($_POST['election_id']);
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    $photo_path = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['photo']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $message = "❌ Only JPG, PNG, or GIF images are allowed.";
        } else {
            $upload_dir = '../assets/images/candidates/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
                $photo_path = 'assets/images/candidates/' . $filename;
            }
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO candidates (election_id, name, bio, photo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $election_id, $name, $bio, $photo_path);
        if ($stmt->execute()) {
            $message = "✅ Candidate added successfully!";
        } else {
            $message = "❌ Failed to add candidate.";
        }
    }
}

// Delete candidate and their photo
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    $result = $conn->query("SELECT photo FROM candidates WHERE id = $delete_id");
    $row = $result->fetch_assoc();
    if ($row && !empty($row['photo']) && file_exists('../' . $row['photo'])) {
        unlink('../' . $row['photo']);
    }

    $conn->query("DELETE FROM candidates WHERE id = $delete_id");
    header("Location: manage-candidates.php");
    exit;
}

// Fetch all candidates
$candidates = $conn->query("SELECT c.*, e.title AS election_title FROM candidates c JOIN elections e ON c.election_id = e.id ORDER BY c.id DESC");

// Fetch all elections for dropdown
$elections = $conn->query("SELECT id, title FROM elections ORDER BY title");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Candidates | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/myglobal.css">
    <link rel="stylesheet" href="css/myadmin-style.css">
    <link rel="stylesheet" href="css/manage-candidates.css">
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
                <li><a href="manage-candidates.php" class="active"><i class="fas fa-users"></i> Candidates</a></li>
                <li><a href="voters.php"><i class="fas fa-user-friends"></i> Voters</a></li>
                <li><a href="results.php"><i class="fas fa-chart-bar"></i> Results</a></li>
            </ul>
        </nav>
    </aside>

    <div class="admin-main-content">
        <header class="admin-main-header">
            <div class="container">
                <span class="welcome-message">Welcome, <?php echo htmlspecialchars($admin_name ?? ''); ?> 👋</span>
                <a href="../index.php" class="btn btn-logout">Logout</a>
                <button class="toggle-sidebar-btn" id="toggleSidebar"><i class="fas fa-bars"></i></button>
            </div>
        </header>

        <main class="admin-content-area container">
            <?php if ($message): ?>
                <div class="alert-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <section class="add-candidate-form">
                <h2>Add New Candidate 🏛️</h2>
                <form method="POST" enctype="multipart/form-data">
                    <label>Election:</label>
                    <select name="election_id" required>
                        <option value="">-- Select Election --</option>
                        <?php while ($e = $elections->fetch_assoc()): ?>
                            <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['title'] ?? ''); ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label>Name:</label>
                    <input type="text" name="name" required>

                    <label>Bio:</label>
                    <textarea name="bio" required></textarea>

                    <label>Photo:</label>
                    <input type="file" name="photo" accept="image/*" required>

                    <button type="submit" class="btn btn-primary">Add Candidate</button>
                </form>
            </section>

            <section class="candidate-list">
                <h2>Existing Candidates 📄</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Bio</th>
                            <th>Election</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $candidates->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($c['photo'])): ?>
                                        <img src="../<?php echo htmlspecialchars($c['photo']); ?>" alt="Candidate Photo" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                    <?php else: ?>
                                        <span>No photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($c['name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['bio'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($c['election_title'] ?? ''); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Delete this candidate?')" class="btn btn-danger btn-sm">Delete</a>
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
