<?php include 'header.php'; ?>
<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barcode_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle gift creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_gift'])) {
    $name = $_POST['gift_name'];
    $quantity = intval($_POST['gift_quantity']);
    $redeemable_tickets = intval($_POST['redeemable_tickets']);

    $result = $conn->query("SELECT * FROM gifts WHERE name = '$name'");
    if ($result->num_rows > 0) {
        $gift = $result->fetch_assoc();
        $new_quantity = $gift['quantity'] + $quantity;
        $conn->query("UPDATE gifts SET quantity = $new_quantity WHERE name = '$name'");
    } else {
        $conn->query("INSERT INTO gifts (name, quantity, redeemable_tickets) VALUES ('$name', $quantity, $redeemable_tickets)");
    }
}

// Update redeemable tickets
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_redeemable_tickets'])) {
    $gift_id = intval($_POST['gift_id']);
    $redeemable_tickets = intval($_POST['redeemable_tickets']);
    $conn->query("UPDATE gifts SET redeemable_tickets = $redeemable_tickets WHERE id = $gift_id");
    $message = "Redeemable tickets updated.";
}

// Fetch gifts
$gifts = $conn->query("SELECT * FROM gifts");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Manager</title>
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
    <div class="form-container">
        <h1>Gift Manager</h1>
        <!-- Form for Adding Gifts -->
        <form method="POST">
            <label for="gift_name">Gift Name:</label>
            <input type="text" id="gift_name" name="gift_name" required>
            <label for="gift_quantity">Quantity:</label>
            <input type="number" id="gift_quantity" name="gift_quantity" required>
            <label for="redeemable_tickets">Redeemable Tickets:</label>
            <input type="number" id="redeemable_tickets" name="redeemable_tickets" required>
            <button type="submit" name="add_gift">Add Gift</button>
        </form>
    </div>

    <div class="form-container">
        <h2>Update Redeemable Tickets</h2>
        <!-- Form for Updating Redeemable Tickets -->
        <form method="POST">
            <label for="update_gift_id">Select Gift:</label>
            <select id="update_gift_id" name="gift_id" required>
                <option value="">Select a gift</option>
                <?php
                // Populate dropdown with gift names
                $gifts = $conn->query("SELECT * FROM gifts");
                while ($row = $gifts->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <label for="new_redeemable_tickets">New Redeemable Tickets:</label>
            <input type="number" id="new_redeemable_tickets" name="redeemable_tickets" required>
            <button type="submit" name="update_redeemable_tickets">Update</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Gifts</h2>
        <!-- Table to Display Gifts -->
        <table>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Redeemable Tickets</th>
            </tr>
            <?php
            // Display gifts in table
            $gifts = $conn->query("SELECT * FROM gifts");
            while ($row = $gifts->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['redeemable_tickets']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
