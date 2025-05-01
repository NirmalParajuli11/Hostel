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

// 🧠 Check profile completeness
$userStmt = $conn->prepare("SELECT name, email, phone, address, food_preference FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userData = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

$isProfileIncomplete = (
    empty($userData['name']) ||
    empty($userData['email']) ||
    empty($userData['phone']) ||
    empty($userData['address']) ||
    empty($userData['food_preference'])
);

// 🛏️ Handle Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'])) {
    if ($isProfileIncomplete) {
        header("Location: book_room.php?status=incomplete_profile");
        exit();
    }

    $roomId = $_POST['room_id'];

    // Check if already booked
    $stmt = $conn->prepare("SELECT * FROM room_bookings WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: book_room.php?status=already_booked");
        exit();
    }

    // Check room availability
    $stmt = $conn->prepare("
        SELECT r.id, r.room_type,
               CASE 
                   WHEN LOWER(r.room_type) = 'double' THEN 2
                   WHEN LOWER(r.room_type) = 'triple' THEN 3
                   ELSE 1
               END AS total_beds,
               (SELECT COUNT(*) FROM room_bookings WHERE room_id = r.id) AS occupied_beds
        FROM rooms r
        WHERE r.id = ?
    ");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();

    if (!$room || $room['occupied_beds'] >= $room['total_beds']) {
        header("Location: book_room.php?status=unavailable");
        exit();
    }

    // Book the room
    $stmt = $conn->prepare("INSERT INTO room_bookings (user_id, room_id, checkin_date) VALUES (?, ?, CURDATE())");
    $stmt->bind_param("ii", $userId, $roomId);
    $stmt->execute();

    // Mark room as fully booked if needed
    if ($room['occupied_beds'] + 1 >= $room['total_beds']) {
        $conn->query("UPDATE rooms SET status = 'booked' WHERE id = $roomId");
    }

    header("Location: book_room.php?status=success");
    exit();
}

// 🧠 Fetch existing booking
$booking = null;
$stmt = $conn->prepare("
    SELECT rb.id as booking_id, rb.created_at, rb.checkin_date, r.room_number, r.room_type, r.room_price 
    FROM room_bookings rb
    JOIN rooms r ON rb.room_id = r.id
    WHERE rb.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$alreadyBooked = $booking ? true : false;

// 🧠 Fetch available rooms
$rooms = [];
if (!$alreadyBooked) {
    $sql = "SELECT r.*, 
                   (SELECT COUNT(*) FROM room_bookings WHERE room_id = r.id) AS occupied 
            FROM rooms r 
            WHERE r.status = 'available'";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $row['available_beds'] = $row['total_beds'] - $row['occupied'];
        if ($row['available_beds'] > 0) {
            $rooms[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book a Room</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { padding: 20px; max-width: 1200px; margin: auto; }
    .room-card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
    .room-details { flex: 1; }
    .room-details h3 { margin: 0 0 10px; font-size: 20px; color: #4b0082; }
    .room-details p { margin: 0; }
    .room-card form { margin-left: 20px; }
    .book-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
    .book-btn:hover { background: #218838; }
    .cancel-btn { background: #dc3545; }
    .cancel-btn:hover { background: #c82333; }
    .update-btn { background: #17a2b8; }
    .update-btn:hover { background: #138496; }
    .back-btn { text-align: center; margin-top: 20px; }
    .back-btn a { background: #007bff; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    .back-btn a:hover { background: #0056b3; }
  </style>
</head>

<body>

<?php include 'user_navbar.php'; ?>

<div class="container">

<!-- Success / Error Messages -->
<?php
if (isset($_GET['status'])) {
    $messages = [
        'success' => ['text' => 'Room booked successfully!', 'type' => 'success'],
        'already_booked' => ['text' => 'You already booked a room.', 'type' => 'info'],
        'unavailable' => ['text' => 'Room is no longer available.', 'type' => 'error'],
        'canceled' => ['text' => 'Booking canceled successfully!', 'type' => 'success'],
        'updated' => ['text' => 'Room updated successfully!', 'type' => 'success'],
        'error' => ['text' => 'Something went wrong.', 'type' => 'error'],
        'incomplete_profile' => ['text' => 'Please update your profile before booking.', 'type' => 'warning']
    ];
    $status = $_GET['status'];
    if (array_key_exists($status, $messages)) {
        echo "<script>
            Swal.fire({
                icon: '{$messages[$status]['type']}',
                title: '{$messages[$status]['text']}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>";
    }
}
?>

<?php if ($alreadyBooked): ?>
  <div class="room-card">
    <div class="room-details">
      <h3>You have already booked Room <?= htmlspecialchars($booking['room_number']) ?></h3>
      <p>Booking Time: <?= date('d M Y, h:i A', strtotime($booking['created_at'])) ?></p>
      <p>Check-In Date: <?= date('d M Y', strtotime($booking['checkin_date'])) ?></p>
      <?php
        $checkinDate = new DateTime($booking['checkin_date']);
        $todayDate = new DateTime();
        $interval = $checkinDate->diff($todayDate);
        $daysStayed = max(1, $interval->days);
      ?>
      <p>Days Stayed: <?= $daysStayed ?> <?= ($daysStayed == 1) ? "day" : "days" ?></p>
    </div>
    <div>
      <form id="cancelBookingForm" method="POST" action="cancel_booking.php" style="display:inline;">
        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>">
        <button type="button" onclick="confirmCheckout();" class="book-btn cancel-btn">Check out</button>
      </form>

      <form method="GET" action="change_rooms.php" style="display:inline;">
        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>">
        <button type="submit" class="book-btn update-btn">Change Room</button>
      </form>
    </div>
  </div>

<?php else: ?>
  <h2>Available Rooms</h2>
  <?php if (count($rooms)): ?>
    <?php foreach ($rooms as $room): ?>
      <div class="room-card">
        <div class="room-details">
          <h3>ROOM NO. <?= htmlspecialchars($room['room_number']) ?> – <?= htmlspecialchars($room['room_type'])  ?> Seater</h3>
          <p>Price: Rs. <?= number_format($room['room_price'], 2) ?></p>
          <p>Available Beds: <?= $room['available_beds'] ?> out of <?= $room['total_beds'] ?></p>
        </div>
        <form method="POST">
          <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
          <button type="submit" class="book-btn">Book Now</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No rooms available currently.</p>
  <?php endif; ?>
<?php endif; ?>

<div class="back-btn">
  <a href="user_dashboard.php">Back to Dashboard</a>
</div>

</div>

<script>
function confirmCheckout() {
  Swal.fire({
    title: 'Are you sure?',
    text: "Did you clear your dues before checkout?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, I have cleared!',
    cancelButtonText: 'No, cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('cancelBookingForm').submit();
    }
  });
}
</script>

</body>
</html>
