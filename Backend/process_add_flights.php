<?php
include('config.php'); // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the input data
    $departureCityId = $_POST['departure_city'];
    $destinationCityId = $_POST['destination_city'];
    $departureTime = $_POST['departure_time'];
    $arrivalTime = $_POST['arrival_time'];
    $price = $_POST['price'];
    $companyId = $_POST['company_id']; // You may want to get this value from the user's session or another secure source
    $planeId = $_POST['plane_id'];

    // You may want to add additional validation and error handling here

    // Calculate the initial remaining seats based on the plane's capacity
    $initialRemainingSeats = getPlaneCapacity($planeId);

    // Insert the new flight into the database
    $insertQuery = "INSERT INTO flights (departure_city_id, destination_city_id, departure_time, arrival_time, price, company_id, plane_id, remaining_seats)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);

    if (!$insertStmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $insertStmt->bind_param("iissdiii", $departureCityId, $destinationCityId, $departureTime, $arrivalTime, $price, $companyId, $planeId, $initialRemainingSeats);
    $insertResult = $insertStmt->execute();

    if ($insertResult) {
        echo "Flight added successfully!";
        header("Location: /Flight-Booking-Website/Frontend/add_flights.php?company_id=" . urlencode($companyId));
    } else {
        echo "Error adding flight: " . $insertStmt->error;
    }

    $insertStmt->close();
} else {
    header("Location: add_flights.php");
    exit();
}

// Function to get the plane's capacity based on the plane_id
function getPlaneCapacity($planeId) {
    global $conn;

    $capacityQuery = "SELECT capacity FROM plane WHERE id = ?";
    $capacityStmt = $conn->prepare($capacityQuery);

    if (!$capacityStmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $capacityStmt->bind_param("i", $planeId);
    $capacityStmt->execute();
    $capacityStmt->bind_result($capacity);
    $capacityStmt->fetch();

    $capacityStmt->close();

    return $capacity;
}
?>