<?php
include 'job_db.php';

// fetch all job postings
$result = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Jobs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      width: 225px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #032D84; /* Job Board color */
      padding-top: 20px;
    }
    .sidebar h2 {
      color: #F7AB52;
      text-align: center;
      font-weight: bold;
      margin-bottom: 30px;
    }
    .sidebar a {
      padding: 12px 20px;
      display: block;
      color: #FEFEFE;
      text-decoration: none;
      border-radius: 6px;
      margin-bottom: 5px;
    }
    .sidebar a:hover {
      background-color: #F7AB52;
      color: #032D84;
    }
    .main-content {
      margin-left: 225px;
      padding: 40px;
    }
    .job-card {
      background: #FEFEFE;
      border: 1px solid #ddd;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .card-badge {
      background-color: #F7AB52;
      color: #032D84;
      padding: 0.2rem 0.5rem;
      border-radius: 5px;
      font-size: 0.8rem;
    }
    .btn-apply {
      background-color: #032D84;
      color: #FEFEFE;
      width: 100%;
    }
    .btn-apply:hover {
      background-color: #021f5d;
      color: #FEFEFE;
    }
  </style>
</head>
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

<!-- Main Content -->
<div class="main-content">
  <h2 style="color:#032D84;">Available Jobs</h2>

  <div class="row">
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-3">
          <div class="job-card shadow-sm">
            <?php if(isset($row['category'])): ?>
              <span class="card-badge"><?= htmlspecialchars($row['category']); ?></span>
            <?php endif; ?>
            <h4 style="color:#032D84;"><?= htmlspecialchars($row['title']); ?></h4>
            <p><?= nl2br(htmlspecialchars(substr($row['description'], 0, 100))); ?>...</p>
            <small class="text-muted">Posted on: <?= $row['created_at']; ?></small>
            <button class="btn btn-apply mt-2" data-bs-toggle="modal" data-bs-target="#applyJobModal<?= $row['id']; ?>">Apply Now</button>
          </div>
        </div>

        <!-- Apply Modal -->
        <div class="modal fade" id="applyJobModal<?= $row['id']; ?>" tabindex="-1">
          <div class="modal-dialog">
            <form class="modal-content" method="POST" action="submit_application.php" enctype="multipart/form-data">
              <div class="modal-header" style="background-color:#032D84; color:#FEFEFE;">
                <h5 class="modal-title">Apply for <?= htmlspecialchars($row['title']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="job_id" value="<?= $row['id']; ?>">
                <div class="mb-2">
                  <input type="text" name="applicant_name" class="form-control" placeholder="Your Name" required>
                </div>
                <div class="mb-2">
                  <input type="email" name="applicant_email" class="form-control" placeholder="Your Email" required>
                </div>
                <div class="mb-2">
                  <textarea name="cover_letter" class="form-control" rows="4" placeholder="Why should we hire you?" required></textarea>
                </div>
                <div class="mb-2">
                  <label>Upload Resume (PDF/DOC/DOCX)</label>
                  <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn" style="background-color:#F7AB52; color:#032D84;">Submit Application</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </div>
        </div>

      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">No jobs available yet.</p>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
