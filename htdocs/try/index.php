<?php
session_start();
include("job_db.php"); // Make sure this file exists and $conn is defined

$message = "";

// LOGIN HANDLER
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Hard-coded admin
    if ($email === "admin@gmail.com" && $password === "1234") {
        $_SESSION['user_id'] = 1;
        $_SESSION['full_name'] = "Admin";
        $_SESSION['email'] = "admin@gmail.com";
        header("Location: dashboard.php");
        exit;
    }

    // Database login
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status='active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "❌ Invalid email or password or account disabled.";
    }
}

// SIGNUP HANDLER
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Optional: check if email already exists
    $stmt_check = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        $message = "❌ Email already exists. Please login.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $email, $password);

        if ($stmt->execute()) {
            // Auto-login after signup
            $user_id = $stmt->insert_id; // get newly inserted user_id
            $_SESSION['user_id'] = $user_id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;

            header("Location: dashboard.php");
            exit;
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
    }
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JobEntry - Login & Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { margin: 0; padding: 0; background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .hero { height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #032D84 50%, #ffffff 50%); }
    .hero-content { display: flex; align-items: center; justify-content: space-between; width: 90%; max-width: 1200px; }
    .hero-left { flex: 1; color: white; padding-right: 40px; }
    .hero-left h1 { font-size: 2.5rem; font-weight: bold; }
    .hero-left p { font-size: 1.1rem; margin-top: 10px; max-width: 400px; }
    .hero-right { flex: 1; background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
    .nav-pills .nav-link.active { background-color: #032D84 !important; }
    .btn-custom { background-color: #032D84; color: white; }
    .btn-custom:hover { background-color: #021f5c; color: #fff; }
  </style>
</head>
<body>
  <div class="hero">
    <div class="hero-content">
      <div class="hero-left">
        <h1>Welcome to JobEntry</h1>
        <p>Your trusted platform connecting job seekers and employers. Find opportunities, post jobs, and grow your career with ease.</p>
      </div>
      <div class="hero-right">
        <?php if($message != ""): ?>
          <div class="alert alert-info text-center"><?= $message; ?></div>
        <?php endif; ?>

        <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button">Login</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" id="signup-tab" data-bs-toggle="pill" data-bs-target="#signup" type="button">Sign Up</button>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane fade show active" id="login">
            <form method="POST">
              <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
              <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
              <button type="submit" name="login" class="btn btn-custom w-100">Login</button>
            </form>
          </div>

          <div class="tab-pane fade" id="signup">
            <form method="POST">
              <div class="mb-3"><label>Full Name</label><input type="text" name="full_name" class="form-control" required></div>
              <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
              <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
              <button type="submit" name="signup" class="btn btn-custom w-100">Sign Up</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
