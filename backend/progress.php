<?php
session_start();
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=YOUR_DATABASE_NAME", "YOUR_MYSQL_USER", "YOUR_MYSQL_PASSWORD", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "User not logged in"]);
        exit();
    }

    $id = $_SESSION['user_id']; // Get user ID from session
    $stmt = $pdo->prepare("SELECT breakfast, lunch, dinner, snacks, daily_goal FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) { 
        $total_calories = ($data['breakfast'] ?? 0) + ($data['lunch'] ?? 0) + ($data['dinner'] ?? 0) + ($data['snacks'] ?? 0);
        $daily_goal = $data['daily_goal'] ?? 2000; // Default if missing
        $progress = min(100, ($total_calories / $daily_goal) * 100);
        
        echo json_encode([
            "progress" => round($progress),
            "current_calories" => $total_calories,
            "daily_goal" => $daily_goal
        ]);
    } else {
        echo json_encode(["progress" => 0, "current_calories" => 0, "daily_goal" => 2000]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
