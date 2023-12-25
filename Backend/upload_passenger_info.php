<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the form
    $email = $_POST['email'];

    // Retrieve the passenger ID based on the email
    $sqlSelect = "SELECT id FROM users WHERE email = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("s", $email);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $passengerId = $row['id'];

        // Upload Photo
        $targetDir = "uploads/";
        $photoTargetFile = $targetDir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photoTargetFile);

        // Upload Passport Image
        $passportImgTargetFile = $targetDir . basename($_FILES["passportImg"]["name"]);
        move_uploaded_file($_FILES["passportImg"]["tmp_name"], $passportImgTargetFile);

        // Save file paths to the database with the associated passenger ID
        $photoPath = $photoTargetFile;
        $passportImgPath = $passportImgTargetFile;

        $sqlInsert = "INSERT INTO passenger_data (passenger_id, photo_path, passport_img_path) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iss", $passengerId, $photoPath, $passportImgPath);
        $stmtInsert->execute();
        $stmtInsert->close();
    } else {
        // Handle the case where the email is not found
        echo "Email not found!";
    }

    $stmtSelect->close();
}
?>