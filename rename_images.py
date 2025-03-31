import os
import tempfile

# Set root directory kung saan nakalagay ang lahat ng pet folders
root_folder = r"C:\xampp\htdocs\userside\newspetimage"

# Listahan ng valid pet breeds na ire-rename lang
valid_breeds = [
    "abyssinian", "american", "basset", "beagle", "bengal", "bird", "birman", "bombay", "boxer", "british",
    "chihuahua", "egyptian", "english", "german", "goldenretriever", "great", "havanese", "japanese",
    "keeshond", "leonberger", "maine", "miniature", "newfoundland", "persian", "pomeranian", "pug",
    "ragdoll", "russian", "saint", "samoyed", "scottish", "shiba", "siamese", "sphynx", "staffordshire",
    "wheaten", "yorkshire"
]

# Loop through each breed name in the list
for breed in valid_breeds:
    folder_path = os.path.join(root_folder, breed)

    # Siguraduhin na existing folder ito bago iproseso
    if os.path.isdir(folder_path):
        prefix = f"{breed}_"

        # Kunin lahat ng image files sa folder
        files = sorted([f for f in os.listdir(folder_path) if f.endswith(('.jpg', '.png', '.jpeg'))])

        # Gamitin ang temp renaming method para maiwasan ang conflict
        temp_files = []
        for index, file in enumerate(files, start=1):
            ext = os.path.splitext(file)[1]  # Kunin ang file extension (.jpg, .png, etc.)
            new_name = f"{prefix}{index}{ext}"  # New filename (e.g., abyssinian_1.jpg)

            old_path = os.path.join(folder_path, file)
            temp_path = os.path.join(folder_path, f"temp_{index}{ext}")  # Temporary filename

            os.rename(old_path, temp_path)  # Rename to temporary name
            temp_files.append((temp_path, os.path.join(folder_path, new_name)))

        # Now, rename from temp files to final names
        for temp_path, final_path in temp_files:
            os.rename(temp_path, final_path)

        print(f"✔️ {breed} folder: Lahat ng files ay na-rename na!")

print("🎉 All specified folders processed successfully!")
