<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - Saathi Hostel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5f5;
      color: #333;
    }

    .hero {
      background: linear-gradient(rgba(75,0,130,0.75), rgba(75,0,130,0.75)), url('assets/images/uploads/contact.jpeg') center/cover no-repeat;
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

    .contact-info-section, .contact-form-section, .map-section, .faq-section {
      padding: 60px 20px;
      text-align: center;
      background-color: #fff;
    }

    .contact-info-section h2,
    .contact-form-section h2,
    .map-section h2,
    .faq-section h2 {
      font-size: 2.5rem;
      color: #4b0082;
      margin-bottom: 40px;
    }

    .contact-cards {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .contact-card {
      flex: 1;
      min-width: 250px;
      background: #fff;
      padding: 30px 20px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .contact-card:hover {
      transform: translateY(-10px);
    }

    .contact-card i {
      font-size: 2.5rem;
      color: #4b0082;
      margin-bottom: 20px;
    }

    .contact-card h3 {
      font-size: 1.3rem;
      margin-bottom: 15px;
      color: #4b0082;
    }

    .contact-card p {
      line-height: 1.6;
    }

    .contact-form {
      max-width: 700px;
      margin: 0 auto;
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-family: inherit;
      font-size: 1rem;
    }

    .form-group textarea {
      height: 150px;
      resize: vertical;
    }

    .submit-btn {
      background-color: #4b0082;
      color: white;
      border: none;
      padding: 14px 28px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      width: 100%;
    }

    .submit-btn:hover {
      background-color: #3a0065;
    }

    .map-section iframe {
      width: 100%;
      height: 450px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      max-width: 1200px;
      margin: 0 auto;
    }

    .faq-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .faq-item {
      background: #fff;
      margin-bottom: 15px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .faq-question {
      padding: 18px 20px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .faq-question:hover {
      background-color: #f5f5f5;
    }

    .faq-question i {
      color: #4b0082;
      transition: transform 0.3s ease;
    }

    .faq-answer {
      padding: 0 20px;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }

    .faq-answer p {
      padding-bottom: 20px;
      line-height: 1.6;
    }

    .faq-item.active .faq-answer {
      max-height: 200px;
    }

    .faq-item.active .faq-question i {
      transform: rotate(180deg);
    }

    @media (max-width: 768px) {
      .contact-card {
        min-width: 100%;
      }
      .contact-form {
        padding: 20px;
      }
      .hero h1 {
        font-size: 2.2rem;
      }
    }
  </style>
</head>
<body>
<?php include('partials/navbar.php'); ?>
  <section class="hero">
    <h1>Contact Us</h1>
    <p>We're here to help! Reach out to us with any questions, feedback, or inquiries.</p>
  </section>

  <section class="contact-info-section">
    <h2>Get In Touch</h2>
    <div class="contact-cards">
      <div class="contact-card">
        <i class="fas fa-map-marker-alt"></i>
        <h3>Visit Us</h3>
        <p>Damak, Jhapa, Nepal</p>
      
      </div>
      <div class="contact-card">
        <i class="fas fa-phone"></i>
        <h3>Call Us</h3>
        <p>+977-9800000000</p>
        <p>Mon-Fri, 9am–5pm</p>
      </div>
      <div class="contact-card">
        <i class="fas fa-envelope"></i>
        <h3>Email Us</h3>
        <p>support@saathihostel.com</p>
        <p>We’ll respond within 24 hours</p>
      </div>
    </div>
  </section>

  <section class="contact-form-section">
    <h2>Send a Message</h2>
    <form class="contact-form" action="process_contact.php" method="POST">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone">
      </div>
      <div class="form-group">
        <label for="subject">Subject</label>
        <select id="subject" name="subject">
          <option value="general">General Inquiry</option>
          <option value="booking">Booking Information</option>
          <option value="feedback">Feedback</option>
          <option value="complaint">Complaint</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="message">Your Message</label>
        <textarea id="message" name="message" required></textarea>
      </div>
      <button type="submit" class="submit-btn">Send Message</button>
    </form>
  </section>

  <section class="map-section">
    <h2>Find Us</h2>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.1956604977956!2d87.69969937450465!3d26.6635583768246!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ef41e8f04d5f69%3A0x69a31fa8d1fc2a3f!2sDamak%2C%20Jhapa!5e0!3m2!1sen!2snp!4v1714361257740!5m2!1sen!2snp" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </section>

  <section class="faq-section">
    <h2>Frequently Asked Questions</h2>
    <div class="faq-container">
      <div class="faq-item">
        <div class="faq-question">What are the check-in and check-out times? <i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer"><p>Check-in time is from 2:00 PM, and check-out time is until 12:00 PM.</p></div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Do you offer monthly accommodation packages? <i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer"><p>Yes, we offer special rates for long-term stays. Contact reception for details.</p></div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Is WiFi available in the hostel? <i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer"><p>Yes, high-speed WiFi is available in all areas of the hostel.</p></div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Are meals included in the accommodation fee? <i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer"><p>Breakfast is included. Lunch and dinner are available as add-ons.</p></div>
      </div>
    </div>
  </section>

  <?php include('partials/footer.php'); ?>

  <script>
    document.querySelectorAll('.faq-question').forEach(question => {
      question.addEventListener('click', () => {
        const item = question.parentNode;
        item.classList.toggle('active');
      });
    });
  </script>
</body>
</html>
