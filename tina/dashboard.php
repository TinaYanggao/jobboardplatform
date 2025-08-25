<?php
session_start();
include 'job_db.php';

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Get current user
$user = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JobEntry Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #FEFEFE;
    }
    .sidebar {
      height: 100vh;
      width: 225px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #032D84; /* Deep Blue */
      padding-top: 20px;
    }
    .sidebar h2 {
      color: #F7AB52; /* Accent Orange */
      text-align: center;
      font-weight: bold;
      margin-bottom: 30px;
    }
    .sidebar a {
      padding: 12px 20px;
      display: block;
      color: #FEFEFE; /* White text */
      text-decoration: none;
      font-weight: 500;
    }
    .sidebar a:hover {
      background-color: #F7AB52; /* Orange hover */
      color: #032D84; /* Deep Blue text */
    }
    .main-content {
      margin-left: 225px;
      padding: 20px;
    }
    .welcome-card {
      background-color: #032D84; /* Deep Blue */
      color: #FEFEFE;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
    }
    .welcome-card h2 {
      font-weight: bold;
    }
    .btn-explore {
      background-color: #F7AB52;
      color: #032D84;
      font-weight: bold;
      margin-right: 10px;
    }
    .btn-explore:hover {
      background-color: #e69f45;
      color: #032D84;
    }
    .btn-hire {
      background-color: #FEFEFE;
      color: #032D84;
      font-weight: bold;
    }
    .btn-hire:hover {
      background-color: #f2f2f2;
      color: #032D84;
    }
  </style>
  <script>
    function confirmLogout(event) {
      if (!confirm("Are you sure you want to sign out?")) {
        event.preventDefault();
      }
    }
  </script>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>JobEntry</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="find_jobs.php">Find Jobs</a>
    <a href="hire_talent.php">Hire Talent</a>
    <a href="profile.php">Profile</a>
    <a href="?logout=true" onclick="confirmLogout(event)">Sign Out</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="welcome-card">
      <h2>Welcome back, <?php echo htmlspecialchars($user); ?>!</h2>
      <p>Discover jobs, connect with top companies, and explore talent opportunities.</p>
      <a href="find_jobs.php" class="btn btn-explore">Explore Jobs</a>
      <a href="hire_talent.php" class="btn btn-hire">Hire Talent</a>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
