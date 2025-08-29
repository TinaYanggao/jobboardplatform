<?php
session_start();
include("job_board_db.php"); // Database connection file

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
        // ⚠️ Production: password_verify($password, $row['password'])
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name']    = $row['name'];
            $_SESSION['role']    = $row['role'];

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
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'jobseeker', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['name']    = $name;
        $_SESSION['role']    = 'jobseeker';

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Signup failed: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>JobEntry - Find Jobs & Hire Talent</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root { --primary-blue:#032D84; --accent-orange:#F7AB52; --white:#fff; --light-gray:#f5f5f5; }
    *{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI,Tahoma,sans-serif;}
    body{background:#fff;color:var(--primary-blue);}
    header{background:#fff;padding:1rem 3rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #ddd;}
    header h1{font-size:2rem;color:var(--primary-blue);}
    nav a{margin-left:2rem;color:var(--primary-blue);font-weight:500;text-decoration:none;}
    nav a:hover{color:var(--accent-orange);}
    .auth-buttons button{background:var(--accent-orange);color:#fff;border:none;padding:0.5rem 1.2rem;border-radius:6px;cursor:pointer;margin-left:0.5rem;}
    .auth-buttons button:hover{background:#e69947;}
    .hero{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:5rem 2rem;background:var(--primary-blue);color:#fff;text-align:center;}
    .hero h2{font-size:2.5rem;margin-bottom:1rem;}
    .hero p{max-width:600px;margin-bottom:2rem;}
    .hero-buttons{display:flex;gap:1rem;}
    .hero-buttons button{padding:0.8rem 2rem;border:none;border-radius:6px;font-weight:bold;cursor:pointer;}
    .btn-job{background:#fff;color:var(--primary-blue);}
    .btn-hire{background:var(--accent-orange);color:#fff;}
    .section-title{text-align:center;margin:3rem 0;font-size:2rem;font-weight:bold;}
    .job-listings{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;padding:0 2rem;}
    .job-card{background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);}
    .job-card h3{margin-bottom:0.5rem;}
    .job-card button{background:var(--primary-blue);color:#fff;padding:0.5rem 1rem;border:none;border-radius:5px;cursor:pointer;}
    .about,.contact{padding:3rem 2rem;text-align:center;}
    .about{background:var(--light-gray);}
    .contact-form{max-width:500px;margin:0 auto;display:flex;flex-direction:column;gap:1rem;}
    .contact-form input,.contact-form textarea{padding:0.8rem;border:1px solid #ccc;border-radius:5px;}
    .contact-form button{background:var(--accent-orange);color:#fff;padding:0.8rem;border:none;border-radius:5px;}
    footer{background:var(--primary-blue);color:#fff;text-align:center;padding:1.5rem;margin-top:3rem;}
    .modal{display:none;position:fixed;z-index:200;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.6);}
    .modal-content{background:#fff;margin:8% auto;padding:2rem;border-radius:10px;max-width:400px;position:relative;}
    .close{position:absolute;right:1rem;top:1rem;cursor:pointer;font-size:1.3rem;}
    .modal-content input{width:100%;padding:0.8rem;margin-bottom:1rem;border:1px solid #ccc;border-radius:6px;}
    .modal-content button{width:100%;padding:0.8rem;background:var(--primary-blue);color:#fff;border:none;border-radius:6px;font-weight:bold;}
    .modal-footer{text-align:center;font-size:0.9rem;}
    .modal-footer a{color:var(--accent-orange);cursor:pointer;}
  </style>
</head>
<body>
  <header>
    <h1>JobEntry</h1>
    <nav>
      <a href="#">Home</a>
      <a href="#jobs">Jobs</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
    </nav>
    <div class="auth-buttons">
      <button id="loginBtn">Login</button>
      <button id="signupBtn">Sign Up</button>
    </div>
  </header>

  <section class="hero">
    <h2>Connecting Talent with Opportunity</h2>
    <p>Find your dream job or hire the best talent with JobEntry.</p>
    <div class="hero-buttons">
      <button class="btn-job modal-trigger">Find Jobs</button>
      <button class="btn-hire modal-trigger">Hire Employees</button>
    </div>
  </section>

  <h2 id="jobs" class="section-title">Latest Job Opportunities</h2>
  <section class="job-listings">
    <div class="job-card">
      <h3>Frontend Developer</h3>
      <p>Company: TechCorp | Location: Manila</p>
      <button class="modal-trigger">Apply Now</button>
    </div>
    <div class="job-card">
      <h3>Backend Developer</h3>
      <p>Company: SoftSolutions | Location: Cebu</p>
      <button class="modal-trigger">Apply Now</button>
    </div>
  </section>

  <section id="about" class="about">
    <h2>About JobEntry</h2>
    <p>JobEntry connects professionals and employers in the Philippines.</p>
  </section>

  <section id="contact" class="contact">
    <h2>Contact Us</h2>
    <form class="contact-form">
      <input type="text" placeholder="Full Name" required>
      <input type="email" placeholder="Email Address" required>
      <textarea rows="4" placeholder="Your Message" required></textarea>
      <button type="submit">Send</button>
    </form>
  </section>

  <footer>&copy; 2025 JobEntry. All Rights Reserved.</footer>

  <!-- Login Modal -->
<div id="loginModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeLogin">&times;</span>
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>
    </form>
    <div class="modal-footer">Don't have an account? <a id="toSignup">Sign Up</a></div>
  </div>
</div>

<!-- Sign Up Modal -->
<div id="signupModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeSignup">&times;</span>
    <h2>Sign Up</h2>
    <form method="POST" action="">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="signup">Create Account</button>
    </form>
    <div class="modal-footer">Already have an account? <a id="toLogin">Login</a></div>
  </div>
</div>

  <script>
    const loginModal=document.getElementById("loginModal");
    const signupModal=document.getElementById("signupModal");
    document.getElementById("loginBtn").onclick=()=>loginModal.style.display="block";
    document.getElementById("signupBtn").onclick=()=>signupModal.style.display="block";
    document.getElementById("closeLogin").onclick=()=>loginModal.style.display="none";
    document.getElementById("closeSignup").onclick=()=>signupModal.style.display="none";
    document.getElementById("toSignup").onclick=()=>{loginModal.style.display="none";signupModal.style.display="block";}
    document.getElementById("toLogin").onclick=()=>{signupModal.style.display="none";loginModal.style.display="block";}
    window.onclick=(e)=>{if(e.target==loginModal)loginModal.style.display="none";if(e.target==signupModal)signupModal.style.display="none";}
    document.querySelectorAll(".modal-trigger").forEach(btn=>{btn.onclick=()=>loginModal.style.display="block";});
  </script>
</body>
</html>
