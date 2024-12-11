
<?php include 'header.php'; ?>
<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barcode_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding tickets
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_tickets'])) {
    $barcode = $_POST['barcode'];
    $tickets = intval($_POST['tickets']);
    $customer_name = $_POST['customer_name'];

    // Check if the user already exists
    $result = $conn->query("SELECT * FROM users WHERE barcode = '$barcode'");
    if ($result->num_rows > 0) {
        // Update the existing user's balance and name
        $user = $result->fetch_assoc();
        $new_balance = $user['balance'] + $tickets;
        $conn->query("UPDATE users SET balance = $new_balance, customer_name = '$customer_name' WHERE barcode = '$barcode'");
    } else {
        // Insert a new user
        $conn->query("INSERT INTO users (barcode, customer_name, balance) VALUES ('$barcode', '$customer_name', $tickets)");
    }
}

// Fetch users
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nabh Funzone Ticket Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        
        .form-container, .table-container {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, select, button {
            margin-top: 5px;
            padding: 10px;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <h1>Ticket Manager</h1>
    <form method="POST">
        <label for="barcode">Barcode:</label>
        <input type="text" id="barcode" name="barcode" required>
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required>
        <label for="tickets">Tickets to Add:</label>
        <input type="number" id="tickets" name="tickets" required>
        <button type="submit" name="add_tickets">Add Tickets</button>
    </form>



    <h2>Users</h2>
    <table border="1">
        <tr>
            <th>Barcode</th>
            <th>Customer Name</th>
            <th>Balance</th>
        </tr>
        <?php while ($row = $users->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['barcode']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['balance']; ?></td>
            </tr>
        <?php } ?>
    </table>
    <?php include 'footer.php'; ?>
</body>
</html>
