<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array(); // Initialize an empty array to hold the response data

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
        // Email already exists, set response accordingly
        $response["success"] = false;
        $response["message"] = "Email already exists";
    } else {
        $stmtCheckEmail->close();

        // Insert user data into the database
        $insertQuery = "INSERT INTO users (email, password, name, tel, userType) VALUES (?, ?, ?, ?, ?)";
        $stmtInsertUser = $conn->prepare($insertQuery);
        $stmtInsertUser->bind_param("sssss", $email, $password, $name, $tel, $userType);

        // Execute the insert statement
        if ($stmtInsertUser->execute()) {
            // Successful insertion
            $response["success"] = true;
            $response["message"] = "User registered successfully";

            // Determine the redirection URL based on userType
            if ($userType == 'passenger') {
                $redirectURL = '../Frontend/passenger_info.html?email=' . urlencode($email);
            } elseif ($userType == 'company') {
                $redirectURL = '../Frontend/company_info.html?email=' . urlencode($email);
            }

            if (isset($redirectURL)) {
                header("Location: $redirectURL");
                exit();
            }
        } else {
            // Insertion failed
            $response["success"] = false;
            $response["message"] = "Error: " . $stmtInsertUser->error;
        }

        $stmtInsertUser->close();
    }

    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);

    // Log the response to a text file
    $logFile = 'response_log.txt';
    $logData = date('Y-m-d H:i:s') . ' - ' . json_encode($response) . PHP_EOL;
    file_put_contents($logFile, $logData, FILE_APPEND);
} else {
    header("Location: signup.html");
    exit();
}
?>