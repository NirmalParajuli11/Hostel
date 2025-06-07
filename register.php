<?php 
include('db/config.php'); 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Saathi Hostel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
      margin-top: 80px;
    }

    .register-wrapper-centered {
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

    .register-card-small {
      background: rgba(255, 255, 255, 0.12);
      padding: 60px 30px 40px;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.35);
      position: relative;
      text-align: center;
    }

    .register-avatar-sm {
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

    .register-avatar-sm img {
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

    .register-submit-sm {
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

    .register-submit-sm:hover {
      background: #6a0dad;
      box-shadow: 0 4px 16px rgba(75, 0, 130, 0.6);
    }

    .login-link {
      display: inline-block;
      margin-top: 20px;
      font-size: 0.95rem;
      font-weight: bold;
      padding: 10px 20px;      
      color: white;
      text-decoration: none;
      transition: 0.3s ease;
    }

    .login-link:hover {      
      transform: scale(1.05);
    }

    .error {
      color: #ff6b6b;
      font-size: 0.85rem;
      margin-top: -15px;
      margin-bottom: 10px;
      display: none;
      text-align: left;
      padding-left: 15px;
    }

    #toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #2c3e50;
      color: #fff;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      display: none;
      font-size: 14px;
      z-index: 9999;
    }
  </style>
</head>
<body>
  <?php include('partials/navbar.php'); ?>

  <div id="toast"></div>

  <main>
    <div class="register-wrapper-centered">
      <div class="register-card-small">
        <div class="register-avatar-sm">
          <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="User Icon" />
        </div>
        <form method="POST" action="register_action.php" id="registerForm">
          <div class="input-wrapper-sm">
            <i class="fas fa-user"></i>
            <input type="text" name="name" id="name" placeholder="Full Name" required>
          </div>
          <div id="nameError" class="error">Use capital letters for each name part (e.g., John Doe)</div>

          <div class="input-wrapper-sm">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder="Enter Gmail address" required>
          </div>
          <div id="emailError" class="error">Please enter a valid Gmail address (example@gmail.com)</div>

          <div class="input-wrapper-sm">
            <i class="fas fa-phone"></i>
            <input type="text" name="phone" id="phone" placeholder="Phone Number (10 digits)" required>
          </div>
          <div id="phoneError" class="error">Phone number must be exactly 10 digits</div>

          <div class="input-wrapper-sm">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <i class="fas fa-eye" id="togglePassword"></i>
          </div>

          <button type="submit" class="register-submit-sm">SIGN UP</button>
        </form>

        <a href="login.php" class="login-link">Already have an account? Login</a>
      </div>
    </div>
  </main>

  <script>
    // Toast message function
    function showToast(message) {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.style.display = "block";
      setTimeout(() => {
        toast.style.display = "none";
      }, 5000);
    }

    // Show message from URL params (registered or error)
    window.onload = function () {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('registered')) {
        showToast("Registration successful! Please wait for admin approval.");
      }
      if (urlParams.has('error')) {
        const error = urlParams.get('error');
        showToast("Error: " + decodeURIComponent(error));
      }
    };

    const form = document.getElementById("registerForm");
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phone");
    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    const phoneError = document.getElementById("phoneError");
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    // Strict Gmail validation function
    function validateGmail(email) {
      // Convert to lowercase for consistent checking
      email = email.toLowerCase();
      
      // Must end with @gmail.com
      if (!email.endsWith('@gmail.com')) {
        return false;
      }

      // Get the username part (before @gmail.com)
      const username = email.split('@')[0];
      
      // Username requirements:
      // 1. Must be between 6 and 30 characters
      // 2. Can only contain letters, numbers, dots, and underscores
      // 3. Cannot start or end with a dot
      // 4. Cannot have consecutive dots
      const usernameRegex = /^[a-z0-9][a-z0-9._]{4,28}[a-z0-9]$/;
      
      return usernameRegex.test(username);
    }

    // Real-time Gmail validation
    emailInput.addEventListener('input', function() {
      const email = this.value.trim();
      
      if (email === '') {
        emailError.style.display = "none";
        return;
      }

      if (!email.toLowerCase().endsWith('@gmail.com')) {
        emailError.textContent = "Email must end with @gmail.com";
        emailError.style.display = "block";
      } else if (!validateGmail(email)) {
        emailError.textContent = "Invalid Gmail format. Use letters, numbers, dots, or underscores";
        emailError.style.display = "block";
      } else {
        emailError.style.display = "none";
      }
    });

    // Password toggle
    togglePassword.addEventListener("click", () => {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      togglePassword.classList.toggle("fa-eye");
      togglePassword.classList.toggle("fa-eye-slash");
    });

    // Form validation with toast and delay
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const nameRegex = /^([A-Z][a-z]+\s?)+$/;
      const phoneRegex = /^\d{10}$/;
      let valid = true;

      if (!nameRegex.test(nameInput.value.trim())) {
        nameError.style.display = "block";
        valid = false;
      } else {
        nameError.style.display = "none";
      }

      const email = emailInput.value.trim();
      if (!validateGmail(email)) {
        if (!email.toLowerCase().endsWith('@gmail.com')) {
          emailError.textContent = "Email must end with @gmail.com";
        } else {
          emailError.textContent = "Invalid Gmail format. Use letters, numbers, dots, or underscores";
        }
        emailError.style.display = "block";
        valid = false;
      } else {
        emailError.style.display = "none";
      }

      if (!phoneRegex.test(phoneInput.value.trim())) {
        phoneError.style.display = "block";
        valid = false;
      } else {
        phoneError.style.display = "none";
      }

      if (valid) {
        showToast("Submitting your registration... Please wait.");
        setTimeout(() => {
          form.submit();
        }, 5000);
      }
    });
  </script>

  <?php include('partials/footer.php'); ?>
</body>
</html>
