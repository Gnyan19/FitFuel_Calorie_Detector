# 🍽️ FitFuel — AI-Powered Calorie Detector & Tracker

> Upload a food photo → AI identifies the food → fetches real calorie data → tracks your daily intake with a live progress bar.

![FitFuel Banner](assests/Landing%20page.jpg)

---

## 🧠 Overview

**FitFuel** is a full-stack web application that combines **computer vision**, **nutritional APIs**, and **health science formulas** to help users track their daily calorie consumption. The application uses a **dual-server architecture** — a PHP backend serves the web interface and handles authentication, while a Python Flask server runs the AI model for food recognition.

### The Problem

Manually counting calories is tedious and error-prone. Users need to search databases, guess portion sizes, and remember to log every meal.

### The Solution

FitFuel automates this process. Simply **snap a photo of your food**, and the AI:
1. **Identifies** what food is in the image using a deep learning model
2. **Fetches** the real calorie count from the USDA nutritional database
3. **Logs** the calories to the correct meal slot (breakfast, lunch, dinner, or snacks)
4. **Updates** your daily progress bar in real time

---

## ✨ Key Features

| Feature | Description |
|---------|-------------|
| 📷 **Food Image Recognition** | Upload a photo and the AI detects the food using MobileNetV2, a deep learning model pre-trained on 1,000+ image categories |
| 🔍 **Real Calorie Lookup** | Detected food names are queried against the **USDA FoodData Central API** — the same database used by nutritionists and healthcare professionals |
| 🧮 **BMI Calculator** | On signup, the app auto-calculates your **BMI** (Body Mass Index), **BFP** (Body Fat Percentage), and **BMR** (Basal Metabolic Rate) |
| 🎯 **Personalized Daily Goals** | Your daily calorie target is dynamically set based on your BMI category — underweight users get a surplus, overweight users get a deficit |
| 📊 **Meal-wise Tracking** | Track calories across 4 meal categories: Breakfast, Lunch, Dinner, and Snacks |
| 📈 **Live Progress Bar** | A visual calorie progress bar that auto-refreshes every 5 seconds without reloading the page |
| 🔐 **Secure Authentication** | PHP session-based auth with `bcrypt`-hashed passwords — the industry standard for password security |
| 🔄 **One-Click Reset** | Reset all meal calories back to 0 with a single button click |

---

## 🛠️ Tech Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Frontend** | HTML5, CSS3, JavaScript (ES6) | User interface, forms, real-time updates |
| **Backend** | PHP 8+ (built-in dev server) | Authentication, session management, database queries |
| **Database** | MySQL 8+ | Persistent storage for user profiles, health metrics, and calorie data |
| **AI Server** | Python 3.9+ / Flask | REST API for food image upload, prediction, and calorie storage |
| **AI Model** | TensorFlow / Keras (MobileNetV2) | Deep learning model for food image classification |
| **Image Processing** | OpenCV (cv2) + NumPy | Image loading, color conversion, and resizing for model input |
| **Nutrition Data** | USDA FoodData Central API | Government-backed calorie and nutrient database |
| **Icons** | Font Awesome 6 | UI icons for the login page |

---

## 🏗️ Architecture & How It Works

