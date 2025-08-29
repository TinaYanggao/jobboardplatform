<?php
$servername = "localhost"; // kung sa Laragon/XAMPP, default is localhost
$username = "root"; // default user
$password = ""; // default password is empty
$dbname = "job_bp"; // or JobEntry, depende sa ginamit mong CREATE DATABASE

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
