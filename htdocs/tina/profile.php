<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "job_entry");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Example: replace with session user_id after login system
$user_id = 1;

// Initialize variables
$name = $email = $phone = $address = $skills = $resume = $profile_pic = "";

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone'];
    $address = $_POST['address'];
    $skills  = $_POST['skills'];

    // Handle resume upload
    if (!empty($_FILES['resume']['name'])) {
        $resumeDir = "uploads/resume/";
        if (!is_dir($resumeDir)) mkdir($resumeDir, 0777, true);

        $resumeName = time() . "_" . basename($_FILES['resume']['name']);
        $resumePath = $resumeDir . $resumeName;
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath)) {
            $resume = $resumePath;
        }
    }

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/profile/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $profile_pic = $targetFile;
        }
    }

    // Build SQL query based on uploaded files
    if ($profile_pic && $resume) {
        $sql = "UPDATE users SET name=?, email=?, phone=?, address=?, skills=?, resume=?, profile_pic=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $name, $email, $phone, $address, $skills, $resume, $profile_pic, $user_id);
    } elseif ($profile_pic) {
        $sql = "UPDATE users SET name=?, email=?, phone=?, address=?, skills=?, profile_pic=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $skills, $profile_pic, $user_id);
    } elseif ($resume) {
        $sql = "UPDATE users SET name=?, email=?, phone=?, address=?, skills=?, resume=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $skills, $resume, $user_id);
    } else {
        $sql = "UPDATE users SET name=?, email=?, phone=?, address=?, skills=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $phone, $address, $skills, $user_id);
    }

    $stmt->execute();
    header("Location: profile.php");
    exit();
}

// Fetch user data
$result = $conn->query("SELECT * FROM users WHERE id=$user_id");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $name = $user['name'] ?? '';
    $email = $user['email'] ?? '';
    $phone = $user['phone'] ?? '';
    $address = $user['address'] ?? '';
    $skills = $user['skills'] ?? '';
    $resume = $user['resume'] ?? '';
    $profile_pic = $user['profile_pic'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background-color: #FEFEFE; }
    .sidebar { height: 100vh; width: 225px; position: fixed; top: 0; left: 0; background-color: #032D84; padding-top: 20px; }
    .sidebar h2 { color: #F7AB52; text-align: center; font-weight: bold; margin-bottom: 30px; }
    .sidebar a { padding: 12px 20px; display: block; color: #FEFEFE; text-decoration: none; font-weight: 500; }
    .sidebar a:hover { background-color: #F7AB52; color: #032D84; }
    .main-content { margin-left: 225px; padding: 40px; }
    .profile-header { text-align: center; margin-bottom: 30px; }
    .profile-header img { width: 120px; height: 120px; border-radius: 50%; border: 4px solid #F7AB52; object-fit: cover; }
    .profile-header h2 { color: #032D84; font-weight: bold; }
    .profile-header p { color: #555; }
    .btn-primary { background-color: #F7AB52; border-color: #F7AB52; color: #032D84; }
    .btn-primary:hover { background-color: #e69f45; border-color: #e69f45; }
    .card { margin-bottom: 20px; border: 1px solid #F7AB52; border-radius: 10px; }
    .card-header { font-weight: bold; border-radius: 10px 10px 0 0; }
    .card-header.bg-primary { background-color: #032D84; color: #FEFEFE; }
    .card-header.bg-success { background-color: #F7AB52; color: #032D84; }
    .modal-content { border-radius: 10px; }
    .modal-header { background-color: #032D84; color: #F7AB52; }
    .form-control { border-radius: 5px; }
  </style>
</head>
<body>

<div class="sidebar">
    <h2>JobEntry</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="find_jobs.php">Find Jobs</a>
    <a href="hire_talent.php">Hire Talent</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Sign Out</a>
</div>

<div class="main-content">
  <div class="profile-header">
    <img src="<?php echo !empty($profile_pic) ? htmlspecialchars($profile_pic) : 'https://via.placeholder.com/120'; ?>" alt="Profile Picture">
    <h2><?php echo htmlspecialchars($name ?: 'Your Name'); ?></h2>
    <p><?php echo htmlspecialchars($email ?: 'your@email.com'); ?></p>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary">Personal Information</div>
        <div class="card-body">
          <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone ?: 'Not set'); ?></p>
          <p><strong>Address:</strong> <?php echo htmlspecialchars($address ?: 'Not set'); ?></p>
          <p><strong>Skills:</strong> <?php echo htmlspecialchars($skills ?: 'Not set'); ?></p>
          <?php if (!empty($resume)) : ?>
            <p><strong>Resume:</strong> <a href="<?php echo htmlspecialchars($resume); ?>" target="_blank">View Resume</a></p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-success">Skills Overview</div>
        <div class="card-body">
          <ul>
            <?php 
              if (!empty($skills)) {
                $skillsArray = explode(",", $skills);
                foreach ($skillsArray as $skill) {
                  echo "<li>" . htmlspecialchars(trim($skill)) . "</li>";
                }
              } else {
                echo "<li>No skills added yet</li>";
              }
            ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>">
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>">
          </div>
          <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>">
          </div>
          <div class="mb-3">
            <label>Skills (comma separated)</label>
            <textarea name="skills" class="form-control"><?php echo htmlspecialchars($skills); ?></textarea>
          </div>
          <div class="mb-3">
            <label>Upload Resume</label>
            <input type="file" name="resume" class="form-control">
            <?php if (!empty($resume)) : ?>
              <a href="<?php echo htmlspecialchars($resume); ?>" target="_blank" class="d-block mt-2">View Current Resume</a>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label>Profile Picture</label>
            <input type="file" name="profile_pic" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
