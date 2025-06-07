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

try {
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
} catch (Exception $e) {
    // Log error
    error_log("Error fetching user data: " . $e->getMessage());
}

// Fetch Room, Price, Check-in
$roomNo = 'N/A';
$roomPrice = 0;
$checkIn = 'N/A';
$totalStayAmount = 0;
$daysStayed = 0;

try {
    $bookingSql = "SELECT r.room_number, r.room_price, b.checkin_date, b.room_id
                FROM room_bookings b
                JOIN rooms r ON b.room_id = r.id 
                WHERE b.user_id = ?
                ORDER BY b.checkin_date DESC 
                LIMIT 1";
    $bookingStmt = $conn->prepare($bookingSql);
    $bookingStmt->bind_param("i", $userId);
    $bookingStmt->execute();
    $bookingResult = $bookingStmt->get_result();

    $roomId = null;
    if ($row = $bookingResult->fetch_assoc()) {
        $roomNo = $row['room_number'];
        $roomPrice = $row['room_price'];
        $checkInDate = $row['checkin_date'];
        $roomId = $row['room_id'];

        $checkIn = date('d M Y', strtotime($checkInDate));

        // Calculate days stayed - improve calculation logic
        $today = new DateTime();
        $checkinDate = new DateTime($checkInDate);
        
        // Only count days if check-in is in the past
        if ($checkinDate <= $today) {
            $interval = $checkinDate->diff($today);
            $daysStayed = max(1, $interval->days);
            $totalStayAmount = $daysStayed * $roomPrice;
        }
    }
    $bookingStmt->close();
} catch (Exception $e) {
    error_log("Error fetching booking data: " . $e->getMessage());
}

// Fetch roommates
$roommates = [];
if ($roomId !== null) {
    try {
        $roommateSql = "SELECT u.name FROM room_bookings b 
                        JOIN users u ON b.user_id = u.id 
                        WHERE b.room_id = ? AND b.user_id != ?";
        $roommateStmt = $conn->prepare($roommateSql);
        $roommateStmt->bind_param("ii", $roomId, $userId);
        $roommateStmt->execute();
        $roommateResult = $roommateStmt->get_result();
        while ($roommateRow = $roommateResult->fetch_assoc()) {
            $roommates[] = $roommateRow['name'];
        }
        $roommateStmt->close();
    } catch (Exception $e) {
        error_log("Error fetching roommate data: " . $e->getMessage());
    }
}

// Fetch Food Total (last 30 days)
$foodTotal = 0;
try {
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
} catch (Exception $e) {
    error_log("Error fetching food data: " . $e->getMessage());
}

// Fetch Total Paid
$totalPaid = 0;
try {
    $paidSql = "SELECT SUM(amount_paid) AS total_paid FROM payments WHERE user_id = ?";
    $paidStmt = $conn->prepare($paidSql);
    $paidStmt->bind_param("i", $userId);
    $paidStmt->execute();
    $paidResult = $paidStmt->get_result();
    if ($paidRow = $paidResult->fetch_assoc()) {
        $totalPaid = $paidRow['total_paid'] ?? 0;
    }
    $paidStmt->close();
} catch (Exception $e) {
    error_log("Error fetching payment data: " . $e->getMessage());
}

// Final Calculations
$grandTotal = $totalStayAmount + $foodTotal;
$remainingAmount = max(0, $grandTotal - $totalPaid);

