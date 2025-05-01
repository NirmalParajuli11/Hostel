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
$sql = "SELECT name, email, role, created_at, phone, address, food_preference, photo FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$photoPath = $user['photo'] 
    ? "assets/images/uploads/" . htmlspecialchars($user['photo']) 
    : "assets/images/ho1.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('assets/images/nirmal1.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(6px);
            z-index: -1;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            padding: 30px 40px;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .profile-card img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 4px solid #fff;
            object-fit: cover;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .profile-card img:hover {
            transform: scale(1.05);
        }

        .profile-card h2 {
            margin-bottom: 10px;
            font-size: 1.9rem;
            font-weight: bold;
        }

        .profile-card p {
            margin: 8px 0;
            font-size: 1.05rem;
        }

        .profile-card p strong {
            color: #ffffff;
        }

        .btn {
            display: inline-block;
            margin: 14px 8px 0;
            padding: 10px 22px;
            background: #6a0dad;
            border-radius: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #8e2be2;
        }

        @media (max-width: 500px) {
            .profile-card {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="profile-card">
    <img src="<?= $photoPath ?>" alt="Profile Picture">
    <h2><?= htmlspecialchars($user['name'] ?: 'User') ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
    <p><strong>Food Preference:</strong> <?= htmlspecialchars($user['food_preference']) ?></p>
    <p><strong>Role:</strong> <?= ucfirst(htmlspecialchars($user['role'])) ?></p>
    <p><strong>Joined:</strong> <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
    
    <!--  Corrected Edit Profile Link -->
    <a href="profile_settings.php" class="btn"><i class="fas fa-cog"></i> Edit Profile</a>
    <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
