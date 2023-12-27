<?php
// Retrieve the flight ID from the URL
$flightId = $_GET['flight_id'];

// Display the passenger information form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Information - FlyEase</title>
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
            <h2>Passenger Information</h2>
            <form id="bookingForm" action="process_booking.php" method="post">
            <!-- Add input fields for passenger information here -->
            <label for="firstName">First name:</label>
            <input type="text" id="passengerName" name="passengerName" required>

            <label for="middleName">Middle name:</label>
            <input type="text" id="passengerEmail" name="passengerEmail" required>

            <label for="lastName">Last name:</label>
            <input type="text" id="passengerEmail" name="passengerEmail" required>

            <label for="passengerEmail">Passenger Email:</label>
            <input type="email" id="passengerEmail" name="passengerEmail" required>

            <label for="phone">Phone number:</label>
            <input type="text" id="passengerphone" name="passengerEmail" required>
            <br>


            <!-- Add a hidden field to store the flight ID -->
            <input type="hidden" name="flightId" value="<?php echo $flightId; ?>">

            <br>
            <!-- Use <a> tag to submit the form -->
            <a href="javascript:void(0);" onclick="submitForm()">
                <input type="button" value="Book Now" style="background-color: #45a049; color: #ffffff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;" onmouseover="this.style.backgroundColor='#45a049'" onmouseout="this.style.backgroundColor='#4caf50'">
            </a>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 FlyEase</p>
    </footer>

    <script>
       function submitForm() {
    // Check if all required fields are filled
    var firstName = document.getElementById('passengerName').value;
            var middleName = document.getElementById('passengerEmail').value;
            var lastName = document.getElementById('passengerEmail').value;
            var passengerEmail = document.getElementById('passengerEmail').value;
            var phone = document.getElementById('passengerEmail').value;

            if (firstName && middleName && lastName && passengerEmail && phone) {
                document.getElementById('bookingForm').submit();
            } else {
                alert('Please fill in all the required fields');
            }
}

    </script>
</body>
</html>
