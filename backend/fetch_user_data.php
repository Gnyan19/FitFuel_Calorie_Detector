<?php
session_start();
header("Content-Type: application/json");
require 'connect.php'; // Ensure this file connects to your database

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

$stmt = $conn->prepare("SELECT id, username, age, weight, height, bmi, bmr, breakfast, lunch, dinner, snacks, daily_goal FROM users WHERE id = ?");
$user_data['id'] = $user_id; // Include user ID

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if ($user_data) {
    // Calculate total consumed calories and progress
    $total_calories = ($user_data['breakfast'] ?? 0) + ($user_data['lunch'] ?? 0) + ($user_data['dinner'] ?? 0) + ($user_data['snacks'] ?? 0);
    $daily_goal = $user_data['daily_goal'] ?? 2000;
    $progress = min(100, ($total_calories / $daily_goal) * 100);

    // Include total calories in response
    $user_data['current_calories'] = $total_calories;
    $user_data['progress'] = round($progress);

    echo json_encode($user_data);
} else {
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
