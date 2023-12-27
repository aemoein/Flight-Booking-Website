<?php
// Retrieve the flight ID from the form
$flightId = $_POST['flightId'];

// Display the payment information form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information - FlyEase</title>
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
            <h2>Payment Information</h2>
            <form id="paymentForm" action="confirm_booking.php" method="post">
                <!-- Add input fields for payment information here -->
                <label for="cardNumber">Card Number:</label>
                <input type="text" id="cardNumber" name="cardNumber" required>

                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" required>

                <label for="expiryDate">Expiry Date (mm/yy):</label>
                <input type="text" id="expiryDate" name="expiryDate" required>

                <br>
                <!-- Add a hidden field to store the flight ID -->
                <input type="hidden" name="flightId" value="<?php echo $flightId; ?>">

                <br>
                <!-- Use <a> tag to submit the form -->
                <a href="javascript:void(0);" onclick="submitForm()">
                    <input type="button" value="Confirm Booking" style="background-color: #4caf50; color: #ffffff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;" onmouseover="this.style.backgroundColor='#45a049'" onmouseout="this.style.backgroundColor='#4caf50'">
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
            var cardNumber = document.getElementById('cardNumber').value;
            var cvv = document.getElementById('cvv').value;
            var expiryDate = document.getElementById('expiryDate').value;

            if (cardNumber && cvv && expiryDate) {
                document.getElementById('paymentForm').submit();
            } else {
                alert('Please fill in all the required fields');
            }
        }
    </script>
</body>
</html>