// Format numbers
$formattedRoomPrice = number_format($roomPrice, 2);
$formattedTotalStayAmount = number_format($totalStayAmount, 2);
$formattedFoodTotal = number_format($foodTotal, 2);
$formattedTotalPaid = number_format($totalPaid, 2);
$formattedRemainingAmount = number_format($remainingAmount, 2);
$formattedGrandTotal = number_format($grandTotal, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
      :root {
        --primary: #4B5AE1;
        --primary-light: #5C6DF2;
        --secondary: #3f37c9;
        --success: #22c55e;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --light: #f8f9fa;
        --dark: #111827;
        --gray: #6b7280;
        --gray-light: #e5e7eb;
        --border-radius: 14px;
        --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.06);
        --shadow-md: 0 6px 20px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.12);
        --transition: all 0.25s ease-in-out;
      }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f7fe;
      margin: 0;
      padding: 0;
      color: var(--dark);
      line-height: 1.6;
      overflow-x: hidden;
    }

    .layout-container {
      display: flex;
      min-height: 100vh;
    }

    .main-content {
      margin-left: 260px;
      padding: 30px;
      width: 100%;
      transition: var(--transition);
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid var(--gray-light);
    }

    .welcome-message {
      font-size: 24px;
      font-weight: 600;
      color: var(--dark);
    }

    .date-display {
      font-size: 14px;
      color: var(--gray);
    }

    .profile-section {
      display: flex;
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      color: #212529;
      margin-bottom: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .profile-info {
      flex: 1;
      padding: 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .profile-name {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 15px;
      color:black;
    }

    .profile-detail {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      font-size: 15px;
      font-weight: 500;
      color: #333;
    }

    .profile-detail i {
      margin-right: 10px;
      color: #4361ee;
      font-size: 16px;
    }

    .profile-image {
      width: 220px;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f5f7fb;
    }

    .profile-pic {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #eee;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .card-group {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .card {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-sm);
      padding: 25px;
      transition: var(--transition);
      border-top: 4px solid transparent;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-md);
    }

    .card.primary {
      border-top-color: var(--primary);
    }

    .card.success {
      border-top-color: var(--success);
    }

    .card.warning {
      border-top-color: var(--warning);
    }

    .card.danger {
      border-top-color: var(--danger);
    }

    .card.info {
      border-top-color: var(--info);
    }

    .card-header {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .card-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: white;
    }

    .card-icon.primary {
      background-color: var(--primary);
    }

    .card-icon.success {
      background-color: var(--success);
    }

    .card-icon.warning {
      background-color: var(--warning);
    }

    .card-icon.danger {
      background-color: var(--danger);
    }

    .card-icon.info {
      background-color: var(--info);
    }

    .card-title {
      font-size: 16px;
      font-weight: 500;
      color: var(--gray);
      margin: 0;
    }

    .card-content {
      margin-top: 10px;
    }

    .amount {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .amount.success {
      color: #2ecc71;
    }

    .amount.danger {
      color: #e74c3c;
    }

    .roommate-list {
      list-style: none;
      padding: 0;
    }

    .roommate-item {
      display: flex;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid var(--gray-light);
    }

    .roommate-item:last-child {
      border-bottom: none;
    }

    .roommate-avatar {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background-color: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      margin-right: 10px;
    }

    .action-section {
      margin-top: 30px;
    }

    .action-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--dark);
    }

    .action-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }

    .action-card {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-sm);
      padding: 25px;
      text-align: center;
      transition: var(--transition);
      border-bottom: 4px solid transparent;
    }

    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-md);
    }

    .action-card.book {
      border-bottom-color: var(--primary);
    }

    .action-card.food {
      border-bottom-color: var(--success);
    }

    .action-card.view {
      border-bottom-color: var(--warning);
    }

    .action-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      color: white;
    }

    .action-icon.book {
      background-color: var(--primary);
    }

    .action-icon.food {
      background-color: var(--success);
    }

    .action-icon.view {
      background-color: var(--warning);
    }

    .action-card h3 {
      font-size: 18px;
      margin-bottom: 10px;
      color: var(--dark);
    }

    .action-card p {
      font-size: 14px;
      color: var(--gray);
      margin-bottom: 15px;
    }

    .btn {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      text-decoration: none;
      transition: var(--transition);
      border: none;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--secondary);
      transform: translateY(-2px);
    }

    .btn-success {
      background-color: var(--success);
      color: white;
    }

    .btn-success:hover {
      background-color: #36b3d4;
      transform: translateY(-2px);
    }

    .btn-warning {
      background-color: var(--warning);
      color: white;
    }

    .btn-warning:hover {
      background-color: #e67e00;
      transform: translateY(-2px);
    }

    .summary-section {
      background: linear-gradient(135deg, #2c3e50, #2980b9);
      border-radius: var(--border-radius);
      padding: 25px;
      color: white;
      margin-top: 30px;
      box-shadow: var(--shadow-md);
    }

    .summary-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      padding-bottom: 10px;
    }

    .summary-content {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .summary-item {
      flex: 1;
      min-width: 200px;
    }

    .summary-label {
      font-size: 14px;
      opacity: 0.8;
      margin-bottom: 5px;
    }

    .summary-value {
      font-size: 24px;
      font-weight: 600;
    }

    .summary-value.success {
      color: #2ecc71;
    }

    .summary-value.danger {
      color: #ff7675;
    }

    /* Sidebar styles */
    .sidebar {
      width: 260px;
      background-color: #fff;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      z-index: 100;
      overflow-y: auto;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
      .main-content {
        margin-left: 80px;
      }

      .sidebar {
        width: 80px;
      }

      .profile-section {
        flex-direction: column;
      }

      .profile-image {
        width: 100%;
        padding: 20px 0;
      }
    }

    @media (max-width: 768px) {
      .summary-content {
        flex-direction: column;
        gap: 15px;
      }

      .summary-item {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 15px;
      }

      .summary-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
      }
    }

    @media (max-width: 576px) {
      .main-content {
        padding: 20px 15px;
        margin-left: 0;
      }

      .sidebar {
        left: -260px;
        width: 260px;
      }

      .sidebar.active {
        left: 0;
      }

      .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .date-display {
        margin-top: 5px;
      }

      .card-group, .action-cards {
        grid-template-columns: 1fr;
      }

      .profile-info {
        padding: 20px;
      }
    }

    /* Mobile navigation toggle */
    .nav-toggle {
      display: none;
      position: fixed;
      top: 15px;
      right: 15px;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      font-size: 16px;
      cursor: pointer;
      z-index: 100;
      box-shadow: var(--shadow-md);
    }

    @media (max-width: 576px) {
      .nav-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
      }
    }

    /* Animation */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card, .action-card, .profile-section, .summary-section {
      animation: fadeIn 0.5s ease forwards;
    }

    .card-group .card:nth-child(1) { animation-delay: 0.1s; }
    .card-group .card:nth-child(2) { animation-delay: 0.2s; }
    .card-group .card:nth-child(3) { animation-delay: 0.3s; }
    .card-group .card:nth-child(4) { animation-delay: 0.4s; }

    .action-cards .action-card:nth-child(1) { animation-delay: 0.5s; }
    .action-cards .action-card:nth-child(2) { animation-delay: 0.6s; }
    .action-cards .action-card:nth-child(3) { animation-delay: 0.7s; }
  </style>
