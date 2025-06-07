<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include('partials/adminnavbar.php');

$conn = new mysqli("localhost", "root", "", "hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total payments from students
$paidResult = $conn->query("
    SELECT SUM(amount_paid) AS total_paid 
    FROM payments
");
$totalPaid = (float) ($paidResult->fetch_assoc()['total_paid'] ?? 0);

// Get total expenses from finances table
$expenseResult = $conn->query("
    SELECT SUM(amount) AS total_expense 
    FROM finances 
    WHERE type = 'Expense'
");
$totalExpense = (float) ($expenseResult->fetch_assoc()['total_expense'] ?? 0);

// ✅ Correct Net Result: Paid - Expenses
$netResult = $totalPaid - $totalExpense;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profit & Loss</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      padding: 20px 40px;
    }

    h2 {
      margin-bottom: 20px;
      font-size: 26px;
    }

    .summary-box {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .summary-box h3 {
      margin-bottom: 10px;
      font-size: 18px;
    }

    .summary-box p {
      font-size: 16px;
      margin: 8px 0;
    }

    .profit {
      color: green;
      font-weight: bold;
    }

    .loss {
      color: red;
      font-weight: bold;
    }

    .back-button {
      display: inline-block;
      margin-top: 20px;
      background: #4b0082;
      color: white;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
    }

    .back-button:hover {
      background: #360061;
    }
  </style>
</head>
<body>

<h2>📈 Profit & Loss Summary</h2>

<div class="summary-box">
  <h3>💵 Total Paid by Students</h3>
  <p>Total Payments Received: <strong>Rs. <?php echo number_format($totalPaid, 2); ?></strong></p>
</div>

<div class="summary-box">
  <h3>📉 Expenses</h3>
  <p>Total Expenses Recorded: <strong>Rs. <?php echo number_format($totalExpense, 2); ?></strong></p>
</div>

<div class="summary-box">
  <h3>📊 Net Result</h3>
  <p>
    Net <?php echo $netResult >= 0 ? '<span class="profit">Profit</span>' : '<span class="loss">Loss</span>'; ?>:
    <strong>Rs. <?php echo number_format(abs($netResult), 2); ?></strong>
  </p>
</div>

<a href="dashboard.php" class="back-button">
  <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

</body>
</html>
