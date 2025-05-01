<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hostel");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$message = "";

// 🔄 Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $food = $_POST['food_preference'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $photo = '';

    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "assets/images/uploads/";
        $fileName = basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
        $photo = $fileName;
    }

    // Start building SQL
    $sql = "UPDATE users SET name=?, email=?, phone=?, address=?, food_preference=?";
    $params = [$name, $email, $phone, $address, $food];
    $types = "sssss";

    if ($photo) {
        $sql .= ", photo=?";
        $params[] = $photo;
        $types .= "s";
    }

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql .= ", password=?";
        $params[] = $hashedPassword;
        $types .= "s";
    }

    $sql .= " WHERE id=?";
    $params[] = $user_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $message = "✅ Profile updated successfully!";
    } else {
        $message = "❌ Error updating profile.";
    }
    $stmt->close();
}

// 🔍 Fetch current data
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
$photoPath = $user['photo'] ? "assets/images/uploads/" . $user['photo'] : "assets/images/profile_bg.jpg";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 50px;
            display: flex;
            justify-content: center;
        }

        .profile-form {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .profile-form h2 {
            margin-bottom: 20px;
            color: #4b0082;
        }

        label {
            margin: 12px 0 5px;
            display: block;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .profile-form img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn:hover {
            background: #218838;
        }

        .message {
            margin-top: 10px;
            color: green;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            color: #4b0082;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .back-btn {
            margin-top: 20px;
            text-align: center;
        }

        .back-btn a {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper i {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: #4b0082;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="profile-form">
    <h2>Edit Your Profile</h2>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>">

        <label>Food Preference</label>
        <select name="food_preference">
            <option value="Veg" <?= $user['food_preference'] == 'Veg' ? 'selected' : '' ?>>Veg</option>
            <option value="Non-Veg" <?= $user['food_preference'] == 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
        </select>

        <label>Profile Image</label><br>
        <img src="<?= $photoPath ?>" alt="Profile"><br>
        <input type="file" name="photo">

        <label>Change Password (optional)</label>
        <div class="password-wrapper">
            <input type="password" name="new_password" id="newPassword" placeholder="New Password">
            <i class="fas fa-eye" id="togglePassword"></i>
        </div>

        <button type="submit" class="btn">Update Profile</button>
    </form>

    <div class="back-btn">
        <a href="user_dashboard.php"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<!-- 👁️ Show/Hide Password Script -->
<script>
    const togglePassword = document.getElementById("togglePassword");
    const newPassword = document.getElementById("newPassword");

    togglePassword.addEventListener("click", function () {
        const type = newPassword.getAttribute("type") === "password" ? "text" : "password";
        newPassword.setAttribute("type", type);
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });
</script>

</body>
</html>
