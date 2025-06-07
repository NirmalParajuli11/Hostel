<style>
  .sidebar {
    width: 260px;
    background: linear-gradient(135deg, #4361ee, #3f37c9);
    color: white;
    padding: 1.5rem 0;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    z-index: 100;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }
  
  .sidebar::-webkit-scrollbar {
    width: 5px;
  }
  
  .sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
  }
  
  .sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
  }
  
  .sidebar-header {
    text-align: center;
    margin-bottom: 1.5rem;
    padding: 0 1rem;
  }
  
  .sidebar-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }
  
  .logo-icon {
    width: 35px;
    height: 35px;
    background-color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4361ee;
    font-size: 20px;
  }
  
  .sidebar-header h2 {
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
    letter-spacing: 0.5px;
  }
  
  .profile-section {
    text-align: center;
    margin: 0 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 1.2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
  }
  
  .profile-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 40px;
    background: rgba(255, 255, 255, 0.05);
    z-index: 0;
  }
  
  .profile-pic-wrapper {
    position: relative;
    width: 90px;
    height: 90px;
    margin: 0 auto 12px;
  }
  
  .profile-pic {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
  }
  
  .profile-pic:hover {
    transform: scale(1.05);
    border-color: white;
  }
  
  .profile-edit-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 26px;
    height: 26px;
    background: #4cc9f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: all 0.2s ease;
    border: 2px solid white;
  }
  
  .profile-edit-btn:hover {
    background: #3db1d5;
    transform: scale(1.1);
  }
  
  .profile-name {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.3rem;
    color: white;
  }
  
  .profile-room {
    font-size: 0.85rem;
    opacity: 0.9;
    background: rgba(255, 255, 255, 0.15);
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-block;
    margin-top: 5px;
  }
  
  .nav-menu {
    list-style: none;
    padding: 0 1rem;
    margin: 0;
  }
  
  .nav-item {
    margin-bottom: 0.5rem;
    position: relative;
  }
  
  .nav-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 0;
    background: rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
  }
  
  .nav-link:hover::before {
    width: 100%;
  }
  
  .nav-link:hover,
  .nav-link.active {
    background-color: rgba(255, 255, 255, 0.15);
    transform: translateX(5px);
  }
  
  .nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
  }
  
  .nav-link.active::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: white;
    border-radius: 0 3px 3px 0;
  }
  
  .nav-link i {
    margin-right: 12px;
    font-size: 1.1rem;
    width: 22px;
    text-align: center;
    transition: all 0.3s ease;
  }
  
  .nav-link span {
    font-weight: 500;
    font-size: 0.95rem;
    letter-spacing: 0.3px;
  }
  
  .menu-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 15px 0;
  }
  
  .sidebar-footer {
    padding: 15px;
    text-align: center;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 20px;
  }
  
  .logout-btn {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: white;
    padding: 10px;
    transition: all 0.3s ease;
  }
  
  .logout-btn:hover {
    background: rgba(247, 37, 133, 0.7);
  }
  
  /* Mobile Styles */
  @media (max-width: 992px) {
    .sidebar {
      width: 80px;
      padding: 1rem 0;
      transform: translateX(0);
    }
    
    .sidebar.active {
      width: 260px;
      transform: translateX(0);
    }
    
    .logo-icon {
      margin: 0 auto;
    }
    
    .sidebar-header h2,
    .profile-name,
    .profile-room,
    .nav-link span,
    .sidebar-footer {
      opacity: 0;
      display: none;
      transition: all 0.3s 0.1s ease;
    }
    
    .sidebar.active .sidebar-header h2,
    .sidebar.active .profile-name,
    .sidebar.active .profile-room,
    .sidebar.active .nav-link span,
    .sidebar.active .sidebar-footer {
      opacity: 1;
      display: block;
    }
    
    .profile-pic-wrapper {
      width: 50px;
      height: 50px;
    }
    
    .profile-pic {
      width: 50px;
      height: 50px;
    }
    
    .profile-edit-btn {
      width: 20px;
      height: 20px;
      font-size: 10px;
    }
    
    .sidebar.active .profile-pic-wrapper {
      width: 90px;
      height: 90px;
    }
    
    .sidebar.active .profile-pic {
      width: 90px;
      height: 90px;
    }
    
    .sidebar.active .profile-edit-btn {
      width: 26px;
      height: 26px;
      font-size: 12px;
    }
    
    .nav-link {
      justify-content: center;
      padding: 15px;
    }
    
    .nav-link i {
      margin: 0;
    }
    
    .sidebar.active .nav-link {
      justify-content: flex-start;
      padding: 12px 16px;
    }
    
    .sidebar.active .nav-link i {
      margin-right: 12px;
    }
  }
  
  @media (max-width: 576px) {
    .sidebar {
      transform: translateX(-80px);
    }
    
    .sidebar.active {
      transform: translateX(0);
      width: 260px;
    }
  }
  
  /* Animations */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
  }
  
  .nav-item {
    animation: fadeIn 0.3s forwards;
  }
  
  .nav-item:nth-child(1) { animation-delay: 0.1s; }
  .nav-item:nth-child(2) { animation-delay: 0.2s; }
  .nav-item:nth-child(3) { animation-delay: 0.3s; }
  .nav-item:nth-child(4) { animation-delay: 0.4s; }
  .nav-item:nth-child(5) { animation-delay: 0.5s; }
  .nav-item:nth-child(6) { animation-delay: 0.6s; }
  .nav-item:nth-child(7) { animation-delay: 0.7s; }
