<?php
session_start();
include 'job_db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = intval($_POST['job_id']);
    $sender_id = $_SESSION['user_id'];
    $receiver_email = $_POST['receiver_email'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (job_id, sender_id, receiver_email, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $job_id, $sender_id, $receiver_email, $message);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Message sent successfully!";
    header("Location: hire_talent.php");
    exit;
}
