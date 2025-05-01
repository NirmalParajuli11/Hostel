<?php 
session_start();
include('partials/navbar.php');
include('partials/header.php');
include('db/config.php');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'hostel');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🏠 Fetch available rooms
$availableRooms = [];
$roomQuery = "
    SELECT r.id, r.room_number, r.room_type, r.room_price,
           CASE 
               WHEN LOWER(r.room_type) = 'double' THEN 2
               WHEN LOWER(r.room_type) = 'triple' THEN 3
               ELSE 1
           END AS total_beds,
           (SELECT COUNT(*) FROM room_bookings WHERE room_id = r.id) AS occupied_beds
    FROM rooms r
    WHERE r.status = 'available'
";
$result = $conn->query($roomQuery);
while ($row = $result->fetch_assoc()) {
    if ($row['occupied_beds'] < $row['total_beds']) {
        $availableRooms[] = $row;
    }
}
?>

<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f9f9ff;
    color: #333;
  }

  .main-content {
    padding: 60px 20px;
  }

  .features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto 60px;
  }

  .feature {
    background: white;
    padding: 30px 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease;
  }

  .feature:hover {
    transform: translateY(-5px);
  }

  .feature h2 {
    font-size: 1.4rem;
    color: #4b0082;
    margin-bottom: 10px;
  }

  .feature p {
    font-size: 0.95rem;
    color: #555;
  }

  /* Available Rooms Slider */
  .rooms-section {
    max-width: 1200px;
    margin: 0 auto 60px;
    padding: 20px;
    text-align: center;
  }

  .rooms-section h2 {
    font-size: 2rem;
    color: #4b0082;
    margin-bottom: 20px;
  }

  .slider-wrapper {
    position: relative;
    overflow: hidden;
  }

  .slider-track {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 10px;
  }

  .slider-track::-webkit-scrollbar {
    display: none;
  }

  .room-card {
    flex: 0 0 230px;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
  }

  .room-card h3 {
    font-size: 1.2rem;
    color: #333;
  }

  .room-card p {
    margin: 8px 0;
    font-size: 0.9rem;
    color: #666;
  }

  .book-btn {
    background-color: #4b0082;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
  }

  .book-btn:hover {
    background-color: #360062;
  }

  .slider-btn {
    position: absolute;
    top: 40%;
    transform: translateY(-50%);
    background: #4b0082;
    color: #fff;
    border: none;
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 1;
  }

  .slider-btn:hover {
    background: #360062;
  }

  .slider-btn.left {
    left: 0;
  }

  .slider-btn.right {
    right: 0;
  }

  @media (max-width: 768px) {
    .room-card { flex: 0 0 70%; }
  }

  @media (max-width: 480px) {
    .room-card { flex: 0 0 100%; }
  }

  .about-section, .menu-section {
    text-align: center;
    max-width: 1000px;
    margin: 60px auto;
    padding: 20px;
  }

  .about-section h2, .menu-section h2 {
    font-size: 2rem;
    color: #4b0082;
    margin-bottom: 15px;
  }

  .about-section p, .menu-section p {
    font-size: 1rem;
    color: #444;
    line-height: 1.6;
  }

  .menu-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  .menu-table th, .menu-table td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
  }

  .menu-table th {
    background-color: #4b0082;
    color: white;
  }

  .menu-table tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  .testimonial-section {
    background: #fff;
    padding: 60px 20px;
    text-align: center;
  }

  .testimonial-section h2 {
    font-size: 2rem;
    color: #4b0082;
    margin-bottom: 30px;
  }

  .testimonial {
  background-color: #fafafa;
  border-left: 4px solid #4b0082;
  padding: 15px 20px;
  margin: 20px auto;
  max-width: 700px;
  border-radius: 8px;
  font-size: 1rem;
  color: #333;
  text-align: left;
}

</style>

<main class="main-content">


