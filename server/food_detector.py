import tensorflow as tf
import numpy as np
import cv2
import os
import requests  # Importing the requests library for the API call
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv(os.path.join(os.path.dirname(__file__), '..', '.env'))

# Load the MobileNetV2 model (pretrained on ImageNet)
model = tf.keras.applications.MobileNetV2(weights="imagenet")

# 🌟 Function to get calories from USDA API
def get_food_calories(food_name):
    API_KEY = os.getenv("USDA_API_KEY", "YOUR_USDA_API_KEY")  # Loaded from .env
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

def predict_food(image_path):
    image = cv2.imread(image_path)
    
    if image is None:
        print(f"Error: Unable to read image from '{image_path}'. Check the path!")
        return

    image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    image = cv2.resize(image, (224, 224))
    image = np.expand_dims(image, axis=0)
    image = tf.keras.applications.mobilenet_v2.preprocess_input(image)

    predictions = model.predict(image)
    best_prediction = max(tf.keras.applications.mobilenet_v2.decode_predictions(predictions, top=3)[0], key=lambda x: x[2])

    food_label = best_prediction[1]
    print(f"Predicted Food: {food_label}")

    # Fetch calories for the detected food
    calories = get_food_calories(food_label)
    print(f"Calories in {food_label}: {calories} kcal")

# Ensure the path is correct
# Path to the sample image in the project root (relative to ai/ folder)
image_path = os.path.join(os.path.dirname(__file__), "..", "bur.jpg")
predict_food(image_path)
