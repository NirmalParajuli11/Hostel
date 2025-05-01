<?php 
session_start();
include('db/config.php');

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize
$popup_message = "";
$popup_type = "";

// Handle form POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];

    $total_beds = 1;
    if ($room_type === 'double') $total_beds = 2;
    elseif ($room_type === 'triple') $total_beds = 3;

    $stmt_check = $conn->prepare("SELECT * FROM rooms WHERE room_number = ?");
    $stmt_check->bind_param("s", $room_number);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        $popup_message = "Room number '$room_number' already exists.";
        $popup_type = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type, room_price, total_beds) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $room_number, $room_type, $room_price, $total_beds);
        if ($stmt->execute()) {
            $popup_message = "Room created successfully!";
            $popup_type = "success";
        } else {
            $popup_message = "Error: " . $stmt->error;
            $popup_type = "error";
        }
        $stmt->close();
    }
    $stmt_check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Room - Saathi Hostel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background-color: #f5f7fa; }
    .topnav {
      background-color: #4b0082; color: white;
      padding: 15px 30px;
      display: flex; justify-content: space-between; align-items: center;
    }
    .topnav h1 { margin: 0; font-size: 1.5rem; }
    .topnav .nav-actions {
      display: flex; gap: 15px; align-items: center;
    }
    .topnav .nav-actions span { font-weight: bold; }
    .topnav .nav-actions a {
      background: white; color: #4b0082;
      padding: 8px 16px; border-radius: 8px;
      text-decoration: none; font-weight: bold;
    }
    .topnav .nav-actions a:hover { background: #eee; }

    .admin-content {
      display: flex; justify-content: center; padding: 40px 20px;
    }
    .card {
      background: white; padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      max-width: 500px; width: 100%;
    }
    .card h2 { color: #4b0082; text-align: center; margin-bottom: 30px; }
    .card label { font-weight: bold; display: block; margin-bottom: 6px; }
    .card input, .card select {
      width: 100%; padding: 12px; margin-bottom: 20px;
      border: 1px solid #ccc; border-radius: 8px;
      background: #f9f9f9;
    }
    .card button {
      width: 100%; padding: 12px;
      background-color: #4b0082; color: white;
      font-weight: bold; font-size: 1.1rem;
      border-radius: 8px; border: none;
      cursor: pointer; transition: 0.3s ease;
    }
    .card button:hover { background-color: #6a0dad; }

    /* Popup */
    .popup-message {
      position: fixed;
      top: 20px; right: 20px;
      background: #4b0082; color: white;
      padding: 14px 20px; border-radius: 8px;
      font-weight: bold; opacity: 0;
      transition: 0.5s ease;
      z-index: 1000;
    }
    .popup-message.success { background: #28a745; }
    .popup-message.error { background: #dc3545; }
  </style>
</head>
<body>
<?php include('partials/adminnavbar.php'); ?> 


<div class="admin-content">
  <div class="card">
    <h2>New Room Details</h2>
    <form method="POST" action="">
      <label for="room_number">Room Number</label>
      <input type="text" id="room_number" name="room_number" required>

      <label for="room_type">Room Type</label>
      <select id="room_type" name="room_type" required>
        <option value="single">Single</option>
        <option value="double">Double</option>
        <option value="triple">Triple</option>
      </select>

      <label for="room_price">Price Per Day</label>
      <input type="number" id="room_price" name="room_price" required>

      <button type="submit">Create Room</button>
    </form>
  </div>
</div>

<?php if (!empty($popup_message)): ?>
<div id="popup" class="popup-message <?= $popup_type ?>">
  <?= htmlspecialchars($popup_message) ?>
</div>
<script>
window.onload = function() {
  const popup = document.getElementById('popup');
  popup.style.opacity = '1';
  setTimeout(() => {
    popup.style.opacity = '0';
    window.location.href = "manage_rooms.php"; // ✅ Redirect to dashboard after popup
  }, 1000);
};
</script>
<?php endif; ?>

</body>
</html>
