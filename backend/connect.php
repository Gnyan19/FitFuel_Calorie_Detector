<?php
$host = "localhost";          // Change if using a remote database
$user = "YOUR_MYSQL_USER";    // Your MySQL username (e.g. root)
$pass = "YOUR_MYSQL_PASSWORD"; // Your MySQL password
$dbname = "YOUR_DATABASE_NAME"; // Your database name (e.g. fitfuel)

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
