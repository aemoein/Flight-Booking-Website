<?php
include('config.php');

$flightId = $_GET['flightid'] ?? null;
$userid = $_GET['userid'] ?? null;

if ($flightId) {
    // Retrieve the remaining seats
    $remainingSeatsQuery = "SELECT remaining_seats FROM flights WHERE id = ?";
    $stmtSeats = $conn->prepare($remainingSeatsQuery);
    $stmtSeats->bind_param("i", $flightId);
    $stmtSeats->execute();
    $stmtSeats->bind_result($remainingSeats);
    $stmtSeats->fetch();
    $stmtSeats->close();

    // Check if there are available seats
    if ($remainingSeats > 0) {
        // Update the remaining seats
        $updateSeatsQuery = "UPDATE flights SET remaining_seats = remaining_seats - 1 WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateSeatsQuery);
        $stmtUpdate->bind_param("i", $flightId);

        if ($stmtUpdate->execute()) {
            echo "Seat taken successfully! Remaining seats: " . ($remainingSeats - 1);
            header ("Location: /Flight-Booking-Website/Frontend/passenger_flights.php?userid=" . urlencode($userid));
        } else {
            echo "Error taking seat: " . $stmtUpdate->error;
        }

        $stmtUpdate->close();
    } else {
        echo "No available seats for this flight.";
    }
} else {
    echo "Flight ID not provided.";
}
?>