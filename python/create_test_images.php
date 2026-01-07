import cv2
import numpy as np
import os

# Create uploads dir if not exists (redundant safety)
if not os.path.exists("uploads/lost"):
    os.makedirs("uploads/lost")

# 1. Create a Red Image
red_img = np.zeros((300, 300, 3), dtype=np.uint8)
red_img[:] = (0, 0, 255) # BGR for Red
cv2.imwrite("uploads/lost/test_red.jpg", red_img)

# 2. Create another Red Image (Slightly different shade for realistic match)
red_img_2 = np.zeros((300, 300, 3), dtype=np.uint8)
red_img_2[:] = (10, 10, 240) 
cv2.imwrite("uploads/lost/test_red_2.jpg", red_img_2)

# 3. Create a Blue Image
blue_img = np.zeros((300, 300, 3), dtype=np.uint8)
blue_img[:] = (255, 0, 0) # BGR for Blue
cv2.imwrite("uploads/lost/test_blue.jpg", blue_img)

print("Test images created.")
