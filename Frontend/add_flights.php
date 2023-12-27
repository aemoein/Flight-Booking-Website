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

    <section id="profile-edit" class="signx-form">
        <h2>Add New Flight</h2>
        <form action="../Backend/process_add_flights.php" method="post">
            <label for="departure_city">Departure City:</label>
            <select id="departure_city" name="departure_city">
                <?php
                    $citiesQuery = "SELECT id, name, country FROM cities";
                    $citiesResult = $conn->query($citiesQuery);

                    if ($citiesResult !== false && $citiesResult->num_rows > 0) {
                        while ($city = $citiesResult->fetch_assoc()) {
                            echo "<option value=\"{$city['id']}\">{$city['name']}, {$city['country']}</option>";
                        }
                    }
                ?>
            </select>

            <label for="destination_city">Destination City:</label>
            <select id="destination_city" name="destination_city">
                <?php
                    $citiesResult = $conn->query($citiesQuery);

                    if ($citiesResult !== false && $citiesResult->num_rows > 0) {
                        while ($city = $citiesResult->fetch_assoc()) {
                            echo "<option value=\"{$city['id']}\">{$city['name']}, {$city['country']}</option>";
                        }
                    }
                ?>
            </select>

            <label for="departure_time">Departure Time:</label>
            <input type="datetime-local" id="departure_time" name="departure_time" required>

            <label for="arrival_time">Arrival Time:</label>
            <input type="datetime-local" id="arrival_time" name="arrival_time" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <input type="hidden" id="company_id" name="company_id">

            <label for="plane_id">Plane:</label>
            <select id="plane_id" name="plane_id">
                <?php
                    $planesQuery = "SELECT id, name_model FROM plane";
                    $planesResult = $conn->query($planesQuery);

                    if ($planesResult !== false && $planesResult->num_rows > 0) {
                        while ($plane = $planesResult->fetch_assoc()) {
                            echo "<option value=\"{$plane['id']}\">{$plane['name_model']}</option>";
                        }
                    }
                ?>
            </select>

            <button type="submit">Add Flight</button>
        </form>
    </section>
</body>
</html>