</style>

<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">
      <div class="logo-icon">
        <i class="fas fa-home"></i>
      </div>
      <h2>Saathi Hostel</h2>
      
    </div>
  </div>
  
  <div class="profile-section">
    <div class="profile-pic-wrapper">
      <img src="<?= htmlspecialchars($imagePath) ?>" alt="Profile Image" class="profile-pic">
      <a href="edit_student_profile.php" class="profile-edit-btn" title="Edit Profile">
        <i class="fas fa-pencil-alt"></i>
      </a>
    </div>
    <div class="profile-details">
  <div class="profile-name"><?= htmlspecialchars($name) ?></div>
  <div class="profile-room">
    <i class="fas fa-door-open"></i> Room: <?= htmlspecialchars($roomNo) ?>
  </div>
</div>
  </div>
  
  <ul class="nav-menu">
    <li class="nav-item">
      <a href="user_dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'user_dashboard.php' ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="book_room.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'book_room.php' ? 'active' : '' ?>">
        <i class="fas fa-door-open"></i><span>Book Room</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="book_food.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'book_food.php' ? 'active' : '' ?>">
        <i class="fas fa-utensils"></i><span>Order Meals</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="view_bookings.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'view_bookings.php' ? 'active' : '' ?>">
        <i class="fas fa-calendar-check"></i><span>My Bookings</span>
      </a>
    </li>
    
    <div class="menu-divider"></div>
    
    <!-- <li class="nav-item">
      <a href="payments.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'payments.php' ? 'active' : '' ?>">
        <i class="fas fa-money-bill-wave"></i><span>Payments</span>
      </a>
    </li> -->
    <li class="nav-item">
      <a href="edit_student_profile.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'edit_student_profile.php' ? 'active' : '' ?>">
        <i class="fas fa-user-circle"></i><span>Profile</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="logout.php" class="nav-link logout-btn">
        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
      </a>
    </li>
  </ul>
  
  <div class="sidebar-footer">
    &copy; 2025 Saathi Hostel System
  </div>
</div>

<script>
  // Mobile sidebar toggle functionality
  document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (navToggle && sidebar) {
      navToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
      });
    }
    
    // Close sidebar when clicking on a link (mobile)
    const navLinks = document.querySelectorAll('.nav-link');
    if (window.innerWidth <= 576) {
      navLinks.forEach(link => {
        link.addEventListener('click', function() {
          sidebar.classList.remove('active');
        });
      });
    }
    
    // Close sidebar when clicking outside (mobile)
    document.addEventListener('click', function(event) {
      if (window.innerWidth <= 576 && sidebar.classList.contains('active')) {
        if (!sidebar.contains(event.target) && event.target !== navToggle) {
          sidebar.classList.remove('active');
        }
      }
    });
    
    // Highlight current page
    const currentPage = window.location.pathname.split("/").pop();
    const navItems = document.querySelectorAll('.nav-link');
    
    navItems.forEach(item => {
      const href = item.getAttribute('href');
      if (href === currentPage) {
        item.classList.add('active');
      }
    });
  });
</script>