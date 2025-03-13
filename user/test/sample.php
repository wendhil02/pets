<!DOCTYPE html>
<html>
<head>
    <title>Add Pet</title>
    <script>
    function toggleVaccineForm() {
        var status = document.getElementById("vaccine_status").value;
        document.getElementById("vaccine_section").style.display = (status === "Vaccinated") ? "block" : "none";
    }

    function addVaccine() {
        let div = document.createElement("div");
        div.classList.add("vaccine_entry");
        div.innerHTML = `
            <label>Vaccine Type:</label> <input type="text" name="vaccine_type[]"><br>
            <label>Vaccine Name:</label> <input type="text" name="vaccine_name[]"><br>
            <label>Vaccine Date:</label> <input type="date" name="vaccine_date[]"><br>
            <label>Administered By:</label> <input type="text" name="administered_by[]"><br>
            <button type="button" onclick="removeVaccine(this)">Remove</button>
        `;
        document.getElementById("vaccine_entries").appendChild(div);
    }

    function removeVaccine(button) {
        button.parentElement.remove();
    }
    </script>
</head>
<body>
    <h2>Add Pet</h2>
    <form action="save_pet.php" method="post" enctype="multipart/form-data">
        Name: <input type="text" name="name" required><br>
        Phone: <input type="text" name="phone" required><br>
        Email: <input type="email" name="email" required><br>
        Address: <input type="text" name="address" required><br>
        Pet Name: <input type="text" name="pet_name" required><br>
        Pet Age: <input type="text" name="pet_age" required><br>
        Pet Type: <input type="text" name="pet_type" required><br>
        Pet Breed: <input type="text" name="pet_breed" required><br>
        Pet Info: <textarea name="pet_info" required></textarea><br>
        Pet Image: <input type="file" name="pet_image" required><br>

        <label>Vaccine Status:</label>
        <select name="vaccine_status" id="vaccine_status" onchange="toggleVaccineForm()">
            <option value="Not Vaccinated">Not Vaccinated</option>
            <option value="Vaccinated">Vaccinated</option>
        </select>

        <div id="vaccine_section" style="display:none;">
            <div id="vaccine_entries">
                <div class="vaccine_entry">
                    <label>Vaccine Type:</label> <input type="text" name="vaccine_type[]"><br>
                    <label>Vaccine Name:</label> <input type="text" name="vaccine_name[]"><br>
                    <label>Vaccine Date:</label> <input type="date" name="vaccine_date[]"><br>
                    <label>Administered By:</label> <input type="text" name="administered_by[]"><br>
                    <button type="button" onclick="removeVaccine(this)">Remove</button>
                </div>
            </div>
            <button type="button" onclick="addVaccine()">Add Another Vaccine</button>
        </div>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
