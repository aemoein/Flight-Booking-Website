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
        function getQueryParam(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        $(document).ready(function () {
            const userid = getQueryParam("userid");

            if (userid) {
                $("#userid").val(userid);
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
                <li><a href="/Flight-Booking-Website/Frontend/messages.php?userid=<?php echo urlencode($userid) ?>">Messages</a></li>
            </ul>
    </nav>

    <section id="flight-list">
        <h3>Flights</h3>
        <div class="flight-cards-container">
        <?php
            function getUserFlights($userid)
            {
                global $conn;
                $flightsQuery = "SELECT fu.user_id, fu.flight_id AS flight_idx, fu.id, f.departure_time, f.arrival_time, f.price, f.remaining_seats, 
                                        c.username AS company_name, p.name_model AS plane_name, 
                                        d.name AS departure_city, d.country AS departure_country, 
                                        des.name AS destination_city, des.country AS destination_country
                                        FROM flights_users fu
                                        JOIN flights f ON fu.flight_id = f.id
                                        JOIN company_data c ON f.company_id = c.user_id
                                        JOIN plane p ON f.plane_id = p.id
                                        JOIN cities d ON f.departure_city_id = d.id
                                        JOIN cities des ON f.destination_city_id = des.id
                                        WHERE fu.user_id = ?";

                $stmt = $conn->prepare($flightsQuery);
                $stmt->bind_param("i", $userid);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result !== false && $result->num_rows > 0) {
                    return $result->fetch_all(MYSQLI_ASSOC);
                }

                return [];
            }

            // Get the user_id from the URL
            if (isset($_GET['userid'])) {
                $userid = $_GET['userid'];

                // Call the function to get user flights
                $userFlights = getUserFlights($userid);

                // Display the flight details
                if (!empty($userFlights)) {

                    foreach ($userFlights as $flight) {
                        echo "<div class='flight-card'>";
                        echo "<h4>{$flight['plane_name']}</h4>";
                        echo "<p>{$flight['departure_city']}, {$flight['departure_country']} to {$flight['destination_city']}, {$flight['destination_country']}</p>";
                        echo "<p>Departure: {$flight['departure_time']}</p>";
                        echo "<p>Arrival: {$flight['arrival_time']}</p>";
                        echo "<p>Price: $" . number_format($flight['price'], 2) . "</p>";
                        echo "<p>Remaining Seats: {$flight['remaining_seats']}</p>";
                        echo "<p>Company: {$flight['company_name']}</p>";

                        // Add a button for canceling the flight
                        echo "<form action='/Flight-Booking-Website/Backend/cancel_flight.php' method='post'>";
                        echo "<input type='hidden' name='userid' value='" . urlencode($userid) . "'>";
                        echo "<input type='hidden' name='flightid' value='" . urlencode($flight['flight_idx']) . "'>";
                        echo "<button type='submit'>Cancel Flight</button>";
                        echo "</form>";

                        echo "</div>";
                    }
                } else {
                    echo "<p>No flights found for the user.</p>";
                }
            } else {
                echo "User ID not provided in the URL.";
            }
        ?>
        </div>
    </section>

</body>
</html>