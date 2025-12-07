<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "QLBH";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>