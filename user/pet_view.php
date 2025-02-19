<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');

// Check if a deletion has been requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['pet_id'])) {
    $pet_id = intval($_POST['pet_id']);

    // Prepare the statement to safely delete the pet listing
    $stmt = $conn->prepare("DELETE FROM adoption WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $pet_id);
    
    if ($stmt->execute()) {
        $message = "Pet listing deleted successfully";
    } else {
        $error = "Failed to delete pet listing";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./disc/partials/header.php'); ?>
    <!-- Bootstrap CSS and Custom Styles -->
    <style>
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .card-img-top {
            width: 100%;
            height: 200px;      
            object-fit: contain; 
            background: #f8f9fa;
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;  
            align-items: flex-start;      
            text-align: left;
        }
        .card-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 14px;
            margin-bottom: 0.25rem;
        }
        .card-footer {
            background: transparent;
            border-top: none;
        }
    </style>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include('./disc/partials/navbar.php'); ?>
        <?php include('./disc/partials/sidebar.php'); ?>
        <main role="main" class="main-content">
            <?php include('./disc/partials/modal-notif.php'); ?>
            <div class="container-fluid">
                <!-- Display success/error messages if available -->
                <?php if (isset($message)) : ?>
                    <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($message); ?></div>
                <?php elseif (isset($error)) : ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="row">
                    <?php
                    // Fetch data from the "adoption" table
                    $sql = "SELECT id, pet_id, owner, pet_name, pet_age, pet_breed, pet_info, mail, pet_image, created_at FROM adoption WHERE approved = 1 ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                    <!-- Card for each pet -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($row['pet_image']); ?>" class="card-img-top" alt="Pet Image">
                            <div class="card-body">
                                <h5 class="card-title">PET INFORMATION</h5>
                                <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($row['pet_name']); ?></p>
                                <p class="card-text"><strong>Breed:</strong> <?php echo htmlspecialchars($row['pet_breed']); ?></p>
                                <p class="card-text"><strong>Info:</strong> <?php echo htmlspecialchars($row['pet_info']); ?></p>
                                <p class="card-text"><strong>Owner Email:</strong> <?php echo htmlspecialchars($row['mail']); ?></p>
                            </div>
                            <!-- Card Footer with Buttons -->
                            <div class="card-footer d-flex justify-content-around align-items-center">
                                <button 
                                    type="button" 
                                    class="btn btn-primary flex-grow-1 mx-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#adoptModal"
                                    data-pet-id="<?php echo $row['id']; ?>"
                                    data-pet-name="<?php echo htmlspecialchars($row['pet_name']); ?>"
                                    data-pet-breed="<?php echo htmlspecialchars($row['pet_breed']); ?>"
                                    data-pet-info="<?php echo htmlspecialchars($row['pet_info']); ?>"
                                    data-pet-image="<?php echo htmlspecialchars($row['pet_image']); ?>"
                                    data-owner-email="<?php echo htmlspecialchars($row['mail']); ?>">
                                    Adopt
                                </button>
                                <?php 
                                    // Only show the delete button if the logged-in user is the pet owner.
                                    if (isset($_SESSION['mail']) && $_SESSION['mail'] === $row['mail']) : 
                                ?>
                                    <form action="" method="POST" onsubmit="return confirmDelete();" class="flex-grow-1 mx-1">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="pet_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger w-100">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<p>No adoption listings available.</p>";
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </main>
        
        <?php include('./script.php'); ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
       <!-- Adoption Request Modal -->
<div class="modal fade" id="adoptModal" tabindex="-1" aria-labelledby="adoptModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="adoptModalLabel">Adoption Application</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="adoptionForm" action="submit_adoption.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          <div class="row">
            <!-- Right Column: Applicant Information -->
            <div class="col-md-8">
              <!-- Section 1: Personal Information -->
              <h6 class="mb-3">Personal Information</h6>
              <div class="row g-3">
                <!-- First Name -->
                <div class="col-md-6">
                  <label for="firstName" class="form-label">First Name*</label>
                  <input type="text" class="form-control" id="firstName" name="firstName" required>
                  <div class="invalid-feedback">First name is required.</div>
                </div>
                <!-- Last Name -->
                <div class="col-md-6">
                  <label for="lastName" class="form-label">Last Name*</label>
                  <input type="text" class="form-control" id="lastName" name="lastName" required>
                  <div class="invalid-feedback">Last name is required.</div>
                </div>
                <!-- Address -->
                <div class="col-12">
                  <label for="address" class="form-label">Address*</label>
                  <input type="text" class="form-control" id="address" name="address" required>
                  <div class="invalid-feedback">Address is required.</div>
                </div>
                <!-- Phone -->
                <div class="col-md-6">
                  <label for="phone" class="form-label">Phone*</label>
                  <input type="tel" class="form-control" id="phone" name="phone" required>
                  <div class="invalid-feedback">Phone is required.</div>
                </div>
                <!-- Email -->
                <div class="col-md-6">
                  <label for="email" class="form-label">Email*</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                  <div class="invalid-feedback">Email is required.</div>
                </div>
                <!-- Birth Date -->
                <div class="col-md-6">
                  <label for="birthDate" class="form-label">Birth Date*</label>
                  <input type="date" class="form-control" id="birthDate" name="birthDate" required>
                  <div class="invalid-feedback">Birth date is required.</div>
                </div>
                <!-- Occupation -->
                <div class="col-md-6">
                  <label for="occupation" class="form-label">Occupation</label>
                  <input type="text" class="form-control" id="occupation" name="occupation">
                </div>
                <!-- Company/Business Name -->
                <div class="col-12">
                  <label for="companyName" class="form-label">Company/Business Name*</label>
                  <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Please type N/A if unemployed" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Social Media Profile -->
                <div class="col-12">
                  <label for="socialMedia" class="form-label">Social Media Profile</label>
                  <input type="url" class="form-control" id="socialMedia" name="socialMedia" placeholder="Please type N/A if no social media; Enter FB/Twitter/IG Link">
                </div>
                <!-- Status -->
                <div class="col-12">
                  <label class="form-label">Status*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusSingle" value="Single" required>
                    <label class="form-check-label" for="statusSingle">Single</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusMarried" value="Married" required>
                    <label class="form-check-label" for="statusMarried">Married</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusOthers" value="Others" required>
                    <label class="form-check-label" for="statusOthers">Others</label>
                  </div>
                </div>
                <!-- Pronouns -->
                <div class="col-12">
                  <label class="form-label">Pronouns*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="pronouns" id="pronounsShe" value="She/her" required>
                    <label class="form-check-label" for="pronounsShe">She/her</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="pronouns" id="pronounsHe" value="He/him" required>
                    <label class="form-check-label" for="pronounsHe">He/him</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="pronouns" id="pronounsThey" value="They/them" required>
                    <label class="form-check-label" for="pronounsThey">They/them</label>
                  </div>
                </div>
                <!-- What prompted you to adopt from PAWS? -->
                <div class="col-12">
                  <label class="form-label">What prompted you to adopt from PAWS?*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedFriends" value="Friends" required>
                    <label class="form-check-label" for="promptedFriends">Friends</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedWebsite" value="Website" required>
                    <label class="form-check-label" for="promptedWebsite">Website</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedSocialMedia" value="Social Media" required>
                    <label class="form-check-label" for="promptedSocialMedia">Social Media</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedOther" value="Other" required>
                    <label class="form-check-label" for="promptedOther">Other</label>
                  </div>
                </div>
                <!-- Adopted before? -->
                <div class="col-12">
                  <label class="form-label">Have you adopted from PAWS before?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="adoptedBefore" id="adoptedBeforeYes" value="Yes" required>
                    <label class="form-check-label" for="adoptedBeforeYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="adoptedBefore" id="adoptedBeforeNo" value="No" required>
                    <label class="form-check-label" for="adoptedBeforeNo">No</label>
                  </div>
                </div>
              </div>
              <!-- End Section 1 -->

              <!-- Section 2: Alternate Contact -->
              <hr class="my-4">
              <h6 class="mb-3">Alternate Contact*</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="altFirstName" class="form-label">First Name*</label>
                  <input type="text" class="form-control" id="altFirstName" name="altFirstName" required>
                  <div class="invalid-feedback">Alternate contact first name is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="altLastName" class="form-label">Last Name*</label>
                  <input type="text" class="form-control" id="altLastName" name="altLastName" required>
                  <div class="invalid-feedback">Alternate contact last name is required.</div>
                </div>
                <div class="col-12">
                  <small class="text-muted">
                    If the applicant is a minor, a parent or guardian must be the alternate contact and co-sign the application.
                  </small>
                </div>
                <div class="col-12">
                  <label for="relationship" class="form-label">Relationship*</label>
                  <input type="text" class="form-control" id="relationship" name="relationship" required>
                  <div class="invalid-feedback">Relationship is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="altPhone" class="form-label">Phone*</label>
                  <input type="tel" class="form-control" id="altPhone" name="altPhone" required>
                  <div class="invalid-feedback">Alternate contact phone is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="altEmail" class="form-label">Email*</label>
                  <input type="email" class="form-control" id="altEmail" name="altEmail" required>
                  <div class="invalid-feedback">Alternate contact email is required.</div>
                </div>
              </div>
              <!-- End Section 2 -->

              <!-- Section 3: Questionnaire -->
              <hr class="my-4">
              <h6 class="mb-3">Questionnaire</h6>
              <div class="row g-3">
                <!-- What are you looking to adopt? -->
                <div class="col-12">
                  <label class="form-label">What are you looking to adopt?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptCat" value="Cat" required>
                    <label class="form-check-label" for="adoptCat">Cat</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptDog" value="Dog" required>
                    <label class="form-check-label" for="adoptDog">Dog</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptBoth" value="Both" required>
                    <label class="form-check-label" for="adoptBoth">Both</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptNotDecided" value="Not Decided" required>
                    <label class="form-check-label" for="adoptNotDecided">Not decided</label>
                  </div>
                </div>
                <!-- Specific animal adoption? -->
                <div class="col-12">
                  <label class="form-label">Are you applying to adopt a specific shelter animal?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="specificAnimal" id="specificAnimalYes" value="Yes" required>
                    <label class="form-check-label" for="specificAnimalYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="specificAnimal" id="specificAnimalNo" value="No" required>
                    <label class="form-check-label" for="specificAnimalNo">No</label>
                  </div>
                </div>
                <!-- Ideal pet description -->
                <div class="col-12">
                  <label for="idealPetDescription" class="form-label">Describe your ideal pet, including its sex, age, appearance, temperament, etc.*</label>
                  <textarea class="form-control" id="idealPetDescription" name="idealPetDescription" rows="3" required></textarea>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Building type -->
                <div class="col-12">
                  <label class="form-label">What type of building do you live in?*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingHouse" value="House" required>
                    <label class="form-check-label" for="buildingHouse">House</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingApartment" value="Apartment" required>
                    <label class="form-check-label" for="buildingApartment">Apartment</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingCondo" value="Condo" required>
                    <label class="form-check-label" for="buildingCondo">Condo</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingOther" value="Other" required>
                    <label class="form-check-label" for="buildingOther">Other</label>
                  </div>
                </div>
                <!-- Rent -->
                <div class="col-12">
                  <label class="form-label">Do you rent?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rent" id="rentYes" value="Yes" required>
                    <label class="form-check-label" for="rentYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rent" id="rentNo" value="No" required>
                    <label class="form-check-label" for="rentNo">No</label>
                  </div>
                </div>
                <!-- Pet when move -->
                <div class="col-12">
                  <label for="petWhenMove" class="form-label">What happens to your pet if or when you move?*</label>
                  <input type="text" class="form-control" id="petWhenMove" name="petWhenMove" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Who do you live with -->
                <div class="col-12">
                  <label class="form-label">Who do you live with?*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveAlone" value="Living alone">
                    <label class="form-check-label" for="liveAlone">Living alone</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveSpouse" value="Spouse">
                    <label class="form-check-label" for="liveSpouse">Spouse</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveParents" value="Parents">
                    <label class="form-check-label" for="liveParents">Parents</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveChildrenOver" value="Children over 18">
                    <label class="form-check-label" for="liveChildrenOver">Children over 18</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveChildrenBelow" value="Children below 18">
                    <label class="form-check-label" for="liveChildrenBelow">Children below 18</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveRelatives" value="Relatives">
                    <label class="form-check-label" for="liveRelatives">Relatives</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveRoommate" value="Roommate(s)">
                    <label class="form-check-label" for="liveRoommate">Roommate(s)</label>
                  </div>
                </div>
                <!-- Allergies -->
                <div class="col-12">
                  <label class="form-label">Are any members of your household allergic to animals?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="allergic" id="allergicYes" value="Yes" required>
                    <label class="form-check-label" for="allergicYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="allergic" id="allergicNo" value="No" required>
                    <label class="form-check-label" for="allergicNo">No</label>
                  </div>
                </div>
                <!-- Care responsibility -->
                <div class="col-12">
                  <label for="careResponsibility" class="form-label">Who will be responsible for feeding, grooming, and generally caring for your pet?*</label>
                  <input type="text" class="form-control" id="careResponsibility" name="careResponsibility" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Financial responsibility -->
                <div class="col-12">
                  <label for="financialResponsibility" class="form-label">Who will be financially responsible for your pet’s needs (i.e. food, vet bills, etc.)?*</label>
                  <input type="text" class="form-control" id="financialResponsibility" name="financialResponsibility" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Vacation care -->
                <div class="col-12">
                  <label for="vacationCare" class="form-label">Who will look after your pet if you go on vacation or in case of emergency?*</label>
                  <input type="text" class="form-control" id="vacationCare" name="vacationCare" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Hours alone -->
                <div class="col-12">
                  <label for="hoursAlone" class="form-label">How many hours in an average workday will your pet be left alone?*</label>
                  <input type="number" class="form-control" id="hoursAlone" name="hoursAlone" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Introduction steps -->
                <div class="col-12">
                  <label for="introductionSteps" class="form-label">What steps will you take to introduce your new pet to his/her new surroundings?*</label>
                  <textarea class="form-control" id="introductionSteps" name="introductionSteps" rows="3" required></textarea>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Family support -->
                <div class="col-12">
                  <label class="form-label">Does everyone in the family support your decision to adopt a pet?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="familySupport" id="familySupportYes" value="Yes" required>
                    <label class="form-check-label" for="familySupportYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="familySupport" id="familySupportNo" value="No" required>
                    <label class="form-check-label" for="familySupportNo">No</label>
                  </div>
                </div>
                <!-- Support explanation -->
                <div class="col-12">
                  <label for="supportExplanation" class="form-label">Please explain*</label>
                  <textarea class="form-control" id="supportExplanation" name="supportExplanation" rows="3" required></textarea>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Other pets -->
                <div class="col-12">
                  <label class="form-label">Do you have other pets?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="otherPets" id="otherPetsYes" value="Yes" required>
                    <label class="form-check-label" for="otherPetsYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="otherPets" id="otherPetsNo" value="No" required>
                    <label class="form-check-label" for="otherPetsNo">No</label>
                  </div>
                </div>
                <!-- Past pets -->
                <div class="col-12">
                  <label class="form-label">Have you had pets in the past?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="pastPets" id="pastPetsYes" value="Yes" required>
                    <label class="form-check-label" for="pastPetsYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="pastPets" id="pastPetsNo" value="No" required>
                    <label class="form-check-label" for="pastPetsNo">No</label>
                  </div>
                </div>
              </div>
              <!-- End Section 3 -->

              <!-- Section 4: Attachments & Interview -->
              <hr class="my-4">
              <h6 class="mb-3">Attachments & Interview</h6>
              <div class="row g-3">
                <!-- Home photos -->
                <div class="col-12">
                  <label for="homePhotos" class="form-label">Please attach photos of your home. This has replaced our on-site ocular inspections.*</label>
                  <input type="file" class="form-control" id="homePhotos" name="homePhotos[]" multiple required>
                  <div class="form-text">Max. file size: 8 MB per file.</div>
                </div>
                <!-- Valid ID -->
                <div class="col-12">
                  <label for="validID" class="form-label">Upload a valid ID*</label>
                  <input type="file" class="form-control" id="validID" name="validID" required>
                  <div class="form-text">Max. file size: 8 MB.</div>
                </div>
                <!-- Interview & Visitation -->
                <div class="col-12">
                  <h6>Interview & Visitation</h6>
                </div>
                <!-- Zoom interview date -->
                <div class="col-md-6">
                  <label for="zoomDate" class="form-label">Preferred date for Zoom interview*</label>
                  <input type="date" class="form-control" id="zoomDate" name="zoomDate" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Zoom interview time -->
                <div class="col-md-6">
                  <label for="zoomTime" class="form-label">Preferred time for Zoom interview*</label>
                  <input type="time" class="form-control" id="zoomTime" name="zoomTime" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <!-- Shelter visit -->
                <div class="col-12">
                  <label class="form-label">Will you be able to visit the shelter for the meet-and-greet?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="shelterVisit" id="shelterVisitYes" value="Yes" required>
                    <label class="form-check-label" for="shelterVisitYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="shelterVisit" id="shelterVisitNo" value="No" required>
                    <label class="form-check-label" for="shelterVisitNo">No</label>
                  </div>
                </div>
              </div>
              <!-- End Section 4 -->
            </div>
            <!-- End Right Column -->
          </div>
          <!-- End Row -->

          <div class="modal-footer mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit Application</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Adoption Request Modal -->
<div class="modal fade" id="adoptModal" tabindex="-1" aria-labelledby="adoptModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content shadow-sm rounded">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="adoptModalLabel">Adoption Application</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="background: #f9f9f9;">
        <form id="adoptionForm" action="submit_adoption.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          <div class="row">
            <!-- Left Column: Pet Information -->
            <div class="col-md-4 border-end">
              <div class="text-center mb-3">
                <img id="modal-pet-image" src="" alt="Pet Image" class="img-fluid rounded" style="max-height: 200px;">
              </div>
              <h6 class="text-center" id="modal-pet-name"></h6>
              <p class="text-center"><strong>Breed:</strong> <span id="modal-pet-breed"></span></p>
              <p id="modal-pet-info"></p>
              <p><strong>Owner Email:</strong> <span id="modal-owner-email"></span></p>
              <!-- Hidden fields for pet info -->
              <input type="hidden" id="modal-pet-id" name="pet_id">
              <input type="hidden" id="modal-owner-email-hidden" name="owner_email">
            </div>

            <!-- Right Column: Applicant Information -->
            <div class="col-md-8">
              <!-- Section 1: Personal Information -->
              <div class="section-title">Personal Information</div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="firstName" class="form-label">First Name*</label>
                  <input type="text" class="form-control" id="firstName" name="firstName" required>
                  <div class="invalid-feedback">First name is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="lastName" class="form-label">Last Name*</label>
                  <input type="text" class="form-control" id="lastName" name="lastName" required>
                  <div class="invalid-feedback">Last name is required.</div>
                </div>
                <div class="col-12">
                  <label for="address" class="form-label">Address*</label>
                  <input type="text" class="form-control" id="address" name="address" required>
                  <div class="invalid-feedback">Address is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Phone*</label>
                  <input type="tel" class="form-control" id="phone" name="phone" required>
                  <div class="invalid-feedback">Phone is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Email*</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                  <div class="invalid-feedback">Email is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="birthDate" class="form-label">Birth Date*</label>
                  <input type="date" class="form-control" id="birthDate" name="birthDate" required>
                  <div class="invalid-feedback">Birth date is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="occupation" class="form-label">Occupation</label>
                  <input type="text" class="form-control" id="occupation" name="occupation">
                </div>
                <div class="col-12">
                  <label for="companyName" class="form-label">Company/Business Name*</label>
                  <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Please type N/A if unemployed" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label for="socialMedia" class="form-label">Social Media Profile</label>
                  <input type="url" class="form-control" id="socialMedia" name="socialMedia" placeholder="Please type N/A if no social media; Enter FB/Twitter/IG Link">
                </div>
                <div class="col-12">
                  <label class="form-label">Status*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusSingle" value="Single" required>
                    <label class="form-check-label" for="statusSingle">Single</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusMarried" value="Married" required>
                    <label class="form-check-label" for="statusMarried">Married</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusOthers" value="Others" required>
                    <label class="form-check-label" for="statusOthers">Others</label>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Pronouns*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="pronouns" id="pronounsShe" value="She/her" required>
                    <label class="form-check-label" for="pronounsShe">She/her</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="pronouns" id="pronounsHe" value="He/him" required>
                    <label class="form-check-label" for="pronounsHe">He/him</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="pronouns" id="pronounsThey" value="They/them" required>
                    <label class="form-check-label" for="pronounsThey">They/them</label>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">What prompted you to adopt from PAWS?*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedFriends" value="Friends" required>
                    <label class="form-check-label" for="promptedFriends">Friends</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedWebsite" value="Website" required>
                    <label class="form-check-label" for="promptedWebsite">Website</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedSocialMedia" value="Social Media" required>
                    <label class="form-check-label" for="promptedSocialMedia">Social Media</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="prompted" id="promptedOther" value="Other" required>
                    <label class="form-check-label" for="promptedOther">Other</label>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Have you adopted from PAWS before?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="adoptedBefore" id="adoptedBeforeYes" value="Yes" required>
                    <label class="form-check-label" for="adoptedBeforeYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="adoptedBefore" id="adoptedBeforeNo" value="No" required>
                    <label class="form-check-label" for="adoptedBeforeNo">No</label>
                  </div>
                </div>
              </div>

              <!-- Section 2: Alternate Contact -->
              <hr class="my-4">
              <div class="section-title">Alternate Contact*</div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="altFirstName" class="form-label">First Name*</label>
                  <input type="text" class="form-control" id="altFirstName" name="altFirstName" required>
                  <div class="invalid-feedback">Alternate contact first name is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="altLastName" class="form-label">Last Name*</label>
                  <input type="text" class="form-control" id="altLastName" name="altLastName" required>
                  <div class="invalid-feedback">Alternate contact last name is required.</div>
                </div>
                <div class="col-12">
                  <small class="text-muted">If the applicant is a minor, a parent or guardian must be the alternate contact and co-sign the application.</small>
                </div>
                <div class="col-12">
                  <label for="relationship" class="form-label">Relationship*</label>
                  <input type="text" class="form-control" id="relationship" name="relationship" required>
                  <div class="invalid-feedback">Relationship is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="altPhone" class="form-label">Phone*</label>
                  <input type="tel" class="form-control" id="altPhone" name="altPhone" required>
                  <div class="invalid-feedback">Alternate contact phone is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="altEmail" class="form-label">Email*</label>
                  <input type="email" class="form-control" id="altEmail" name="altEmail" required>
                  <div class="invalid-feedback">Alternate contact email is required.</div>
                </div>
              </div>

              <!-- Section 3: Questionnaire -->
              <hr class="my-4">
              <div class="section-title">Questionnaire</div>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">What are you looking to adopt?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptCat" value="Cat" required>
                    <label class="form-check-label" for="adoptCat">Cat</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptDog" value="Dog" required>
                    <label class="form-check-label" for="adoptDog">Dog</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptBoth" value="Both" required>
                    <label class="form-check-label" for="adoptBoth">Both</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lookingToAdopt" id="adoptNotDecided" value="Not Decided" required>
                    <label class="form-check-label" for="adoptNotDecided">Not decided</label>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Are you applying to adopt a specific shelter animal?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="specificAnimal" id="specificAnimalYes" value="Yes" required>
                    <label class="form-check-label" for="specificAnimalYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="specificAnimal" id="specificAnimalNo" value="No" required>
                    <label class="form-check-label" for="specificAnimalNo">No</label>
                  </div>
                </div>
                <div class="col-12">
                  <label for="idealPetDescription" class="form-label">Describe your ideal pet, including its sex, age, appearance, temperament, etc.*</label>
                  <textarea class="form-control" id="idealPetDescription" name="idealPetDescription" rows="3" required></textarea>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label class="form-label">What type of building do you live in?*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingHouse" value="House" required>
                    <label class="form-check-label" for="buildingHouse">House</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingApartment" value="Apartment" required>
                    <label class="form-check-label" for="buildingApartment">Apartment</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingCondo" value="Condo" required>
                    <label class="form-check-label" for="buildingCondo">Condo</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="buildingType" id="buildingOther" value="Other" required>
                    <label class="form-check-label" for="buildingOther">Other</label>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Do you rent?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rent" id="rentYes" value="Yes" required>
                    <label class="form-check-label" for="rentYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rent" id="rentNo" value="No" required>
                    <label class="form-check-label" for="rentNo">No</label>
                  </div>
                </div>
                <div class="col-12">
                  <label for="petWhenMove" class="form-label">What happens to your pet if or when you move?*</label>
                  <input type="text" class="form-control" id="petWhenMove" name="petWhenMove" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label class="form-label">Who do you live with?*</label>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveAlone" value="Living alone">
                    <label class="form-check-label" for="liveAlone">Living alone</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveSpouse" value="Spouse">
                    <label class="form-check-label" for="liveSpouse">Spouse</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveParents" value="Parents">
                    <label class="form-check-label" for="liveParents">Parents</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveChildrenOver" value="Children over 18">
                    <label class="form-check-label" for="liveChildrenOver">Children over 18</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveChildrenBelow" value="Children below 18">
                    <label class="form-check-label" for="liveChildrenBelow">Children below 18</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveRelatives" value="Relatives">
                    <label class="form-check-label" for="liveRelatives">Relatives</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="liveWith[]" id="liveRoommate" value="Roommate(s)">
                    <label class="form-check-label" for="liveRoommate">Roommate(s)</label>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Are any members of your household allergic to animals?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="allergic" id="allergicYes" value="Yes" required>
                    <label class="form-check-label" for="allergicYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="allergic" id="allergicNo" value="No" required>
                    <label class="form-check-label" for="allergicNo">No</label>
                  </div>
                </div>
                <div class="col-12">
                  <label for="careResponsibility" class="form-label">Who will be responsible for feeding, grooming, and generally caring for your pet?*</label>
                  <input type="text" class="form-control" id="careResponsibility" name="careResponsibility" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label for="financialResponsibility" class="form-label">Who will be financially responsible for your pet’s needs (i.e. food, vet bills, etc.)?*</label>
                  <input type="text" class="form-control" id="financialResponsibility" name="financialResponsibility" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label for="vacationCare" class="form-label">Who will look after your pet if you go on vacation or in case of emergency?*</label>
                  <input type="text" class="form-control" id="vacationCare" name="vacationCare" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label for="hoursAlone" class="form-label">How many hours in an average workday will your pet be left alone?*</label>
                  <input type="number" class="form-control" id="hoursAlone" name="hoursAlone" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label for="introductionSteps" class="form-label">What steps will you take to introduce your new pet to his/her new surroundings?*</label>
                  <textarea class="form-control" id="introductionSteps" name="introductionSteps" rows="3" required></textarea>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label class="form-label">Does everyone in the family support your decision to adopt a pet?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="familySupport" id="familySupportYes" value="Yes" required>
                    <label class="form-check-label" for="familySupportYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="familySupport" id="familySupportNo" value="No" required>
                    <label class="form-check-label" for="familySupportNo">No</label>
                  </div>
                </div>
                <div class="col-12">
                  <label for="supportExplanation" class="form-label">Please explain*</label>
                  <textarea class="form-control" id="supportExplanation" name="supportExplanation" rows="3" required></textarea>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label class="form-label">Do you have other pets?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="otherPets" id="otherPetsYes" value="Yes" required>
                    <label class="form-check-label" for="otherPetsYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="otherPets" id="otherPetsNo" value="No" required>
                    <label class="form-check-label" for="otherPetsNo">No</label>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Have you had pets in the past?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="pastPets" id="pastPetsYes" value="Yes" required>
                    <label class="form-check-label" for="pastPetsYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="pastPets" id="pastPetsNo" value="No" required>
                    <label class="form-check-label" for="pastPetsNo">No</label>
                  </div>
                </div>
              </div>

              <!-- Section 4: Attachments & Interview -->
              <hr class="my-4">
              <div class="section-title">Attachments & Interview</div>
              <div class="row g-3">
                <div class="col-12">
                  <label for="homePhotos" class="form-label">Please attach photos of your home. This has replaced our on-site ocular inspections.*</label>
                  <input type="file" class="form-control" id="homePhotos" name="homePhotos[]" multiple required>
                  <div class="form-text">Max. file size: 8 MB per file.</div>
                </div>
                <div class="col-12">
                  <label for="validID" class="form-label">Upload a valid ID*</label>
                  <input type="file" class="form-control" id="validID" name="validID" required>
                  <div class="form-text">Max. file size: 8 MB.</div>
                </div>
                <div class="col-12">
                  <h6>Interview & Visitation</h6>
                </div>
                <div class="col-md-6">
                  <label for="zoomDate" class="form-label">Preferred date for Zoom interview*</label>
                  <input type="date" class="form-control" id="zoomDate" name="zoomDate" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-md-6">
                  <label for="zoomTime" class="form-label">Preferred time for Zoom interview*</label>
                  <input type="time" class="form-control" id="zoomTime" name="zoomTime" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
                <div class="col-12">
                  <label class="form-label">Will you be able to visit the shelter for the meet-and-greet?*</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="shelterVisit" id="shelterVisitYes" value="Yes" required>
                    <label class="form-check-label" for="shelterVisitYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="shelterVisit" id="shelterVisitNo" value="No" required>
                    <label class="form-check-label" for="shelterVisitNo">No</label>
                  </div>
                </div>
              </div>
            </div> <!-- End Right Column -->
          </div> <!-- End Row -->

          <div class="modal-footer mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit Application</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Custom Styles -->
<style>
  .section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 0.3rem;
    margin-bottom: 1rem;
    margin-top: 1rem;
  }
