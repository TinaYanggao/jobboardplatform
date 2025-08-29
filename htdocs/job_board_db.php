<?php
$host = "localhost";   // or 127.0.0.1
$user = "root";        // default in phpMyAdmin
$pass = "";            // set password if you created one
$dbname = "job_board"; // <-- use your db name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
