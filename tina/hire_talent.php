<?php
include 'job_db.php';

// Delete job
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM jobs WHERE id=$id");
    header("Location: hire_talent.php");
    exit();
}

// Add/Edit job
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];
    $salary = $_POST['salary'];
    $location = $_POST['location'];
    $employment_type = $_POST['employment_type'];
    $contact_name = $_POST['contact_name'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];

    if ($id) {
        // Update job
        $sql = "UPDATE jobs SET 
                title='$title', 
                description='$description',
                requirements='$requirements',
                salary='$salary',
                location='$location',
                employment_type='$employment_type',
                contact_name='$contact_name',
                contact_email='$contact_email',
                contact_phone='$contact_phone'
                WHERE id=$id";
    } else {
        // Insert new job
        $sql = "INSERT INTO jobs 
                (title, description, requirements, salary, location, employment_type, contact_name, contact_email, contact_phone) 
                VALUES 
                ('$title', '$description', '$requirements', '$salary', '$location', '$employment_type', '$contact_name', '$contact_email', '$contact_phone')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: hire_talent.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch all jobs
$result = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hire Talent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background-color: #f8f9fa; }
    .sidebar {
      height: 100vh; width: 225px; position: fixed; top: 0; left: 0;
      background-color: #032D84; padding-top: 20px;
    }
    .sidebar h2 { color: #F7AB52; text-align: center; font-weight: bold; margin-bottom: 30px; }
    .sidebar a { padding: 12px 20px; display: block; color: #FEFEFE; text-decoration: none; border-radius:6px; margin-bottom:5px; }
    .sidebar a:hover { background-color: #F7AB52; color: #032D84; }
    .main-content { margin-left: 225px; padding: 40px; }
    .job-card { background: #FEFEFE; border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 15px; }
    .btn-create-job { background-color: #F7AB52; color: #032D84; }
    .btn-create-job:hover { background-color: #e69947; color: #032D84; }
    .btn-edit { background-color: #032D84; color: #FEFEFE; }
    .btn-edit:hover { background-color: #021f5d; color: #FEFEFE; }
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
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color:#032D84;">Hire Talent</h2>
    <button class="btn btn-create-job" data-bs-toggle="modal" data-bs-target="#jobModal">+ Create Job Posting</button>
  </div>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="job-card shadow-sm">
        <h4 style="color:#032D84;"><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
        <p><strong>Requirements:</strong> <?php echo nl2br(htmlspecialchars($row['requirements'])); ?></p>
        <p><strong>Salary:</strong> <?php echo htmlspecialchars($row['salary']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
        <p><strong>Employment Type:</strong> <?php echo htmlspecialchars($row['employment_type']); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_name']); ?> | 
            <?php echo htmlspecialchars($row['contact_email']); ?> | 
            <?php echo htmlspecialchars($row['contact_phone']); ?></p>
        <small class="text-muted">Posted on: <?php echo $row['created_at']; ?></small><br>
        
        <button class="btn btn-edit btn-sm mt-2"
                data-bs-toggle="modal"
                data-bs-target="#jobModal"
                data-id="<?php echo $row['id']; ?>"
                data-title="<?php echo htmlspecialchars($row['title']); ?>"
                data-description="<?php echo htmlspecialchars($row['description']); ?>"
                data-requirements="<?php echo htmlspecialchars($row['requirements']); ?>"
                data-salary="<?php echo htmlspecialchars($row['salary']); ?>"
                data-location="<?php echo htmlspecialchars($row['location']); ?>"
                data-employment="<?php echo htmlspecialchars($row['employment_type']); ?>"
                data-contactname="<?php echo htmlspecialchars($row['contact_name']); ?>"
                data-contactemail="<?php echo htmlspecialchars($row['contact_email']); ?>"
                data-contactphone="<?php echo htmlspecialchars($row['contact_phone']); ?>">
          Edit
        </button>
        <a href="hire_talent.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger mt-2"
           onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-muted">There's no job posted yet.</p>
  <?php endif; ?>
</div>

<!-- Modal for Add/Edit Job -->
<div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header" style="background-color:#032D84; color:#FEFEFE;">
          <h5 class="modal-title">Create Job Posting</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="jobId">
          <div class="mb-3">
            <label class="form-label">Job Title</label>
            <input type="text" name="title" id="jobTitle" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Job Description</label>
            <textarea name="description" id="jobDescription" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Requirements</label>
            <textarea name="requirements" id="jobRequirements" class="form-control"></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Salary Range</label>
              <input type="text" name="salary" id="jobSalary" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Job Location</label>
              <input type="text" name="location" id="jobLocation" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Employment Type</label>
            <select name="employment_type" id="jobEmployment" class="form-control">
              <option value="Full-time">Full-time</option>
              <option value="Part-time">Part-time</option>
              <option value="Contract">Contract</option>
              <option value="Internship">Internship</option>
            </select>
          </div>
          <hr>
          <h5>Contact Information</h5>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Contact Name</label>
              <input type="text" name="contact_name" id="jobContactName" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Contact Email</label>
              <input type="email" name="contact_email" id="jobContactEmail" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Contact Phone</label>
              <input type="text" name="contact_phone" id="jobContactPhone" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Job</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Auto-fill modal when editing
  var jobModal = document.getElementById('jobModal');
  jobModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;

    var id = button.getAttribute('data-id');
    var title = button.getAttribute('data-title');
    var description = button.getAttribute('data-description');
    var requirements = button.getAttribute('data-requirements');
    var salary = button.getAttribute('data-salary');
    var location = button.getAttribute('data-location');
    var employment = button.getAttribute('data-employment');
    var contactName = button.getAttribute('data-contactname');
    var contactEmail = button.getAttribute('data-contactemail');
    var contactPhone = button.getAttribute('data-contactphone');

    var modalTitle = jobModal.querySelector('.modal-title');
    document.getElementById('jobId').value = id || "";
    document.getElementById('jobTitle').value = title || "";
    document.getElementById('jobDescription').value = description || "";
    document.getElementById('jobRequirements').value = requirements || "";
    document.getElementById('jobSalary').value = salary || "";
    document.getElementById('jobLocation').value = location || "";
    document.getElementById('jobEmployment').value = employment || "Full-time";
    document.getElementById('jobContactName').value = contactName || "";
    document.getElementById('jobContactEmail').value = contactEmail || "";
    document.getElementById('jobContactPhone').value = contactPhone || "";

    modalTitle.textContent = id ? "Edit Job Posting" : "Create Job Posting";
  });
</script>
</body>
</html>

