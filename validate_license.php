<?php
include 'config/db.php'; // Database connection

function getSystemIdentifier() {
    // Replace with a secure method for getting a unique system ID
    return php_uname('n'); // Example: Use system hostname
}

function validateLicense($license_key, $conn) {
    $system_id = getSystemIdentifier();

    // Fetch license from the database
    $query = $conn->query("SELECT * FROM licenses WHERE license_key = '$license_key' AND is_active = 1");
    if ($query->num_rows === 0) {
        return "Invalid or inactive license key.";
    }

    $license = $query->fetch_assoc();
    $registered_systems = json_decode($license['system_ids'], true) ?: [];
    $max_allowed = $license['max_allowed_systems'];

    // Check if system is already registered
    if (in_array($system_id, $registered_systems)) {
        return "License validated for this system.";
    }

    // Check if max allowed systems reached
    if (count($registered_systems) >= $max_allowed) {
        return "License is already registered on maximum allowed systems.";
    }

    // Register the current system
    $registered_systems[] = $system_id;
    $registered_systems_json = json_encode($registered_systems);

    $conn->query("UPDATE licenses SET system_ids = '$registered_systems_json' WHERE license_key = '$license_key'");
    return "License successfully registered on this system.";
}

// Example Usage
$license_key = "75CAE9BA00C2788D"; // Replace with a real license key or form input
echo validateLicense($license_key, $conn);
?>
