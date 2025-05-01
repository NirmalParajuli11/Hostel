<style>
  .navbar {
    background-color: #4b0082;
    padding: 16px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    position: relative;
    z-index: 10;
  }

  .navbar .logo {
    font-size: 1.6rem;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer; /* Make it clickable */
  }

  .navbar .nav-links {
    display: flex;
    gap: 20px;
  }

  .navbar .nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 6px;
    transition: background-color 0.3s ease;
  }

  .navbar .nav-links a:hover {
    background-color: rgba(255, 255, 255, 0.15);
  }

  @media (max-width: 768px) {
    .navbar {
      flex-direction: column;
      align-items: flex-start;
    }

    .navbar .nav-links {
      flex-direction: column;
      width: 100%;
      margin-top: 10px;
    }

    .navbar .nav-links a {
      width: 100%;
      padding: 10px;
    }
  }
</style>

<div class="navbar">
  <div class="logo" onclick="location.href='index.php'">Saathi Hostel</div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
  </div>
</div>
