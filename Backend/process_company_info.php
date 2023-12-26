<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
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
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception("Failed to create directory: $targetDir");
            }
        }

        // Check if the file was successfully uploaded
        if (!move_uploaded_file($_FILES["logoImg"]["tmp_name"], $logoImgTargetFile)) {
            throw new Exception("Error uploading Logo Image: " . $_FILES["logoImg"]["error"]);
        }

        // Save file path to the database with the associated user_id
        $logoImgPath = $logoImgTargetFile;

        $sqlInsert = "INSERT INTO company_data (user_id, bio, address, location, username, logo_img_path) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);

        if (!$stmtInsert) {
            throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        $stmtInsert->bind_param("isssss", $user_id, $bio, $address, $location, $username, $logoImgPath);

        if (!$stmtInsert->execute()) {
            throw new Exception("Execute failed: " . $stmtInsert->error);
        }

        // Insert successful, redirect or provide feedback to the user
        echo "Database insert successful<br>";
        header("Location: ../Frontend/signin.html");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        if (isset($stmtInsert)) {
            $stmtInsert->close();
        }
    }
}

// Function to get user_id based on email
function getUserIdByEmail($email) {
    global $conn;
    $sqlSelect = "SELECT id FROM users WHERE email = ?";
    $stmtSelect = $conn->prepare($sqlSelect);

    if (!$stmtSelect) {
        throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmtSelect->bind_param("s", $email);

    if (!$stmtSelect->execute()) {
        throw new Exception("Execute failed: " . $stmtSelect->error);
    }

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