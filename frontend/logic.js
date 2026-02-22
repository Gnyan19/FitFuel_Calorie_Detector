document.addEventListener("DOMContentLoaded", function () {
    let userId = null; // Global variable for user ID

    function fetchUserData() {
        fetch("fetch_user_data.php?t=" + new Date().getTime()) // Prevent cache
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Error:", data.error);
                    return;
                }

                userId = data.id; // Store user ID globally

                // Update Profile
                document.getElementById("username").textContent = data.username;
                document.getElementById("age").textContent = data.age;
                document.getElementById("weight").textContent = data.weight + " kg";
                document.getElementById("height").textContent = data.height + " cm";
                document.getElementById("bmi").textContent = data.bmi;
                document.getElementById("bmr").textContent = data.bmr;
                document.getElementById("recommended-calories").textContent = data.daily_goal + " kcal";

                // Update Calorie Intake
                document.getElementById("breakfast").textContent = data.breakfast + " kcal";
                document.getElementById("lunch").textContent = data.lunch + " kcal";
                document.getElementById("dinner").textContent = data.dinner + " kcal";
                document.getElementById("snacks").textContent = data.snacks + " kcal";

                // Update Progress Bar
                let progressBar = document.getElementById("progress-bar");
                progressBar.style.width = data.progress + "%"; 
                progressBar.textContent = `${data.current_calories} cal`;
            })
            .catch(error => console.error("Error fetching user data:", error));
    }

    // Fetch data initially
    fetchUserData();

    // Fetch updates every 5 seconds
    setInterval(fetchUserData, 5000);

    // Image Upload Handling (Modified to Automatically Use User ID)
    const imageInput = document.getElementById("imageInput");

    imageInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            if (!userId) {
                alert("User ID not found. Please refresh the page.");
                return;
            }

            const mealType = prompt("Which meal? (breakfast, lunch, dinner, snacks)").toLowerCase();
            if (!["breakfast", "lunch", "dinner", "snacks"].includes(mealType)) {
                alert("Invalid input!");
                return;
            }

            uploadImage(file, userId, mealType);
        }
    });

    function uploadImage(file, userId, mealType) {
        const formData = new FormData();
        formData.append("image", file);
        formData.append("user_id", userId);
        formData.append("meal_type", mealType);

        fetch("http://127.0.0.1:5000/upload", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("Error: " + data.error);
            } else {
                updateCalorieDisplay(mealType, data.calories);
            }
        })
        .catch(error => console.error("Error:", error));
    }

    function updateCalorieDisplay(mealType, calories) {
        let mealElement = document.getElementById(mealType);
        let currentCalories = mealElement.textContent.match(/\d+/);
        currentCalories = currentCalories ? parseInt(currentCalories[0]) : 0;
        mealElement.textContent = `${currentCalories + calories} kcal`;
    }

    document.getElementById("reset-btn").addEventListener("click", function () {
        if (!confirm("Are you sure you want to reset your calorie progress?")) return;

        fetch("reset_calories.php", { method: "POST" })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Calorie progress has been reset!");
                    fetchUserData(); // Refresh without page reload
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Reset failed:", error));
    });

});

