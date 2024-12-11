<?php
include 'config/db.php'; // Database connection
include 'validate_license.php'; // Licensing validation script

$license_key = "75CAE9BA00C2788D"; // Replace with a user-provided key or stored value
$license_validation = validateLicense($license_key, $conn);

if (strpos($license_validation, "successfully") === false && strpos($license_validation, "validated") === false) {
    die($license_validation); // Stop execution if license validation fails
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);        
        }

        .company-banner{
          
           background-color: yellow;
           animation-name: example;
           animation-duration: 8s;
           text-align: center;
           margin-bottom: 20px;
        }

        @keyframes example {
           from {background-color: red;}
           to {background-color: yellow;}
}

        }
        
        
        .company-banner img {
            max-width: 100%;
            height: auto;
        }
        .nav-bar {
            background-color: #f4f4f4;
            padding: 10px;
            display: flex;
            gap: 15px;
            border-bottom: 1px solid #ddd;
        }
        .nav-bar a {
            text-decoration: none;
            color: #333;
            padding: 8px 12px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .nav-bar a:hover {
            border: 1px solid #007BFF;
            background-color: #007BFF;
            color: white;
        }
        .Mainheader   {
            color: black;
            border: 2px solid black;
            padding: 30px;
            text-align:center;
        }
        
    </style>
</head>
<body>
<div class="company-banner">

        <img src="Nabhfunzone.jpeg" alt="Company Logo">
    </div>
    <h4 class="Mainheader">HIMAZ Gaming Software Version 3.1</h4>
    <div class="nav-bar">
        <a href="index.php">Ticket Manager</a>
        <a href="gift_manager.php">Gift Manager</a>
        <a href="redeem.php">Redeem Gifts</a>
        <a href="analytics.php">Analytics Dashboard</a>
        <a href="analytics1.php">Enhanced Analytics Dashboard</a>
        <a href="adanalytics.php">User Management</a>
    </div>
</body>
</html>
