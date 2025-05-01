<style>
  .success-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    font-size: 0.95rem;
    z-index: 9999;
    animation: fadeSlideIn 0.3s ease-out;
    transition: opacity 0.5s ease;
  }

  @keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
  <div class="success-popup" id="successMessage">
     User account created successfully! Please wait for admin approval.
  </div>
  <script>
    setTimeout(() => {
      const msg = document.getElementById('successMessage');
      if (msg) {
        msg.style.opacity = '0';
        setTimeout(() => msg.style.display = 'none', 500);
      }
    }, 3000);
  </script>
<?php endif; ?>
