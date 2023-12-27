<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>

    <link rel="stylesheet" href="company_info.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="search_flights.css?v=<?php echo time(); ?>">
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
                <li><a href="/Flight-Booking-Website/Frontend/company_messaging.php?company_id=<?php echo urlencode($company_id) ?>">Messages</a></li>
            </ul>
    </nav>

    <div class="signx-form">
        <h2>Message</h2>
        <form action="/Flight-Booking-Website/Backend/company_messaging.php" method="post">
        <label for="userid">User:</label>
        <select id="userid" name="userid">
            <?php
            $userQuery = "SELECT id, name FROM users WHERE userType='passenger'";
            $userResult = $conn->query($userQuery);

            if ($userResult !== false && $userResult->num_rows > 0) {
                while ($user = $userResult->fetch_assoc()) {
                    echo "<option value=\"{$user['id']}\">{$user['name']}</option>";
                }
            }
            ?>
        </select>

        <input type='hidden' class='company_id' id='company_id' name="company_id">

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" required></textarea>


        <button type="submit">Save</button>
    </div>



</body>
</html>