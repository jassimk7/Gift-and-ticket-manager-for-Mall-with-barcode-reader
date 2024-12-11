<?php
// Include database connection
include('config/db.php');
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs to prevent SQL injection
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contact = filter_var($_POST['contact'], FILTER_SANITIZE_STRING);

    // Generate unique system ID
    $system_id = php_uname('n') . '_' . hash('sha256', gethostbyname(gethostname())); 
    $status = 'pending'; // Set status to pending for admin approval

    // Check if the system is already registered
    $stmt = $conn->prepare("SELECT * FROM registered_systems WHERE system_id = ?");
    $stmt->bind_param("s", $system_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color: red;'>This system is already registered.</p>";
    } else {
        // Insert system details into the database
        $stmt = $conn->prepare("INSERT INTO registered_systems (email, contact, system_id, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $contact, $system_id, $status);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Your system has been registered. Please wait for admin approval.</p>";
            sendAdminNotification($email, $contact, $system_id);
        } else {
            echo "<p style='color: red;'>Registration failed. Please try again.</p>";
        }
    }
}

// Function to notify admin via email
function sendAdminNotification($email, $contact, $system_id) {
    $adminEmail = 'jcube.se@gmail.com'; // Replace with admin email
    $appEmail = ''; // Replace with your email
    $appPassword = ''; // Replace with your app password

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $appEmail; 
        $mail->Password = $appPassword; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($appEmail, 'System Registration'); 
        $mail->addAddress($adminEmail);

        $mail->Subject = 'New System Registration';
        $mail->Body = "A new system has been registered:\n\nEmail: $email\nContact: $contact\nSystem ID: $system_id";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. PHPMailer Error: " . $mail->ErrorInfo);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>System Registration</title>
</head>
<body>
    <h1>System Registration</h1>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="contact">Contact Number:</label>
        <input type="text" id="contact" name="contact" required>
        <br>
        <button type="submit">Register System</button>
    </form>
</body>
</html>
