<?php include 'header.php'; ?>

<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barcode_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Stats
$total_customers = $conn->query("SELECT COUNT(*) AS total_customers FROM users")->fetch_assoc()['total_customers'];
$top_customers = $conn->query("SELECT customer_name, barcode, balance FROM users ORDER BY balance DESC LIMIT 5");
$total_tickets = $conn->query("SELECT SUM(balance) AS total_tickets FROM users")->fetch_assoc()['total_tickets'];
$total_tickets_redeemed = $conn->query("SELECT SUM(ticket_balance) AS total_tickets_redeemed FROM redemptions")->fetch_assoc()['total_tickets_redeemed'];

$total_gifts = $conn->query("SELECT COUNT(*) AS total_gifts FROM gifts")->fetch_assoc()['total_gifts'];
$top_redeemed_gifts = $conn->query("SELECT gift_name, SUM(redeemed_tickets) AS total_redeemed FROM redemptions GROUP BY gift_name ORDER BY total_redeemed DESC LIMIT 5");
$low_stock_gifts = $conn->query("SELECT name, quantity FROM gifts ORDER BY quantity ASC LIMIT 5");
$total_tickets_spent = $conn->query("SELECT SUM(redeemed_tickets) AS total_tickets_spent FROM redemptions")->fetch_assoc()['total_tickets_spent'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-container {
            padding: 20px;
        }
        .card-title {
            font-size: 1.5rem;
        }
        .table-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="text-center mb-4">Analytics Dashboard</h1>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text fs-3"><?php echo $total_customers; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Tickets</h5>
                        <p class="card-text fs-3"><?php echo $total_tickets; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Tickets Redeemed</h5>
                        <p class="card-text fs-3"><?php echo $total_tickets_redeemed; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Total Gifts</h5>
                        <p class="card-text fs-3"><?php echo $total_gifts; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <h2 class="mt-5">Top 5 Customers</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Barcode</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $top_customers->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td><?php echo $row['barcode']; ?></td>
                            <td><?php echo $row['balance']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h2 class="mt-5">Top 5 Most Redeemed Gifts</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Gift Name</th>
                        <th>Total Redeemed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $top_redeemed_gifts->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['gift_name']; ?></td>
                            <td><?php echo $row['total_redeemed']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h2 class="mt-5">Gifts with the Least Stock</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Gift Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $low_stock_gifts->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
