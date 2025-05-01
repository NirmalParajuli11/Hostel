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
    $theme = $_POST['theme'];
    $dashboard_view = $_POST['dashboard_view'];
    $notifications = isset($_POST['notifications']) ? 1 : 0;

    // Handle photo upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = uniqid("user_") . "." . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], "assets/images/uploads/" . $photo);
    }

    if ($photo) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, food_preference=?, theme=?, dashboard_view=?, notifications_enabled=?, photo=? WHERE id=?");
        $stmt->bind_param("sssssssisi", $name, $email, $phone, $address, $food, $theme, $dashboard_view, $notifications, $photo, $admin_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, food_preference=?, theme=?, dashboard_view=?, notifications_enabled=? WHERE id=?");
        $stmt->bind_param("sssssssii", $name, $email, $phone, $address, $food, $theme, $dashboard_view, $notifications, $admin_id);
    }

    $stmt->execute();
    $stmt->close();

    $_SESSION['success_message'] = " Settings saved successfully! ";
    header("Location: settings.php");
    exit();
}

$sql = "SELECT name, email, phone, address, food_preference, theme, dashboard_view, notifications_enabled, photo FROM users WHERE id = ?";
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
    <title>Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #4b0082, #6a0dad);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 650px;
            animation: fadeIn 0.5s ease;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 30px;
            background: #ff007f;
            color: #fff;
            padding: 12px 18px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            font-weight: bold;
            z-index: 9999;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(50px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        h2 {
            text-align: center;
            color: #4b0082;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="file"],
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #aaa;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .inline-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            color: #333;
        }

        .form-group button {
            background: #4b0082;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            width: 100%;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .form-group button:hover {
            background: #2e005c;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 12px;
            text-decoration: none;
            font-weight: bold;
            color: #4b0082;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

        .current-photo {
            text-align: center;
            margin-bottom: 15px;
        }

        .current-photo img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 3px solid #4b0082;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['success_message'])): ?>
<div class="toast" id="toast">
    <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
</div>
<script>
    setTimeout(() => {
        const toast = document.getElementById("toast");
        if (toast) toast.style.display = "none";
    }, 4000);
</script>
<?php endif; ?>

<div class="form-container">
    <h2><i class="fas fa-cog"></i> Settings</h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($user['photo']): ?>
        <div class="current-photo">
            <img src="assets/images/uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Current Photo">
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="photo">Change Profile Photo</label>
            <input type="file" name="photo" id="photo" accept="image/*">
        </div>

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
                <option value="Veg" <?= $user['food_preference'] === 'Veg' ? 'selected' : '' ?>>Veg</option>
                <option value="Non-Veg" <?= $user['food_preference'] === 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
            </select>
        </div>
        <div class="form-group">
            <label for="theme">Theme</label>
            <select name="theme" id="theme">
                <option value="light" <?= $user['theme'] === 'light' ? 'selected' : '' ?>>Light</option>
                <option value="dark" <?= $user['theme'] === 'dark' ? 'selected' : '' ?>>Dark</option>
            </select>
        </div>
        <div class="form-group">
            <label for="dashboard_view">Default Dashboard View</label>
            <select name="dashboard_view" id="dashboard_view">
                <option value="rooms" <?= $user['dashboard_view'] === 'rooms' ? 'selected' : '' ?>>Rooms</option>
                <option value="students" <?= $user['dashboard_view'] === 'students' ? 'selected' : '' ?>>Students</option>
                <option value="rent" <?= $user['dashboard_view'] === 'rent' ? 'selected' : '' ?>>Rent</option>
            </select>
        </div>
        <div class="form-group inline-checkbox">
            <input type="checkbox" name="notifications" id="notifications" <?= $user['notifications_enabled'] ? 'checked' : '' ?>>
            <label for="notifications">Receive Email Notifications</label>
        </div>
        <div class="form-group">
            <button type="submit"><i class="fas fa-save"></i> Save Settings</button>
        </div>
    </form>
    <a class="back-btn" href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
