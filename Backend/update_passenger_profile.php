<?php
include('../Backend/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userId = $_POST["userid"];
    $username = $_POST["username"];
    $tel = $_POST["tel"];
    $expass = $_POST["expass"];
    $newpass = $_POST["newpass"];

    $targetDir = "uploads/";

    if (!empty($_FILES["profileImg"]["name"])) {
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception("Failed to create directory: $targetDir");
            }
        }
    
        if ($_FILES["profileImg"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading profile Image: " . $_FILES["profileImg"]["error"]);
        }
    
        $profileImgTargetFile = $targetDir . uniqid() . '_' . basename($_FILES["profileImg"]["name"]);
    
        if (!move_uploaded_file($_FILES["profileImg"]["tmp_name"], $profileImgTargetFile)) {
            throw new Exception("Error moving uploaded file to $profileImgTargetFile");
        }
    
        $profileImgPath = $profileImgTargetFile;
    
        if (updateImageData($userId, $profileImgPath)) {
            echo "Image changed successfully";
        } else {
            echo "Error updating database";
        }
    } else {
        echo "Image not selected";
    }

    if (!empty($_FILES["passImg"]["name"])) {
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception("Failed to create directory: $targetDir");
            }
        }
    
        if ($_FILES["passImg"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading profile Image: " . $_FILES["passImg"]["error"]);
        }
    
        $passImgTargetFile = $targetDir . uniqid() . '_' . basename($_FILES["passImg"]["name"]);
    
        if (!move_uploaded_file($_FILES["passImg"]["tmp_name"], $passImgTargetFile)) {
            throw new Exception("Error moving uploaded file to $passImgTargetFile");
        }
    
        $passImgPath = $passImgTargetFile;
    
        if (updateImageData($userId, $passImgPath)) {
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

    if (updateUserData($userId, $username, $tel))
    {
        echo "User data updated successfully";
        header("Location: /Flight-Booking-Website/Frontend/passenger_profile.php?userid=" . urlencode($userId));
        exit();
    } else {
        echo "Error updating user data";
    }
} else {
    header("Location: /Flight-Booking-Website/Frontend/index.html");
    exit();
}


    function updateUserData($userId, $username, $tel) {
        global $conn;

        $sql = "UPDATE users SET name=?, tel=? WHERE id=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ssi", $username, $tel, $userId);
        $result = $stmt->execute();

        $stmt->close();

        return $result;
    }

    function updateImageData($userId, $profileImgPath) {
        global $conn;
    
        $sql = "UPDATE passenger_data SET photo_path=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
    
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
    
        $stmt->bind_param("si", $profileImgPath, $userId);
        $result = $stmt->execute();
    
        $stmt->close();
    
        return $result;
    }

    function updatePassportImageData($userId, $passImgPath) {
        global $conn;
    
        $sql = "UPDATE passenger_data SET passport_img_path=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
    
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
    
        $stmt->bind_param("si", $passImgPath, $userId);
        $result = $stmt->execute();
    
        $stmt->close();
    
        return $result;
    }
?>