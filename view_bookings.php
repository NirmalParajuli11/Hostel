<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  Fetch room booking details by joining room_bookings and rooms
$bookingQuery = $conn->query("
    SELECT rooms.room_number, rooms.room_type, room_bookings.checkin_date
    FROM room_bookings
    JOIN rooms ON room_bookings.room_id = rooms.id
    WHERE room_bookings.user_id = $userId
    ORDER BY room_bookings.checkin_date DESC
    LIMIT 1
");

$booking = $bookingQuery->fetch_assoc();

//  Fetch food bookings
$foodQuery = $conn->query("
    SELECT meals.meal_name, meals.category, meals.price, food_bookings.booked_at
    FROM food_bookings
    JOIN meals ON food_bookings.meal_id = meals.id
    WHERE food_bookings.user_id = $userId
    ORDER BY food_bookings.booked_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      padding: 30px;
    }

    h2 {
      margin-bottom: 20px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    table th, table td {
      padding: 10px 14px;
      border: 1px solid #ddd;
    }

    table th {
      background: #4b0082;
      color: white;
    }

    .no-data {
      color: #999;
      font-style: italic;
    }

    .back-button {
      display: inline-block;
      margin-top: 25px;
      background: #4b0082;
      color: white;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
    }

    .back-button:hover {
      background: #360061;
    }
  </style>
</head>
<body>

<h2>📋 My Booking Details</h2>

<div class="card">
  <h3>🏠 Room Booking</h3>
  <?php if ($booking): ?>
    <p><strong>Room No:</strong> <?php echo htmlspecialchars($booking['room_number']); ?></p>
    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($booking['room_type']); ?></p>
    <p><strong>Check-In Date:</strong> <?= date('d M Y', strtotime($booking['checkin_date'])) ?></p>
  <?php else: ?>
    <p class="no-data">You haven't booked a room yet.</p>
  <?php endif; ?>
</div>

<div class="card">
  <h3>🍽️ Food Booking History</h3>
  <?php if ($foodQuery->num_rows > 0): ?>
    <table>
      <tr>
        <th>Meal</th>
        <th>Category</th>
        <th>Price</th>
        <th>Booked At</th>
      </tr>
      <?php while ($row = $foodQuery->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['meal_name']); ?></td>
          <td><?php echo htmlspecialchars($row['category']); ?></td>
          <td>Rs. <?php echo number_format($row['price'], 2); ?></td>
          <td><?php echo date('d M Y, h:i A', strtotime($row['booked_at'])); ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p class="no-data">No food bookings found.</p>
  <?php endif; ?>
</div>

<a href="user_dashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

</body>
</html>
