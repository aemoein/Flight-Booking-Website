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

        $userid = getUserIdByEmail($email);
    ?>
    <nav class="navbar">
            <div class="container">
                <h1><a href="/Flight-Booking-Website/Frontend/index.html" style="text-decoration: none; color: inherit;">FlyEase</a></h1>
            </div>
            <ul>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_dashboard.php?email=<?php echo $email ?>">Home</a></li>
                <li><a href="#">Search Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_flights.php?userid=<?php echo urlencode($userid) ?>">Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_profile.php?userid=<?php echo urlencode($userid) ?>">Profile</a></li>
                <li><a href="#">Messages</a></li>
            </ul>
    </nav>
</body>
</html>