<?php
$host = 'localhost';
$db = 'environment_dashboard';
$user = 'root';
$pass = '0000';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>