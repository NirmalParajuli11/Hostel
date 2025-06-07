<?php
session_start();
include('db/config.php');

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get student ID from URL
if (!isset($_GET['id'])) {
    die("No student ID provided.");
}
$id = intval($_GET['id']);

// Fetch student info
$student = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

// ✅ Check if student has a booking
$hasBooking = false;
$bookingCheck = $conn->query("SELECT id FROM room_bookings WHERE user_id = $id LIMIT 1");
if ($bookingCheck && $bookingCheck->num_rows > 0) {
    $hasBooking = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET name = ?, email = ?, status = ?, password = ? WHERE id = ?");
        $update->bind_param("ssssi", $name, $email, $status, $hashedPassword, $id);
    } else {
        $update = $conn->prepare("UPDATE users SET name = ?, email = ?, status = ? WHERE id = ?");
        $update->bind_param("sssi", $name, $email, $status, $id);
    }

    $update->execute();
    header("Location: manage_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student - Saathi Hostel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            padding: 40px;
        }

        .form-card {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4b0082;
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            padding-right: 44px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            background-color: #f9f9f9;
        }

        select:disabled {
            background-color: #eee;
            cursor: not-allowed;
        }

        .password-wrapper {
            position: relative;
            margin-top: 5px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.2rem;
            color: #666;
            user-select: none;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #4b0082;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #6a0dad;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            font-weight: bold;
            color: #4b0082;
        }

        .note {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>

<div class="form-card">
    <h2>Edit Student</h2>
    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

        <label>Status</label>
        <select name="status" <?= $hasBooking ? 'disabled' : '' ?>>
            <option value="approved" <?= $student['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="pending" <?= $student['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        </select>
        <?php if ($hasBooking): ?>
            <small class="note">Status change disabled: student has booked a room.</small>
        <?php endif; ?>

        <label>Change Password <span class="note">(Leave blank to keep current)</span></label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="New password (optional)">
            <span class="toggle-password" onclick="togglePassword()">👁</span>
        </div>

        <button type="submit">Update Student</button>
    </form>

    <a class="back-link" href="manage_students.php">← Back to Manage Students</a>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.querySelector('.toggle-password');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = '🔒';
        } else {
            input.type = 'password';
            icon.textContent = '👁';
        }
    }
</script>

</body>
</html>
