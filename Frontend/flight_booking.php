<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>

    <link rel="stylesheet" href="company_info.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="signup.css?v=<?php echo time(); ?>">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
    function getQueryParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const params = {};
        for (const [key, value] of urlParams) {
            params[key] = value;
        }
        return params;
    }

    $(document).ready(function () {
        const queryParams = getQueryParams();
        const userid = queryParams["userid"];
        const flightid = queryParams["flightid"];

        if (userid) {
            $("#userid").val(userid);
        }

        if (flightid) {
            $("#flightid").val(flightid);
        }
    });
</script>
</head>
<body>
    <?php
        include('../Backend/config.php');

        function getUserEmail($userid) {
            global $conn;
            $sql = "SELECT email FROM users WHERE id = ?";
            
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("i", $userid);
            $stmt->execute();

            if ($stmt->errno) {
                die("Error executing statement: " . $stmt->error);
            }

            $stmt->bind_result($email);
            if (!$stmt->fetch()) {
                return null; 
            }

            $stmt->close();

            return $email;
        }

        // Get the user_id from the URL
        if (isset($_GET['userid'])) {
            $userid = $_GET['userid'];

            // Call the function to get the user email
            $email = getUserEmail($userid);

            if ($email) {
                echo "User Email: $email";
            } else {
                echo "User not found or email not available.";
            }
        } else {
            echo "User ID not provided in the URL.";
        }
    ?>
    <nav class="navbar">
            <div class="container">
                <h1><a href="/Flight-Booking-Website/Frontend/index.html" style="text-decoration: none; color: inherit;">FlyEase</a></h1>
            </div>
            <ul>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_dashboard.php?email=<?php echo $email ?>">Home</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/search_flights.php?userid=<?php echo urlencode($userid) ?>">Search Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_flights.php?userid=<?php echo urlencode($userid) ?>">Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_profile.php?userid=<?php echo urlencode($userid) ?>">Profile</a></li>
                <li><a href="#">Messages</a></li>
            </ul>
    </nav>

    <section class="signx-form">
    <?php
    // Retrieve userid and flight_id from the URL
    $userid = $_GET['userid'] ?? null;
    $flightId = $_GET['flight_id'] ?? null;

    $flightDetailsQuery = "SELECT f.id, f.remaining_seats AS remaining_seats, d.name AS departure_city, d.country AS departure_country,
            des.name AS destination_city, des.country AS destination_country,
            f.departure_time, f.arrival_time, f.price, u.username, p.name_model AS plane_name
            FROM flights f
            JOIN company_data u ON f.company_id = u.user_id
            JOIN plane p ON f.plane_id = p.id
            JOIN cities d ON f.departure_city_id = d.id
            JOIN cities des ON f.destination_city_id = des.id
            WHERE f.id = ?";

    $stmt = $conn->prepare($flightDetailsQuery);
    $stmt->bind_param("i", $flightId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result !== false && $result->num_rows > 0) {
        $flightDetails = $result->fetch_assoc();

        // Display flight details (modify based on your design)
        echo "<h2>Flight Details</h2>";
        echo "<p><strong>Flight ID:</strong> {$flightDetails['id']}</p>";
        echo "<p><strong>Departure City:</strong> {$flightDetails['departure_city']}</p>";
        echo "<p><strong>Destination City:</strong> {$flightDetails['destination_city']}</p>";
        echo "<p><strong>Price:</strong> $" . number_format($flightDetails['price'], 2) . "</p>";
        echo "<p><strong>Remaining Seats:</strong> " .$flightDetails['remaining_seats'] . "</p>";
        echo "<p><strong>Departure Time:</strong> {$flightDetails['departure_time']}</p>";
        echo "<p><strong>Arrival Time:</strong> {$flightDetails['arrival_time']}</p>";

        echo '<form action="/Flight-Booking-Website/Backend/reserve_seat.php" method="get">';
        echo '<input type="hidden" name="userid" value="' . $userid . '">';
        echo '<input type="hidden" name="flightid" value="' . $flightId . '">';
        echo '<button type="submit">Reserve Seat</button>';
        echo '</form>';
    } else {
        echo "<p>Flight not found or invalid flight ID.</p>";
    }

    $stmt->close();
?>
    </section>


</body>
</html>