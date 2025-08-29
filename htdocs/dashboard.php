<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Job Board</title>
    <style>
    :root {
        --primary-blue: #032D84;
        --accent-orange: #F7AB52;
        --white: #FEFEFE;
        --light-gray: #f5f5f5;
    }
    * {margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma;}
    body {background-color: var(--light-gray);color: var(--primary-blue);}
    .sidebar {width:220px;height:100vh;background:var(--primary-blue);position:fixed;top:0;left:0;padding:2rem 1rem;color:var(--white);}
    .sidebar h2 {text-align:center;margin-bottom:2rem;}
    .sidebar a {color:var(--white);padding:0.8rem 1rem;display:block;margin-bottom:1rem;text-decoration:none;border-radius:6px;}
    .sidebar a:hover {background:var(--accent-orange);}
    .main-content {margin-left:220px;padding:2rem 3rem;}
    .navbar {display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;}
    .navbar button {background:var(--accent-orange);color:#fff;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer;}
    .dashboard-welcome {background:linear-gradient(135deg,#032D84,#021a5e);color:#fff;padding:2.5rem;border-radius:16px;text-align:center;margin-bottom:2rem;}
    .dashboard-welcome h2 {font-size:2.5rem;margin-bottom:1rem;}
    .section {display:none;}
    .section.active {display:block;}
    </style>
</head>
<body>
<div class="sidebar">
    <h2>JobEntry</h2>
    <a href="#" onclick="showSection('dashboard')">Dashboard</a>
    <a href="#" onclick="showSection('findJobs')">Find Jobs</a>
    <a href="#" onclick="showSection('hireEmployees')">Hire Employees</a>
    <a href="#" onclick="showSection('profile')">Profile</a>
    <a href="logout.php">Sign Out</a>
</div>
<div class="main-content">
    <div class="navbar">
        <h1>JobEntry</h1>
        <button onclick="window.location.href='logout.php'">Sign Out</button>
    </div>
    <div class="dashboard-welcome" id="dashboard">
        <h2>Welcome back, <?php echo $_SESSION['name']; ?>!</h2>
        <p>Discover jobs, connect with companies, and explore talent opportunities.</p>
    </div>
    <div id="findJobs" class="section">
        <h2>Available Jobs</h2>
        <p>Job Listings go here...</p>
    </div>
    <div id="hireEmployees" class="section">
        <h2>Top Talent</h2>
        <p>Talent Listings go here...</p>
    </div>
    <div id="profile" class="section">
        <h2>Your Profile</h2>
        <p>Name: <?php echo $_SESSION['name']; ?></p>
        <p>Role: <?php echo $_SESSION['role']; ?></p>
    </div>
</div>
<script>
function showSection(id){
    document.querySelectorAll('.dashboard-welcome,.section').forEach(sec=>sec.style.display="none");
    document.getElementById(id).style.display="block";
}
</script>
</body>
</html>
