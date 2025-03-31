import os

# Gumamit ng tamang file path format
directory_path = r"C:\xampp\htdocs\userside\newspetimage"

# Kunin lang ang folder names (hindi kasama ang files)
folders = [f for f in os.listdir(directory_path) if os.path.isdir(os.path.join(directory_path, f))]

# I-print ang mga folder names
print("📂 List of Folders:")
for folder in folders:
    print(folder)

# Kung gusto mong i-save ang listahan sa isang file
with open("folder_list.txt", "w") as file:
    for folder in folders:
        file.write(folder + "\n")

print("✅ Folder names saved to folder_list.txt!")
