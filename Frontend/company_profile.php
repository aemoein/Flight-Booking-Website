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
        function hideProfileData() {
            var profileDataSection = document.getElementById('profile-data-section');
            if (profileDataSection) {
                profileDataSection.style.display = 'none';

                // Create a cancel button
                var cancelButton = document.createElement('button');
                cancelButton.textContent = 'Cancel';
                cancelButton.onclick = showProfileData; // Define a function to show the profile data when cancel is clicked

                // Append the cancel button to the same container as the original "Edit Profile" button
                var container = document.querySelector('.name-container');
                container.appendChild(cancelButton);
            }
        }

        function showProfileData() {
            var profileDataSection = document.getElementById('profile-data-section');
            if (profileDataSection) {
                profileDataSection.style.display = 'block';

                // Remove the cancel button
                var cancelButton = document.querySelector('.name-container button');
                if (cancelButton) {
                    cancelButton.remove();
                }
            }
        }
    </script>
</head>
<body>
    <?php
        include('../Backend/config.php');

        function getCompanyData($companyId) {
            global $conn;
            $sql = "SELECT c.*, u.email, u.tel FROM company_data c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.user_id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("i", $companyId);
            $stmt->execute();

            if ($stmt->errno) {
                die("Error executing statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $stmt->close();

            return $result->fetch_assoc();
        }

        $urlSegments = explode('/', $_SERVER['REQUEST_URI']);
        $companyId = end($urlSegments);

        if (!empty($companyId)) {
            $companyData = getCompanyData($companyId);

            if ($companyData) {
                $companyName = $companyData['username'];
                $logoImagePath = $companyData['logo_img_path'];
                $bio = $companyData['bio'];
                $address = $companyData['address'];
                $location = $companyData['location'];
                $email = $companyData['email'];
                $tel = $companyData['tel'];

                //echo "<h1>{$companyData['username']}</h1>";
                //echo "<p>Company Name: {$companyName}</p>";
                //echo "<p>Logo Image Path: {$logoImagePath}</p>";
                //echo "<p>Bio: {$bio}</p>";
                //echo "<p>Address: {$address}</p>";
                //echo "<p>Location: {$location}</p>";
                //echo "<p>Email: {$email}</p>";
                //echo "<p>Telephone: {$tel}</p>";
            } else {
                echo "<p>Company not found</p>";
            }
        } else {
            echo "<p>Company ID not provided in the URL</p>";
        }
    ?>
    
    <nav class="navbar">
        <div class="container">
            <h1><a href="../index.html" style="text-decoration: none; color: inherit;">FlyEase</a></h1>
        </div>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Add Flight</a></li>
            <li><a href="#">Flights</a></li>
            <li><a href="/Flight-Booking-Website/Frontend/company_profile.php/<?php echo $companyId ?>">Profile</a></li>
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
                <button onclick="hideProfileData()" class="edit-profile-button">Edit Profile</button>
            </div>
        </div>
    </section>

    <section id="profile-data-section" class="profile-data">
        <h2>Profile Data</h2>
        <p><strong>Bio:</strong> <?php echo $bio ?></p>
        <p><strong>Address:</strong> <?php echo $address ?></p>
        <p><strong>Location:</strong></p>
        <iframe src="<?php echo $location ?>" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        <p><strong>Email:</strong> <?php echo $email ?></p>
        <p><strong>Tel:</strong> <?php echo $tel ?></p>
    </section>
</body>
</html>