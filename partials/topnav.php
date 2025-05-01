<nav class="admin-nav">
    <div class="nav-left">
        <h1>Saathi Hostel</h1>
    </div>
    <div class="nav-right">
        <span><?php echo $_SESSION['user_name']; ?> (<?php echo ucfirst($_SESSION['user_role']); ?>)</span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>
