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

// Retrieve search parameters from the form (you may need to adjust this based on your form structure)
$from = $_POST['from'];
$to = $_POST['to'];
$fromDate = $_POST['fromDate'];
$toDate = $_POST['toDate'];

// Output received data for debugging
echo "Received data:\n";
echo "From: $from\n";
echo "To: $to\n";
echo "From Date: $fromDate\n";
echo "To Date: $toDate\n";

// Prepare and execute the SQL query
$sql = "SELECT * FROM flights WHERE from_city = '$from' AND to_city = '$to' AND departure_date >= '$fromDate' AND arrival_date <= '$toDate';";
$result = $conn->query($sql);

// Check for errors in the query
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Output the SQL query for debugging
echo "SQL Query: $sql\n";
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
            // Display search results
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p>Flight from {$row['from_city']} to {$row['to_city']} by {$row['airline']} on {$row['departure_date']}</p>";
                }
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
