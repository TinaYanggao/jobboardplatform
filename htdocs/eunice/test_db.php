<?php
include("job_board_db.php");

$result = $conn->query("SELECT * FROM users");

if ($result) {
    while($row = $result->fetch_assoc()){
        echo "User: " . $row['name'] . " - Email: " . $row['email'] . "<br>";
    }
} else {
    echo "Error: " . $conn->error;
}
?>