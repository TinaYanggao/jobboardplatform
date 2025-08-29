<?php
session_start();
include 'job_db.php';

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Delete job
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: hire_talent.php");
    exit();
}

// Add/Edit job
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['send_message'])) {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $requirements = $_POST['requirements'] ?? '';
    $salary = $_POST['salary'] ?? '';
    $location = $_POST['location'] ?? '';
    $employment_type = $_POST['employment_type'] ?? '';
    $contact_name = $_POST['contact_name'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';

    if (!empty($title) && !empty($description)) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE jobs SET title=?, description=?, requirements=?, salary=?, location=?, employment_type=?, contact_name=?, contact_email=?, contact_phone=? WHERE id=?");
            $stmt->bind_param("sssssssssi", $title, $description, $requirements, $salary, $location, $employment_type, $contact_name, $contact_email, $contact_phone, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO jobs (title, description, requirements, salary, location, employment_type, contact_name, contact_email, contact_phone, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssssss", $title, $description, $requirements, $salary, $location, $employment_type, $contact_name, $contact_email, $contact_phone);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: hire_talent.php");
        exit();
    }
}

// Send message to applicant by user ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $job_id = $_POST['job_id'] ?? 0;
    $receiver_id = $_POST['receiver_id'] ?? 0; // use applicant's user ID
    $message_text = $_POST['message'] ?? '';
    $sender_id = $_SESSION['user_id'];

    if (!empty($receiver_id) && !empty($message_text)) {
        $stmt = $conn->prepare("INSERT INTO messages (job_id, sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $job_id, $sender_id, $receiver_id, $message_text);
        $stmt->execute();
        $stmt->close();
        header("Location: hire_talent.php");
        exit();
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
body { margin:0; font-family:Arial,sans-serif; background-color:#FEFEFE; color:#032D84; }
.sidebar { height:100vh; width:225px; position:fixed; top:0; left:0; background-color:#032D84; padding-top:20px; }
.sidebar h2 { color:#FEFEFE; text-align:center; font-weight:bold; margin-bottom:30px; }
.sidebar a { padding:12px 20px; display:block; color:#FEFEFE; text-decoration:none; border-radius:6px; margin-bottom:5px; font-weight:500; }
.sidebar a:hover { background-color:#ffffff; color:#032D84; }
.main-content { margin-left:225px; padding:40px; }
.job-card { background:#FEFEFE; border:2px solid #032D84; padding:20px; border-radius:8px; margin-bottom:15px; }
.applicant-card { background-color:#f8f9fa; border:1px solid #032D84; padding:15px; border-radius:6px; margin-top:10px; }
.btn-create-job { background-color:#032D84; color:#FEFEFE; border:2px solid #032D84; }
.btn-create-job:hover { background-color:#ffffff; color:#032D84; }
.btn-edit { background-color:#032D84; color:#FEFEFE; border:2px solid #032D84; }
.btn-edit:hover { background-color:#ffffff; color:#032D84; }
.btn-delete { background-color:#FEFEFE; color:#032D84; border:2px solid #032D84; }
.btn-delete:hover { background-color:#032D84; color:#FEFEFE; }
.modal-header { background-color:#032D84; color:#FEFEFE; }
.modal-footer button { border-radius:5px; }
.message-box { display:flex; flex-direction:column; max-height:250px; overflow-y:auto; padding:10px; border:1px solid #dee2e6; border-radius:8px; background-color:#ffffff; }
.message-sent { background:#032D84; color:#FEFEFE; text-align:right; padding:8px 12px; border-radius:12px; margin-bottom:6px; max-width:75%; align-self:flex-end; }
.message-received { background:#f1f3f5; color:#032D84; text-align:left; padding:8px 12px; border-radius:12px; margin-bottom:6px; max-width:75%; align-self:flex-start; }
</style>
</head>
<body>

<div class="sidebar">
<h2>JobEntry</h2>
<a href="dashboard.php">Dashboard</a>
<a href="find_job.php">Find Jobs</a>
<a href="hire_talent.php">Hire Talent</a>
<a href="profile.php">Profile</a>
<a href="?logout=true">Sign Out</a>
</div>

<div class="main-content">
<div class="d-flex justify-content-between align-items-center mb-4">
<h2>Hire Talent</h2>
<button class="btn btn-create-job" data-bs-toggle="modal" data-bs-target="#jobModal">+ Create Job Posting</button>
</div>

<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<div class="job-card shadow-sm">
<h4><?= htmlspecialchars($row['title']); ?></h4>
<p><?= nl2br(htmlspecialchars($row['description'])); ?></p>
<p><strong>Requirements:</strong> <?= nl2br(htmlspecialchars($row['requirements'])); ?></p>
<p><strong>Salary:</strong> <?= htmlspecialchars($row['salary']); ?></p>
<p><strong>Location:</strong> <?= htmlspecialchars($row['location']); ?></p>
<p><strong>Employment Type:</strong> <?= htmlspecialchars($row['employment_type']); ?></p>
<p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_name']); ?> | <?= htmlspecialchars($row['contact_email']); ?> | <?= htmlspecialchars($row['contact_phone']); ?></p>
<small class="text-muted">Posted on: <?= $row['created_at']; ?></small><br>

<button class="btn btn-edit btn-sm mt-2"
        data-bs-toggle="modal"
        data-bs-target="#jobModal"
        data-id="<?= $row['id']; ?>"
        data-title="<?= htmlspecialchars($row['title']); ?>"
        data-description="<?= htmlspecialchars($row['description']); ?>"
        data-requirements="<?= htmlspecialchars($row['requirements']); ?>"
        data-salary="<?= htmlspecialchars($row['salary']); ?>"
        data-location="<?= htmlspecialchars($row['location']); ?>"
        data-employment="<?= htmlspecialchars($row['employment_type']); ?>"
        data-contactname="<?= htmlspecialchars($row['contact_name']); ?>"
        data-contactemail="<?= htmlspecialchars($row['contact_email']); ?>"
        data-contactphone="<?= htmlspecialchars($row['contact_phone']); ?>">
Edit
</button>
<a href="hire_talent.php?delete=<?= $row['id']; ?>" class="btn btn-delete btn-sm mt-2" onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>

<?php
// Fetch applicants
$stmt = $conn->prepare("SELECT * FROM applications WHERE job_id=? ORDER BY applied_at DESC");
$stmt->bind_param("i", $row['id']);
$stmt->execute();
$applicants_result = $stmt->get_result();
?>
<?php if ($applicants_result->num_rows > 0): ?>
<h5 class="mt-3">Applicants:</h5>
<?php while ($applicant = $applicants_result->fetch_assoc()): ?>
<div class="applicant-card">
<p><strong>Name:</strong> <?= htmlspecialchars($applicant['applicant_name']); ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($applicant['applicant_email']); ?></p>
<p><strong>Cover Letter:</strong><br><?= nl2br(htmlspecialchars($applicant['cover_letter'])); ?></p>
<?php if (!empty($applicant['resume_path'])): ?>
<p><a href="<?= htmlspecialchars($applicant['resume_path']); ?>" target="_blank">View Resume</a></p>
<?php endif; ?>

<?php
// Fetch messages from applicant to employer only
$stmt2 = $conn->prepare("SELECT * FROM messages WHERE job_id=? AND receiver_email=? ORDER BY created_at ASC");
$stmt2->bind_param("is", $row['id'], $applicant['applicant_email']);
$stmt2->execute();
$messages_result = $stmt2->get_result();
?>
<div class="message-box">
<?php while ($msg = $messages_result->fetch_assoc()): ?>
<div class="message-received">
<small class="d-block mb-1"><?= htmlspecialchars($applicant['applicant_name']); ?> (<?= date('M d, H:i', strtotime($msg['created_at'])) ?>)</small>
<p class="mb-0"><?= nl2br(htmlspecialchars($msg['message'])); ?></p>
</div>
<?php endwhile; ?>
</div>
<?php $stmt2->close(); ?>

<form method="POST" class="mt-2">
<input type="hidden" name="job_id" value="<?= $row['id']; ?>">
<input type="hidden" name="receiver_email" value="<?= $applicant['applicant_email']; ?>">
<textarea name="message" class="form-control mb-2" placeholder="Write a message..." required></textarea>
<button type="submit" name="send_message" class="btn btn-create-job btn-sm">Send Message</button>
</form>
</div>
<?php endwhile; ?>
<?php else: ?>
<p class="text-muted mt-3">No applicants yet.</p>
<?php endif; ?>
<?php $stmt->close(); ?>
</div>
<?php endwhile; ?>
<?php else: ?>
<p class="text-muted">No job postings yet.</p>
<?php endif; ?>
</div>

<!-- Modal for Add/Edit Job -->
<div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
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
<button type="submit" class="btn btn-edit">Save Job</button>
<button type="button" class="btn btn-delete" data-bs-dismiss="modal">Cancel</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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

    jobModal.querySelector('.modal-title').textContent = id ? "Edit Job Posting" : "Create Job Posting";
});
</script>
</body>
</html>
