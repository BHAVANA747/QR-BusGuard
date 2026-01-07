import cv2
import sys
import json
import os

def compare_images(lost_image_path, found_image_path):
    try:
        # Load images
        img1 = cv2.imread(lost_image_path)
        img2 = cv2.imread(found_image_path)

        if img1 is None or img2 is None:
            return {"error": "One or both images could not be loaded"}

        # Resize for consistent comparison (speed optimization)
        img1 = cv2.resize(img1, (300, 300))
        img2 = cv2.resize(img2, (300, 300))

        # Convert to HSV color space
        hsv_img1 = cv2.cvtColor(img1, cv2.COLOR_BGR2HSV)
        hsv_img2 = cv2.cvtColor(img2, cv2.COLOR_BGR2HSV)

        # Calculate histograms (Hue, Saturation)
        # Using 50 bins for hue and 60 for saturation
        hist_img1 = cv2.calcHist([hsv_img1], [0, 1], None, [50, 60], [0, 180, 0, 256])
        hist_img2 = cv2.calcHist([hsv_img2], [0, 1], None, [50, 60], [0, 180, 0, 256])

        # Normalize histograms
        cv2.normalize(hist_img1, hist_img1, 0, 1, cv2.NORM_MINMAX)
        cv2.normalize(hist_img2, hist_img2, 0, 1, cv2.NORM_MINMAX)

        # Compare histograms using Intersection method
        # Result is matching coefficient
        similarity = cv2.compareHist(hist_img1, hist_img2, cv2.HISTCMP_INTERSECT)

        # Higher is better for INTERSECT, but its scale depends on normalization.
        # Alternatively use CORREL (Correlation). Let's use Correlation for standard -1 to 1 score.
        score = cv2.compareHist(hist_img1, hist_img2, cv2.HISTCMP_CORREL)

        # Simple threshold for "Match"
        is_match = score > 0.6 # Correlation above 0.6 is usually a good match

        return {
            "score": score,
            "is_match": bool(is_match),
            "match_status": "Strong Match" if score > 0.8 else ("Possible Match" if score > 0.5 else "No Match")
        }

    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Missing arguments. Usage: python image_match.py <lost_img> <found_img>"}))
        sys.exit(1)

    lost_path = sys.argv[1]
    found_path = sys.argv[2]

    # Ensure paths are valid (PHP might pass relative or absolute, best to be careful)
    # python script is in /python/, images are in /uploads/... 
    # The PHP shell_exec should pass absolute paths to avoid confusion.

    result = compare_images(lost_path, found_path)
    print(json.dumps(result))
