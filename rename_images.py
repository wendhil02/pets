import os

# Root directory ng lahat ng pet breed folders
root_folder = r"C:\xampp\htdocs\revise\newspetimage"

# List of valid breeds
valid_breeds = [
    "abyssinian", "american", "basset", "beagle", "bengal", "bird", "birman", "bombay", "boxer", "british",
    "chihuahua", "egyptian", "english", "german", "goldenretriever", "great", "havanese", "japanese",
    "keeshond", "leonberger", "maine", "miniature", "newfoundland", "persian", "pomeranian", "pug",
    "ragdoll", "russian", "saint", "samoyed", "scottish", "shiba", "siamese", "sphynx", "staffordshire",
    "wheaten", "yorkshire"
]

# Loop bawat breed folder
for breed in valid_breeds:
    folder_path = os.path.join(root_folder, breed)

    if os.path.isdir(folder_path):
        # Kunin lang ang image files
        image_files = [
            f for f in os.listdir(folder_path)
            if f.lower().endswith(('.jpg', '.jpeg', '.png'))
        ]

        # Temp rename para maiwasan ang name conflict
        for i, filename in enumerate(image_files):
            old_path = os.path.join(folder_path, filename)
            temp_path = os.path.join(folder_path, f"tempfile_{i}.tmp")
            os.rename(old_path, temp_path)

        # After temp, rename to final format
        temp_files = sorted([
            f for f in os.listdir(folder_path)
            if f.startswith("tempfile_")
        ])

        for idx, temp_file in enumerate(temp_files, start=1):
            ext = ".jpg"  # default ext
            if temp_file.lower().endswith(".png"):
                ext = ".png"
            elif temp_file.lower().endswith(".jpeg"):
                ext = ".jpeg"

            final_name = f"{breed}_{idx}{ext}"
            os.rename(
                os.path.join(folder_path, temp_file),
                os.path.join(folder_path, final_name)
            )

        print(f"✔️ {breed} folder cleaned and renamed.")

print("🎉 All folders done with clean format: breedname_#.ext")
