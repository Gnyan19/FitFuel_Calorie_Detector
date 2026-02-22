<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calorie Progress</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <header class="navbar" id="navigation-bar">
        <div class="navbar-contents">
            <img src="logo.png" alt="FitFuel Logo" class="logo">
            <button class="logout-btn" onclick="window.location.href='logout.php'">logout
                <span>⏻</span></button>
        </div>
    </header>
    <main class="main">
        <section class="lp">
            <section class="profile3">
                <div class="container">
                    <div class="profile-section">
                        <h2>Welcome, <span id="username">Loading...</span></h2>
                        <p><strong>Age:</strong> <span id="age">Loading...</span></p>
                        <p><strong>Weight:</strong> <span id="weight">Loading...</span></p>
                        <p><strong>Height:</strong> <span id="height">Loading...</span></p>
                        <p><strong>BMI:</strong> <span id="bmi">Loading...</span></p>
                        <p><strong>BMR:</strong> <span id="bmr">Loading...</span></p>
                    </div>
                </div>
            </section>
            <section class="container">
                <h1>Calorie Progress</h1>
                <div id="progress-container">
                    <div id="progress-bar">0%</div>
                </div>
                <button class="reset-btn" id="reset-btn">
                    reset progress
                </button>
            </section>
        </section>
        <section class="rp">
            <section class="profile2">
                <div class="container">
                    <div class="recommended">
                        <h3>Your Recommended Calorie</h3>
                        <p id="recommended-calories">Loading...</p>
                    </div>
                    <div class="food-calorie-count">
                        <div class="consumed">
                            <h3>Calories Consumed:</h3>
                            <p><strong>Breakfast:</strong> <span id="breakfast">Loading...</span></p>
                            <p><strong>Lunch:</strong> <span id="lunch">Loading...</span></p>
                            <p><strong>Dinner:</strong> <span id="dinner">Loading...</span></p>
                            <p><strong>Snacks:</strong> <span id="snacks">Loading...</span></p>
                        </div>
                    </div>
                    <div class="upload-btn">
                        <input type="file" id="imageInput" accept="image/*" style="display: none;">
                        <button onclick="document.getElementById('imageInput').click()">Upload Food Image</button>
                    </div>                    
                </div>
            </section>

        </section>
    </main>


    <script src="logic.js"></script>

</body>

</html>
