<?php
session_start();
include 'job_db.php';

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values safely
    $job_id = intval($_POST['job_id']);
    $applicant_name = $_POST['applicant_name'];
    $applicant_email = $_POST['applicant_email'];
    $cover_letter = $_POST['cover_letter'];

    // Handle resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $uploadDir = 'uploads/resumes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $resume_name = time() . '_' . basename($_FILES['resume']['name']);
        $resume_path = $uploadDir . $resume_name;
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    } else {
        $resume_path = null;
    }

    // Prepared statement to prevent SQL errors/injections
    $stmt = $conn->prepare("INSERT INTO applications (job_id, applicant_name, applicant_email, cover_letter, resume_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $job_id, $applicant_name, $applicant_email, $cover_letter, $resume_path);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Application submitted successfully!";
    } else {
        $_SESSION['error'] = "Error submitting application: " . $stmt->error;
    }

    $stmt->close();
    header("Location: find_job.php");
    exit;
}
?>
