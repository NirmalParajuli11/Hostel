<?php
session_start();
include('db/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Saathi Hostel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('assets/images/hostel2.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(75, 0, 130, 0.6);
      z-index: -1;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 80px; /* Shifted below navbar */
    }

    .login-wrapper-centered {
      backdrop-filter: blur(12px);
      background: rgba(255, 255, 255, 0.08);
      padding: 50px 30px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
      max-width: 420px;
      width: 100%;
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-card-small {
      background: rgba(255, 255, 255, 0.12);
      padding: 60px 30px 40px;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.35);
      position: relative;
      text-align: center;
    }

    .login-avatar-sm {
      position: absolute;
      top: -60px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: linear-gradient(145deg, #6a0dad, #4b0082);
      padding: 5px;
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-avatar-sm img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
    }

    .input-wrapper-sm {
      display: flex;
      align-items: center;
      background: #f9f9fc;
      border: 1px solid #ccc;
      border-radius: 10px;
      margin-bottom: 20px;
      padding: 12px 15px;
      transition: 0.3s;
      position: relative;
    }

    .input-wrapper-sm:hover {
      border-color: #8e2be2;
      box-shadow: 0 0 5px #8e2be2;
    }

    .input-wrapper-sm i {
      color: #4b0082;
      font-size: 1rem;
      margin-right: 12px;
    }

    .input-wrapper-sm input {
      border: none;
      background: transparent;
      outline: none;
      font-size: 1rem;
      color: #333;
      width: 100%;
    }

    .input-wrapper-sm input::placeholder {
      color: #999;
    }

    #togglePassword {
      position: absolute;
      right: 15px;
      cursor: pointer;
    }

    .login-options-sm {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.85rem;
      margin-bottom: 20px;
      color: #eee;
    }

    .login-options-sm a {
      color: #d2bfff;
      text-decoration: none;
    }

    .login-options-sm a:hover {
      text-decoration: underline;
    }

    .login-submit-sm {
      width: 100%;
      padding: 12px;
      background: #4b0082;
      color: white;
      font-size: 1rem;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s ease;
      box-shadow: 0 4px 12px rgba(75, 0, 130, 0.4);
    }

    .login-submit-sm:hover {
      background: #6a0dad;
      box-shadow: 0 4px 16px rgba(75, 0, 130, 0.6);
    }

    .back-home {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #d2bfff;
      font-weight: bold;
      font-size: 0.95rem;
    }

    .back-home:hover {
      text-decoration: underline;
    }

    #popup-message {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 16px;
      color: white;
      z-index: 9999;
      animation: fadein 0.5s, fadeout 0.5s 1.5s;
    }

    @keyframes fadein {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes fadeout {
      from { opacity: 1; }
      to { opacity: 0; }
    }
  </style>
</head>
<body>

<?php include('partials/navbar.php'); ?>

<?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
  <div id="popup-message" style="background-color: <?= isset($_GET['error']) ? '#dc3545' : '#28a745' ?>;">
    <?php
      if (isset($_GET['error'])) {
          if ($_GET['error'] == 'invalid_password') echo "Incorrect Password!";
          elseif ($_GET['error'] == 'user_not_found') echo "User Not Found!";
          elseif ($_GET['error'] == 'pending_approval') echo "Your account is pending wait for approval.";
      } elseif (isset($_GET['success']) && $_GET['success'] == 'logged_out') {
          echo "Logged Out Successfully!";
      }
    ?>
  </div>
  <script>
    setTimeout(function() {
      var popup = document.getElementById('popup-message');
      if (popup) {
        popup.style.opacity = '0';
        setTimeout(function() { popup.style.display = 'none'; }, 500);
      }
    }, 2000);
  </script>
<?php endif; ?>

<main>
  <div class="login-wrapper-centered">
    <div class="login-card-small">
      <div class="login-avatar-sm">
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="User Icon" />
      </div>
      <form method="POST" action="login_action.php">
        <div class="input-wrapper-sm">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="Email ID" required />
        </div>
        <div class="input-wrapper-sm">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" id="passwordInput" placeholder="Password" required />
          <i class="fas fa-eye" id="togglePassword" style="color: #4b0082;"></i>
        </div>
        <div class="login-options-sm">
          <label><input type="checkbox" /> Remember me</label>
          <a href="#">Forgot Password?</a>
        </div>
        <button type="submit" class="login-submit-sm">LOGIN</button>
      </form>
      <a href="index.php" class="back-home">← Back to Homepage</a>
    </div>
  </div>
</main>

<?php include('partials/footer.php'); ?>

<!-- 🔐 Show Password Toggle Script -->
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
