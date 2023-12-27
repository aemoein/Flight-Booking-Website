<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking System - Signup</title>

    <link rel="stylesheet" href="company_info.css">
    <link rel="stylesheet" href="signup.css">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="signup.js"></script>

    <script>
        function getQueryParam(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        $(document).ready(function () {
            const company_id = getQueryParam("company_id");
        
            if (company_id) {
                $("#company_id").val(company_id);
            }
        });
    </script>
</head>
<body>
    <?php
        include('../Backend/config.php');

        function getUserEmail($company_id) {
            global $conn;
            $sql = "SELECT email FROM users WHERE id = ?";
            
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("i", $company_id);
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
        if (isset($_GET['company_id'])) {
            $company_id = $_GET['company_id'];

            // Call the function to get the user email
            $email = getUserEmail($company_id);

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
                <li><a href="/Flight-Booking-Website/Frontend/company_dashboard.php?email=<?php echo $email ?>">Home</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/add_flights.php?company_id=<?php echo urlencode($company_id); ?>">Add Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/display_flights.php?company_id=<?php echo urlencode($company_id) ?>">Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/company_profile.php/<?php echo urlencode($company_id) ?>">Profile</a></li>
                <li><a href="#">Messages</a></li>
            </ul>
    </nav> 
    <section id="flight-list">
        <h3>Flights</h3>
        <div class="flight-cards-container">
            <?php
            // Assuming $company_id is available
            $sql = "SELECT f.id, f.remaining_seats AS remaining_seats, d.name AS departure_city, d.country AS departure_country,
                        des.name AS destination_city, des.country AS destination_country,
                        f.departure_time, f.arrival_time, f.price, u.username, p.name_model AS plane_name
                    FROM flights f
                    JOIN company_data u ON f.company_id = u.user_id
                    JOIN plane p ON f.plane_id = p.id
                    JOIN cities d ON f.departure_city_id = d.id
                    JOIN cities des ON f.destination_city_id = des.id
                    WHERE f.company_id = $company_id";

            $result = $conn->query($sql);

            if ($result !== false && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="flight-card">
                        <h4><?php echo htmlspecialchars($row['plane_name']); ?></h4>
                        <p><?php echo htmlspecialchars($row['departure_city']); ?>, <?php echo htmlspecialchars($row['departure_country']); ?> to <?php echo htmlspecialchars($row['destination_city']); ?>, <?php echo htmlspecialchars($row['destination_country']); ?></p>
                        <p>Departure: <?php echo date('M d, Y H:i', strtotime($row['departure_time'])); ?></p>
                        <p>Arrival: <?php echo date('M d, Y H:i', strtotime($row['arrival_time'])); ?></p>
                        <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                        <p>Company: <?php echo htmlspecialchars($row['username']); ?></p>
                        <p>Remaining Seats: <?php echo htmlspecialchars($row['remaining_seats']); ?></p>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No flights available</p>";
            }
            ?>
        </div>
    </section>
</body>
</html>