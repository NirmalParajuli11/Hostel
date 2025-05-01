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

// Get total room rent collected
$roomResult = $conn->query("
    SELECT SUM(room_price) AS total_rent 
    FROM rooms 
    WHERE user_id IS NOT NULL
");
$roomRent = $roomResult->fetch_assoc()['total_rent'] ?? 0;

// Get total food charges collected
$foodResult = $conn->query("
    SELECT SUM(meals.price) AS total_food 
    FROM food_bookings 
    JOIN meals ON food_bookings.meal_id = meals.id
");
$foodIncome = $foodResult->fetch_assoc()['total_food'] ?? 0;

// Get total expenses
$expenseResult = $conn->query("SELECT SUM(amount) AS total_expense FROM expenses");
$totalExpense = $expenseResult->fetch_assoc()['total_expense'] ?? 0;

// Calculate profit/loss
$totalIncome = $roomRent + $foodIncome;
$profit = $totalIncome - $totalExpense;
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
      padding: 30px;
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
  <h3>💰 Income</h3>
  <p>Room Rent Collected: <strong>Rs. <?php echo number_format($roomRent, 2); ?></strong></p>
  <p>Food Charges Collected: <strong>Rs. <?php echo number_format($foodIncome, 2); ?></strong></p>
  <p><strong>Total Income: Rs. <?php echo number_format($totalIncome, 2); ?></strong></p>
</div>

<div class="summary-box">
  <h3>📉 Expenses</h3>
  <p>Total Expenses Recorded: <strong>Rs. <?php echo number_format($totalExpense, 2); ?></strong></p>
</div>

<div class="summary-box">
  <h3>📊 Net Result</h3>
  <p>
    Net <?php echo $profit >= 0 ? '<span class="profit">Profit</span>' : '<span class="loss">Loss</span>'; ?>:
    <strong>Rs. <?php echo number_format(abs($profit), 2); ?></strong>
  </p>
</div>

<a href="dashboard.php" class="back-button">
  <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

</body>
</html>
