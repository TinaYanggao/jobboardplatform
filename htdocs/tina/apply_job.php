<?php
include 'job_db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $job_id = $_POST['job_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $cover_letter = $_POST['cover_letter'];

    $stmt = $conn->prepare("INSERT INTO applications (job_id, applicant_name, applicant_email, cover_letter) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $job_id, $name, $email, $cover_letter);

    if ($stmt->execute()) {
        echo "<script>alert('Application submitted successfully!'); window.location.href='find_jobs.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>