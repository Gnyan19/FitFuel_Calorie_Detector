import cv2
from tkinter import filedialog

def upload_image():
    file_path = filedialog.askopenfilename(title="Select an image")  # Open file dialog
    if not file_path:
        print("No file selected!")
        return None
    
    image = cv2.imread(file_path)  # Read image
    cv2.imshow("Uploaded Image", image)  # Show image
    cv2.waitKey(0)
    cv2.destroyAllWindows()
    
    return file_path  # Return file path
