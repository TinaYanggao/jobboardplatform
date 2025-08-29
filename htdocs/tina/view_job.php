<?php
include 'job_db.php';

if (!isset($_GET['id'])) {
    die("Job not found.");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM jobs WHERE id = $id");

if ($result->num_rows == 0) {
    die("Job not found.");
}

$job = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($job['title']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .sidebar {
      height: 100vh;
      width: 225px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #002b80; /* Dark Blue */
      padding-top: 20px;
    }
    .sidebar h2 {
      color: white;
      text-align: center;
      font-weight: bold;
      margin-bottom: 30px;
    }
    .sidebar a {
      padding: 12px 20px;
      display: block;
      color: white;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #0040cc;
    }
    .main-content {
      margin-left: 225px;
      padding: 20px;
    }
    .welcome-card {
      background-color: #0d6efd; /* Bootstrap primary blue */
      color: white;
      padding: 40px;
      border-radius: 8px;
      text-align: center;
    }
    .welcome-card h2 {
      font-weight: bold;
    }
    .btn-warning {
      margin-right: 10px;
    }
  </style>
<body>
    <!-- Sidebar -->
<div class="sidebar">
    <h2>JobEntry</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="find_jobs.php">Find Jobs</a>
    <a href="hire_talent.php">Hire Talent</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Sign Out</a>
</div>

<div class="main-content">
  <h2><?php echo htmlspecialchars($job['title']); ?></h2>
  <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
  <p><strong>Requirements:</strong><br><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
  <p><strong>Contact Details:</strong><br><?php echo nl2br(htmlspecialchars($job['contact'])); ?></p>
  <p><strong>Posted On:</strong> <?php echo $job['created_at']; ?></p>


<a href="find_jobs.php" class="btn btn-secondary">Back to Jobs</a>

</div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>