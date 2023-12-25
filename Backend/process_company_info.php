<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user_id from the form based on the email
    $email = $_POST['email'];
    echo "Email: $email<br>";
    $user_id = getUserIdByEmail($email);

    // Get other form data
    $bio = $_POST["bio"];
    $address = $_POST["address"];
    $location = $_POST["location"];
    $username = $_POST["username"];

    // Upload Logo Image
    $targetDir = "uploads_company/";
    $logoImgTargetFile = $targetDir . basename($_FILES["logoImg"]["name"]);

    // Check if the "uploads_company" directory exists
    if (!file_exists($targetDir)) {
        // Attempt to create the directory
        if (mkdir($targetDir, 0755, true)) {
            echo "Directory created successfully: $targetDir<br>";
        } else {
            echo "Failed to create directory: $targetDir<br>";
            exit();  // Stop execution if directory creation fails
        }
    } else {
        echo "Directory already exists: $targetDir<br>";
    }

    // Check if the file was successfully uploaded
    if (move_uploaded_file($_FILES["logoImg"]["tmp_name"], $logoImgTargetFile)) {
        echo "Logo Image uploaded successfully<br>";
    } else {
        echo "Error uploading Logo Image: " . $_FILES["logoImg"]["error"] . "<br>";
        exit();  // Stop execution if file upload fails
    }

    // Save file path to the database with the associated user_id
    $logoImgPath = $logoImgTargetFile;

    $sqlInsert = "INSERT INTO company_data (user_id, bio, address, location, username, logo_img_path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("isssss", $user_id, $bio, $address, $location, $username, $logoImgPath);

    if ($stmtInsert->execute()) {
        // Insert successful, redirect or provide feedback to the user
        echo "Database insert successful<br>";
        header("Location: ../Frontend/signin.html");
        exit();
    } else {
        // Handle the case where the insertion fails
        echo "Error: " . $stmtInsert->error;
    }

    $stmtInsert->close();
}

// Function to get user_id based on email
function getUserIdByEmail($email) {
    global $conn;
    $sqlSelect = "SELECT id FROM users WHERE email = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("s", $email);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    $stmtSelect->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {
        // Handle the case where the email is not found
        echo "Email not found!";
        exit();
    }
}
?>