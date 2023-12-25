<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $email = $_POST["email"];
    $name = $_POST["name"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $tel = $_POST["tel"];
    $userType = $_POST["userType"];

    // Check for existing email
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmtCheckEmail = $conn->prepare($checkEmailQuery);
    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $stmtCheckEmail->store_result();

    if ($stmtCheckEmail->num_rows > 0) {
        // Email already exists, handle accordingly (redirect or display error message)
        $stmtCheckEmail->close();
        header("Location: signup.php?error=Email already exists");
        exit();
    }

    $stmtCheckEmail->close();

    // Insert user data into the database
    $insertQuery = "INSERT INTO users (email, password, name, tel, userType) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertUser = $conn->prepare($insertQuery);
    $stmtInsertUser->bind_param("sssss", $email, $password, $name, $tel, $userType);

    // Execute the insert statement
    if ($stmtInsertUser->execute()) {
        // Redirect to a success page or login page
        header("Location: signup_success.php");
        exit();
    } else {
        // Handle the case where the insertion fails
        echo "Error: " . $stmtInsertUser->error;
    }

    $stmtInsertUser->close();
} else {
    header("Location: signup.php");
    exit();
}
?>