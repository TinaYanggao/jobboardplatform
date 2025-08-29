<?php
session_start();
include("job_board_db.php");

// Dummy logged-in user for demo
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

// Logged-in user info
$user_id = $_SESSION['user_id'];
$userQuery = $conn->prepare("SELECT name,email,role FROM users WHERE id=?");
$userQuery->bind_param("i",$user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Example jobs
$jobsResult = [
    ['id'=>1,'title'=>'Frontend Developer','description'=>'Develop UI with Bootstrap','location'=>'Remote','category'=>'IT','company'=>'TechCorp'],
    ['id'=>2,'title'=>'Marketing Specialist','description'=>'Handle digital campaigns','location'=>'Manila','category'=>'Marketing','company'=>'BizSolutions']
];

// Example talent (people who applied)
$talentResult = [
    ['id'=>1,'name'=>'Alice Cruz','email'=>'alice@example.com','resume'=>'alice_resume.pdf','applied_job'=>'Frontend Developer'],
    ['id'=>2,'name'=>'Mark Santos','email'=>'mark@example.com','resume'=>'mark_resume.pdf','applied_job'=>'Marketing Specialist']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Board Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.sidebar { min-height: 100vh; background-color: #032D84; color: #FEFEFE; }
.sidebar a { color: #FEFEFE; text-decoration: none; display: block; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 0.5rem; }
.sidebar a:hover { background-color: #F7AB52; color: #032D84; }
.card-badge { background-color: #F7AB52; color: #032D84; padding: 0.2rem 0.5rem; border-radius: 5px; font-size: 0.8rem; }
.btn-create-job { background-color: #F7AB52; color: #032D84; }
.btn-create-job:hover { background-color: #e69947; color: #032D84; }
.card { background-color: #FEFEFE; }
</style>
</head>
<body class="bg-light">

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-2 d-none d-md-block sidebar p-3">
      <h2 class="text-center mb-4" style="color:#F7AB52;">JobBoard</h2>
      <div class="nav flex-column" role="tablist">
        <a class="nav-link active" data-bs-toggle="tab" href="#dashboard" role="tab">Dashboard</a>
        <a class="nav-link" data-bs-toggle="tab" href="#jobs" role="tab">Find Jobs</a>
        <a class="nav-link" data-bs-toggle="tab" href="#talent" role="tab">Hire Talent</a>
        <a class="nav-link" data-bs-toggle="tab" href="#profile" role="tab">Profile</a>
        <a class="nav-link" href="#">Sign Out</a>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="col-md-10 ms-sm-auto px-md-4 py-4">
      <div class="tab-content">

        <!-- Dashboard -->
        <div class="tab-pane fade show active" id="dashboard">
          <div class="card mb-4">
            <div class="card-body text-center" style="background-color:#032D84; color:#FEFEFE;">
              <h2>Welcome back, <?= htmlspecialchars($user['name']); ?>!</h2>
              <p>Discover jobs or hire top talent.</p>
              <div class="d-flex justify-content-center gap-2 flex-wrap">
                <button class="btn" style="background-color:#F7AB52; color:#032D84;" data-bs-toggle="tab" data-bs-target="#jobs">Explore Jobs</button>
                <button class="btn btn-light" style="color:#032D84;" data-bs-toggle="tab" data-bs-target="#talent">Hire Talent</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Find Jobs -->
        <div class="tab-pane fade" id="jobs">
          <h2 style="color:#032D84;">Available Jobs</h2>
          <div class="row">
            <?php foreach($jobsResult as $job): ?>
            <div class="col-md-4 mb-3">
              <div class="card shadow-sm">
                <div class="card-body">
                  <span class="card-badge"><?= htmlspecialchars($job['category']); ?></span>
                  <h5 class="card-title mt-2" style="color:#032D84;"><?= htmlspecialchars($job['title']); ?></h5>
                  <h6 class="text-muted"><?= htmlspecialchars($job['company']); ?> | <?= htmlspecialchars($job['location']); ?></h6>
                  <p class="card-text mt-2"><?= htmlspecialchars($job['description']); ?></p>
                  <button class="btn w-100" style="background-color:#032D84; color:#FEFEFE;" data-bs-toggle="modal" data-bs-target="#applyJobModal" data-job="<?= htmlspecialchars($job['title']); ?>">Apply Now</button>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Hire Talent -->
        <div class="tab-pane fade" id="talent">
          <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-create-job" data-bs-toggle="modal" data-bs-target="#createJobModal">+ Create Job Post</button>
          </div>
          <h2 style="color:#032D84;">Applicants</h2>
          <div class="row">
            <?php foreach($talentResult as $talent): ?>
            <div class="col-md-4 mb-3">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h5 class="card-title" style="color:#032D84;"><?= htmlspecialchars($talent['name']); ?></h5>
                  <h6 class="text-muted">Applied for: <?= htmlspecialchars($talent['applied_job']); ?></h6>
                  <p class="card-text"><?= htmlspecialchars($talent['email']); ?></p>
                  <button class="btn w-100" style="background-color:#F7AB52; color:#032D84;" data-bs-toggle="modal" data-bs-target="#contactModal" data-name="<?= htmlspecialchars($talent['name']); ?>" data-resume="<?= htmlspecialchars($talent['resume']); ?>">Contact</button>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Profile -->
        <div class="tab-pane fade" id="profile">
          <h2 style="color:#032D84;">Your Profile</h2>
          <div class="card p-3">
            <form>
              <div class="mb-3">
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']); ?>">
              </div>
              <div class="mb-3">
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>">
              </div>
              <button type="submit" class="btn" style="background-color:#032D84; color:#FEFEFE;">Save Profile</button>
            </form>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<!-- Create Job Modal -->
<div class="modal fade" id="createJobModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content p-3" method="POST" action="create_job.php">
      <div class="modal-header" style="background-color:#032D84; color:#FEFEFE;">
        <h5 class="modal-title">Create Job</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-2" name="title" placeholder="Job Title" required>
        <input type="text" class="form-control mb-2" name="location" placeholder="Location" required>
        <textarea class="form-control mb-2" name="description" placeholder="Job Description" required></textarea>
        <input type="text" class="form-control mb-2" name="category" placeholder="Category" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn" style="background-color:#F7AB52; color:#032D84;">Post Job</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<!-- Apply Job Modal -->
<div class="modal fade" id="applyJobModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header" style="background-color:#032D84; color:#FEFEFE;">
        <h5 class="modal-title">Apply for Job</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-2" placeholder="Your Name">
        <input type="email" class="form-control mb-2" placeholder="Your Email">
        <textarea class="form-control mb-2" rows="4" placeholder="Why should we hire you?"></textarea>
        <input type="file" class="form-control mb-2" accept=".pdf,.doc,.docx">
        <small class="text-muted job-name"></small>
      </div>
      <div class="modal-footer">
        <button class="btn" style="background-color:#F7AB52; color:#032D84;" data-bs-dismiss="modal">Submit Application</button>
      </div>
    </div>
  </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header" style="background-color:#032D84; color:#FEFEFE;">
        <h5 class="modal-title">Applicant Info</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Name:</strong> <span class="applicant-name"></span></p>
        <p><strong>Resume:</strong> <a href="#" target="_blank" class="applicant-resume">View</a></p>
        <button class="btn w-100" style="background-color:#032D84; color:#FEFEFE;">I'm Interested</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
var applyModal = document.getElementById('applyJobModal');
applyModal.addEventListener('show.bs.modal', function(event) {
  var button = event.relatedTarget;
  var jobTitle = button.getAttribute('data-job');
  applyModal.querySelector('.job-name').textContent = "Applying for: " + jobTitle;
});

var contactModal = document.getElementById('contactModal');
contactModal.addEventListener('show.bs.modal', function(event) {
  var button = event.relatedTarget;
  var name = button.getAttribute('data-name');
  var resume = button.getAttribute('data-resume');
  contactModal.querySelector('.applicant-name').textContent = name;
  contactModal.querySelector('.applicant-resume').href = resume;
});
</script>
</body>
</html>
