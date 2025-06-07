<?php
session_start();
include('db/config.php');
include('partials/adminnavbar.php');

if (!isset($_GET['room_id'])) {
    die("Invalid room ID.");
}

$roomId = intval($_GET['room_id']);
$students = [];

// Fetch all current bookings for this room
$sql = "SELECT u.name AS student_name, u.email, u.phone, u.photo, b.checkin_date
        FROM users u
        JOIN room_bookings b ON u.id = b.user_id
        WHERE b.room_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 0px;
        }
        .student-card {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .student-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .student-info {
            flex: 1;
        }
        .student-info strong {
            display: block;
            font-size: 1.1em;
            color: #333;
        }
        .back-btn {
            margin-bottom: 20px;
            display: inline-block;
            padding: 8px 12px;
            background: #6c5ce7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>


<h2>Students in Room <?= htmlspecialchars($roomId) ?></h2>

<?php if (count($students)): ?>
    <?php foreach ($students as $student): ?>
        <div class="student-card">
            <img src="<?= !empty($student['photo']) ? 'assets/images/uploads/' . $student['photo'] : 'assets/images/profile_bg.jpg' ?>" alt="Photo">
            <div class="student-info">
                <strong><?= htmlspecialchars($student['student_name']) ?></strong>
                Email: <?= htmlspecialchars($student['email']) ?><br>
                Phone: <?= htmlspecialchars($student['phone']) ?><br>
                Check-In: <?= htmlspecialchars($student['checkin_date']) ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No students currently assigned to this room.</p>
<?php endif; ?>

<a href="manage_rooms.php" class="back-btn">← Back to Manage Rooms</a>

</body>
</html>
