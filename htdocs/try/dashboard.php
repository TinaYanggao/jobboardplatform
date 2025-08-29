<?php
session_start();
include 'job_db.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get current user name and email
$user = $_SESSION['full_name'] ?? 'User';
$user_email = $_SESSION['email'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch messages (both received and sent) grouped by conversation
$messages = [];
if ($user_email) {
    $stmt = $conn->prepare("
        SELECT m.*, 
               u.full_name AS sender_name, u.email AS sender_email,
               j.title AS job_title
        FROM messages m
        LEFT JOIN users u ON m.sender_id = u.user_id
        LEFT JOIN jobs j ON m.job_id = j.id
        WHERE m.receiver_email = ? OR m.sender_id = ?
        ORDER BY m.created_at ASC
    ");
    $stmt->bind_param("si", $user_email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $messages = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JobEntry Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
.sidebar { height: 100vh; width: 225px; position: fixed; top: 0; left: 0; background-color: #032D84; padding-top: 20px; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
.sidebar h2 { color: #ffffff; text-align: center; font-weight: bold; margin-bottom: 30px; }
.sidebar a { padding: 12px 20px; display: block; color: #ffffff; text-decoration: none; font-weight: 500; border-radius: 6px; margin: 5px 10px; transition: all 0.3s ease; }
.sidebar a:hover { background-color: #ffffff; color: #032D84; }
.main-content { margin-left: 225px; padding: 40px; }
.welcome-card { background-color: #032D84; color: #ffffff; padding: 50px; border-radius: 15px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.welcome-card h2 { font-weight: bold; margin-bottom: 15px; }
.welcome-card p { font-size: 1.1rem; margin-bottom: 25px; }
.btn-explore { background-color: #ffffff; color: #032D84; font-weight: 600; padding: 10px 20px; border-radius: 8px; margin-right: 10px; border: 2px solid #ffffff; }
.btn-explore:hover { background-color: #f0f0f0; color: #032D84; }
.btn-hire { background-color: transparent; color: #ffffff; font-weight: 600; padding: 10px 20px; border-radius: 8px; border: 2px solid #ffffff; }
.btn-hire:hover { background-color: #ffffff; color: #032D84; }
.message-sent { background:#032D84; color:#FEFEFE; text-align:right; padding:8px 12px; border-radius:12px; margin-bottom:6px; max-width:75%; align-self:flex-end; }
.message-received { background:#f1f3f5; color:#032D84; text-align:left; padding:8px 12px; border-radius:12px; margin-bottom:6px; max-width:75%; align-self:flex-start; }
.message-box { display:flex; flex-direction:column; max-height:250px; overflow-y:auto; padding:10px; border:1px solid #dee2e6; border-radius:8px; background-color:#ffffff; margin-bottom:10px; }
</style>
</head>
<body>

<div class="sidebar">
  <h2>JobEntry</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="find_job.php">Find Jobs</a>
  <a href="hire_talent.php">Hire Talent</a>
  <a href="profile.php">Profile</a>
  <a href="?logout=true" onclick="confirmLogout(event)">Sign Out</a>
</div>

<div class="main-content">
  <div class="welcome-card">
    <h2>Welcome back, <?php echo htmlspecialchars($user); ?>!</h2>
    <p>Discover jobs, connect with top companies, and explore talent opportunities.</p>
    <a href="find_job.php" class="btn btn-explore">Explore Jobs</a>
    <a href="hire_talent.php" class="btn btn-hire">Hire Talent</a>
  </div>

  <div class="mt-4">
    <h4>Messages</h4>
    <?php if (!empty($messages)): ?>
        <?php
        // Group messages by sender
        $conversations = [];
        foreach ($messages as $msg) {
            $key = $msg['sender_email'] ?? 'Unknown';
            $conversations[$key][] = $msg;
        }
        ?>
        <?php foreach ($conversations as $sender_email => $msgs): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <strong>From: <?php echo htmlspecialchars($msgs[0]['sender_name'] ?? $sender_email); ?></strong><br>
                    <?php if (!empty($msgs[0]['job_title'])): ?>
                        <small><em>Regarding Job: <?php echo htmlspecialchars($msgs[0]['job_title']); ?></em></small><br>
                    <?php endif; ?>

                    <div class="message-box">
                        <?php foreach ($msgs as $msg): ?>
                            <div class="<?= ($msg['sender_id'] == $user_id) ? 'message-sent' : 'message-received'; ?>">
                                <small class="d-block mb-1"><?= htmlspecialchars($msg['sender_name'] ?? $sender_email); ?> (<?= date('M d, H:i', strtotime($msg['created_at'])) ?>)</small>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($msg['message'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Reply form -->
                    <form method="POST" action="hire_talent.php" class="mt-2">
                        <input type="hidden" name="job_id" value="<?= htmlspecialchars($msgs[0]['job_id']); ?>">
                        <input type="hidden" name="receiver_email" value="<?= htmlspecialchars($sender_email); ?>">
                        <textarea name="message" class="form-control mb-2" placeholder="Write a reply..." required></textarea>
                        <button type="submit" name="send_message" class="btn btn-sm btn-primary">Send Reply</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No messages yet.</p>
    <?php endif; ?>
  </div>

</div>

<script>
function confirmLogout(event) {
  if (!confirm("Are you sure you want to sign out?")) {
    event.preventDefault();
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
