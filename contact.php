<?php
require_once 'config.php';
$page_title = 'Contact Us';
include 'includes/header.php';
?>

  <div class="section" style="padding-top: 6rem;">
    <div class="container" style="max-width: 600px;">
      <!-- Back Button -->
      <a href="index.php" class="btn btn-ghost" style="margin-bottom: 1rem;">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Back to Home
      </a>
      
      <div class="section-header">
        <h2 class="section-title">Get In Touch</h2>
        <p class="section-description">Have questions? We'd love to hear from you</p>
      </div>
      
      <form action="contact-handler.php" method="POST" class="form" id="contactForm">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="input" required placeholder="Your full name">
          </div>
          <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="input" required placeholder="your@email.com">
          </div>
        </div>
        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label">Phone (Optional)</label>
          <input type="tel" name="phone" class="input" placeholder="(555) 123-4567">
        </div>
        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label">Message *</label>
          <textarea name="message" class="textarea" rows="5" required placeholder="How can we help you?"></textarea>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Send Message</button>
          <a href="index.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>

      <!-- Contact Info Cards -->
      <div class="cards" style="margin-top: 3rem;">
        <div class="card" data-animate>
          <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📧</div>
            <h4>Email Us</h4>
            <p style="color: var(--muted-foreground);">info@pawheaven.com</p>
          </div>
        </div>
        <div class="card" data-animate>
          <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📞</div>
            <h4>Call Us</h4>
            <p style="color: var(--muted-foreground);">(555) 123-4567</p>
          </div>
        </div>
        <div class="card" data-animate>
          <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📍</div>
            <h4>Visit Us</h4>
            <p style="color: var(--muted-foreground);">123 Main St, New York, NY</p>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