</head>
<body>
<div class="layout-container">
  <?php include 'user_navbar.php'; ?>

  <button class="nav-toggle" id="navToggle">
    <i class="fas fa-bars"></i>
  </button>

  <div class="main-content">
    <div class="dashboard-header">
      <div>
        <h1 class="welcome-message">Welcome, <?= htmlspecialchars($name) ?>!</h1>
        <div class="date-display"><?= date('l, d M Y') ?></div>
      </div>
    </div>

    <div class="profile-section">
      <div class="profile-info">
        <!-- <h2 class="profile-name"><?= htmlspecialchars($name) ?></h2> -->
        <div class="profile-details">
          <div class="profile-detail">
            <i class="fas fa-door-open"></i>
            <span>Room: <strong><?= htmlspecialchars($roomNo) ?></strong></span>
          </div>
          <div class="profile-detail">
            <i class="fas fa-calendar-check"></i>
            <span>Check-In: <strong><?= htmlspecialchars($checkIn) ?></strong></span>
          </div>
          <div class="profile-detail">
            <i class="fas fa-clock"></i>
            <span>Days Stayed: <strong><?= $daysStayed ?></strong></span>
          </div>
          <div class="profile-detail">
            <i class="fas fa-money-bill-wave"></i>
            <span>Per Day: <strong>Rs. <?= $formattedRoomPrice ?></strong></span>
          </div>
        </div>
      </div>
      <div class="profile-image">
        <img src="<?= htmlspecialchars($imagePath) ?>" alt="Profile Image" class="profile-pic">
      </div>
    </div>

    <div class="card-group">
      <div class="card primary">
        <div class="card-header">
          <div class="card-icon primary">
            <i class="fas fa-home"></i>
          </div>
          <h3 class="card-title">ROOM DETAILS</h3>
        </div>
        <div class="card-content">
          <div class="amount">Room <?= htmlspecialchars($roomNo) ?></div>
          <p>Rs. <?= $formattedRoomPrice ?> per day</p>
        </div>
      </div>

      <div class="card info">
        <div class="card-header">
          <div class="card-icon info">
            <i class="fas fa-users"></i>
          </div>
          <h3 class="card-title">ROOMMATES</h3>
        </div>
        <div class="card-content">
          <?php if (!empty($roommates)): ?>
            <ul class="roommate-list">
              <?php foreach ($roommates as $r): ?>
                <li class="roommate-item">
                  <div class="roommate-avatar">
                    <?= substr(htmlspecialchars($r), 0, 1) ?>
                  </div>
                  <span><?= htmlspecialchars($r) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>No roommates assigned yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card success">
        <div class="card-header">
          <div class="card-icon success">
            <i class="fas fa-utensils"></i>
          </div>
          <h3 class="card-title">FOOD EXPENSES</h3>
        </div>
        <div class="card-content">
          <div class="amount">Rs. <?= $formattedFoodTotal ?></div>
          <p>Last 30 days</p>
        </div>
      </div>

      <div class="card <?= $remainingAmount == 0 ? 'success' : 'danger' ?>">
        <div class="card-header">
          <div class="card-icon <?= $remainingAmount == 0 ? 'success' : 'danger' ?>">
            <i class="fas fa-wallet"></i>
          </div>
          <h3 class="card-title">PAYMENT STATUS</h3>
        </div>
        <div class="card-content">
          <div class="amount <?= $remainingAmount == 0 ? 'success' : 'danger' ?>">
            Rs. <?= $formattedRemainingAmount ?>
          </div>
          <p><?= $remainingAmount == 0 ? 'All Paid!' : 'Remaining Amount' ?></p>
        </div>
      </div>
    </div>

    <div class="summary-section">
      <h3 class="summary-title">Financial Summary</h3>
      <div class="summary-content">
        <div class="summary-item">
          <div class="summary-label">Stay Amount</div>
          <div class="summary-value">Rs. <?= $formattedTotalStayAmount ?></div>
        </div>
        <div class="summary-item">
          <div class="summary-label">Food Total</div>
          <div class="summary-value">Rs. <?= $formattedFoodTotal ?></div>
        </div>
        <div class="summary-item">
          <div class="summary-label">Total Expenses</div>
          <div class="summary-value">Rs. <?= $formattedGrandTotal ?></div>
        </div>
        <div class="summary-item">
          <div class="summary-label">Total Paid</div>
          <div class="summary-value">Rs. <?= $formattedTotalPaid ?></div>
        </div>
        <div class="summary-item">
          <div class="summary-label">Remaining</div>
          <div class="summary-value <?= $remainingAmount == 0 ? 'success' : 'danger' ?>">
            Rs. <?= $formattedRemainingAmount ?>
          </div>
        </div>
      </div>
    </div>

    <div class="action-section">
      <h3 class="action-title">Quick Actions</h3>
      <div class="action-cards">
        <div class="action-card book">
          <div class="action-icon book">
            <i class="fas fa-bed"></i>
          </div>
          <h3>Book Room</h3>
          <p>Reserve your accommodation with ease</p>
          <a href="book_room.php" class="btn btn-primary">Book Now</a>
        </div>
        <div class="action-card food">
          <div class="action-icon food">
            <i class="fas fa-utensils"></i>
          </div>
          <h3>Order Meals</h3>
          <p>Browse and order delicious meals</p>
          <a href="book_food.php" class="btn btn-success">Order Now</a>
        </div>
        <div class="action-card view">
          <div class="action-icon view">
            <i class="fas fa-list-alt"></i>
          </div>
          <h3>View Bookings</h3>
          <p>Check your room and meal bookings</p>
          <a href="view_bookings.php" class="btn btn-warning">See Bookings</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Mobile navigation toggle
  document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (navToggle && sidebar) {
      navToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
      });
    }
    
    // Add responsive behavior if screen size changes
    window.addEventListener('resize', function() {
      if (window.innerWidth > 576 && sidebar && sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
      }
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
      if (sidebar && 
          sidebar.classList.contains('active') && 
          !sidebar.contains(event.target) && 
          event.target !== navToggle) {
        sidebar.classList.remove('active');
      }
    });
  });
</script>
</body>
</html>