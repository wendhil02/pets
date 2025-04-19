<?php
require '../internet/connect_ka.php'; // DB connection

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Fetch adoption request details
    $sql = "SELECT ar.id, ar.email AS adopter_email, ar.pet_id, ar.status, ar.schedule_date, ar.pickup_time, ar.created_at
            FROM adoption_requests ar
            WHERE ar.id = '$id' AND ar.status = 'pending'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $request = $result->fetch_assoc();
        $adopterEmail = $request['adopter_email'];
        $petId = $request['pet_id'];
        $status = $request['status'];
        $scheduleDate = $request['schedule_date'];
        $pickupTime = $request['pickup_time'];
        $createdAt = $request['created_at'];

        // Get current owner's email before changing
        $ownerQuery = "SELECT email FROM pet WHERE id = '$petId'";
        $ownerResult = $conn->query($ownerQuery);
        $oldOwnerEmail = '';
        if ($ownerResult && $ownerResult->num_rows > 0) {
            $oldOwnerEmail = $ownerResult->fetch_assoc()['email'];
        }

        // Step 1: Approve request
        $updateRequestSql = "UPDATE adoption_requests SET status = 'own', email_confirmed = 1 WHERE id = '$id'";
        if ($conn->query($updateRequestSql) === TRUE) {

            // Archive request before making any changes
            $archiveSql = "INSERT INTO adoption_requests_archive (id, pet_id, email, contact, status, schedule_date, created_at, pickup_time, archived_at)
                           VALUES ('$id', '$petId', '$adopterEmail', (SELECT contact FROM adoption_requests WHERE id = '$id'), 'approved', '$scheduleDate', '$createdAt', '$pickupTime', NOW())";
            $conn->query($archiveSql);

            // Step 2: Notify current owner (before transferring pet)
            if (!empty($oldOwnerEmail)) {
                $mail1 = new PHPMailer(true);
                try {
                    $mail1->isSMTP();
                    $mail1->Host       = 'smtp.gmail.com';
                    $mail1->SMTPAuth   = true;
                    $mail1->Username   = 'wendhil10@gmail.com';
                    $mail1->Password   = 'ffml onzu stox lcwb';
                    $mail1->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail1->Port       = 587;

                    $mail1->setFrom('your_email@gmail.com', 'Pet Adoption Notice');
                    $mail1->addAddress($oldOwnerEmail);
                    $mail1->isHTML(true);
                    $mail1->Subject = 'Your Pet Has Been Adopted';
                    $mail1->Body    = "
                        <h3>Hello!</h3>
                        <p>Someone has successfully adopted your pet (Pet ID: $petId).</p>
                        <p>If you have any concerns, please contact the admin team.</p>
                        <br><p>Thank you for being a responsible pet caretaker.</p>
                    ";
                    $mail1->send();
                } catch (Exception $e) {
                    // Continue even if this email fails
                }
            }

            // Step 3: Transfer pet to new adopter
            $updatePetSql = "UPDATE pet SET email = '$adopterEmail', status = 'own' WHERE id = '$petId'";
            if ($conn->query($updatePetSql) === TRUE) {

                // Step 4: Notify the new owner
                $mail2 = new PHPMailer(true);
                try {
                    $mail2->isSMTP();
                    $mail2->Host       = 'smtp.gmail.com';
                    $mail2->SMTPAuth   = true;
                    $mail2->Username   = 'wendhil10@gmail.com';
                    $mail2->Password   = 'ffml onzu stox lcwb';
                    $mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail2->Port       = 587;

                    $mail2->setFrom('your_email@gmail.com', 'Pet Adoption Team');
                    $mail2->addAddress($adopterEmail);
                    $mail2->isHTML(true);
                    $mail2->Subject = 'Adoption Approved!';
                    $mail2->Body    = "
                        <h2>Congratulations!</h2>
                        <p>Your adoption request has been approved.</p>
                        <p>You are now the official owner of the adopted pet (Pet ID: $petId).</p>
                        <br><p>Thank you for supporting animal welfare.</p>
                    ";
                    $mail2->send();

                    echo "<script>alert('Adoption approved. Emails sent to previous and new owners.'); window.location.href = 'manage_adoptions.php';</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Adoption approved. Failed to notify new owner: {$mail2->ErrorInfo}'); window.location.href = 'manage_adoptions.php';</script>";
                }

                // Step 5: Delete the request from the main table after archiving
                $deleteSql = "DELETE FROM adoption_requests WHERE id = '$id'";
                if ($conn->query($deleteSql) === TRUE) {
                    // Deletion successful, no further action needed
                } else {
                    echo "<script>alert('Error deleting request: " . $conn->error . "'); window.location.href = 'manage_adoptions.php';</script>";
                }

            } else {
                echo "<script>alert('Error updating pet email.'); window.location.href = 'manage_adoptions.php';</script>";
            }

        } else {
            echo "<script>alert('Error approving request: " . $conn->error . "'); window.location.href = 'manage_adoptions.php';</script>";
        }

    } else {
        echo "<script>alert('Request not found or already approved.'); window.location.href = 'manage_adoptions.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'manage_adoptions.php';</script>";
}

$conn->close();
?>
