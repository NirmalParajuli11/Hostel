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

// Cancel logic with message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booked_at'])) {
    $bookedAt = $_POST['booked_at'];
    $message = $_POST['message'] ?? '';

    // Only delete if status is still pending
    $check = $conn->prepare("SELECT status, meal_id FROM food_bookings WHERE user_id = ? AND booked_at = ?");
    $check->bind_param("is", $userId, $bookedAt);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['status'] !== 'approved') {
        // Get meal details for notification
        $mealStmt = $conn->prepare("SELECT meal_name FROM meals WHERE id = ?");
        $mealStmt->bind_param("i", $row['meal_id']);
        $mealStmt->execute();
        $mealResult = $mealStmt->get_result();
        $meal = $mealResult->fetch_assoc();

        // Delete the booking
        $stmt = $conn->prepare("DELETE FROM food_bookings WHERE user_id = ? AND booked_at = ?");
        $stmt->bind_param("is", $userId, $bookedAt);
        if ($stmt->execute()) {
            // Send notification to admin
            $to = "admin@hostel.com"; // Replace with actual admin email
            $subject = "Food Order Cancelled";
            $message = "A student has cancelled their order for " . $meal['meal_name'] . ". Reason: " . $message;
            $headers = "From: hostel@example.com";
            mail($to, $subject, $message, $headers);
        }
        $stmt->close();
    }

    $check->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=cancelled");
    exit();
}

// Room booking
$bookingQuery = $conn->query("
    SELECT rooms.room_number, rooms.room_type, room_bookings.checkin_date
    FROM room_bookings
    JOIN rooms ON room_bookings.room_id = rooms.id
    WHERE room_bookings.user_id = $userId
    ORDER BY room_bookings.checkin_date DESC
    LIMIT 1
");
$booking = $bookingQuery->fetch_assoc();

// Food booking
$foodQuery = $conn->query("
    SELECT meals.meal_name, meals.category, meals.price, food_bookings.booked_at, food_bookings.status
    FROM food_bookings
    JOIN meals ON food_bookings.meal_id = meals.id
    WHERE food_bookings.user_id = $userId
    ORDER BY food_bookings.booked_at DESC
");

include('init_user_data.php');
include('user_navbar.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0px;
      margin-left: 295px;
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
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
      text-align: center;
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

    .cancel-btn {
      background: #e74c3c;
      color: white;
      padding: 7px 15px;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      cursor: pointer;
      transition: 0.3s;
    }

    .cancel-btn:hover {
      background-color: #c0392b;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .cancel-btn i {
      margin-right: 5px;
    }

    .approved-badge {
      color: green;
      font-weight: 600;
    }

    .status {
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: bold;
    }

    .pending {
      background: #ffeaa7;
      color: #d35400;
    }

    .approved {
      background: #dff9fb;
      color: #27ae60;
    }

    .declined {
      background: #ffd8d8;
      color: #e74c3c;
    }
  </style>
</head>
<body>

  <h2>📋 My Booking Details</h2>

  <div class="card">
    <h3>🏠 Room Booking</h3>
    <?php if ($booking): ?>
      <p><strong>Room No:</strong> <?= htmlspecialchars($booking['room_number']) ?></p>
      <p><strong>Room Type:</strong> <?= htmlspecialchars($booking['room_type']) ?></p>
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
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php while ($row = $foodQuery->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['meal_name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td>Rs. <?= number_format($row['price'], 2) ?></td>
            <td><?= date('d M Y, h:i A', strtotime($row['booked_at'])) ?></td>
            <td>
              <span class="status <?= $row['status'] ?>">
                <?= ucfirst($row['status']) ?>
              </span>
            </td>
            <td>
              <?php if ($row['status'] === 'approved'): ?>
                <span class="approved-badge"><i class="fas fa-check-circle"></i> Order Accepted</span>
              <?php else: ?>
                <button onclick="cancelOrder('<?= $row['booked_at'] ?>')" class="cancel-btn">
                  <i class="fas fa-times-circle"></i> Cancel
                </button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p class="no-data">No food bookings found.</p>
    <?php endif; ?>
  </div>

  <a href="user_dashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

  <script>
  function cancelOrder(bookedAt) {
    Swal.fire({
      title: 'Cancel Order',
      text: 'Please provide a reason for cancelling this order:',
      input: 'text',
      inputPlaceholder: 'Enter reason for cancellation',
      showCancelButton: true,
      confirmButtonColor: '#e74c3c',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Cancel Order',
      inputValidator: (value) => {
        if (!value) {
          return 'Please provide a reason!';
        }
      }
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="booked_at" value="${bookedAt}">
          <input type="hidden" name="message" value="${result.value}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  <?php
  if (isset($_GET['status']) && $_GET['status'] === 'cancelled') {
    echo "Swal.fire({
      icon: 'success',
      title: 'Order Cancelled!',
      text: 'Your order has been cancelled successfully.',
      showConfirmButton: false,
      timer: 2000
    });";
  }
  ?>
  </script>

</body>
</html>
