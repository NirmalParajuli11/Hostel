<?php
include('db/config.php');

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the room data based on the ID
    $sql = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();

    if (!$room) {
        // If no room is found, redirect to manage rooms page
        header("Location: manage_rooms.php");
        exit();
    }
} else {
    // If no ID is provided, redirect to manage rooms page
    header("Location: manage_rooms.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];

    // Update the room details in the database
    $updateSql = "UPDATE rooms SET room_number = ?, room_type = ?, room_price = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssdi", $room_number, $room_type, $room_price, $id);

    if ($updateStmt->execute()) {
        header("Location: manage_rooms.php");
        exit();
    } else {
        echo "Error: " . $updateStmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room - Saathi Hostel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include('partials/adminnavbar.php'); ?>

<div class="admin-content">
    <div class="card">
        <h2>Edit Room</h2>
        <form method="POST" action="">
            <label for="room_number">Room Number</label>
            <input type="text" id="room_number" name="room_number" value="<?php echo $room['room_number']; ?>" required>

            <label for="room_type">Room Type</label>
            <select id="room_type" name="room_type" required>
                <option value="single" <?php if ($room['room_type'] == 'single') echo 'selected'; ?>>Single</option>
                <option value="double" <?php if ($room['room_type'] == 'double') echo 'selected'; ?>>Double</option>
                <option value="triple" <?php if ($room['room_type'] == 'triple') echo 'selected'; ?>>Triple</option>
            </select>

            <label for="room_price">Room Price (Per Day)</label>
            <input type="number" id="room_price" name="room_price" value="<?php echo $room['room_price']; ?>" required>

            <button type="submit">Update Room</button>
        </form>
    </div>
</div>

</body>
</html>
