<style>
  .error-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #ff4d4d;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    font-size: 0.95rem;
    z-index: 9999;
    animation: fadeSlideIn 0.3s ease-out;
  }

  @keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<?php if (isset($_GET['error']) && $_GET['error'] !== ''): ?>
  <div class="error-popup">
    <?php echo htmlspecialchars($_GET['error']); ?>
  </div>
<?php endif; ?>