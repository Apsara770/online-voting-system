<?php
require_once 'includes/db.php';
date_default_timezone_set('Asia/Colombo');

$current_time = date('Y-m-d H:i:s');

// Fetch all elections
$stmt = $conn->prepare("SELECT * FROM elections ORDER BY start_time DESC");
$stmt->execute();
$result = $stmt->get_result();
$elections = [];

while ($row = $result->fetch_assoc()) {
    $start = strtotime($row['start_time']);
    $end = strtotime($row['end_time']);
    $now = time();

    if ($now < $start) {
        $row['status'] = 'Upcoming';
    } elseif ($now >= $start && $now <= $end) {
        $row['status'] = 'Live';
    } else {
        $row['status'] = 'Ended';
    }

    $elections[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Secure Your Voice</title>
    <link rel="stylesheet" href="assets/css/myglobal.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
<header class="main-header">
    <div class="container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="Voting System Logo"> <span>VoteSmart</span>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="#about">About</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="login.php" class="nav-btn btn btn-primary-nav">Login</a></li>
                <li><a href="register.php" class="nav-btn btn btn-outline-nav">Register</a></li>
            </ul>
        </nav>
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span><span class="bar"></span><span class="bar"></span>
        </div>
    </div>
</header>

<main>
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Your Voice, Securely Counted.</h1>
                <p class="tagline">Experience seamless, transparent, and fair elections online.</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary btn-large">Get Started Now</a>
                    <a href="#how-it-works" class="btn btn-secondary btn-large">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <section class="current-elections-section">
        <div class="container">
            <h2>Current & Upcoming Elections</h2>
            <div class="election-cards-grid">
                <?php if (!empty($elections)): ?>
                    <?php foreach ($elections as $election): ?>
                        <div class="election-card">
                            <h3><?php echo htmlspecialchars($election['title']); ?></h3>
                            <p class="status <?php echo strtolower($election['status']); ?>">
                                <?php echo $election['status']; ?>
                            </p>
                            <p>Start: <?php echo date('F d, Y h:i A', strtotime($election['start_time'])); ?></p>
                            <p>End: <?php echo date('F d, Y h:i A', strtotime($election['end_time'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-elections-msg">No elections available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About / Features -->
    <section id="features-section" class="features-section">
        <div class="container">
            <h2 id="about">Why Choose VoteSmart?</h2>
            <div class="features-grid">
                <div class="feature-item"><i class="fas fa-shield-alt"></i><h3>Secure & Anonymous</h3><p>Your vote is protected with advanced encryption.</p></div>
                <div class="feature-item"><i class="fas fa-check-circle"></i><h3>Transparent Results</h3><p>Auditable and tamper-proof outcomes.</p></div>
                <div class="feature-item"><i class="fas fa-mobile-alt"></i><h3>Easy to Use</h3><p>Vote from any device, anywhere.</p></div>
                <div class="feature-item"><i class="fas fa-hourglass-half"></i><h3>Time-Efficient</h3><p>Vote in minutes without lines.</p></div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="how-it-works-section">
        <div class="container">
            <h2>How It Works</h2>
            <div class="steps-grid">
                <div class="step-item"><span class="step-number">1</span><h3>Register</h3><p>Create your account and verify.</p></div>
                <div class="step-item"><span class="step-number">2</span><h3>Browse Elections</h3><p>See all you are eligible for.</p></div>
                <div class="step-item"><span class="step-number">3</span><h3>Vote</h3><p>Select your candidate securely.</p></div>
                <div class="step-item"><span class="step-number">4</span><h3>Results</h3><p>View fair, real-time results.</p></div>
            </div>
        </div>
    </section>

    <section id="faq" class="faq-section">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3>Is my vote truly anonymous? <i class="fas fa-chevron-down faq-arrow"></i></h3>
                <p class="faq-answer">Yes, your vote cannot be traced back to you.</p>
            </div>
            <div class="faq-item">
                <h3>What if I encounter an issue? <i class="fas fa-chevron-down faq-arrow"></i></h3>
                <p class="faq-answer">Our team is available 24/7 to assist you.</p>
            </div>
            <div class="faq-item">
                <h3>Can I verify the results? <i class="fas fa-chevron-down faq-arrow"></i></h3>
                <p class="faq-answer">Yes. We provide cryptographic proofs for verification.</p>
            </div>
          
        </div>
    </section>
</main>

<footer class="main-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>VoteSmart</h3>
                <p>Empowering secure digital voting.</p>
                <p>&copy; <?php echo date("Y"); ?> VoteSmart</p>
            </div>
            <div class="footer-col">
                <h3>Links</h3>
                <ul>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Support</h3>
                <ul>
                 <li>Contact</li>
                    <li>Privacy</li>
                    <li>Terms</li>
                </ul>
            </div>
            <div class="footer-col social-links">
                <h3>Follow</h3>
                <a href="#"><img src="assets/images/fb.jpeg" alt="Facebook"></a>
                <a href="#"><img src="assets/images/tw.jpeg" alt="Twitter"></a>
                <a href="#"><img src="assets/images/lin.jpeg" alt="LinkedIn"></a>
            </div>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
