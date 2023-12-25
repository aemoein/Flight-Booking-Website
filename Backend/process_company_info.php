<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user_id from the form (you may modify this based on your logic)
    $email = $_POST['email'];
    $user_id = getUserIdByEmail($email);

    // Get other form data
    $bio = $_POST["bio"];
    $address = $_POST["address"];
    $location = $_POST["location"];
    $username = $_POST["username"];

    // Upload Logo Image
    $targetDir = "uploads/";
    $logoImgTargetFile = $targetDir . basename($_FILES["logoImg"]["name"]);
    move_uploaded_file($_FILES["logoImg"]["tmp_name"], $logoImgTargetFile);

    // Save file path to the database with the associated user_id
    $logoImgPath = $logoImgTargetFile;

    $sqlInsert = "INSERT INTO company_data (user_id, bio, address, location, username, logo_img_path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("isssss", $user_id, $bio, $address, $location, $username, $logoImgPath);
    
    if ($stmtInsert->execute()) {
        // Insert successful, redirect or provide feedback to the user
        header("Location: company_info_success.php");
        exit();
    } else {
        // Handle the case where the insertion fails
        echo "Error: " . $stmtInsert->error;
    }

    $stmtInsert->close();
}

// Function to get user_id based on email (you may modify this based on your logic)
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