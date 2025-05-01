<?php include('db/config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Saathi Hostel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f5f7fa;
      font-family: Arial, sans-serif;
    }

    .register-container {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #e3eafc, #fcebe8);
      padding: 40px 20px;
    }

    .register-box {
      background: white;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      max-width: 480px;
      width: 100%;
    }

    .register-box h2 {
      text-align: center;
      color: #4b0082;
      margin-bottom: 25px;
    }

    .register-box form {
      display: flex;
      flex-direction: column;
    }

    .register-box input,
    .register-box select {
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    .register-box label {
      margin: 8px 0 4px;
      font-size: 0.9rem;
      font-weight: bold;
      color: #333;
    }

    .register-box input[type="submit"] {
      background: #4b0082;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .register-box input[type="submit"]:hover {
      background: #350065;
    }

    .login-link {
      text-align: center;
      margin-top: 20px;
    }

    .login-link a {
      color: #4b0082;
      text-decoration: none;
      font-weight: bold;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    /* Password input group styling */
    .password-wrapper {
      position: relative;
    }

    .password-wrapper i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #4b0082;
      cursor: pointer;
    }
  </style>
</head>
<body>

<?php include('partials/navbar.php'); ?>
<?php include('partials/error-popup.php'); ?>
<?php include('partials/success-popup.php'); ?>

<div class="register-container">
  <div class="register-box">
    <h2>Create Your Account</h2>
    <form method="POST" action="register_action.php" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="phone" placeholder="Phone Number" required>
      <input type="text" name="address" placeholder="Permanent Address" required>

      <div class="password-wrapper">
        <input type="password" name="password" id="passwordInput" placeholder="Password" required>
        <i class="fas fa-eye" id="togglePassword"></i>
      </div>

      <label>Are you Vegetarian or Non-Vegetarian?</label>
      <select name="food_preference" required>
        <option value="">-- Select Option --</option>
        <option value="Veg">Vegetarian</option>
        <option value="Non-Veg">Non-Vegetarian</option>
      </select>

      <label>Upload Photo (for safety purpose)</label>
      <input type="file" name="photo" required>

      <input type="submit" value="Sign Up">
    </form>
    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</div>

<?php include('partials/footer.php'); ?>

<!-- 🔐 Password Show/Hide Script -->
<script>
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("passwordInput");

  togglePassword.addEventListener("click", function () {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });
</script>

</body>
</html>
