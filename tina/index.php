<?php
session_start();

// Hardcoded admin credentials
$admin_name = "admin";
$admin_password = "1234";

$error = "";

// LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']); // Using email input but only admin exists
    $password = trim($_POST['password']);

    if ($email === "admin@admin.com" && $password === $admin_password) {
        $_SESSION['user_id'] = 1;
        $_SESSION['name'] = $admin_name;
        $_SESSION['role'] = "admin";

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}

// SIGNUP (just auto-login admin for simplicity)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // If they enter admin + 1234, allow login
    if ($name === $admin_name && $password === $admin_password) {
        $_SESSION['user_id'] = 1;
        $_SESSION['name'] = $admin_name;
        $_SESSION['role'] = "admin";

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Signup failed. Use admin / 1234.";
    }
}

$user_name = $_SESSION['name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>JobEntry - Find Jobs & Hire Talent</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { margin:0; font-family: Arial, sans-serif; }
    .navbar { background-color:#FEFEFE; }
    .navbar-brand { color:#032D84 !important; font-weight:bold; }
    .btn-warning { background-color:#F7AB52; color:#032D84; border:none; }
    .btn-outline-warning { border-color:#F7AB52; color:#F7AB52; }
    .hero { background-color:#032D84; color:#FEFEFE; padding:60px 0; text-align:center; }
    .btn-primary, .btn-primary:hover, .btn-primary:focus { background-color:#032D84 !important; border:none; }
    .card-title { color:#032D84; }
    .section-title { color:#032D84; }
    .contact-section { background-color:#F7AB52; color:#032D84; }
    footer { background-color:#032D84; color:#FEFEFE; padding:15px 0; text-align:center; }
    .modal-header { background-color:#032D84; color:#FEFEFE; }
    a { color:#032D84; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">JobEntry</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="#" style="color:#032D84;">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#jobs" style="color:#032D84;">Jobs</a></li>
        <li class="nav-item"><a class="nav-link" href="#about" style="color:#032D84;">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact" style="color:#032D84;">Contact</a></li>
        <?php if($user_name): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown" style="color:#032D84;">
              <?php echo htmlspecialchars($user_name); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="?logout=true">Sign Out</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><button class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button></li>
          <li class="nav-item"><button class="btn btn-outline-warning ms-2" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</button></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h2 class="display-5 fw-bold">Connecting Talent with Opportunity</h2>
    <p class="lead mb-4">Find your dream job or hire the best talent with JobEntry.</p>
    <div class="d-flex justify-content-center gap-3">
      <a href="#jobs" class="btn btn-light text-primary" style="color:#032D84;">Find Jobs</a>
      <a href="#jobs" class="btn btn-warning">Hire Employees</a>
    </div>
  </div>
</section>

<!-- Latest Jobs -->
<section id="jobs" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4 section-title">Latest Job Opportunities</h2>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Frontend Developer</h5>
            <p class="card-text">Company: TechCorp | Location: Manila</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Apply Now</button>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Backend Developer</h5>
            <p class="card-text">Company: SoftSolutions | Location: Cebu</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Apply Now</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="py-5 text-center" style="background-color:#FEFEFE; color:#032D84;">
  <div class="container">
    <h2 class="mb-4 section-title">About JobEntry</h2>
    <p>JobEntry connects professionals and employers in the Philippines.</p>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section py-5 text-center">
  <div class="container">
    <h2 class="mb-4 section-title">Contact Us</h2>
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
      <button type="submit" class="btn btn-primary w-100">Send</button>
    </form>
  </div>
</section>

<!-- Footer -->
<footer>&copy; 2025 JobEntry. All Rights Reserved.</footer>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
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
          <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
        </form>
      </div>
      <div class="modal-footer text-center">
        Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-dismiss="modal">Sign Up</a>
      </div>
    </div>
  </div>
</div>

<!-- Sign Up Modal -->
<div class="modal fade" id="signupModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
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
          <button type="submit" class="btn btn-warning w-100" name="signup">Create Account</button>
        </form>
      </div>
      <div class="modal-footer text-center">
        Already have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
