<?php
include('config.php');

$userid = $_GET['userid'] ?? null;
$flightId = $_GET['flightid'] ?? null;

if ($userid && $flightId) {
    $insertQuery = "INSERT INTO pending_flights_users (user_id, flight_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ii", $userid, $flightId);

    if ($stmt->execute()) {
        echo "Seat reserved successfully!";
        header ("Location: /Flight-Booking-Website/Frontend/final_booking.php?userid=" . urlencode($userid));
    } else {
        echo "Error reserving seat: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "User ID or Flight ID not provided.";
}
?>