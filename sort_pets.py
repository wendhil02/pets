import os
import shutil

# Ilagay ang path ng folder na may mga halo-halong images
source_folder = r"C:\xampp\htdocs\userside\petimage\images"

# Ilagay ang path kung saan ihihiwalay ang mga images per breed
destination_folder = r"C:\xampp\htdocs\userside\newspetimage"

# Siguraduhin na existing ang destination folder
os.makedirs(destination_folder, exist_ok=True)

# I-loop ang lahat ng images sa source_folder
for filename in os.listdir(source_folder):
    if filename.endswith((".jpg", ".jpeg", ".png")):  # Check kung image file
        # Kunin ang breed name mula sa filename (hal. "labrador_001.jpg" → "labrador")
        breed_name = filename.split("_")[0]  

        # Gumawa ng folder per breed kung wala pa
        breed_folder = os.path.join(destination_folder, breed_name)
        os.makedirs(breed_folder, exist_ok=True)

        # Ilipat ang image sa tamang folder
        shutil.move(os.path.join(source_folder, filename), os.path.join(breed_folder, filename))

print("✔ Tapos na! Ang mga images ay hiwalay na per breed.")
