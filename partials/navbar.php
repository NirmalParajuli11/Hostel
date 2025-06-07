<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Saathi Hostel - Enhanced Navbar</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background-color: #f5f5f5;
    }
    
    .navbar {
      background-color: #4b0082;
      padding: 0 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: all 0.3s ease;
      height: 70px;
    }
    
    .navbar.scrolled {
      height: 60px;
      background-color: rgba(75, 0, 130, 0.95);
      backdrop-filter: blur(10px);
    }
    
    .navbar .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: white;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      cursor: pointer;
      display: flex;
      align-items: center;
    }
    
    .logo-icon {
      margin-right: 10px;
      font-size: 1.5rem;
    }
    
    .navbar .nav-links {
      display: flex;
      gap: 5px;
      align-items: center;
    }
    
    .navbar .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      padding: 10px 16px;
      border-radius: 6px;
      transition: all 0.3s ease;
      position: relative;
    }
    
    .navbar .nav-links a:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }
    
    .navbar .nav-links a.active {
      background-color: rgba(255, 255, 255, 0.2);
    }
    
    /* Dropdown styles */
    .dropdown {
      position: relative;
      display: inline-block;
    }
    
    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      min-width: 200px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      border-radius: 8px;
      z-index: 1;
      top: 45px;
      right: 0;
      overflow: hidden;
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.3s, transform 0.3s;
    }
    
    .dropdown:hover .dropdown-content {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }
    
    .dropdown-content a {
      color: #333 !important;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      transition: background-color 0.3s;
      border-bottom: 1px solid #f1f1f1;
    }
    
    .dropdown-content a:last-child {
      border-bottom: none;
    }
    
    .dropdown-content a:hover {
      background-color: #f9f9f9;
    }
    
    /* Button styles */
    .primary-btn {
      background-color: #ff6b6b;
      color: white;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }
    
    .primary-btn:hover {
      background-color: #ff5252;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(255, 107, 107, 0.3);
    }
    
    /* Mobile menu toggle */
    .menu-toggle {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
    }
    
    /* Search box */
    .search-box {
      display: flex;
      align-items: center;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 50px;
      padding: 5px 15px;
      margin-right: 15px;
      transition: all 0.3s ease;
    }
    
    .search-box:focus-within {
      background: rgba(255, 255, 255, 0.25);
      box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
    }
    
    .search-box input {
      background: transparent;
      border: none;
      color: white;
      padding: 8px 5px;
      width: 150px;
      outline: none;
    }
    
    .search-box input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }
    
    .search-box button {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
    }
    
    /* Notification badge */
    .notification {
      position: relative;
    }
    
    .notification-badge {
      position: absolute;
      top: 0;
      right: 5px;
      background-color: #ff6b6b;
      color: white;
      font-size: 0.7rem;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* Media queries for responsiveness */
    @media (max-width: 1024px) {
      .navbar {
        padding: 0 20px;
      }
      
      .search-box {
        display: none;
      }
    }
    
    @media (max-width: 768px) {
      .navbar {
        padding: 0 20px;
      }
      
      .menu-toggle {
        display: block;
      }
      
      .nav-links {
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background-color: #4b0082;
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(-150%);
        transition: transform 0.3s ease-in-out;
        z-index: -1;
      }
      
      .nav-links.active {
        transform: translateY(0);
      }
      
      .navbar .nav-links a {
        width: 100%;
        padding: 12px;
      }
      
      .dropdown-content {
        position: static;
        box-shadow: none;
        background-color: rgba(255, 255, 255, 0.1);
        width: 100%;
        margin-top: 10px;
        display: none;
        opacity: 1;
        transform: none;
      }
      
      .dropdown-content a {
        color: white !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      .dropdown.mobile-active .dropdown-content {
        display: block;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="logo" onclick="location.href='index.php'">
      <span class="logo-icon">🏠</span>Saathi Hostel
    </div>
    
    <button class="menu-toggle" id="menuToggle">☰</button>
    
    <div class="nav-links" id="navLinks">      
      <a href="index.php">Home</a>
      <a href="about.php">About Us</a>
      <a href="contact.php" class="">Contact Us</a>
      <a href="login.php" ><i class="fas fa-user" style="margin-right: 6px;"></i>Account</a>
      
      </div>
      
      

  </nav>

  

  <script>
    // Toggle mobile menu
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');
    
    menuToggle.addEventListener('click', () => {
      navLinks.classList.toggle('active');
    });
    
    // Dropdown functionality for mobile
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
      const dropdownLink = dropdown.querySelector('a');
      
      dropdownLink.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
          e.preventDefault();
          dropdown.classList.toggle('mobile-active');
        }
      });
    });
    
    // Navbar scroll effect
    window.addEventListener('scroll', () => {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  </script>
</body>
</html>