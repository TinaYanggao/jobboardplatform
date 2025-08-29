<?php
session_start();
header('Content-Type: application/json');
include("job_board_db.php");

// Disable any PHP output that might break JSON
error_reporting(0);

if (!$conn) {
    echo json_encode(['status'=>'error','message'=>'Database connection failed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $location = $_POST['location'] ?? '';
    $requirements = $_POST['requirements'] ?? '';
    $type = $_POST['type'] ?? '';
    $employer_id = $_SESSION['user_id'];
    $category_id = 1; // default

    if (!$title || !$location || !$requirements || !$type) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO jobs (title, description, location, status, category_id, employer_id, type, date_posted)
         VALUES (?, ?, ?, 'Open', ?, ?, ?, NOW())"
    );
    $stmt->bind_param("sssiss", $title, $requirements, $location, $category_id, $employer_id, $type);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Job created successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit;
}
