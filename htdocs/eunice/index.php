<?php
session_start();
include("job_board_db.php");

$error = "";

// LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}

// SIGNUP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already exists.";
    } else {
        $sql = "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'jobseeker', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = 'jobseeker';
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Signup failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>JobEntry - Find Jobs & Hire Talent</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm" style="background-color:#FEFEFE;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#" style="color:#032D84;">JobEntry</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="#" style="color:#032D84;">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#jobs" style="color:#032D84;">Jobs</a></li>
        <li class="nav-item"><a class="nav-link" href="#about" style="color:#032D84;">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact" style="color:#032D84;">Contact</a></li>
        <li class="nav-item"><button class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#loginModal" style="background-color:#F7AB52; color:#032D84;">Login</button></li>
        <li class="nav-item"><button class="btn btn-outline-warning ms-2" data-bs-toggle="modal" data-bs-target="#signupModal" style="border-color:#F7AB52; color:#F7AB52;">Sign Up</button></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="text-center text-white py-5" style="background-color:#032D84;">
  <div class="container">
    <h2 class="display-5 fw-bold">Connecting Talent with Opportunity</h2>
    <p class="lead mb-4">Find your dream job or hire the best talent with JobEntry.</p>
    <div class="d-flex justify-content-center gap-3">
      <button class="btn btn-light text-primary" data-bs-toggle="modal" data-bs-target="#loginModal" style="color:#032D84;">Find Jobs</button>
      <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#loginModal" style="background-color:#F7AB52; color:#032D84;">Hire Employees</button>
    </div>
  </div>
</section>

<!-- Latest Jobs -->
<section id="jobs" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4" style="color:#032D84;">Latest Job Opportunities</h2>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title" style="color:#032D84;">Frontend Developer</h5>
            <p class="card-text">Company: TechCorp | Location: Manila</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal" style="background-color:#032D84;">Apply Now</button>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title" style="color:#032D84;">Backend Developer</h5>
            <p class="card-text">Company: SoftSolutions | Location: Cebu</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal" style="background-color:#032D84;">Apply Now</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="py-5 text-center" style="background-color:#FEFEFE; color:#032D84;">
  <div class="container">
    <h2 class="mb-4">About JobEntry</h2>
    <p>JobEntry connects professionals and employers in the Philippines.</p>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5 text-center" style="background-color:#F7AB52; color:#032D84;">
  <div class="container">
    <h2 class="mb-4">Contact Us</h2>
    <form class="mx-auto" style="max-width:500px;">
      <div class="mb-3">
        <input type="text" class="form-control" placeholder="Full Name" required>
      </div>
      <div class="mb-3">
        <input type="email" class="form-control" placeholder="Email Address" required>
      </div>
      <div class="mb-3">
        <textarea class="form-control" rows="4" placeholder="Your Message" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary w-100" style="background-color:#032D84;">Send</button>
    </form>
  </div>
</section>

<!-- Footer -->
<footer class="text-center py-3" style="background-color:#032D84; color:#FEFEFE;">
  &copy; 2025 JobEntry. All Rights Reserved.
</footer>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#032D84; color:#FEFEFE;">
        <h5 class="modal-title">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?php if (!empty($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <form method="POST">
          <div class="mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
          </div>
          <div class="mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100" name="login" style="background-color:#032D84;">Login</button>
        </form>
      </div>
      <div class="modal-footer text-center">
        Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-dismiss="modal" style="color:#032D84;">Sign Up</a>
      </div>
    </div>
  </div>
</div>

<!-- Sign Up Modal -->
<div class="modal fade" id="signupModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#032D84; color:#FEFEFE;">
        <h5 class="modal-title">Sign Up</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
          <div class="mb-3">
            <input type="text" class="form-control" name="name" placeholder="Full Name" required>
          </div>
          <div class="mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
          </div>
          <div class="mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <button type="submit" class="btn btn-warning w-100" name="signup" style="background-color:#F7AB52; color:#032D84;">Create Account</button>
        </form>
      </div>
      <div class="modal-footer text-center">
        Already have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal" style="color:#032D84;">Login</a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
