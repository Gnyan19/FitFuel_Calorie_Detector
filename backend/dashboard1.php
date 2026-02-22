<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$conn = new mysqli("localhost", "YOUR_MYSQL_USER", "YOUR_MYSQL_PASSWORD", "YOUR_DATABASE_NAME");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT username, age, weight, height, bmi, bmr, daily_goal, breakfast, lunch, dinner, snacks FROM users WHERE id=?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($username, $age, $weight, $height, $bmi, $bmr, $daily_goal, $breakfast, $lunch, $dinner, $snacks);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode([
    "username" => $username,
    "age" => $age,
    "weight" => $weight,
    "height" => $height,
    "bmi" => $bmi,
    "bmr" => $bmr,
    "daily_goal" => $daily_goal,
    "breakfast" => $breakfast,
    "lunch" => $lunch,
    "dinner" => $dinner,
    "snacks" => $snacks
]);
?>
