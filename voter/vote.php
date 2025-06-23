<?php
require_once '../includes/auth.php'; // Ensure authentication is handled
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id'], $_POST['election_id'])) {
    $election_id = intval($_POST['election_id']);
    $candidate_id = intval($_POST['candidate_id']);

    // Check if voter already voted in this election
    $check = $conn->prepare("SELECT id FROM votes WHERE voter_id = ? AND election_id = ?");
    $check->bind_param("ii", $user_id, $election_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = " You’ve already voted in this election.";
        $message_type = 'error';
    } else {
        // Record vote
        $stmt = $conn->prepare("INSERT INTO votes (voter_id, election_id, candidate_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $election_id, $candidate_id);
        if ($stmt->execute()) {
            $message =  "Your vote has been submitted successfully!";
            $message_type = 'success';
        } else {
            $message = " Failed to record vote. Try again.";
            $message_type = 'error';
        }
    }
}

// Fetch user name for header
$user_name = '';
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
if ($user_data) {
    $user_name = $user_data['name'];
}

// Get active elections
$elections = $conn->query("SELECT * FROM elections WHERE status = 'active' ORDER BY start_time ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote | Online Voting System</title>
    <link rel="stylesheet" href="../assets/css/myglobal.css">
    <link rel="stylesheet" href="css/myvoter-style.css">
    <link rel="stylesheet" href="css/vote.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="dashboard-wrapper">
        <header class="dashboard-header">
            <div class="container">
                <div class="header-left">
                 
                         <img src="../assets/images/logo.png" alt="Voting System Logo">
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
                <section class="vote-section">
                    <h2>Vote in Active Elections 🗳️</h2>

                    <?php if ($message): ?>
                        <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
                    <?php endif; ?>

                    <?php if ($elections->num_rows > 0): ?>
                        <div class="election-list">
                            <?php while ($election = $elections->fetch_assoc()): ?>
                                <div class="election-card">
                                    <h3 class="election-title"><?php echo htmlspecialchars($election['title']); ?></h3>
                                    <p class="election-description"><?php echo htmlspecialchars($election['description']); ?></p>
                                    <p class="election-dates">
                                        <i class="fas fa-calendar-alt"></i> Starts: <?php echo date('M d, Y H:i A', strtotime($election['start_time'])); ?><br>
                                        <i class="fas fa-calendar-check"></i> Ends: <?php echo date('M d, Y H:i A', strtotime($election['end_time'])); ?>
                                    </p>

                                    <form method="POST" class="vote-form">
                                        <input type="hidden" name="election_id" value="<?php echo $election['id']; ?>">
                                        <div class="candidate-options-grid">
                                            <?php
                                            $stmt = $conn->prepare("SELECT * FROM candidates WHERE election_id = ?");
                                            $stmt->bind_param("i", $election['id']);
                                            $stmt->execute();
                                            $candidates = $stmt->get_result();

                                            if ($candidates->num_rows > 0):
                                                while ($candidate = $candidates->fetch_assoc()):
                                                    ?>
                                                    <label class="candidate-option-card">
                                                        <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" required>
                                                        <div class="candidate-info">
                                                            <span class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></span>
                                                            <?php if (!empty($candidate['party'])): ?>
                                                                <span class="candidate-party">(<?php echo htmlspecialchars($candidate['party']); ?>)</span>
                                                            <?php endif; ?>
                                                            <?php if (!empty($candidate['bio'])): ?>
                                                                <p class="candidate-bio"><?php echo htmlspecialchars($candidate['bio']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                     
                                                    </label>
                                                    <?php
                                                endwhile;
                                            else:
                                                echo "<p class='no-candidates'>No candidates listed for this election yet.</p>";
                                            endif;
                                            ?>
                                        </div>
                                       <div class="form-buttons">
    <button type="submit" class="btn btn-primary btn-submit-vote">Submit Your Vote</button>
  
</div>
  
                                    </form>
                                 
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-elections-message">
                            <p><i class="fas fa-info-circle"></i> No active elections available right now.</p>
                            <p>Please check back later or <a href="dashboard.php">return to dashboard</a>.</p>
                        </div>
                    <?php endif; ?>
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