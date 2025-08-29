<?php
include 'job_db.php';

if (!isset($_GET['id'])) {
    die("Job ID not provided.");
}

$job_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Job not found.");
}

$job = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #FEFEFE;
      font-family: Arial, sans-serif;
    }
    .card {
      border: 2px solid #032D84;
      border-radius: 10px;
    }
    .card h2 {
      color: #032D84;
      font-weight: bold;
    }
    .text-muted {
      color: #032D84 !important;
    }
    .btn-primary, .btn-primary:hover {
      background-color: #032D84;
      border-color: #032D84;
      color: #FEFEFE;
    }
    .btn-secondary, .btn-secondary:hover {
      background-color: #FEFEFE;
      border-color: #032D84;
      color: #032D84;
    }
    .modal-header {
      background-color: #032D84;
      color: #FEFEFE;
    }
    .modal-footer .btn-secondary {
      background-color: #FEFEFE;
      color: #032D84;
      border-color: #032D84;
    }
    .modal-footer .btn-secondary:hover {
      background-color: #e6e6e6;
    }
    .modal-footer .btn-primary {
      background-color: #032D84;
      color: #FEFEFE;
      border-color: #032D84;
    }
    .modal-footer .btn-primary:hover {
      background-color: #021f5d;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <div class="card shadow-lg p-4">
    <h2><?php echo htmlspecialchars($job['title']); ?></h2>
    <p class="text-muted">Posted on: <?php echo $job['created_at']; ?></p>
    <hr>
    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>

    <a href="find_jobs.php" class="btn btn-secondary mt-3">Back to Jobs</a>
    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#applyModal">
      Apply Now
    </button>
  </div>
</div>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="apply_job.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Apply for <?php echo htmlspecialchars($job['title']); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Cover Letter</label>
            <textarea name="cover_letter" class="form-control" rows="4"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit Application</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