</style>

<!-- JavaScript to Populate Modal Fields & Validate Form -->
<script>
  // Populate pet information when modal is shown
  document.getElementById('adoptModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var petId      = button.getAttribute('data-pet-id');
    var petName    = button.getAttribute('data-pet-name');
    var petBreed   = button.getAttribute('data-pet-breed');
    var petInfo    = button.getAttribute('data-pet-info');
    var petImage   = button.getAttribute('data-pet-image');
    var ownerEmail = button.getAttribute('data-owner-email');

    document.getElementById('modal-pet-id').value = petId;
    document.getElementById('modal-pet-name').textContent = petName;
    document.getElementById('modal-pet-breed').textContent = petBreed;
    document.getElementById('modal-pet-info').textContent = petInfo;
    document.getElementById('modal-pet-image').src = petImage;
    document.getElementById('modal-owner-email').textContent = ownerEmail;
    document.getElementById('modal-owner-email-hidden').value = ownerEmail;
  });

  // Bootstrap form validation
  (function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
      .forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
  })();
</script>

        
        <!-- JavaScript to Populate Modal Fields and Confirm Deletion -->
        <script>
            document.getElementById('adoptModal').addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                // Extract data attributes from the clicked button
                var petId      = button.getAttribute('data-pet-id');
                var petName    = button.getAttribute('data-pet-name');
                var petBreed   = button.getAttribute('data-pet-breed');
                var petInfo    = button.getAttribute('data-pet-info');
                var petImage   = button.getAttribute('data-pet-image');
                var ownerEmail = button.getAttribute('data-owner-email');

                // Populate modal fields
                document.getElementById('modal-pet-id').value = petId;
                document.getElementById('modal-pet-name').textContent = petName;
                document.getElementById('modal-pet-breed').textContent = petBreed;
                document.getElementById('modal-pet-info').textContent = petInfo;
                document.getElementById('modal-pet-image').src = petImage;
                document.getElementById('modal-owner-email').textContent = ownerEmail;
                document.getElementById('modal-owner-email-hidden').value = ownerEmail;
            });
            
            function confirmDelete() {
                return confirm("Are you sure you want to delete this adoption listing?");
            }
            
            // Fade out alert messages after 3 seconds
            $(document).ready(function(){
                setTimeout(function(){
                    $('.alert').fadeOut('fast');
                }, 3000);
            });
        </script>
    </div>
</body>
</html>
