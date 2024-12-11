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

// Initialize variables to avoid undefined variable warnings
$barcode = '';
$user = [];
$gift_name = '';
$redeem_tickets = 0;
$new_balance = 0;
$message = "";
// Handle redemption
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['redeem_gift'])) {
    $barcode = $_POST['barcode'];
    $gift_name = $_POST['gift_name'];
    $redeem_tickets = intval($_POST['redeem_tickets']);

    // Fetch the user details
    $result = $conn->query("SELECT * FROM users WHERE barcode = '$barcode'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $new_balance = $user['balance'] - $redeem_tickets;

        if ($new_balance >= 0) {
            // Fetch gift details
            $gift_result = $conn->query("SELECT * FROM gifts WHERE name = '$gift_name'");
            if ($gift_result->num_rows > 0) {
                $gift = $gift_result->fetch_assoc();

                // Calculate the number of gifts that can be redeemed
                $tickets_per_gift = $gift['redeemable_tickets'];
                $max_gifts_redeemable = floor($redeem_tickets / $tickets_per_gift);

                if ($gift['quantity'] >= $max_gifts_redeemable) {
                    // Deduct tickets from user balance
                    $conn->query("UPDATE users SET balance = $new_balance WHERE barcode = '$barcode'");

                    // Deduct the proportional quantity from the gifts table
                    $new_quantity = $gift['quantity'] - $max_gifts_redeemable;
                    $conn->query("UPDATE gifts SET quantity = $new_quantity WHERE name = '$gift_name'");

                    // Record the redemption
                    $conn->query("INSERT INTO redemptions (barcode_id, gift_name, redeemed_tickets, customer_name, ticket_balance)
                                  VALUES ('$barcode', '$gift_name', $redeem_tickets, '{$user['customer_name']}', $new_balance)");

                    echo "<p style='color: green;'>Gift redeemed successfully! {$max_gifts_redeemable} gifts have been deducted from stock.</p>";
                } else {
                    echo "<p style='color: red;'>Not enough stock for the selected gift!</p>";
                }
            } else {
                echo "<p style='color: red;'>Gift not found!</p>";
            }
        } else {
            echo "<p style='color: red;'>Not enough tickets to redeem this gift!</p>";
        }
    } else {
        echo "<p style='color: red;'>User not found!</p>";
    }
}


// Fetch users and gifts
$users = $conn->query("SELECT * FROM users");
$gifts = $conn->query("SELECT * FROM gifts");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redeem Gifts</title>
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

        p {
        display: block;
        margin-top: 1em;
        margin-bottom: 1em;
        margin-left: 20;
        margin-right: 0;

}

        @media print {
            body * {
                visibility: hidden;
            }
            #printableArea, #printableArea * {
                visibility: visible;
            }
            #printableArea {
                position: absolute;
                left: 0;
                top: 6px;
                width: 80mm;
                display: block;
                text-align: center;
                border: 4px solid;
            }
        }
    </style>

        
   
</head>
<body>
    <h1>Redeem Gifts</h1>
    <form method="POST">
        <label for="barcode">Barcode:</label>
        <input type="text" id="barcode" name="barcode" required oninput="fetchCustomerName(this.value)">
        <p id="customer_name_display">Customer Name: </p>

        <label for="gift_name">Gift Name:</label>
        <select id="gift_name" name="gift_name" required>
            <option value="">Select a gift</option>
            <?php while ($row = $gifts->fetch_assoc()) { ?>
                <option value="<?php echo $row['name']; ?>"><?php echo $row['name']; ?> (<?php echo $row['redeemable_tickets']; ?> tickets)</option>
            <?php } ?>
        </select>

        <label for="redeem_tickets">Tickets to Redeem:</label>
        <input type="number" id="redeem_tickets" name="redeem_tickets" required>
        <button type="submit" name="redeem_gift">Redeem</button>
        <button type="button" onclick="window.print()">Print Receipt</button>
    </form>
    <br><br><br>

    <div id="printableArea">
        <div class="company-banner">
            <img src="Nabhfunzone.jpeg" alt="Company Logo">
        </div>
        <h2>Nabh fun Zone</h2>
    <p>Address Line 1</p>
    <p>Address Line 2</p>
    <hr>
        <p>Barcode No.: <?php echo htmlspecialchars($barcode); ?></p>
        <p>Customer Name: <?php echo isset($user['customer_name']) ? htmlspecialchars($user['customer_name']) : 'N/A'; ?></p>
        <p>Gift Name: <?php echo htmlspecialchars($gift_name); ?></p>
        <p>Redeemed Tickets: <?php echo htmlspecialchars($redeem_tickets); ?></p>
        <p>Remaining Balance: <?php echo htmlspecialchars($new_balance); ?></p>
        <p>Date: <?php echo date("Y-m-d H:i:s"); ?></p>
        <hr>
    <p>Thank you for your visit!</p>
    </div>

    <script>
        function fetchCustomerName(barcode) {
            const users = <?php
                $usersArray = [];
                while ($row = $users->fetch_assoc()) {
                    $usersArray[$row['barcode']] = $row['customer_name'];
                }
                echo json_encode($usersArray);
            ?>;
            document.getElementById('customer_name_display').innerText = "Customer Name: " + (users[barcode] || "Not found");
        }
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>
