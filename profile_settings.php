<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $food = $_POST['food_preference'];

    $photo_name = $_FILES['photo']['name'] ?? '';
    $photo_tmp = $_FILES['photo']['tmp_name'] ?? '';

    if ($photo_name && $photo_tmp) {
        $upload_dir = "assets/images/uploads/";
        $upload_path = $upload_dir . basename($photo_name);
        move_uploaded_file($photo_tmp, $upload_path);

        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, food_preference=?, photo=? WHERE id=?");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $food, $photo_name, $admin_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, food_preference=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $address, $food, $admin_id);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    exit();
}

$sql = "SELECT name, email, phone, address, food_preference, photo FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #6a0dad, #4b0082);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .form-container h2 {
            text-align: center;
            color: #4b0082;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #aaa;
            border-radius: 8px;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background: #6a0dad;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }

        .form-group button:hover {
            background: #4b0082;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Profile</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address" value="<?= htmlspecialchars($user['address']) ?>" required>
        </div>
        <div class="form-group">
            <label for="food_preference">Food Preference</label>
            <select name="food_preference" id="food_preference">
                <option value="Veg" <?= $user['food_preference'] == 'Veg' ? 'selected' : '' ?>>Veg</option>
                <option value="Non-Veg" <?= $user['food_preference'] == 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
            </select>
        </div>
        <div class="form-group">
            <label for="photo">Profile Photo</label>
            <input type="file" name="photo" id="photo" accept="image/*">
            <?php if ($user['photo']): ?>
                <p>Current: <img src="assets/images/uploads/<?= htmlspecialchars($user['photo']) ?>" width="50" height="50" style="border-radius:50%"></p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <button type="submit">Save Changes</button>
        </div>
    </form>
</div>

</body>
</html>
