<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "job_bp");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? 1;

$result = $conn->query("SELECT * FROM users WHERE user_id=$user_id");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $name = $user['full_name'] ?? '';
    $email = $user['email'] ?? '';
    $phone = $user['phone'] ?? '';
    $address = $user['address'] ?? '';
    $skills = $user['skills'] ?? '';
    $resume = $user['resume'] ?? '';
    $profile_pic = $user['profile_pic'] ?? '';
} else {
    $name = $email = $phone = $address = $skills = $resume = $profile_pic = "";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $skills = $_POST['skills'] ?? '';

    $current_resume = $resume;
    $current_profile = $profile_pic;

    if (!empty($_FILES['resume']['name'])) {
        $resumeDir = "uploads/resume/";
        if (!is_dir($resumeDir)) mkdir($resumeDir, 0777, true);
        $resumeName = time() . "_" . basename($_FILES['resume']['name']);
        $resumePath = $resumeDir . $resumeName;
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath)) $current_resume = $resumePath;
    }

    if (!empty($_FILES['profile_pic']['name'])) {
        $profileDir = "uploads/profile/";
        if (!is_dir($profileDir)) mkdir($profileDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
        $targetFile = $profileDir . $fileName;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) $current_profile = $targetFile;
    }

    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, address=?, skills=?, resume=?, profile_pic=? WHERE user_id=?");
    $stmt->bind_param("sssssssi", $name, $email, $phone, $address, $skills, $current_resume, $current_profile, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile - JobEntry</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #FEFEFE;
    color: #032D84;
}
.sidebar {
    height: 100vh;
    width: 220px;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #032D84;
    padding-top: 25px;
}
.sidebar h2 {
    color: #FEFEFE;
    text-align: center;
    margin-bottom: 30px;
    font-weight: bold;
}
.sidebar a {
    display: block;
    padding: 12px 20px;
    color: #FEFEFE;
    text-decoration: none;
    border-radius: 6px;
    margin-bottom: 5px;
    font-weight: 500;
}
.sidebar a:hover {
    background-color: #FEFEFE;
    color: #032D84;
}
.main-content {
    margin-left: 220px;
    padding: 40px;
}
.profile-header {
    text-align: center;
    margin-bottom: 30px;
}
.profile-header img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    border: 4px solid #032D84;
    object-fit: cover;
    margin-bottom: 15px;
}
.profile-header h2 {
    margin-bottom: 5px;
}
.profile-header p {
    font-size: 0.95rem;
}
.btn-primary {
    background-color: #032D84;
    color: #FEFEFE;
    border: none;
}
.btn-primary:hover {
    background-color: #FEFEFE;
    color: #032D84;
    border: 1px solid #032D84;
}
.card {
    border: 2px solid #032D84;
    border-radius: 10px;
    margin-bottom: 20px;
}
.card-header {
    background-color: #032D84;
    color: #FEFEFE;
    font-weight: bold;
    border-radius: 10px 10px 0 0;
}
.form-control {
    border-radius: 6px;
    border: 1px solid #032D84;
}
.modal-content {
    border-radius: 10px;
}
.modal-header {
    background-color: #032D84;
    color: #FEFEFE;
}
.modal-footer .btn-primary {
    background-color: #032D84;
    color: #FEFEFE;
}
.modal-footer .btn-primary:hover {
    background-color: #FEFEFE;
    color: #032D84;
    border: 1px solid #032D84;
}
</style>
</head>
<body>

<div class="sidebar">
<h2>JobEntry</h2>
<a href="dashboard.php">Dashboard</a>
<a href="find_jobs.php">Find Jobs</a>
<a href="hire_talent.php">Hire Talent</a>
<a href="profile.php">Profile</a>
<a href="?logout=true">Sign Out</a>
</div>

<div class="main-content">
<div class="profile-header">
<img src="<?= !empty($profile_pic) ? htmlspecialchars($profile_pic) : 'https://via.placeholder.com/130'; ?>" alt="Profile Picture">
<h2><?= htmlspecialchars($name ?: 'Your Name'); ?></h2>
<p><?= htmlspecialchars($email ?: 'your@email.com'); ?></p>
<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
</div>

<div class="row">
<div class="col-md-6">
<div class="card">
<div class="card-header">Personal Information</div>
<div class="card-body">
<p><strong>Phone:</strong> <?= htmlspecialchars($phone ?: 'Not set'); ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars($address ?: 'Not set'); ?></p>
<p><strong>Skills:</strong> <?= htmlspecialchars($skills ?: 'Not set'); ?></p>
<?php if ($resume): ?>
<p><strong>Resume:</strong> <a href="<?= htmlspecialchars($resume); ?>" target="_blank">View Resume</a></p>
<?php endif; ?>
</div>
</div>
</div>

<div class="col-md-6">
<div class="card">
<div class="card-header">Skills Overview</div>
<div class="card-body">
<ul>
<?php
if (!empty($skills)) {
    foreach (explode(",", $skills) as $skill) echo "<li>" . htmlspecialchars(trim($skill)) . "</li>";
} else { echo "<li>No skills added yet</li>"; }
?>
</ul>
</div>
</div>
</div>
</div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" enctype="multipart/form-data">
<div class="modal-header">
<h5 class="modal-title">Edit Profile</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name); ?>"></div>
<div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email); ?>"></div>
<div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone); ?>"></div>
<div class="mb-3"><label>Address</label><input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address); ?>"></div>
<div class="mb-3"><label>Skills (comma separated)</label><textarea name="skills" class="form-control"><?= htmlspecialchars($skills); ?></textarea></div>
<div class="mb-3"><label>Upload Resume</label><input type="file" name="resume" class="form-control">
<?php if($resume): ?><a href="<?= htmlspecialchars($resume); ?>" target="_blank" class="d-block mt-2">View Current Resume</a><?php endif; ?></div>
<div class="mb-3"><label>Profile Picture</label><input type="file" name="profile_pic" class="form-control"></div>
</div>
<div class="modal-footer">
<button type="submit" class="btn btn-primary">Save Changes</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
