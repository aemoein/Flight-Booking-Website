<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the form
    $email = $_POST['email'];
    echo "Email: $email<br>";

    // Retrieve the passenger ID based on the email
    $sqlSelect = "SELECT id FROM users WHERE email = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("s", $email);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();

    // Use $result->fetch_assoc() to fetch the result set as an associative array
    $row = $result->fetch_assoc();

    if ($result->num_rows > 0) {
        $passengerId = $row['id'];

        // Upload Photo
        $targetDir = "uploads/";

        // Check if the "uploads" directory exists
        if (!file_exists($targetDir)) {
            // Attempt to create the directory
            if (mkdir($targetDir, 0755, true)) {
                echo "Directory created successfully: $targetDir<br>";
            } else {
                echo "Failed to create directory: $targetDir<br>";
            }
        } else {
            echo "Directory already exists: $targetDir<br>";
        }

        $photoTargetFile = $targetDir . basename($_FILES["photo"]["name"]);
        echo "Target File: $photoTargetFile<br>";

        // Check if the file was successfully uploaded
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photoTargetFile)) {
            echo "File uploaded successfully<br>";
        } else {
            echo "Error uploading file: " . $_FILES["photo"]["error"] . "<br>";
        }

        // Upload Passport Image
        $passportImgTargetFile = $targetDir . basename($_FILES["passportImg"]["name"]);
        echo "Passport Image Target File: $passportImgTargetFile<br>";

        // Check if the file was successfully uploaded
        if (move_uploaded_file($_FILES["passportImg"]["tmp_name"], $passportImgTargetFile)) {
            echo "Passport Image uploaded successfully<br>";
        } else {
            echo "Error uploading Passport Image: " . $_FILES["passportImg"]["error"] . "<br>";
        }

        // Save file paths to the database with the associated passenger ID
        $photoPath = $photoTargetFile;
        $passportImgPath = $passportImgTargetFile;

        $sqlInsert = "INSERT INTO passenger_data (user_id, photo_path, passport_img_path) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iss", $passengerId, $photoPath, $passportImgPath);

        // Check if the database insert was successful
        if ($stmtInsert->execute()) {
            echo "Database insert successful<br>";
            header("Location: ../Frontend/signin.html");
            exit();
        } else {
            echo "Database insert failed: " . $stmtInsert->error . "<br>";
        }

        $stmtInsert->close();
    } else {
        // Handle the case where the email is not found
        echo "Email not found!<br>";
    }

    $stmtSelect->close();
}
?>