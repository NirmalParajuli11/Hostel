<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Student - Saathi Hostel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f5f7fa;
      font-family: Arial, sans-serif;
    }

    .create-container {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #e3eafc, #fcebe8);
      padding: 40px 20px;
    }

    .create-box {
      background: white;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      max-width: 480px;
      width: 100%;
    }

    .create-box h2 {
      text-align: center;
      color: #4b0082;
      margin-bottom: 25px;
    }

    .create-box form {
      display: flex;
      flex-direction: column;
    }

    .create-box input,
    .create-box select {
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    .create-box label {
      margin: 8px 0 4px;
      font-size: 0.9rem;
      font-weight: bold;
      color: #333;
    }

    .create-box input[type="submit"] {
      background: #4b0082;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .create-box input[type="submit"]:hover {
      background: #350065;
    }

    .back-link {
      text-align: center;
      margin-top: 20px;
    }

    .back-link a {
      color: #4b0082;
      text-decoration: none;
      font-weight: bold;
    }

    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<?php include('partials/adminnavbar.php'); ?>

<div class="create-container">
  <div class="create-box">
    <h2>Create New Student Account</h2>
    <form action="create_student_action.php" method="POST" enctype="multipart/form-data">
      
      <input type="text" name="student_name" placeholder="Full Name" required>
      <input type="email" name="student_email" placeholder="Email Address" required>
      <input type="text" name="student_phone" placeholder="Phone Number" required>
      <input type="text" name="student_address" placeholder="Permanent Address" required>
      <input type="password" name="student_password" placeholder="Password" required>

      <label>Are you Vegetarian or Non-Vegetarian?</label>
      <select name="student_food_preference" required>
        <option value="">-- Select Option --</option>
        <option value="Veg">Vegetarian</option>
        <option value="Non-Veg">Non-Vegetarian</option>
      </select>

      <label>Upload Photo (for safety purpose)</label>
      <img src="<?= $photoPath ?>" alt="Profile"><br>
        <input type="file" name="photo">
      <input type="submit" value="Create Student">
    </form>

    
  </div>
</div>

</body>
</html>
