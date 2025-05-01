<style>
  html, body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
  }

  .hero {
    position: relative;
    width: 100%;
    min-height: 400px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    overflow: hidden;
    text-align: center;
    padding: 60px 20px 100px;
    flex-direction: column;
    transition: background-image 1s ease-in-out;
  }

  .hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.25);
    z-index: 1;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    padding: 20px;
  }

  .hero-content h1 {
    font-size: 2.7rem;
    font-weight: bold;
    margin-bottom: 12px;
  }

  .hero-content p {
    font-size: 1.1rem;
    color: #e0e0e0;
    line-height: 1.6;
    margin-bottom: 25px;
  }

  .hero-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
  }

  .hero-buttons a {
    padding: 12px 24px;
    background: white;
    color: #4b0082;
    font-weight: bold;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .hero-buttons a:hover {
    background: #e0d4ff;
  }
</style>

<script>
  const heroImages = [
    'assets/images/hostel1.jpg',
    'assets/images/hostel2.jpg',
    'assets/images/hostel3.jpg'
  ];

  let currentImage = 0;

  function rotateHeroBackground() {
    const hero = document.querySelector('.hero');
    hero.style.backgroundImage = `url('${heroImages[currentImage]}')`;
    currentImage = (currentImage + 1) % heroImages.length;
  }

  document.addEventListener('DOMContentLoaded', () => {
    rotateHeroBackground();
    setInterval(rotateHeroBackground, 4000);
  });
</script>

<header class="hero">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1>Welcome to Saathi Hostel</h1>
    <p>Your second home in the city — safe, affordable, and vibrant.<br>Join a community that feels like family.</p>
    <div class="hero-buttons">
      <a href="register.php">Join Now</a>
      <a href="about.php">Learn More</a>
    </div>
  </div>
</header>