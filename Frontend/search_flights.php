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

    <section id="profile-edit" class="signx-form">
        <h2>Search Flights</h2>
        <form action="display_search.php?userid=<?php echo urlencode($userid) ?>" method="post">
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

            <label for="fromDate">From Date:</label>
            <input type="date" id="fromDate" required>

            <button type="submit">Search</button>
        </form>
    </section>

    <footer>
        <p>&copy; 2023 FlyEase</p>
    </footer>
</body>
</html>