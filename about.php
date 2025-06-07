<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - Saathi Hostel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5f5;
      color: #333;
    }

    .custom-message {
      text-align: center;
      background-color: #4b0082;
      color: #fff;
      padding: 1rem;
      font-size: 1.2rem;
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(rgba(75,0,130,0.75), rgba(75,0,130,0.75)), url('assets/images/uploads/aboutus.jpeg') center/cover no-repeat;
      text-align: center;
      padding: 100px 20px;
      color: white;
    }
    .hero h1 {
      font-size: 3rem;
      margin-bottom: 0.5rem;
    }
    .hero p {
      font-size: 1.2rem;
      max-width: 700px;
      margin: auto;
    }

    /* About Section */
    .about-section {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
      background: #fff;
    }
    .about-content {
      flex: 1;
      padding: 20px;
      max-width: 600px;
    }
    .about-content h2 {
      color: #4b0082;
      font-size: 2.5rem;
      margin-bottom: 20px;
    }
    .about-content p {
      line-height: 1.6;
      margin-bottom: 1rem;
    }
    .about-img {
      flex: 1;
      max-width: 500px;
      padding: 20px;
    }
    .about-img img {
      width: 100%;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Contact Section */
    .contact-section {
      background: #fff;
      padding: 60px 20px;
      text-align: center;
    }
    .contact-section h2 {
      font-size: 2.5rem;
      color: #4b0082;
      margin-bottom: 20px;
    }
    .contact-details {
      max-width: 800px;
      margin: 0 auto;
      line-height: 1.8;
      font-size: 1rem;
    }
    .contact-details p {
      margin: 10px 0;
    }
    .contact-details i {
      margin-right: 8px;
      color: #4b0082;
    }

    /* Map Section */
    .map-section {
      padding: 0 20px 60px;
      text-align: center;
    }
    .map-section iframe {
      width: 100%;
      height: 400px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Footer */
    .footer {
      text-align: center;
      background: #4b0082;
      color: #fff;
      padding: 1rem;
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .about-section {
        flex-direction: column;
        text-align: center;
      }
      .about-content, .about-img {
        max-width: 100%;
        padding: 10px;
      }
    }
  </style>
</head>
<body>

<?php include('partials/navbar.php'); ?>

  <!-- 
  <header class="navbar">
    <div class="logo">SAATHI HOSTEL</div>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    </div>
  </header>
  -->

  <section class="hero">
    <h1>Welcome to Saathi Hostel</h1>
    <p>Your home away from home. Experience comfort and community in the heart of the city.</p>
  </section>

  <section class="about-section">
    <div class="about-content">
      <h2>About Us</h2>
      <p>Located in the heart of the city, Saathi Hostel offers a perfect blend of comfort, security, and community. Whether you're a student, professional, or traveler, our hostel ensures you have a place you can truly call home.</p>
      <p>Our mission is to provide a safe and welcoming environment where individuals from all walks of life can come together and create lasting memories. With 24/7 security, clean and cozy rooms, nutritious meals, and a supportive atmosphere, Saathi Hostel is more than just a place to stay—it’s a place to belong.</p>
    </div>
    <div class="about-img">
      <img src="assets/images/uploads/aboutus.jpeg" alt="Saathi Hostel">
    </div>
  </section>

  <section class="contact-section">
    <h2>Contact Us</h2>
    <div class="contact-details">
      <p><i class="fas fa-map-marker-alt"></i> Damak, Jhapa, Nepal</p>
      <p><i class="fas fa-phone"></i> +977-9800000000</p>
      <p><i class="fas fa-envelope"></i> support@saathihostel.com</p>
      <p><i class="fas fa-globe"></i> www.saathihostel.com</p>
    </div>
  </section>

  <section class="map-section">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.1956604977956!2d87.69969937450465!3d26.6635583768246!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ef41e8f04d5f69%3A0x69a31fa8d1fc2a3f!2sDamak%2C%20Jhapa!5e0!3m2!1sen!2snp!4v1714361257740!5m2!1sen!2snp" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </section>

</body>
</html>
<?php include('partials/footer.php'); ?>
