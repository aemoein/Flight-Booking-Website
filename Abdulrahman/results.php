<?php
// Establish a connection to the database (modify these details)
$servername = "localhost";
$username = "root";
$password = "Ix@0504163525";
$dbname = "ss";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve search parameters from the URL (using $_GET)
$from = $_GET['from'];
$to = $_GET['to'];
$fromDate = $_GET['fromDate'];
//$toDate = $_GET['toDate'];

// Prepare and execute the SQL query
$sql = "SELECT * FROM flights WHERE from_city = '$from' AND to_city = '$to' AND departure_date >= '$fromDate';";
$result = $conn->query($sql);

// Check for errors in the query
if (!$result) {
    die("Query failed: " . $conn->error);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Search Results - FlyEase</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS file -->
</head>
<body>
    <header>
        <h1>FlyEase</h1>
    </header>

    <div class="container">
        <div class="sidenav">
            <h2>Sidebar</h2>
            <p>This is a sidebar with some content.</p>
        </div>

        <div class="result-box">
    <?php
    // Display search results in a styled table
    if ($result->num_rows > 0) {
        echo "<table style='width: 60%; padding: 20px; background-color: #fff; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin-left: 20px; border-collapse: collapse;'>";

        // Table header with a black background
        echo "<tr style='background-color: black; color: white;'>";
        echo "<th style='padding: 10px;'>From City</th>";
        echo "<th style='padding: 10px;'>To City</th>";
        echo "<th style='padding: 10px;'>Airline</th>";
        echo "<th style='padding: 10px;'>Departure Date</th>";
        echo "<th style='padding: 10px;'>Arraival Date</th>";
        echo "<th style='padding: 10px;'>Booking</th>";
        echo "</tr>";

        while ($row = $result->fetch_assoc()) {
            // Table row for each flight
            echo "<tr style='border: 1px solid #ccc;'>";
            echo "<td style='padding: 10px;'>{$row['from_city']}</td>";
            echo "<td style='padding: 10px;'>{$row['to_city']}</td>";
            echo "<td style='padding: 10px;'>{$row['airline']}</td>";
            echo "<td style='padding: 10px;'>{$row['departure_date']}</td>";
            echo "<td style='padding: 10px;'>{$row['arrival_date']}</td>";

            // Add a column with a booking option
            echo "<td style='padding: 10px;'>";
            echo "<a href='passenger_info.php?flight_id={$row['flight_id']}'><input type='button' value='Book Now' style='background-color: #45a049; color: #ffffff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;' onmouseover='this.style.backgroundColor=\"#45a049\"' onmouseout='this.style.backgroundColor=\"#4caf50\"'></a>";
            echo "</td>";


            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No flights found</p>";
    }
    ?>
</div>

    </div>

    <footer>
        <p>&copy; 2023 FlyEase</p>
    </footer>
</body>
</html>