### High-Level Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                        USER'S BROWSER                            │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────────────┐ │
│  │ bmi.html │  │login.html│  │index.php │  │    logic.js      │ │
│  │ (Signup) │  │ (Login)  │  │(Dashboard│  │ (Fetch & Upload) │ │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └───────┬──────────┘ │
└───────┼──────────────┼─────────────┼────────────────┼────────────┘
        │              │             │                │
        ▼              ▼             ▼                ▼
┌───────────────────────────────┐  ┌──────────────────────────────┐
│     PHP SERVER (Port 8000)    │  │  FLASK AI SERVER (Port 5000) │
│                               │  │                              │
│  connect1.php → Registration  │  │  POST /upload                │
│  login.php    → Auth          │  │    ├── Save image to disk    │
│  index.php    → Session guard │  │    ├── MobileNetV2 predict   │
│  dashboard1.php → User stats  │  │    ├── USDA API lookup       │
│  fetch_user_data.php → Meals  │  │    └── UPDATE MySQL calories │
│  progress.php → Progress %    │  │                              │
│  reset_calories.php → Reset   │  │  server.py                   │
│  logout.php   → Session kill  │  │                              │
└───────────┬───────────────────┘  └──────────────┬───────────────┘
            │                                      │
            └──────────────┬───────────────────────┘
                           ▼
                  ┌─────────────────┐
                  │   MySQL (3306)  │
                  │   Database:     │
                  │   fitfuel       │
                  │                 │
                  │   Table: users  │
                  └─────────────────┘
```

### Complete User Flow

#### 1️⃣ Registration Flow
```
User fills form (bmi.html)
    → POST to connect1.php
    → Server calculates BMI, BFP, BMR
    → Determines BMI category
    → Sets personalized daily calorie goal
    → Hashes password with bcrypt
    → INSERT into MySQL users table
    → Displays results
    → Redirects to login.html
```

#### 2️⃣ Login Flow
```
User enters email + password (login.html)
    → POST to login.php
    → SELECT password_hash from MySQL
    → password_verify() checks bcrypt hash
    → On success: create PHP session → redirect to index.php
    → On failure: alert message → back to login.html
```

#### 3️⃣ Dashboard Flow
```
Browser loads index.php
    → PHP checks session → if not logged in → redirect to login.html
    → If logged in → render dashboard HTML
    → logic.js fires on DOMContentLoaded
    → fetch("fetch_user_data.php") → GET user profile + meal data
    → Updates DOM: username, age, weight, height, BMI, BMR
    → Updates meal calories: breakfast, lunch, dinner, snacks
    → Calculates and renders progress bar
    → setInterval: re-fetches every 5 seconds for live updates
```

#### 4️⃣ Food Upload & AI Detection Flow
```
User clicks "Upload Food Image"
    → Selects image file
    → Prompted for meal type (breakfast/lunch/dinner/snacks)
    → logic.js sends POST to Flask server (http://127.0.0.1:5000/upload)
        → FormData: image file + user_id + meal_type
    → Flask server (server.py):
        1. Saves image to uploads/ directory
        2. Loads image with OpenCV (cv2.imread)
        3. Converts BGR → RGB color space
        4. Resizes to 224×224 pixels (MobileNetV2 input size)
        5. Applies MobileNetV2 preprocessing (normalization)
        6. Runs model.predict() → top 3 predictions
        7. Selects highest-confidence prediction
        8. Queries USDA API with food name → gets calories
        9. UPDATE MySQL: adds calories to user's meal column
        10. Returns JSON: {"food": "pizza", "calories": 266}
    → logic.js updates the meal display instantly
    → Background polling picks up the updated progress bar
```

---

## 📖 Core Concepts & Terminologies

### Health Science Formulas

#### BMI (Body Mass Index)
A measure of body fat based on height and weight. Used worldwide as a quick screening tool for weight categories.

```
BMI = weight (kg) ÷ height² (m²)
```

| BMI Range | Category | App Behavior |
|-----------|----------|-------------|
| ≤ 18.0 | Underweight | Daily goal = BMR × 1.6 (calorie surplus) |
| 18.1 – 26.0 | Healthy weight | Daily goal = BMR × 1.2 (maintenance) |
| > 26.0 | Overweight/Obese | Daily goal = BMR × 0.8 (calorie deficit) |

#### BFP (Body Fat Percentage)
Estimates the percentage of your body that is composed of fat tissue.

```
BFP = (1.20 × BMI) + (0.23 × Age) − 5.4
```

> This uses the **Deurenberg formula**, a widely-referenced estimation method that correlates BMI with body fat.

#### BMR (Basal Metabolic Rate)
The number of calories your body burns at complete rest — just to keep your organs functioning (breathing, circulation, cell production).

```
Male:   BMR = (10 × weight_kg) + (6.25 × height_cm) − (5 × age) + 5
Female: BMR = (10 × weight_kg) + (6.25 × height_cm) − (5 × age) − 161
```

> This uses the **Mifflin-St Jeor Equation**, which is considered the most accurate BMR formula by the American Dietetic Association.

---

### AI & Machine Learning Concepts

#### MobileNetV2
A lightweight deep learning model developed by Google, designed for mobile and edge devices. It is **pre-trained on ImageNet** — a dataset of over 14 million images across 1,000 categories (including many food items).

**Why MobileNetV2?**
- Small model size (~14 MB) — fast to load
- Optimized for real-time inference
- Good accuracy despite being lightweight
- Pre-trained — no custom training needed

**How it works in this app:**
1. Image is resized to **224×224 pixels** (the fixed input size MobileNetV2 expects)
2. Pixel values are **normalized** to the range [-1, 1] using `preprocess_input()`
3. The model outputs probabilities for each of the 1,000 ImageNet classes
4. The top predictions are decoded to human-readable labels (e.g., "pizza", "bagel")
5. The label with the **highest confidence score** is selected as the detected food

#### ImageNet
A massive dataset of labeled images used to train computer vision models. MobileNetV2 was pre-trained on ImageNet, meaning it already "knows" thousands of object categories — we leverage this without needing to collect our own training data.

#### OpenCV (cv2)
An open-source computer vision library used here for:
- `cv2.imread()` — Loading images from disk
- `cv2.cvtColor()` — Converting BGR (OpenCV's default) to RGB (what TensorFlow expects)
- `cv2.resize()` — Resizing images to 224×224

#### TensorFlow & Keras
**TensorFlow** is Google's open-source machine learning framework. **Keras** is its high-level API that makes it easy to load and use pre-trained models with just a few lines of code:
```python
model = tf.keras.applications.MobileNetV2(weights="imagenet")
predictions = model.predict(image)
```

---

### Web Development Concepts

#### PHP Sessions
HTTP is stateless — the server doesn't remember who you are between requests. **PHP sessions** solve this by:
1. Creating a unique session ID on login
2. Storing it as a cookie in the browser
3. On each request, PHP reads the session cookie and retrieves the user's data (like `user_id` and `username`)
4. This keeps the user "logged in" across pages

#### bcrypt Password Hashing
Passwords are **never stored in plain text**. When a user registers:
```php
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
```
This creates a **one-way hash** — it's computationally infeasible to reverse. On login:
```php
password_verify($input_password, $stored_hash);
```
This compares the input against the stored hash without ever decrypting it.

#### CORS (Cross-Origin Resource Sharing)
The PHP server runs on port **8000** and the Flask server runs on port **5000**. Browsers block requests between different origins (ports) by default — this is a security feature. `flask-cors` adds the necessary headers to allow the frontend to communicate with the AI server.

#### AJAX (Asynchronous JavaScript)
The dashboard uses `fetch()` API calls to retrieve data from PHP endpoints **without reloading the page**. This creates a smooth, real-time experience where calorie data updates automatically every 5 seconds.

#### REST API
The Flask server exposes a single REST endpoint:
- **POST `/upload`** — Accepts an image file, user ID, and meal type; returns the detected food name and its calorie count as JSON

#### PDO vs MySQLi
The project uses **both** PHP database extensions:
- **MySQLi** (MySQL Improved) — Used in `connect.php`, `connect1.php`, `login.php`, `dashboard1.php`, and `fetch_user_data.php`. Object-oriented, supports prepared statements.
- **PDO** (PHP Data Objects) — Used in `progress.php` and `reset_calories.php`. More flexible, supports multiple database types, and offers a cleaner exception-based error handling model.

Both are secure when used with **prepared statements** (parameterized queries), which prevent SQL injection attacks.

---

### Nutrition Data

#### USDA FoodData Central API
A free, government-maintained API provided by the **U.S. Department of Agriculture**. It contains nutritional data for over 300,000 food items. This app queries it to get the **Energy (calories)** value for detected foods.

```
GET https://api.nal.usda.gov/fdc/v1/foods/search?query={food_name}&api_key={key}
```

The response contains an array of matching foods. The app takes the **first match** and extracts the `Energy` nutrient value (measured in kcal per 100g).

---

## 📁 Project Structure

```
fitfuel-calorie-detector/
│
├── ai/                          # Standalone Python AI utility scripts
│   ├── calorie_detector.py      #   CLI tool: upload image → enter food → get calories
│   ├── food_detector.py         #   CLI tool: image → auto-detect food → get calories
│   └── image_upload.py          #   Helper: Tkinter file dialog for image selection
│
├── assests/                     # Static assets (logos, landing images)
│   ├── Landing page.jpg         #   Hero banner for the README
│   ├── Logo-white.png           #   White variant of the logo
│   └── ...                      #   Other design assets
│
├── db/
│   └── final queries.sql        # SQL schema to initialize the database
│
├── uploads/                     # User-uploaded food images (git-ignored)
│
├── connect.php                  # Shared MySQL connection (MySQLi)
├── connect1.php                 # Registration handler (BMI/BMR calc + DB insert)
├── login.php                    # Login handler (session-based auth with bcrypt)
├── logout.php                   # Session destruction → redirect to login
├── index.php                    # Dashboard entry point (session-protected)
├── dashboard.php                # Minimal dashboard (fallback)
├── dashboard1.php               # JSON API: user profile stats
├── fetch_user_data.php          # JSON API: complete user data + calorie progress
├── progress.php                 # JSON API: calorie progress percentage
├── reset_calories.php           # Resets all meal calories to 0
│
├── server.py                    # Flask AI server (image → detect → calories → DB)
│
├── logic.js                     # Frontend JS (upload handler, live data fetching)
│
├── bmi.html                     # Signup page with BMI explanation
├── bmi.css                      # Signup page styles
├── login.html                   # Login page
├── style.css                    # Login page styles
├── styles.css                   # Main dashboard styles
├── bphp.css                     # Registration result page styles
├── pro.css                      # Progress bar styles
│
├── logo.png                     # App logo
├── requirements.txt             # Python dependencies
├── .gitignore                   # Git ignore rules
└── README.md                    # This file
```

---

## 📄 Detailed File-by-File Breakdown

### Frontend Layer

| File | Purpose | Key Details |
|------|---------|-------------|
| `bmi.html` | **Signup Page** | Form captures: username, email, password, age, weight (kg), height (cm), gender. Includes BMI formula explanation. POSTs to `connect1.php` |
| `login.html` | **Login Page** | Email + password form. Uses Font Awesome icons. POSTs to `login.php` |
| `index.php` | **Main Dashboard** | Session-protected. Shows user profile (name, age, weight, height, BMI, BMR), calorie progress bar, meal breakdown, "Upload Food Image" button, and reset button. Loads `logic.js` |
| `logic.js` | **Frontend Logic** | On page load: fetches user data via AJAX. Every 5 seconds: re-fetches for live updates. Handles image upload → prompts for meal type → POSTs to Flask → updates display |

### Backend Layer (PHP)

| File | Type | Purpose |
|------|------|---------|
| `connect.php` | DB Connection | Creates a shared MySQLi connection object (`$conn`) used by other PHP files via `require` |
| `connect1.php` | Registration | Receives form POST → calculates BMI, BFP, BMR → determines BMI category → calculates daily calorie goal → hashes password → INSERTs into MySQL → shows results |
| `login.php` | Authentication | Receives email/password → queries DB → verifies bcrypt hash → creates session → redirects to dashboard |
| `logout.php` | Session Kill | Destroys session → redirects to login page |
| `dashboard1.php` | JSON API | Returns user profile data as JSON (username, age, weight, height, BMI, BMR, daily_goal, all meal columns) |
| `fetch_user_data.php` | JSON API | Returns complete user data including calculated progress percentage and total calories consumed |
| `progress.php` | JSON API | Returns calorie progress: `{ progress: 65, current_calories: 1300, daily_goal: 2000 }` |
| `reset_calories.php` | Action | SETs breakfast, lunch, dinner, snacks to 0 for all users |

### AI Layer (Python)

| File | Purpose | Key Details |
|------|---------|-------------|
| `server.py` | **Flask AI Server** | The heart of the AI system. Loads MobileNetV2 at startup. Exposes `POST /upload` endpoint. Processes image → predicts food → fetches USDA calories → updates MySQL → returns JSON |
| `ai/food_detector.py` | Standalone CLI tool | Processes a hardcoded image path → runs MobileNetV2 → queries USDA API → prints food name + calories. Useful for testing the AI pipeline independently |
| `ai/calorie_detector.py` | Standalone CLI tool | Opens a file dialog → user selects image → manually enters food name → fetches USDA calories. Semi-automated workflow |
| `ai/image_upload.py` | Helper module | Uses Tkinter's `filedialog` to open a native OS file picker. Displays the selected image with OpenCV. Returns the file path |

---

## 🗄️ Database Schema

A single `users` table stores everything — user credentials, health metrics, and daily calorie tracking:

```sql
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,           -- bcrypt hash
    age           TINYINT UNSIGNED NOT NULL,
    weight        SMALLINT UNSIGNED NOT NULL,       -- in kg
    height        SMALLINT UNSIGNED NOT NULL,       -- in cm
    gender        ENUM('m', 'f') NOT NULL,
    bmi           FLOAT DEFAULT NULL,               -- auto-calculated on signup
    bmi_category  VARCHAR(100) DEFAULT NULL,         -- e.g., "Healthy range"
    bfp           FLOAT(5, 2) DEFAULT NULL,          -- Body Fat Percentage
    bmr           FLOAT(8, 2) DEFAULT NULL,          -- Basal Metabolic Rate
    breakfast     INT DEFAULT 0,                     -- calories consumed
    lunch         INT DEFAULT 0,
    dinner        INT DEFAULT 0,
    snacks        INT DEFAULT 0,
    daily_goal    INT DEFAULT 0,                     -- personalized target
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Column Logic

| Column | Set By | Updated By |
|--------|--------|------------|
| `username`, `email`, `password_hash` | `connect1.php` (registration) | Never |
| `age`, `weight`, `height`, `gender` | `connect1.php` (registration) | Never |
| `bmi`, `bmi_category`, `bfp`, `bmr`, `daily_goal` | `connect1.php` (auto-calculated) | Never |
| `breakfast`, `lunch`, `dinner`, `snacks` | Default 0 | `server.py` (adds calories), `reset_calories.php` (resets to 0) |

---

## 🔌 API Endpoints

### PHP Endpoints (Port 8000)

| Method | Endpoint | Auth | Response |
|--------|----------|------|----------|
| POST | `/connect1.php` | No | HTML — registration result with BMI/BMR stats |
| POST | `/login.php` | No | Redirect to `/index.php` on success |
| GET | `/index.php` | Session | Dashboard HTML |
| GET | `/fetch_user_data.php` | Session | JSON — full user data + progress |
| GET | `/dashboard1.php` | Session | JSON — user profile stats |
| GET | `/progress.php` | Session | JSON — `{ progress, current_calories, daily_goal }` |
| POST | `/reset_calories.php` | No | JSON — `{ status: "success" }` |
| GET | `/logout.php` | Session | Redirect to `/login.html` |

### Flask AI Endpoint (Port 5000)

| Method | Endpoint | Parameters | Response |
|--------|----------|-----------|----------|
| POST | `/upload` | `image` (file), `user_id` (string), `meal_type` (string) | JSON — `{ food: "pizza", calories: 266 }` |

Valid `meal_type` values: `breakfast`, `lunch`, `dinner`, `snacks`

---

## ⚙️ Setup Guide

### Prerequisites

| Tool | Version | Purpose |
|------|---------|---------|
| PHP | 8.0+ | Backend web server |
| MySQL | 8.0+ | Database |
| Python | 3.9+ | AI server |
| pip | Any | Python package manager |
| Git | Any | Version control |

> **💡 Windows Users:** Install [XAMPP](https://www.apachefriends.org/download.html) to get both PHP and MySQL in one package. Add `C:\xampp\php` to your system PATH.

---

### Step 1 — Clone the Repository

```bash
git clone https://github.com/EnochJackson/fitfuel-calorie-detector.git
cd fitfuel-calorie-detector
```

---

### Step 2 — Enable PHP Extensions

Open your `php.ini` file (find its location with `php --ini`) and **uncomment** these lines by removing the leading `;`:

```ini
extension=mysqli
extension=pdo_mysql
```

Verify extensions are loaded:

```bash
php -m | grep -E "mysqli|pdo_mysql"
```

---

### Step 3 — Set Up the Database

1. Start MySQL (via XAMPP Control Panel or your MySQL service)

2. Log in to MySQL:
   ```bash
   mysql -u root -p
   ```

3. Create the database and import the schema:
   ```sql
   CREATE DATABASE fitfuel;
   USE fitfuel;
   SOURCE db/final queries.sql;
   EXIT;
   ```

---

### Step 4 — Configure Database Credentials

Search for `YOUR_MYSQL_USER`, `YOUR_MYSQL_PASSWORD`, and `YOUR_DATABASE_NAME` in the following files and replace them with your actual MySQL credentials:

| File | What to update |
|------|----------------|
| `connect.php` | `$user`, `$pass`, `$dbname` |
| `connect1.php` | `mysqli(...)` arguments |
| `dashboard1.php` | `mysqli(...)` arguments |
| `progress.php` | PDO connection string |
| `reset_calories.php` | PDO connection string |
| `server.py` | `user`, `password`, `database` fields |

**Example** — `connect.php`:
```php
$user   = "root";           // ← your MySQL username
$pass   = "your_password";  // ← your MySQL password
$dbname = "fitfuel";        // ← your database name
```

**Example** — `server.py`:
```python
return mysql.connector.connect(
    host="localhost",
    user="root",             # ← your MySQL username
    password="your_password",# ← your MySQL password
    database="fitfuel"       # ← your database name
)
```

---

### Step 5 — Get a USDA API Key

1. Go to: [https://fdc.nal.usda.gov/api-key-signup.html](https://fdc.nal.usda.gov/api-key-signup.html)
2. Sign up for a free API key
3. Open `server.py` and replace the `API_KEY` value:
   ```python
   API_KEY = "your_usda_api_key_here"
   ```

---

### Step 6 — Install Python Dependencies

```bash
pip install -r requirements.txt
```

This installs: `flask`, `flask-cors`, `opencv-python`, `numpy`, `tensorflow`, `requests`, `mysql-connector-python`

---

### Step 7 — Start Both Servers

You need **two terminals** running simultaneously:

**Terminal 1 — AI Server (Flask + TensorFlow):**
```bash
python server.py
```
Runs on `http://127.0.0.1:5000`

> ⏳ First launch takes ~10-15 seconds as TensorFlow loads the MobileNetV2 model into memory.

**Terminal 2 — PHP Server:**
```bash
php -S localhost:8000
```
Runs on `http://localhost:8000`

---

## 🚀 Usage

### 1. Create an Account
1. Open `http://localhost:8000/bmi.html`
2. Fill in your details: username, email, password, age, weight (kg), height (cm), gender
3. Click **Calculate** — your BMI, BFP, BMR, and daily calorie goal are auto-calculated
4. You'll see your health stats and a button to go to login

### 2. Log In
1. Go to `http://localhost:8000/login.html`
2. Enter your email and password
3. You'll be redirected to the dashboard

### 3. Track Calories
1. On the dashboard, click **Upload Food Image**
2. Select a photo of your food
3. When prompted, enter the meal type: `breakfast`, `lunch`, `dinner`, or `snacks`
4. The AI detects the food, fetches calories from USDA, and updates your dashboard
5. Watch your progress bar fill up toward your daily goal!

### 4. Reset Progress
- Click the **Reset Progress** button to zero out all meal calories for a fresh day

---

## 🔐 Security Considerations

| Aspect | Implementation |
|--------|---------------|
| **Password Storage** | bcrypt hashing via `password_hash(PASSWORD_BCRYPT)` — never stored in plain text |
| **SQL Injection** | Prevented via prepared statements (`$stmt->bind_param()` and PDO `execute()`) |
| **Session Security** | PHP native sessions with server-side storage |
| **CORS** | `flask-cors` enables controlled cross-origin requests between PHP (port 8000) and Flask (port 5000) |
| **Input Validation** | Server-side checks for required fields, valid meal types, and file presence |

### Creating a Password Hash Manually

If you want to insert a test user directly via SQL:
```bash
php -r "echo password_hash('your_plain_password', PASSWORD_DEFAULT);"
```
Use the output as the `password_hash` value in your INSERT statement.

---

## 👨‍💻 Author

**Gnyanprakhash M**

- 📧 Email: [gnyanprakhash2005@gmail.com](mailto:gnyanprakhash2005@gmail.com)
- 💼 LinkedIn: [linkedin.com/in/gnyanprakhash-m-46104b361](https://www.linkedin.com/in/gnyanprakhash-m-46104b361)

---

## 📄 License

MIT License © 2026 Gnyanprakhash M
