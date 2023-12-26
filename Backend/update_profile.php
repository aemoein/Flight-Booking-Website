<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['company_id'];
    $compname = $_POST['company-name'];
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    $address = $_POST['address'];
    $location = $_POST['location'];
    $oldPass = $_POST['expass'];
    $newpass = $_POST['newpass'];
    $tel = $_POST['tel'];

    $targetDir = "uploads_company/";

// Check if a file was selected for upload
if (!empty($_FILES["logoImg"]["name"])) {

    // Check if the "uploads_company" directory exists
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            throw new Exception("Failed to create directory: $targetDir");
        }
    }

    // Check for upload errors
    if ($_FILES["logoImg"]["error"] !== UPLOAD_ERR_OK) {
        throw new Exception("Error uploading Logo Image: " . $_FILES["logoImg"]["error"]);
    }

    // Generate a unique file name to avoid overwriting
    $logoImgTargetFile = $targetDir . uniqid() . '_' . basename($_FILES["logoImg"]["name"]);

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES["logoImg"]["tmp_name"], $logoImgTargetFile)) {
        throw new Exception("Error moving uploaded file to $logoImgTargetFile");
    }

    $logoImgPath = $logoImgTargetFile;

    // Update the database with the file path
    if (updateImageData($userId, $logoImgPath)) {
        echo "Image changed successfully";
    } else {
        echo "Error updating database";
    }
} else {
    echo "Image not selected";
}

    $getPasswordQuery = "SELECT password FROM users WHERE id = ?";
    $getPasswordStmt = $conn->prepare($getPasswordQuery);

    if (!$getPasswordStmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $getPasswordStmt->bind_param("i", $userId);
    $getPasswordStmt->execute();

    if ($getPasswordStmt->errno) {
        die("Error executing statement: " . $getPasswordStmt->error);
    }

    $getPasswordStmt->bind_result($dbHashedPassword);
    $getPasswordStmt->fetch();

    $getPasswordStmt->close();

    if (!empty($oldPass) && !empty($newPass)) {
        if (password_verify($oldPass, $dbHashedPassword)) {
            $newPassHashed = password_hash($newPass, PASSWORD_DEFAULT);
    
            $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
    
            if (!$updateStmt) {
                die("Error preparing statement: " . $conn->error);
            }
    
            $updateStmt->bind_param("si", $newPassHashed, $userId);
            $updateStmt->execute();
    
            if ($updateStmt->errno) {
                die("Error executing statement: " . $updateStmt->error);
            }
    
            $updateStmt->close();
    
            echo "Password updated successfully!";
        } else {
            echo "Incorrect old password.";
        }
    } else {
        echo "Both old and new passwords are required.";
    }

    if (updateProfileData($userId, $compname, $bio, $address, $location)) {
        echo "Profile data updated successfully";
        if (updateUserData($userId, $username, $tel))
        {
            echo "User data updated successfully";
            header("Location: /Flight-Booking-Website/Frontend/company_profile.php/" . urlencode($userId));
            exit();
        } else {
            echo "Error updating user data";
        }
    } else {
        echo "Error updating profile data";
    }
} else {
    header("Location: /Flight-Booking-Website/Frontend/index.html");
    exit();
}

function updateProfileData($userId, $compname, $bio, $address, $location) {
    global $conn;

    $sql = "UPDATE company_data SET username=?, bio=?, address=?, location=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $compname, $bio, $address, $location, $userId);
    $result = $stmt->execute();

    $stmt->close();

    return $result;
}

function updateImageData($userId, $logoImgPath) {
    global $conn;

    $sql = "UPDATE company_data SET logo_img_path=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("si", $logoImgPath, $userId);
    $result = $stmt->execute();

    $stmt->close();

    return $result;
}

function updateUserData($userId, $username, $tel) {
    global $conn;

    $sql = "UPDATE users SET name=?, tel=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssi", $username, $tel, $userId); // Corrected $user_id to $userId
    $result = $stmt->execute();

    $stmt->close();

    return $result;
}
?>