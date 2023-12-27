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

    <script>
        function redirectToBookingPage(userid, flightId) {
            const bookingPageUrl = `/Flight-Booking-Website/Frontend/flight_booking.php?userid=${userid}&flight_id=${flightId}`;
            window.location.href = bookingPageUrl;
        }
    </script>

<style>
        .flight-card {
            cursor: pointer;
        }
    </style>
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

    <section id="flight-list">
        <h3>Flights</h3>
        <div class="flight-cards-container">
            <?php

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Retrieve form data
                $departureCityId = $_POST["departure_city"];
                $destinationCityId = $_POST["destination_city"];
                $fromDate = date('Y-m-d', strtotime($_POST["fromDate"]));

                $searchQuery = "SELECT f.id, f.remaining_seats AS remaining_seats, d.name AS departure_city, d.country AS departure_country,
                                des.name AS destination_city, des.country AS destination_country,
                                f.departure_time, f.arrival_time, f.price, u.username, p.name_model AS plane_name
                                FROM flights f
                                JOIN company_data u ON f.company_id = u.user_id
                                JOIN plane p ON f.plane_id = p.id
                                JOIN cities d ON f.departure_city_id = d.id
                                JOIN cities des ON f.destination_city_id = des.id
                                WHERE f.departure_city_id = ? AND f.destination_city_id = ? ";

                $stmt = $conn->prepare($searchQuery);
                $stmt->bind_param("ii", $departureCityId, $destinationCityId);
                $stmt->execute();
                $result = $stmt->get_result();

                // Display search results
                if ($result !== false && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Display flight information (adjust this based on your UI design)
                        echo "<div class='flight-card' onclick='redirectToBookingPage({$userid}, {$row['id']})'>";
                        echo "<input type='hidden' class='flight-id' value='{$row['id']}'>";
                        echo "<h4>{$row['plane_name']}</h4>";
                        echo "<p>{$row['departure_city']}, {$row['departure_country']} to {$row['destination_city']}, {$row['destination_country']}</p>";
                        echo "<p>Departure: " . date('M d, Y H:i', strtotime($row['departure_time'])) . "</p>";
                        echo "<p>Arrival: " . date('M d, Y H:i', strtotime($row['arrival_time'])) . "</p>";
                        echo "<p>Price: $" . number_format($row['price'], 2) . "</p>";
                        echo "<p>Company: {$row['username']}</p>";
                        echo "<p>Remaining Seats:" .htmlspecialchars($row['remaining_seats']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No flights found for the given criteria.</p>";
                }

                // Close the statement
                $stmt->close();
            }
            ?>
        </div>
</section>

</body>
</html>
