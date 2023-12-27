<?php
include('config.php');

$userid = $_GET['userid'] ?? null;
$flightId = $_GET['flightid'] ?? null;

if ($userid && $flightId) {
    // Check the user's bank balance
    $balanceQuery = "SELECT account FROM users WHERE id = ?";
    $stmtBalance = $conn->prepare($balanceQuery);
    $stmtBalance->bind_param("i", $userid);
    $stmtBalance->execute();
    $stmtBalance->bind_result($userBalance);
    $stmtBalance->fetch();
    $stmtBalance->close();

    // Retrieve the price of the flight and company ID
    $flightInfoQuery = "SELECT f.price, f.company_id FROM flights f WHERE f.id = ?";
    $stmtInfo = $conn->prepare($flightInfoQuery);
    $stmtInfo->bind_param("i", $flightId);
    $stmtInfo->execute();
    $stmtInfo->bind_result($flightPrice, $companyId);
    $stmtInfo->fetch();
    $stmtInfo->close();

    // Check if the user has sufficient funds
    if ($userBalance >= $flightPrice) {
        // Deduct money from the user's account
        $deductQuery = "UPDATE users SET account = account - ? WHERE id = ?";
        $stmtDeduct = $conn->prepare($deductQuery);
        $stmtDeduct->bind_param("di", $flightPrice, $userid);

        // Add money to the company's account
        $addQuery = "UPDATE users SET account = account + ? WHERE id = ?";
        $stmtAdd = $conn->prepare($addQuery);
        $stmtAdd->bind_param("di", $flightPrice, $companyId);

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Execute deduction query
            $stmtDeduct->execute();

            // Execute addition query
            $stmtAdd->execute();

            // Commit the transaction
            $conn->commit();

            // Proceed to delete the record from pending_flights_users
            $deleteQuery = "DELETE FROM pending_flights_users WHERE user_id = ? AND flight_id = ?";
            $stmtDelete = $conn->prepare($deleteQuery);
            $stmtDelete->bind_param("ii", $userid, $flightId);

            if ($stmtDelete->execute()) {
                echo "Transaction successful! Record deleted.";

                // Insert into flights_users table
                $insertFlightsUsersQuery = "INSERT INTO flights_users (user_id, flight_id) VALUES (?, ?)";
                $stmtInsert = $conn->prepare($insertFlightsUsersQuery);
                $stmtInsert->bind_param("ii", $userid, $flightId);

                if ($stmtInsert->execute()) {
                    echo "Record added to flights_users table.";
                    header("Location: /Flight-Booking-Website/Backend/take_seat.php?userid=" . urlencode($userid) . "&flightid=" . urlencode($flightId));
                } else {
                    echo "Error adding record to flights_users table: " . $stmtInsert->error;
                }

                $stmtInsert->close();
            } else {
                echo "Error deleting record: " . $stmtDelete->error;
            }

            $stmtDelete->close();
        } catch (Exception $e) {
            // An error occurred, rollback the transaction
            $conn->rollback();
            echo "Transaction failed: " . $e->getMessage();
        }

        $stmtDeduct->close();
        $stmtAdd->close();
    } else {
        echo "Insufficient funds. Please add funds to your account.";
    }
} else {
    echo "User ID or Flight ID not provided.";
}
?>