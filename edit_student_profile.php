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
$messageType = "";

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
        $message = "Profile updated successfully!";
        $messageType = "success";
    } else {
        $message = "Error updating profile.";
        $messageType = "error";
    }

    $stmt->close();
}

$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
$photoPath = $user['photo'] ? "assets/images/uploads/" . $user['photo'] : "assets/images/profile_bg.jpg";

$conn->close();
include('init_user_data.php');
include('user_navbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student Profile</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4b0082;
            --secondary-color: #6c63ff;
            --accent-color: #f3f0ff;
            --success-color: #38b2ac;
            --error-color: #e53e3e;
            --text-color: #2d3748;
            --light-text: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
            color: var(--text-color);
            margin: 0;
            padding: 0;
            margin-left: 300px;
        }

        .content-container {
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .profile-container {
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .profile-header {
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            position: relative;
        }

        .profile-title {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
        }

        .profile-subtitle {
            margin-top: 0.5rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .profile-content {
            padding: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
            font-size: 0.875rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236c63ff' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        .photo-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-align: center;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: var(--shadow-md);
            margin-bottom: 1rem;
        }

        .photo-upload-btn {
            position: relative;
            overflow: hidden;
            display: inline-block;
            cursor: pointer;
        }

        .photo-upload-btn input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .photo-upload-btn span {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background-color: var(--accent-color);
            color: var(--secondary-color);
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .photo-upload-btn:hover span {
            background-color: rgba(108, 99, 255, 0.2);
        }

        .photo-upload-btn i {
            margin-right: 0.5rem;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            cursor: pointer;
            font-size: 0.9375rem;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #5851db;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background-color: white;
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
            color: var(--secondary-color);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle input {
            padding-right: 2.5rem;
        }

        .password-toggle i {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            color: var(--light-text);
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .password-toggle i:hover {
            color: var(--secondary-color);
        }

        .popup-message {
            position: fixed;
            top: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            z-index: 1000;
            max-width: 350px;
            animation: fadeout 3s forwards;
        }

        .popup-message.success {
            background-color: var(--success-color);
        }

        .popup-message.error {
            background-color: var(--error-color);
        }

        .popup-message i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        @keyframes fadeout {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .profile-container {
                border-radius: 0;
            }
            
            .content-container {
                padding: 0;
            }
            
            .profile-content {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    
<div class="content-container">
    <div class="profile-container">
        <div class="profile-header">
            <h1 class="profile-title">Edit Your Profile</h1>
            <p class="profile-subtitle">Update your personal information</p>
        </div>

        <?php if ($message): ?>
        <div class="popup-message <?= $messageType ?>">
            <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <span><?= $message ?></span>
        </div>
        <?php endif; ?>

        <div class="profile-content">
            <form method="post" enctype="multipart/form-data">
                <div class="photo-upload">
                    <img src="<?= $photoPath ?>" alt="Profile" class="profile-photo">
                    <div class="photo-upload-btn">
                        <span><i class="fas fa-camera"></i> Change Photo</span>
                        <input type="file" name="photo">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="food">Food Preference</label>
                        <select id="food" name="food_preference" class="form-control form-select">
                            <option value="Veg" <?= $user['food_preference'] == 'Veg' ? 'selected' : '' ?>>Vegetarian</option>
                            <option value="Non-Veg" <?= $user['food_preference'] == 'Non-Veg' ? 'selected' : '' ?>>Non-Vegetarian</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="newPassword">Change Password (leave blank to keep current)</label>
                        <div class="password-toggle">
                            <input type="password" id="newPassword" name="new_password" class="form-control" placeholder="Enter new password">
                            <i class="fas fa-eye" id="togglePassword"></i>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="user_dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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