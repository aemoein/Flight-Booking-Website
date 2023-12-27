<?php
include('config.php');
$userid = $_POST['userid'];
$flightId = $_POST['flightid'];

$userid2= $userid;
$flightid2 = $flightId;

echo $userid2;
echo $flightid2;

if ($userid && $flightId) {
    // Step 1: Retrieve flight details
    $flightDetailsQuery = "SELECT f.price, f.company_id FROM flights f WHERE f.id = ?";
    $stmt1 = $conn->prepare($flightDetailsQuery);
    $stmt1->bind_param("i", $flightId);
    $stmt1->execute();
    $result = $stmt1->get_result();

    if ($result !== false && $result->num_rows > 0) {
        $flightDetails = $result->fetch_assoc();
        
        // Step 2: Return money to the user's account
        $returnMoneyQuery = "UPDATE users SET account = account + ? WHERE id = ?";
        $stmt2 = $conn->prepare($returnMoneyQuery);
        $stmt2->bind_param("di", $flightDetails['price'], $userid);
        $stmt2->execute();
        $stmt2->close();

        // Step 3: Subtract money from the company's account
        $subtractMoneyQuery = "UPDATE users SET account = account - ? WHERE id = ?";
        $stmt3 = $conn->prepare($subtractMoneyQuery);
        $stmt3->bind_param("di", $flightDetails['price'], $flightDetails['company_id']);
        $stmt3->execute();
        $stmt3->close();


        // Step 4: Remove the entry from the flights_users table
        $removeFlightQuery = "DELETE FROM flights_users WHERE user_id = ? AND flight_id = ?";
        $stmt4 = $conn->prepare($removeFlightQuery);
        $stmt4->bind_param("ii", $userid2, $flightid2);
        
        if ($stmt4->execute()) {
            echo "Flight canceled successfully!";
            header ("Location: /Flight-Booking-Website/Frontend/passenger_flights.php?userid=" . urlencode($userid));
        } else {
            echo "Error canceling flight: " . $stmt4->error;
        }

        $stmt4->close();
    } else {
        echo "Flight not found or invalid flight ID.";
    }
} else {
    echo "User ID or Flight ID not provided.";
}
?>