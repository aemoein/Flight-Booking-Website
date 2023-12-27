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
                $("#email").val(email);
            }
        });
    </script>

    <style>
        .hero {
        height: 100vh;
        background-image: url("https://wallpapercave.com/dwp2x/wp3528296.jpg");
        background-size: cover;
        background-position: center;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #ffffff;
        }

        .hero h1 {
        font-size: 30px;
        margin: 0px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
        }

        .hero p {
        font-size: 24px;
        margin: 0px;
        text-align: center;
        margin-bottom: 40px;
        }

        .btn {
        display: inline-block;
        background-color: #3d5a80;
        text-align: center;
        opacity: 95%;
        margin: 0px;
        color: #ffffff;
        text-decoration: none;
        font-size: 18px;
        padding: 10px 20px;
        border-radius: 4px;
        transition: background-color 0.2s ease;
        }

        .hero .container{
        justify-content: center;
        align-items: center;
        top:0;
        }

        .btn:hover {
        background-color: #005388;
        }
    </style>
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
                <li><a href="/Flight-Booking-Website/Frontend/search_flights.php?userid=<?php echo urlencode($userid) ?>">Search Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_flights.php?userid=<?php echo urlencode($userid) ?>">Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/passenger_profile.php?userid=<?php echo urlencode($userid) ?>">Profile</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/messages.php?userid=<?php echo urlencode($userid) ?>">Messages</a></li>
            </ul>
    </nav>

    <section class="hero">
        <div class="container">
            <h1 class = "hero-title">Experience the World</h1>
            <p class = "hero-desc">Discover new places and create unforgettable memories.</p>
            <a href="/Flight-Booking-Website/Frontend/search_flights.php?userid=<?php echo urlencode($userid) ?>" class="btn">Explore Destinations</a>
        </div>
    </section>
</body>
</html>