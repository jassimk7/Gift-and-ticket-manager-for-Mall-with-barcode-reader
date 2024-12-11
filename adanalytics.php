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

// Handle Add/Edit/Delete User
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $customer_name = $_POST['customer_name'];
        $barcode = $_POST['barcode'];
        $balance = intval($_POST['balance']);
        $conn->query("INSERT INTO users (customer_name, barcode, balance) VALUES ('$customer_name', '$barcode', $balance)");
    } elseif (isset($_POST['edit_user'])) {
        $id = intval($_POST['id']);
        $customer_name = $_POST['customer_name'];
        $barcode = $_POST['barcode'];
        $balance = intval($_POST['balance']);
        $conn->query("UPDATE users SET customer_name = '$customer_name', barcode = '$barcode', balance = $balance WHERE id = $id");
    } elseif (isset($_POST['delete_user'])) {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM users WHERE id = $id");
    }
}

// Export to CSV
if (isset($_GET['export'])) {
    $output = fopen('php://output', 'w');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=users.csv');
    fputcsv($output, ['ID', 'Customer Name', 'Barcode', 'Balance', 'Created At']);
    $result = $conn->query("SELECT * FROM users");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Search and Pagination
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;

// Get Users with Search and Pagination
$query = "SELECT * FROM users WHERE customer_name LIKE '%$search%' OR barcode LIKE '%$search%' LIMIT $per_page OFFSET $offset";
$users = $conn->query($query);

// Get Total User Count
$total_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE customer_name LIKE '%$search%' OR barcode LIKE '%$search%'");
$total_users = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        .form-container { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .pagination { margin: 20px 0; text-align: center; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 5px 10px; border: 1px solid #ddd; }
        .pagination a.active { background-color: #007BFF; color: #fff; }
    </style>
</head>
<body>
    <h1>User Management</h1>

    <!-- Search Form -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by name or barcode" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <a href="user_management.php">Clear</a>
    </form>

    <!-- Add User Form -->
    <div class="form-container">
        <h2>Add User</h2>
        <form method="POST">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>
            <label for="barcode">Barcode:</label>
            <input type="text" id="barcode" name="barcode" required>
            <label for="balance">Balance:</label>
            <input type="number" id="balance" name="balance" required>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>

    <!-- Export to CSV -->
    <a href="user_management.php?export=true" style="margin-bottom: 20px; display: inline-block;">Export to CSV</a>

    <!-- User Table -->
    <h2>User List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Barcode</th>
            <th>Balance</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $users->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['barcode']; ?></td>
                <td><?php echo $row['balance']; ?></td>
                <td>
                    <!-- Edit User Form -->
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="customer_name" value="<?php echo $row['customer_name']; ?>" required>
                        <input type="text" name="barcode" value="<?php echo $row['barcode']; ?>" required>
                        <input type="number" name="balance" value="<?php echo $row['balance']; ?>" required>
                        <button type="submit" name="edit_user">Edit</button>
                    </form>
                    <!-- Delete User Form -->
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php } ?>
    </div>
</body>
</html>
