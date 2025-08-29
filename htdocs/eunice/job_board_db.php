<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job_board";

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn) {
    echo json_encode(['status'=>'error','message'=>'Database connection failed']);
    exit;
}


$conn->set_charset("utf8");
?>