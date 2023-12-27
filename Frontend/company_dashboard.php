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
            const email = getQueryParam("email");
            if (email) {
               // alert("Email: " + email);

                // Set the email value to the hidden input field
                $("#email").val(email);
            }
        });
    </script>
</head>
<body>
    <?php
        include('../Backend/config.php');

        function getUserIdByEmail($email)
        {
            global $conn;
            $sqlSelect = "SELECT id FROM users WHERE email = ?";
            $stmtSelect = $conn->prepare($sqlSelect);

            if (!$stmtSelect) {
                echo ("Error preparing statement: " . $conn->error);
            }

            $stmtSelect->bind_param("s", $email);
            $stmtSelect->execute();

            if ($stmtSelect->errno) {
                echo ("Error executing statement: " . $stmtSelect->error);
            }

            $result = $stmtSelect->get_result();
            $stmtSelect->close();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['id'];
            } else {
                // Handle the case where the email is not found
                echo ("Email not found!");
            }
        }

        $email = $_GET['email'];

        try {
            //echo "Email: " . $email . "<br>";

            $user_id = getUserIdByEmail($email);
            //echo "User ID: " . $user_id . "<br>";

            // Fetch company information based on the user ID
            $sqlCompany = "SELECT username, logo_img_path FROM company_data WHERE user_id = ?";
            $stmtCompany = $conn->prepare($sqlCompany);

            if (!$stmtCompany) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmtCompany->bind_param("i", $user_id);
            $stmtCompany->execute();

            if ($stmtCompany->errno) {
                die("Error executing statement: " . $stmtCompany->error);
            }

            $resultCompany = $stmtCompany->get_result();
            $stmtCompany->close();

            if ($resultCompany->num_rows > 0) {
                $rowCompany = $resultCompany->fetch_assoc();
                $companyName = $rowCompany['username'];
                $logoImagePath = $rowCompany['logo_img_path'];

                //echo "Company Name: " . $companyName . "<br>";
                //echo "Logo Image Path: " . $logoImagePath . "<br>";
            } else {
                // Handle the case where company data is not found
                $companyName = "Company Not Found";
                $logoImagePath = "path/to/default/logo.jpg"; // Replace with a default logo path

                echo "Company not found. Using default values.<br>";
            }
        } catch (Exception $e) {
            // Handle exceptions
            echo "An error occurred: " . $e->getMessage();
        }
    ?>
    <nav class="navbar">
        <div class="container">
            <h1><a href="/Flight-Booking-Website/Frontend/index.html" style="text-decoration: none; color: inherit;">FlyEase</a></h1>
        </div>
        <ul>
            <li><a href="/Flight-Booking-Website/Frontend/company_dashboard.php?email=<?php echo $email ?>">Home</a></li>
            <li><a href="/Flight-Booking-Website/Frontend/add_flights.php?company_id=<?php echo urlencode($user_id); ?>">Add Flights</a></li>
            <li><a href="/Flight-Booking-Website/Frontend/display_flights.php?company_id=<?php echo urlencode($user_id); ?>">Flights</a></li>
            <li><a href="/Flight-Booking-Website/Frontend/company_profile.php/<?php echo $user_id ?>">Profile</a></li>
            <li><a href="#">Messages</a></li>
        </ul>
    </nav> 

    <section id="hero-section" style="background-image: url('../Backend/<?php echo $logoImagePath; ?>');">
    <div class="blur-overlay"></div>
    <div id="company-info">
        <div class="logo-container">
            <img src="../Backend/<?php echo $logoImagePath; ?>" alt="Company Logo">
        </div>
        <div class="name-container">
            <h2><?php echo $companyName; ?></h2>
        </div>
    </div>
    </section>

    <section id="flight-list">
        <h3>Flights</h3>
        <div class="flight-cards-container">
            <?php
            $sql = "SELECT f.id, d.name AS departure_city, d.country AS departure_country,
                        des.name AS destination_city, des.country AS destination_country,
                        f.departure_time, f.arrival_time, f.price, u.username, p.name_model AS plane_name
                    FROM flights f
                    JOIN company_data u ON f.company_id = u.user_id
                    JOIN plane p ON f.plane_id = p.id
                    JOIN cities d ON f.departure_city_id = d.id
                    JOIN cities des ON f.destination_city_id = des.id
                    WHERE f.company_id = $user_id
                    LIMIT 4";

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
                    </div>
                    <?php
                }
            } else {
                echo "<p>No flights available</p>";
            }
            ?>
        </div>
    </section>


    <div id="chatbox-icon">
        <img src="Resources/icons8-chat-48.png" alt="Chat Icon">
    </div>
    
    <!-- Chatbox Container -->
    <div id="chatbox">
        <div id="chatbox-header">
            Chat
        </div>
        <div id="chatbox-body">
            <!-- Chat messages will be displayed here -->
        </div>
        <div id="chatbox-input">
            <!-- Chat input form goes here -->
            <input type="text" placeholder="Type your message">
            <button id="send-btn">Send</button>
        </div>
    </div>
    
    <script>
        // jQuery functions for handling chatbox
        $(document).ready(function () {
            // Toggle chatbox on icon click
            $("#chatbox-icon").click(function () {
                $("#chatbox").slideToggle();
            });
    
            // Toggle chatbox on header click
            $("#chatbox-header").click(function () {
                $("#chatbox").slideToggle();
            });
    
            // Add your logic for sending messages here
            $("#send-btn").click(function () {
                // Implement the logic to send messages
            });
        });
    </script>    
</body>
</html>