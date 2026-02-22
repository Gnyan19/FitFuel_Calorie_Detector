import sys
import os
sys.path.insert(0, os.path.dirname(__file__))  # Ensure ai/ folder is on path

import requests
from dotenv import load_dotenv
from image_upload import upload_image  # Import function

# Load environment variables from .env file
load_dotenv(os.path.join(os.path.dirname(__file__), '..', '.env'))

# 🛑 USDA API Key (loaded from .env)
API_KEY = os.getenv("USDA_API_KEY", "YOUR_USDA_API_KEY")

# Function to get calories from USDA API
def get_food_calories(food_name):
    url = f"https://api.nal.usda.gov/fdc/v1/foods/search?query={food_name}&api_key={API_KEY}"
    response = requests.get(url)
    
    if response.status_code == 200:
        data = response.json()
        if data.get("foods"):
            food = data["foods"][0]
            calories = next((nutrient["value"] for nutrient in food["foodNutrients"] if nutrient["nutrientName"] == "Energy"), None)
            return calories if calories else "Calorie data not found"
        else:
            return "Food not found"
    else:
        return "API Error"

# 🌟 Main Program
image_path = upload_image()  # Step 1: Upload Image
if image_path:
    food_name = input("Enter the detected food name: ")  # Step 2: Enter Food Name
    calories = get_food_calories(food_name)  # Step 3: Get Calories
    print(f"Calories in {food_name}: {calories} kcal")  # Step 4: Display Output
