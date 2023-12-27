<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>

    <link rel="stylesheet" href="company_info.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="signup.css?v=<?php echo time(); ?>">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <style>
        .profile-data {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 30px;
            border-radius: 8px;
        }

        h2 {
            color: #333;
        }

        .profile-data p {
            margin: 0;
            padding: 10px;
            font-size: 18px;
        }

        .passport-image-container img {
            max-width: 100%;
            height: auto;
        }
    </style>

    <script>
        function toggleEditCancelButtons() {
            var profileDataSection = $('#profile-data-section');
            var profileEdit = $('#profile-edit');
            var editButton = $('#edit-button');
            var cancelButton = $('#cancel-button');

            if (profileDataSection.length && editButton.length && cancelButton.length && profileEdit.length) {
                if (profileDataSection.is(':visible')) {
                    profileDataSection.hide();
                    editButton.hide();
                    cancelButton.show();
                    profileEdit.show();
                } else {
                    profileDataSection.show();
                    editButton.show();
                    cancelButton.hide();
                    profileEdit.hide();
                }
            }
        }

        function getQueryParam(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        $(document).ready(function () {
            const userid = getQueryParam("userid");

            //alert(userid);

            if (userid) {
                $("#userid").val(userid);
            }
        });
    </script>
</head>

<body>
    <?php
    include('../Backend/config.php');

    function getPassengerData($userid)
    {
        global $conn;
        $sql = "SELECT p.*, u.name , u.email, u.tel, u.account FROM passenger_data p
                    JOIN users u ON p.user_id = u.id
                    WHERE p.user_id = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("i", $userid);
        $stmt->execute();

        if ($stmt->errno) {
            die("Error executing statement: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    if (isset($_GET['userid'])) {
        $userid = $_GET['userid'];

        if (!empty($userid)) {
            $passengerData = getPassengerData($userid);

            if ($passengerData) {
                $username = $passengerData['name'];
                $photoPath = $passengerData['photo_path'];
                $passPath = $passengerData['passport_img_path'];
                $email = $passengerData['email'];
                $tel = $passengerData['tel'];
                $account = $passengerData['account'];
            } else {
                echo "<p>Passenger not found</p>";
            }
        } else {
            echo "<p>User ID not provided in the URL</p>";
        }
    } else {
        echo "<p>User ID not provided in the URL.</p>";
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

    <section id="hero-section" style="background-image: url('../Backend/<?php echo $photoPath; ?>');">
        <div class="blur-overlay"></div>
        <div id="company-info">
            <div class="logo-container">
                <img src="../Backend/<?php echo $photoPath; ?>" alt="Company Logo">
            </div>
            <div class="name-container">
                <h2><?php echo $username; ?></h2>
                <button id="edit-button" onclick="toggleEditCancelButtons()">Edit</button>
                <button id="cancel-button" style="display: none;" onclick="toggleEditCancelButtons()">Cancel</button>
            </div>
        </div>
    </section>

    <section id="profile-data-section" class="profile-data">
        <h2>Profile Data</h2>
        <p><strong>Passport:</strong></p>
        <div class="passport-image-container">
            <?php
            if ($passPath) {
                echo '<img src="../Backend/' . htmlspecialchars($passPath) . '" alt="Passport Image">';
            } else {
                echo '<p>No passport image available</p>';
            }
            ?>
        </div>
        <p><strong>Email:</strong> <?php echo $email ?></p>
        <p><strong>Tel:</strong> <?php echo $tel ?></p>
        <p><strong>Account Balance:</strong> $<?php echo number_format($account, 2) ?></p>
    </section>

    <section id="profile-edit" class="signx-form" style="display: none;">
        <h2>Edit Profile Data</h2>
        <form id="profile-form" action="/Flight-Booking-Website/Backend/update_passenger_profile.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="userid" name="userid" value="<?php echo $userid; ?>">

            <label for="profileImg">Profile Image:</label>
            <input type="file" id="profileImg" name="profileImg" accept="image/*">

            <label for="passImg">Passport Image:</label>
            <input type="file" id="passImg" name="passImg" accept="image/*">

            <label for="username">User Name:</label>
            <input type="text" id="username" name="username" value="<?php echo $username ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email ?>" readonly>

            <label for="expass">Old Password:</label>
            <input type="expass" id="expass" name="expass">

            <label for="newpass">New Password:</label>
            <input type="newpass" id="newpass" name="newpass">

            <label for="tel">Tel:</label>
            <input type="tel" id="tel" name="tel" value="<?php echo $tel ?>">

            <div class="buttons-container">
                <button type="button" id="edit-button" onclick="toggleEditCancelButtons()">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </section>
</body>

</html>