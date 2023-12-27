<?php
    include('config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $companyId = $_POST['company_id'];
        $userId = $_POST['userid'];
        $message = $_POST['message'];

        $insertQuery = "INSERT INTO messages (user_id, company_id, message) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        if (!$insertStmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $insertStmt->bind_param("iis", $userId, $companyId, $message);
        $insertResult = $insertStmt->execute();

        if ($insertResult) {
            echo "Message sent successfully!";
            header("Location: /Flight-Booking-Website/Frontend/messages.php?userid=" . urlencode($userid));
        } else {
            echo "Error sending message: " . $insertStmt->error;
        }

        $insertStmt->close();
    } else {
        exit();
    }
?>
