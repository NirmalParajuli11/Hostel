<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include('db/config.php');

$userId = $_SESSION['user_id'];

// Fetch student name and photo
$name = 'Student';
$imagePath = 'assets/images/profile_bg.jpg';

$userSql = "SELECT name, photo FROM users WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
if ($userRow = $userResult->fetch_assoc()) {
    $name = $userRow['name'];
    if (!empty($userRow['photo'])) {
        $imagePath = 'assets/images/uploads/' . $userRow['photo'];
    }
}
$userStmt->close();

// Fetch Room, Price, Check-in
$roomNo = 'N/A';
$roomPrice = 0;
$checkIn = 'N/A';
$totalStayAmount = 0;

$bookingSql = "SELECT r.room_number, r.room_price, b.checkin_date 
               FROM room_bookings b
               JOIN rooms r ON b.room_id = r.id 
               WHERE b.user_id = ?
               ORDER BY b.checkin_date DESC 
               LIMIT 1";
$bookingStmt = $conn->prepare($bookingSql);
$bookingStmt->bind_param("i", $userId);
$bookingStmt->execute();
$bookingResult = $bookingStmt->get_result();

if ($row = $bookingResult->fetch_assoc()) {
    $roomNo = $row['room_number'];
    $roomPrice = $row['room_price'];
    $checkInDate = $row['checkin_date'];

    $checkIn = date('d M Y', strtotime($checkInDate));

    $today = new DateTime();
    $checkinDate = new DateTime($checkInDate);
    $interval = $checkinDate->diff($today);
    $daysStayed = max(1, $interval->days);

    $totalStayAmount = $daysStayed * $roomPrice;
} else {
    $daysStayed = 0;
}
$bookingStmt->close();

// Fetch Food Total (last 30 days)
$foodTotal = 0;
$foodSql = "SELECT SUM(meals.price) AS total 
            FROM food_bookings 
            JOIN meals ON food_bookings.meal_id = meals.id 
            WHERE food_bookings.user_id = ? 
              AND booked_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$foodStmt = $conn->prepare($foodSql);
$foodStmt->bind_param("i", $userId);
$foodStmt->execute();
$foodResult = $foodStmt->get_result();
if ($foodRow = $foodResult->fetch_assoc()) {
    $foodTotal = $foodRow['total'] ?? 0;
}
$foodStmt->close();

// Fetch Total Paid
$totalPaid = 0;
$paidSql = "SELECT SUM(amount_paid) AS total_paid FROM payments WHERE user_id = ?";
$paidStmt = $conn->prepare($paidSql);
$paidStmt->bind_param("i", $userId);
$paidStmt->execute();
$paidResult = $paidStmt->get_result();
if ($paidRow = $paidResult->fetch_assoc()) {
    $totalPaid = $paidRow['total_paid'] ?? 0;
}
$paidStmt->close();

// Final Calculations
$grandTotal = $totalStayAmount + $foodTotal;
$remainingAmount = max(0, $grandTotal - $totalPaid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Poppins', sans-serif; background: #f1f1f1; min-height: 100vh; }
    .container { padding: 50px 20px; display: flex; flex-direction: column; align-items: center; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px; width: 100%; max-width: 1100px; }
    .card {
      background: white; padding: 25px; border-radius: 16px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08); text-align: center;
    }
    .card h3 { margin-bottom: 10px; font-size: 18px; color: #4b0082; }
    .card p { font-size: 14px; color: #555; margin-bottom: 10px; }
    .card img.profile-pic {
      width: 80px; height: 80px; border-radius: 50%;
      object-fit: cover; margin-bottom: 10px; border: 2px solid #4b0082;
    }
    .green-button {
      background: #28a745; padding: 10px 16px; border-radius: 6px;
      color: white; text-decoration: none; font-weight: 600;
      margin-top: 10px; display: inline-block;
    }
    .green-button:hover { background: #218838; }
    footer {
      text-align: center; padding: 20px; font-size: 13px;
      color: #888; background-color: #fff; margin-top: 50px;
    }
    .highlight-red { color: red; font-weight: 600; }
    .highlight-green { color: green; font-weight: 600; }
  </style>
</head>
<body>

<?php include 'user_navbar.php'; ?>

<div class="container">
  <!-- Profile Section -->
  <div class="grid" style="margin-bottom: 20px;">
    <div class="card">
      <img src="<?= htmlspecialchars($imagePath) ?>" alt="Profile Image" class="profile-pic">
      <h3><?= htmlspecialchars($name) ?></h3>
      <p>Room No: <strong><?= htmlspecialchars($roomNo) ?></strong></p>
      <p>Days Stayed: <strong><?= htmlspecialchars($daysStayed) ?> day(s)</strong></p>
      <p>Price Per Day: <strong>Rs. <?= htmlspecialchars(number_format($roomPrice, 2)) ?></strong></p>
      <p>Check-In Date: <strong><?= htmlspecialchars($checkIn) ?></strong></p>
      <p>Total Stay Rent: <strong>Rs. <?= htmlspecialchars(number_format($totalStayAmount, 2)) ?></strong></p>
      <p>Food Total (Last 30 Days): <strong>Rs. <?= htmlspecialchars(number_format($foodTotal, 2)) ?></strong></p>
      <p>Grand Total (Rent + Food): <strong>Rs. <?= htmlspecialchars(number_format($grandTotal, 2)) ?></strong></p>
      <p>Paid Amount: <strong>Rs. <?= htmlspecialchars(number_format($totalPaid, 2)) ?></strong></p>
      <p>Remaining Amount: 
        <strong class="<?= $remainingAmount == 0 ? 'highlight-green' : 'highlight-red' ?>">
          Rs. <?= htmlspecialchars(number_format($remainingAmount, 2)) ?>
        </strong>
      </p>
    </div>

    <div class="card">
      <h3>👥 Roommates</h3>
      <p>No roommates assigned yet.</p>
    </div>
  </div>

  <!-- Action Panel -->
  <div class="grid">
    <div class="card">
      <h3>🚪 Book a Room</h3>
      <p>Reserve your stay at Saathi Hostel easily and securely.</p>
      <a href="book_room.php" class="green-button">Book Now</a>
    </div>
    <div class="card">
      <h3>🍛 Order Meals</h3>
      <p>Pre-book weekly meals with ease and comfort.</p>
      <a href="book_food.php" class="green-button">Order Now</a>
    </div>
    <div class="card">
      <h3>📚 View Bookings</h3>
      <p>See your full booking history and current status.</p>
      <a href="view_bookings.php" class="green-button">See Bookings</a>
    </div>
  </div>

  <!-- Notification -->
  <div class="grid" style="margin-top: 30px;">
    <div class="card">
      <h3>🔔 Notifications</h3>
      <p>No new announcements</p>
    </div>
  </div>
</div>

<footer>
  © <?= date("Y") ?> Saathi Hostel. All rights reserved.
</footer>

</body>
</html>
