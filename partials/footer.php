<style>
  .footer {
    background-color: #4b0082;
    color: white;
    padding: 50px 20px;
    font-size: 0.95rem;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    margin-top: 40px;
  }

  .footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .footer-column h4 {
    margin-bottom: 15px;
    font-size: 1.2rem;
    color: #f0dfff;
  }

  .footer-column p,
  .footer-column a {
    color: #ccc;
    font-size: 0.9rem;
    text-decoration: none;
    margin-bottom: 10px;
    display: block;
    transition: color 0.3s ease;
  }

  .footer-column a:hover {
    color: white;
  }

  .social-icons a {
    color: #ccc;
    margin-right: 10px;
    font-size: 1.3rem;
    transition: color 0.3s ease;
  }

  .social-icons a:hover {
    color: white;
  }

  .footer-bottom {
    text-align: center;
    margin-top: 30px;
    font-size: 0.85rem;
    color: #ccc;
  }

  .footer-bottom a {
    color: #f0dfff;
    text-decoration: underline;
  }
</style>

<footer class="footer">
  <div class="footer-container">
    
    <!-- About Saathi Hostel -->
    <div class="footer-column">
      <h4>About Saathi Hostel</h4>
      <p>Saathi Hostel provides a safe, comfortable, and affordable home-away-from-home experience. Perfect for students, professionals, and travelers.</p>
    </div>
    
    <!-- Quick Links -->
    <div class="footer-column">
      <h4>Quick Links</h4>
      <a href="index.php">Home</a>
      <a href="about.php">About Us</a>
      <a href="login.php">Login</a>
     
    </div>
    
    <!-- Contact & Social Media -->
    <div class="footer-column">
      <h4>Contact Us</h4>
      <p>📍 Damak, Jhapa, Nepal</p>
      <p>📞 +977-9824057085</p>
      <p>📧 support@saathihostel.com</p>
      <!-- <div class="social-icons" style="margin-top: 10px;">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
      </div> -->
    </div>

  </div>

  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> Saathi Hostel. All rights reserved.</p>
    <p>Need help? <a href="mailto:support@saathihostel.com">Contact Support</a></p>
  </div>
</footer>
