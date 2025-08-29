<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php"); // replace with your login page
exit();
?>