<!-- 🛠 Services Heading -->
<section style="text-align:center; margin-bottom: 20px;">
    <h2 style="font-size: 2rem; color: #4b0082;">Our Services</h2>
  </section>

  <!-- 🛏 Features -->
  <section class="features">
    <div class="feature"><h2>🛏 Comfortable Rooms</h2><p>Spacious and fully furnished rooms with attached bathrooms.</p></div>
    <div class="feature"><h2>📶 Free Wi-Fi</h2><p>High-speed internet to keep you connected 24/7.</p></div>
    <div class="feature"><h2>🔒 24/7 Security</h2><p>Secure environment with surveillance and support staff.</p></div>
    <div class="feature"><h2>🍽 Healthy Meals</h2><p>Nutritious and delicious food served daily.</p></div>
  </section>

  <!-- 🚪 Available Rooms -->
  <section class="rooms-section">
    <h2>Available Rooms</h2>
    <?php if (count($availableRooms) > 0): ?>
      <div class="slider-wrapper">
        <button class="slider-btn left" onclick="slide(-1)">&#10094;</button>
        <div class="slider-track" id="sliderTrack">
          <?php foreach ($availableRooms as $room): ?>
            <div class="room-card">
              <h3>Room No: <?php echo htmlspecialchars($room['room_number']); ?></h3>
              <p>Type: <?php echo ucfirst(htmlspecialchars($room['room_type'])); ?></p>
              <p>Price: Rs.<?php echo htmlspecialchars($room['room_price']); ?> per day</p>
              <form method="POST" action="book_room.php">
                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                <button class="book-btn" type="submit">Book Now</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="slider-btn right" onclick="slide(1)">&#10095;</button>
      </div>
    <?php else: ?>
      <p>No rooms available at the moment. Please check back later!</p>
    <?php endif; ?>
  </section>

  <!-- ℹ️ About -->
  <section class="about-section">
    <h2>Why Choose Saathi Hostel?</h2>
    <p>Located in the heart of the city, Saathi Hostel provides a perfect blend of comfort, security, and community. Whether you're a student, a professional, or a traveler, our hostel ensures that you have a place you can truly call home.</p>
  </section>

  <!-- 🍽 Menu -->
  <section class="menu-section">
    <h2>🍛 Weekly Menu</h2>
    <p>Our meals are healthy, hygienic, and full of taste! Here's a glimpse of what we serve throughout the week:</p>
    <table class="menu-table">
      <thead><tr><th>Day</th><th>Breakfast</th><th>Lunch</th><th>Dinner</th></tr></thead>
      <tbody>
        <tr><td>Monday</td><td>Poha & Tea</td><td>Rice, Dal, Veg Curry</td><td>Chapati, Paneer Butter Masala</td></tr>
        <tr><td>Tuesday</td><td>Sandwich & Juice</td><td>Rice, Rajma, Salad</td><td>Chapati, Mixed Veg Curry</td></tr>
        <tr><td>Wednesday</td><td>Idli & Sambar</td><td>Fried Rice, Manchurian</td><td>Chapati, Chana Masala</td></tr>
        <tr><td>Thursday</td><td>Paratha & Curd</td><td>Rice, Chicken Curry</td><td>Chapati, Aloo Gobhi</td></tr>
        <tr><td>Friday</td><td>Upma & Coffee</td><td>Rice, Dal Tadka, Mix Veg</td><td>Chapati, Mutter Paneer</td></tr>
        <tr><td>Saturday</td><td>Chowmein</td><td>Rice, Egg Curry</td><td>Chapati, Bhindi Fry</td></tr>
        <tr><td>Sunday</td><td>Special Breakfast</td><td>Biriyani Special</td><td>Chapati, Seasonal Veg Curry</td></tr>
      </tbody>
    </table>
  </section>

 <!-- 💬 Testimonials -->
<!-- 💬 Testimonials -->
<section class="testimonial-section">
  <h2>What Our Residents Say</h2>
  <?php
    // Fetch average rating
    $avgSql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews";
    $avgResult = $conn->query($avgSql);
    if ($avgRow = $avgResult->fetch_assoc()):
      $avgRating = round($avgRow['avg_rating'], 1);
      $filled = floor($avgRating);
      $half = ($avgRating - $filled >= 0.5);
      $empty = 5 - $filled - ($half ? 1 : 0);
  ?>
    <div style="font-size: 1.2rem; margin-bottom: 25px;">
      <strong>Average Rating:</strong>
      <span style="color: gold;">
        <?= str_repeat("★", $filled) ?><?= $half ? "½" : "" ?><?= str_repeat("☆", $empty) ?>
      </span>
      <span style="color: #555;">(<?= $avgRating ?>/5 from <?= $avgRow['total_reviews'] ?> reviews)</span>
    </div>
  <?php endif; ?>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; max-width: 1000px; margin: auto;">
    <?php
      $reviewSql = "
        SELECT r.rating, r.comment, r.created_at, u.name, u.photo 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC 
        LIMIT 6";
      $reviewResult = $conn->query($reviewSql);
      if ($reviewResult && $reviewResult->num_rows > 0):
        while ($review = $reviewResult->fetch_assoc()):
          $stars = str_repeat("⭐", $review['rating']);
          $comment = htmlspecialchars($review['comment']);
          $name = htmlspecialchars($review['name']);
          $photo = !empty($review['photo']) ? 'assets/images/uploads/' . $review['photo'] : 'assets/images/profile_bg.jpg';
          $date = date("d M Y", strtotime($review['created_at']));
    ?>
      <div style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; background: #fff;">
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
          <img src="<?= $photo ?>" alt="User Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
          <div>
            <strong><?= $name ?></strong><br>
            <span style="font-size: 0.8rem; color: #777;"><?= $date ?></span>
          </div>
        </div>
        <p style="font-size: 0.95rem; margin: 10px 0;">“<?= $comment ?>”</p>
        <div style="color: gold;"><?= $stars ?></div>
      </div>
    <?php endwhile; else: ?>
      <p class="testimonial">No reviews yet. Be the first to share your thoughts!</p>
    <?php endif; ?>
  </div>
</section>


</main>

<script>
  function slide(direction) {
    const slider = document.getElementById("sliderTrack");
    const scrollAmount = slider.offsetWidth;
    const currentScroll = slider.scrollLeft;
    const newScroll = direction > 0 ? currentScroll + scrollAmount : currentScroll - scrollAmount;
    slider.scrollTo({ left: newScroll, behavior: 'smooth' });
  }
</script>

<?php include('partials/footer.php'); ?>
