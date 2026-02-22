<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <link rel="stylesheet" href="bphp.css"> <!-- Link to external CSS -->
</head>

<body>
    <header>
        <div class="header-left">
            <img src="logo.png" alt="FitFuel Logo" class="logo">
        </div>
    </header>
    <div class="sliding-background">
        <img src="https://i.pinimg.com/736x/c2/1f/b3/c21fb3af88be43c89e80924efa5d651d.jpg" alt="Image 1">
        <img src="https://i.pinimg.com/736x/df/f3/a3/dff3a3e9819404f3a9a46c4af413ccd6.jpg" alt="Image 2">
        <img src="https://mc.today/wp-content/uploads/2021/03/Depositphotos_150139860_xl-2015-768x447.jpg"
            alt="Image 3">
        <img src="https://i.pinimg.com/736x/d3/ba/86/d3ba868a56eef7d88f023534388ba2d3.jpg" alt="Image 4">
    </div>

    <div class="container">
        <?php
        if (!isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['age'], $_POST['weight'], $_POST['height'], $_POST['gender'])) {
            die("<p class='error'>Error: Missing form data.</p>");
        }

        // Sanitize input
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $age = (int) $_POST['age'];
        $weight = (int) $_POST['weight'];
        $height = (int) $_POST['height'];
        $gender = $_POST['gender'];

        // BMI calculations 
        $height_metre = $height / 100;
        $bmi = round($weight / ($height_metre * $height_metre), 1);

        // BFP & BMR Calculation
        $bfp = round((1.20 * $bmi) + (0.23 * $age) - 5.4, 2);
        $bmr = ($gender === 'm') ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5
            : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;

        // BMI category & Daily Goal using switch
        switch (true) {
            case ($bmi > 26):
                $daily_goal = $bmr * 0.8;
                $bmi_category = "Overweight or Obese. Reduce calorie intake.";
                break;

            case ($bmi > 18 && $bmi <= 26):
                $daily_goal = $bmr * 1.2;
                $bmi_category = "Healthy range. Maintain a balanced intake.";
                break;

            case ($bmi <= 18):
                $daily_goal = $bmr * 1.6;
                $bmi_category = "Underweight. Increase your calorie intake.";
                break;

            default:
                $bmi_category = "Invalid BMI.";
                $daily_goal = 0;
                break;
        }

        // Database connection
        $conn = new mysqli("localhost", "YOUR_MYSQL_USER", "YOUR_MYSQL_PASSWORD", "YOUR_DATABASE_NAME");
        if ($conn->connect_error) {
            die("<p class='error'>Connection Failed: " . $conn->connect_error . "</p>");
        }

        // Insert user with daily goal in `users` table
        $stmt = $conn->prepare("INSERT INTO users(username, email, password_hash, age, weight, height, gender, bmi, bmi_category, bfp, bmr, daily_goal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiissssii", $username, $email, $password, $age, $weight, $height, $gender, $bmi, $bmi_category, $bfp, $bmr, $daily_goal);

        if ($stmt->execute()) {

            echo "<h2>RESULT</h2>
            <p>Your BMI: " . number_format($bmi, 2) . " --> " . $bmi_category . "<br>";
            echo "Your Body Fat Percentage: " . number_format($bfp, 2) . "<br>";
            echo "Your Basal Metabolic Rate: " . number_format($bmr, 2) . " kcal/day<br>";
            echo "Your Recommended Daily Calorie Intake: " . $daily_goal . " kcal</p>";
            echo "<button onclick='window.location.href=\"login.html\"'>Login</button>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>

</body>

</html>