<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Paw Heaven</title>
  <meta name="description" content="Adopt your perfect furry companion from Paw Heaven. Browse pets waiting for loving homes.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Toast Container -->
  <div class="toast-container">
    <?php if (isset($_SESSION['success'])): ?>
      <div class="toast toast-success show"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="toast toast-error show"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (isset($error) && $error): ?>
      <div class="toast toast-error show"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (isset($success) && $success): ?>
      <div class="toast toast-success show"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
  </div>

  <!-- Navigation -->
  <nav class="navbar" id="navbar">
    <div class="container nav-container">
      <a href="index.php" class="logo">
        <span class="logo-icon">🐾</span>
        <span class="logo-text">Paw Heaven</span>
      </a>
      
      <ul class="nav-links" id="navLinks">
        <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Home</a></li>
        <li><a href="pets.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'pets.php' ? 'active' : ''; ?>">Find Pets</a></li>
        <li><a href="about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : ''; ?>">About</a></li>
        <li><a href="contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
        <?php if (isLoggedIn()): ?>
          <li><a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <?php endif; ?>
      </ul>
      
      <div class="nav-actions">
        <?php if (isLoggedIn()): ?>
          <button class="btn btn-ghost nav-favorites" onclick="showToast('Favorites are shown in your dashboard!', 'info')">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
            <span>Favorites</span>
          </button>
          <a href="logout.php" class="btn btn-primary">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">Sign In</a>
        <?php endif; ?>
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="4" x2="20" y1="12" y2="12"/>
            <line x1="4" x2="20" y1="6" y2="6"/>
            <line x1="4" x2="20" y1="18" y2="18"/>
          </svg>
        </button>
      </div>
    </div>
  </nav>
  