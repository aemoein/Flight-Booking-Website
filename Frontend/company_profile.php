<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>

    <link rel="stylesheet" href="../company_info.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../signup.css?v=<?php echo time(); ?>">

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
    </style>

    <script>
        function toggleEditCancelButtons() {
            var profileDataSection = document.getElementById('profile-data-section');
            var profileEdit = document.getElementById('profile-edit')
            var editButton = document.getElementById('edit-button');
            var cancelButton = document.getElementById('cancel-button');

            if (profileDataSection && editButton && cancelButton && profileEdit) {
                if (profileDataSection.style.display === 'block') {
                    profileDataSection.style.display = 'none';
                    editButton.style.display = 'none';
                    cancelButton.style.display = 'inline-block';
                    profileEdit.style.display = 'block';
                } else {
                    profileDataSection.style.display = 'block';
                    editButton.style.display = 'inline-block';
                    cancelButton.style.display = 'none';
                    profileEdit.style.display = 'none';
                }
            }
        }
    </script>

    <script>
        $(document).ready(function () {
            // Get the last segment of the path, which is the company_id
            const companyId = window.location.pathname.split('/').pop();
            console.log("companyId:", companyId);  // Check the value in the console
            if (companyId) {
                $("#company_id").val(companyId);
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
                <li><a href="/Flight-Booking-Website/Frontend/add_flights.php?company_id=<?php echo urlencode($companyId); ?>">Add Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/display_flights.php?company_id=<?php echo urlencode($companyId) ?>">Flights</a></li>
                <li><a href="/Flight-Booking-Website/Frontend/company_profile.php/<?php echo urlencode($companyId) ?>">Profile</a></li>
                <li><a href="#">Messages</a></li>
            </ul>
    </nav> 

    <section id="hero-section" style="background-image: url('../../Backend/<?php echo $logoImagePath; ?>');">
        <div class="blur-overlay"></div>
        <div id="company-info">
            <div class="logo-container">
                <img src="../../Backend/<?php echo $logoImagePath; ?>" alt="Company Logo">
            </div>
            <div class="name-container">
                <h2><?php echo $companyName; ?></h2>
                <button id="edit-button" onclick="toggleEditCancelButtons()">Edit</button>
                <button id="cancel-button" style="display: none;" onclick="toggleEditCancelButtons()">Cancel</button>
            </div>
        </div>
    </section>

    <section id="profile-data-section" class="profile-data">
        <h2>Profile Data</h2>
        <p><strong>Bio:</strong> <?php echo $bio ?></p>
        <p><strong>Address:</strong> <?php echo $address ?></p>
        <p><strong>Location:</strong></p>
        <?php echo $location ?>
        <p><strong>User Name:</strong> <?php echo $username ?></p>
        <p><strong>Email:</strong> <?php echo $email ?></p>
        <p><strong>Tel:</strong> <?php echo $tel ?></p>
    </section>

    <section id="profile-edit" class="signx-form" style="display: none;">
        <h2>Edit Profile Data</h2>
        <form id="profile-form" action="/Flight-Booking-Website/Backend/update_profile.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="company_id" name="company_id">

            <label for="company-name">Company Name:</label>
            <input type="text" id="company-name" name="company-name" value="<?php echo $companyName ?>">

            <label for="logoImg">Logo Image:</label>
            <input type="file" id="logoImg" name="logoImg" accept="image/*">

            <label for="username">User Name:</label>
            <input type="text" id="username" name="username" value="<?php echo $username ?>">

            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio"><?php echo $bio ?></textarea>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $address ?>">

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value='<?php echo $location ?>'>

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