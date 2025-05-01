<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('db/config.php');

// Insert transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO finances (description, amount, type, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $description, $amount, $type, $date);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_finances.php");
    exit();
}

$result = $conn->query("SELECT * FROM finances ORDER BY date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Finances - Saathi Hostel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: #f8f9fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-content {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }
        .card {
            background: white;
            padding: 30px;
            margin-bottom: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        h2 {
            color: #4b0082;
            margin-bottom: 20px;
        }
        input, select {
            padding: 12px;
            margin-bottom: 15px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        button {
            background-color: #4b0082;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #6a0dad;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background-color: #4b0082;
            color: white;
        }
        .income {
            color: green;
            font-weight: bold;
        }
        .expense {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include('partials/adminnavbar.php'); ?>
<div class="admin-content">
    <div class="card">
        <h2>Add Finance Record</h2>
        <form method="POST">
            <input type="text" name="description" placeholder="Description" required>
            <input type="number" name="amount" step="0.01" placeholder="Amount" required>
            <select name="type" required>
                <option value="">Select Type</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
            <input type="date" name="date" required>
            <button type="submit">Add Record</button>
        </form>
    </div>

    <div class="card">
        <h2>All Transactions</h2>
        <table>
            <tr>
                <th>Description</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Date</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo number_format($row['amount'], 2); ?></td>
                <td class="<?php echo $row['type']; ?>"><?php echo ucfirst($row['type']); ?></td>
                <td><?php echo $row['date']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>
