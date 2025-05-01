<!-- user_navbar.php -->
<style>
  /* Navbar Styles */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  header {
    background: #4b0082;
    color: white;
    padding: 16px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  }

  .logo {
    font-size: 1.3rem;
    font-weight: 600;
  }

  .nav-links {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .nav-links a,
  .nav-links form button {
    background: #28a745;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .nav-links a:hover,
  .nav-links form button:hover {
    background: #218838;
  }

  .profile-container {
    position: relative;
  }

  .profile-button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background 0.3s ease;
  }

  .profile-button:hover {
    background-color: #218838;
  }

  .dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 110%;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    min-width: 160px;
    z-index: 1000;
    overflow: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
  }

  .dropdown.show {
    display: block;
    opacity: 1;
    visibility: visible;
  }

  .dropdown a,
  .dropdown form button {
    display: block;
    width: 100%;
    padding: 12px 16px;
    text-align: left;
    background: white;
    border: none;
    font-size: 14px;
    color: #28a745;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.3s ease, color 0.3s ease;
  }

  .dropdown a:hover,
  .dropdown form button:hover {
    background: #e6f9ec;
    color: #1f7e30;
  }
</style>

<header>
  <div class="logo">Saathi Hostel – Student Dashboard</div>
  <div class="nav-links">
  <a href="user_dashboard.php"><i class="fas fa-CASH"></i> Dashboard</a>
    <a href="book_room.php"><i class="fas fa-bed"></i> Room</a>
    <a href="book_food.php"><i class="fas fa-utensils"></i> Food</a>
    <a href="view_bookings.php"><i class="fas fa-list"></i> Bookings</a>

    <div class="profile-container">
      <button class="profile-button" id="profileToggle">
        <i class="fas fa-user"></i> Profile
      </button>
      <div class="dropdown" id="profileDropdown">
        <a href="edit_student_profile.php"><i class="fas fa-user-cog"></i> Edit Profile</a>
        <form action="logout.php" method="POST" style="margin: 0;">
          <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
      </div>
    </div>
  </div>
</header>

<script>
  const profileToggle = document.getElementById("profileToggle");
  const profileDropdown = document.getElementById("profileDropdown");

  profileToggle.addEventListener("click", function (e) {
    e.stopPropagation();
    profileDropdown.classList.toggle("show");
  });

  document.addEventListener("click", function (e) {
    if (!profileDropdown.contains(e.target) && !profileToggle.contains(e.target)) {
      profileDropdown.classList.remove("show");
    }
  });
</script>
