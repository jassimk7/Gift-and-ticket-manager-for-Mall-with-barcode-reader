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
$total_tickets = $conn->query("SELECT SUM(balance) AS total_tickets FROM users")->fetch_assoc()['total_tickets'];
$total_tickets_redeemed = $conn->query("SELECT SUM(ticket_balance) AS total_tickets_redeemed FROM redemptions")->fetch_assoc()['total_tickets_redeemed'];
$total_tickets_spent = $conn->query("SELECT SUM(redeemed_tickets) AS total_tickets_spent FROM redemptions")->fetch_assoc()['total_tickets_spent'];

// Prepare data for charts
$top_customers_data = $conn->query("SELECT customer_name, balance FROM users ORDER BY balance DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$top_redeemed_gifts_data = $conn->query("SELECT gift_name, SUM(redeemed_tickets) AS total_redeemed FROM redemptions GROUP BY gift_name ORDER BY total_redeemed DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            padding: 20px;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 400px;
        }
        table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="text-center mb-4">Analytics Dashboard</h1>

        <!-- Stats Cards -->
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
                        <h5 class="card-title">Tickets Spent</h5>
                        <p class="card-text fs-3"><?php echo $total_tickets_spent; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-5">
            <!-- Top Customers Chart -->
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="topCustomersChart"></canvas>
                </div>
                <!-- Table for Top Customers -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_customers_data as $customer) { ?>
                            <tr>
                                <td><?php echo $customer['customer_name']; ?></td>
                                <td><?php echo $customer['balance']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Top Gifts Redeemed Chart -->
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="topGiftsChart"></canvas>
                </div>
                <!-- Table for Top Redeemed Gifts -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Gift Name</th>
                            <th>Tickets Redeemed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_redeemed_gifts_data as $gift) { ?>
                            <tr>
                                <td><?php echo $gift['gift_name']; ?></td>
                                <td><?php echo $gift['total_redeemed']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart Data -->
    <script>
        // Top Customers Data
        const topCustomersData = {
            labels: <?php echo json_encode(array_column($top_customers_data, 'customer_name')); ?>,
            datasets: [{
                label: 'Balance',
                data: <?php echo json_encode(array_column($top_customers_data, 'balance')); ?>,
                backgroundColor: ['#4CAF50', '#2196F3', '#FF5722', '#FFC107', '#E91E63'],
                borderWidth: 1
            }]
        };

        // Top Gifts Redeemed Data
        const topGiftsData = {
            labels: <?php echo json_encode(array_column($top_redeemed_gifts_data, 'gift_name')); ?>,
            datasets: [{
                label: 'Tickets Redeemed',
                data: <?php echo json_encode(array_column($top_redeemed_gifts_data, 'total_redeemed')); ?>,
                backgroundColor: ['#673AB7', '#03A9F4', '#8BC34A', '#FF9800', '#FFEB3B'],
                borderWidth: 1
            }]
        };

        // Render Top Customers Chart
        const topCustomersCtx = document.getElementById('topCustomersChart').getContext('2d');
        new Chart(topCustomersCtx, {
            type: 'bar',
            data: topCustomersData,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
            },
        });

        // Render Top Gifts Chart
        const topGiftsCtx = document.getElementById('topGiftsChart').getContext('2d');
        new Chart(topGiftsCtx, {
            type: 'pie',
            data: topGiftsData,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
