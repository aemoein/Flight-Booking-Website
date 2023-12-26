<?php
// Include the database configuration
include 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the form
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate user input (you can add more validation as needed)
    if (empty($email) || empty($password)) {
        // Handle empty fields
        echo "Please fill in all fields.";
    } else {
        // Perform authentication using the database
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Authentication successful
                $userType = $row['userType'];
                echo "Welcome, $email! Your userType is: $userType";

                // Redirect based on userType
                if ($userType == 'passenger') {
                    header("Location: ../Frontend/passenger_dashboard.php?email=" . urlencode($email));
                    exit();
                } elseif ($userType == 'company') {
                    header("Location: ../Frontend/company_dashboard.php?email=" . urlencode($email));
                    exit();
                } else {
                    // Handle other user types or show an error message
                    echo "Invalid userType.";
                }
            } else {
                // Authentication failed
                echo "Invalid email or password.";
            }
        } else {
            // User not found
            echo "User not found.";
        }

        $stmt->close();
    }
} else {
    // Handle non-POST requests
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>