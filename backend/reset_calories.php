<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=YOUR_DATABASE_NAME", "YOUR_MYSQL_USER", "YOUR_MYSQL_PASSWORD", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("UPDATE users SET breakfast = 0, lunch = 0, dinner = 0, snacks = 0");
    $stmt->execute();

    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>