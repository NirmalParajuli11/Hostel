<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include('db/config.php');
$conn = new mysqli('localhost', 'root', '', 'hostel');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// Get Room Booking Info
$bookingSql = "SELECT rb.id as booking_id, rb.checkin_date, r.id as room_id, r.room_price 
               FROM room_bookings rb
               JOIN rooms r ON rb.room_id = r.id
               WHERE rb.user_id = ?";
$stmt = $conn->prepare($bookingSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header("Location: book_room.php?status=error");
    exit();
}

$checkin = new DateTime($booking['checkin_date']);
$today = new DateTime();
$daysStayed = max(1, $checkin->diff($today)->days);
$stayTotal = $daysStayed * $booking['room_price'];

// Get Food Total
$foodSql = "SELECT SUM(meals.price) AS total 
            FROM food_bookings 
            JOIN meals ON food_bookings.meal_id = meals.id 
            WHERE food_bookings.user_id = ? 
              AND booked_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($foodSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$foodResult = $stmt->get_result()->fetch_assoc();
$foodTotal = $foodResult['total'] ?? 0;

// Get Total Paid
$paidSql = "SELECT SUM(amount_paid) AS total_paid FROM payments WHERE user_id = ?";
$stmt = $conn->prepare($paidSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$paidResult = $stmt->get_result()->fetch_assoc();
$totalPaid = $paidResult['total_paid'] ?? 0;

// Final Balance Check
$grandTotal = $stayTotal + $foodTotal;
$remainingAmount = max(0, $grandTotal - $totalPaid);

if ($remainingAmount > 0) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Payment Not Cleared!',
            text: 'Remaining Amount: Rs. " . number_format($remainingAmount, 2) . ". Please clear your dues before changing room.',
            timer: 5000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'book_room.php';
        });
    </script>";
    exit();
}

// ✅ Handle Room Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_room_id'])) {
    $newRoomId = $_POST['new_room_id'];

    // Check if room is available
    $stmt = $conn->prepare("
        SELECT r.id, r.room_type,
            CASE 
                WHEN LOWER(r.room_type) = 'double' THEN 2
                WHEN LOWER(r.room_type) = 'triple' THEN 3
                ELSE 1
            END AS total_beds,
            (SELECT COUNT(*) FROM room_bookings WHERE room_id = r.id) AS occupied_beds
        FROM rooms r WHERE r.id = ?
    ");
    $stmt->bind_param("i", $newRoomId);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();

    if ($room && $room['occupied_beds'] < $room['total_beds']) {
        // Change room
        $stmt = $conn->prepare("UPDATE room_bookings SET room_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $newRoomId, $booking['booking_id']);
        $stmt->execute();

        // Mark old room available
        $conn->query("UPDATE rooms SET status = 'available' WHERE id = " . $booking['room_id']);

        // Mark new room as booked if now full
        $stmt = $conn->prepare("
            SELECT 
                CASE 
                    WHEN LOWER(r.room_type) = 'double' THEN 2
                    WHEN LOWER(r.room_type) = 'triple' THEN 3
                    ELSE 1
                END AS total_beds,
                (SELECT COUNT(*) FROM room_bookings WHERE room_id = r.id) AS occupied_beds
            FROM rooms r WHERE r.id = ?
        ");
        $stmt->bind_param("i", $newRoomId);
        $stmt->execute();
        $updated = $stmt->get_result()->fetch_assoc();

        if ($updated['occupied_beds'] >= $updated['total_beds']) {
            $conn->query("UPDATE rooms SET status = 'booked' WHERE id = $newRoomId");
        }

        header("Location: book_room.php?status=updated");
        exit();
    } else {
        header("Location: book_room.php?status=unavailable");
        exit();
    }
}

// Available Rooms
$rooms = [];
$sql = "SELECT r.*, 
        (SELECT COUNT(*) FROM room_bookings WHERE room_id = r.id) AS occupied 
        FROM rooms r 
        WHERE r.status = 'available'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $row['available_beds'] = $row['total_beds'] - $row['occupied'];
    if ($row['available_beds'] > 0) $rooms[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Change Room</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .container { padding: 40px; max-width: 1100px; margin: auto; }
    .room-card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
    .room-details { flex: 1; }
    .room-details h3 { margin-bottom: 10px; font-size: 20px; color: #4b0082; }
    .room-details p { margin: 0; color: #555; }
    .room-card form { margin-left: 20px; }
    .book-btn { background: #17a2b8; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
    .book-btn:hover { background: #138496; }
    .back-btn { text-align: center; margin-top: 20px; }
    .back-btn a { background: #007bff; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    .back-btn a:hover { background: #0056b3; }
  </style>
</head>
<body>

<?php include 'user_navbar.php'; ?>

<div class="container">
  <h2>Choose a New Room</h2>

  <?php if (count($rooms)): ?>
    <?php foreach ($rooms as $room): ?>
      <div class="room-card">
        <div class="room-details">
          <h3>ROOM NO. <?= htmlspecialchars($room['room_number']) ?> – <?= htmlspecialchars($room['room_type']) ?> Seater</h3>
          <p>Price: Rs. <?= number_format($room['room_price'], 2) ?></p>
          <p>Available Beds: <?= $room['available_beds'] ?> out of <?= $room['total_beds'] ?></p>
        </div>
        <form method="POST" onsubmit="return confirmChange(this);">
          <input type="hidden" name="new_room_id" value="<?= $room['id'] ?>">
          <button type="submit" class="book-btn">Change to This Room</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No available rooms at the moment.</p>
  <?php endif; ?>

  <div class="back-btn">
    <a href="book_room.php">Back to Booking</a>
  </div>
</div>

<script>
function confirmChange(form) {
  event.preventDefault();
  Swal.fire({
    title: 'Are you sure?',
    text: "You are about to change your room.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, change it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) form.submit();
  });
  return false;
}
</script>

</body>
</html>
