<?php
require_once 'config.php';
$page_title = 'Home';

$conn = getDBConnection();

// Get featured pets - ensure variety
$petsStmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM pets p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'available' 
    GROUP BY p.id
    ORDER BY RAND() 
    LIMIT 6
");
$petsStmt->execute();
$pets = $petsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$petsStmt->close();

// Get testimonials
$reviews = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);

// Dynamic stats
$adoptedCount = $conn->query("SELECT COUNT(*) as count FROM pets WHERE status = 'adopted'")->fetch_assoc()['count'];
$familyCount = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM adoption_applications WHERE status = 'completed'")->fetch_assoc()['count'];
$shelterCount = $conn->query("SELECT COUNT(*) as count FROM shelters")->fetch_assoc()['count'];

$conn->close();

include 'includes/header.php';
?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container hero-container">
      <div class="hero-content">
        <div class="hero-badge">
          <span class="badge-icon">🐾</span>
          <span>Find Your Perfect Companion</span>
        </div>
        <h1 class="hero-title">
          Every Pet Deserves a <span class="text-gradient">Loving Home</span>
        </h1>
        <p class="hero-description">
          Connect with adorable pets waiting for their forever families. 
          Start your journey to unconditional love today.
        </p>
        <div class="hero-buttons">
          <a href="pets.php" class="btn btn-primary btn-lg">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.3-4.3"/>
            </svg>
            Browse Pets
          </a>
          <a href="#how-it-works" class="btn btn-outline btn-lg">Learn More</a>
        </div>
        <div class="hero-stats">
          <div class="stat" data-animate>
            <span class="stat-value counter"><?php echo $adoptedCount + 500; ?>+</span>
            <span class="stat-label">Pets Adopted</span>
          </div>
          <div class="stat" data-animate>
            <span class="stat-value counter"><?php echo $familyCount + 100; ?>+</span>
            <span class="stat-label">Happy Families</span>
          </div>
          <div class="stat" data-animate>
            <span class="stat-value counter"><?php echo $shelterCount + 50; ?>+</span>
            <span class="stat-label">Partner Shelters</span>
          </div>
        </div>
      </div>
      <div class="hero-image">
        <div class="hero-image-card">
          <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=600&h=700&fit=crop" alt="Happy golden retriever" class="hero-img">
          <div class="hero-image-overlay">
            <span class="hero-image-badge">Ready for adoption!</span>
          </div>
        </div>
        <div class="floating-card floating-card-1" data-animate>
          <span class="floating-icon">🐕</span>
          <span>Dogs</span>
        </div>
        <div class="floating-card floating-card-2" data-animate>
          <span class="floating-icon">🐈</span>
          <span>Cats</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Featured Pets Section -->
  <section class="featured-pets">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">
          <span>🏠</span>
          <span>Looking for Home</span>
        </div>
        <h2 class="section-title">
          Meet Our <span class="text-gradient">Featured Pets</span>
        </h2>
        <p class="section-description">
          These adorable companions are waiting to meet you
        </p>
      </div>
      
      <div class="pets-grid">
        <?php foreach ($pets as $index => $pet): ?>
          <div class="pet-card" data-animate style="animation-delay: <?php echo $index * 0.1; ?>s;">
            <div class="pet-image-container">
              <img src="<?php echo htmlspecialchars($pet['image_url'] ?? 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&h=300&fit=crop'); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">
              <button class="favorite-btn" aria-label="Add to favorites">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                </svg>
              </button>
              <span class="pet-species-badge"><?php echo htmlspecialchars($pet['category_name']); ?></span>
            </div>
            <div class="pet-info">
              <div class="pet-header">
                <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                <span class="pet-gender <?php echo $pet['gender']; ?>">
                  <?php echo ($pet['gender'] === 'male') ? '♂' : '♀'; ?>
                </span>
              </div>
              <p class="pet-breed"><?php echo htmlspecialchars($pet['breed'] ?? 'Mixed Breed'); ?></p>
              <div class="pet-details">
                <span class="pet-detail">
                  <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                  </svg>
                  <?php echo htmlspecialchars($pet['age']); ?>
                </span>
                <span class="pet-detail">
                  <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                    <circle cx="12" cy="10" r="3"/>
                  </svg>
                  <?php echo htmlspecialchars($pet['weight'] ?? 'Unknown'); ?> lbs
                </span>
              </div>
              <a href="adopt.php?pet_id=<?php echo $pet['id']; ?>" class="btn btn-primary btn-full">Apply to Adopt</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="section-footer">
        <a href="pets.php" class="btn btn-outline btn-lg">
          View All Pets
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </a>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="how-it-works" id="how-it-works">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">
          <span>📋</span>
          <span>Simple Process</span>
        </div>
        <h2 class="section-title">
          How <span class="text-gradient">Adoption Works</span>
        </h2>
        <p class="section-description">
          Finding your new best friend is just a few steps away
        </p>
      </div>

      <div class="steps-grid">
        <div class="step-card" data-animate>
          <div class="step-number">1</div>
          <div class="step-icon">🔍</div>
          <h3 class="step-title">Browse Pets</h3>
          <p class="step-description">
            Explore our collection of adorable pets waiting for their forever homes
          </p>
        </div>

        <div class="step-connector">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </div>

        <div class="step-card" data-animate>
          <div class="step-number">2</div>
          <div class="step-icon">📝</div>
          <h3 class="step-title">Apply</h3>
          <p class="step-description">
            Fill out our simple adoption application to get started
          </p>
        </div>

        <div class="step-connector">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </div>

        <div class="step-card" data-animate>
          <div class="step-number">3</div>
          <div class="step-icon">🤝</div>
          <h3 class="step-title">Meet & Greet</h3>
          <p class="step-description">
            Schedule a visit to meet your potential new family member
          </p>
        </div>

        <div class="step-connector">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </div>

        <div class="step-card" data-animate>
          <div class="step-number">4</div>
          <div class="step-icon">🏠</div>
          <h3 class="step-title">Welcome Home</h3>
          <p class="step-description">
            Complete the adoption and bring your new friend home!
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">
          <span>💬</span>
          <span>Happy Families</span>
        </div>
        <h2 class="section-title">
          What Our <span class="text-gradient">Adopters Say</span>
        </h2>
        <p class="section-description">
          Real stories from families who found their perfect companions
        </p>
      </div>

      <div class="testimonials-grid">
        <?php foreach ($reviews as $review): ?>
          <div class="testimonial-card" data-animate>
            <div class="testimonial-stars">
              <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
            </div>
            <p class="testimonial-text">
              "<?php echo htmlspecialchars($review['comment']); ?>"
            </p>
            <div class="testimonial-author">
              <div class="author-avatar">
                <img src="<?php echo htmlspecialchars($review['user_avatar'] ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop'); ?>" alt="Reviewer">
              </div>
              <div class="author-info">
                <span class="author-name"><?php echo htmlspecialchars($review['user_name'] ?? 'Happy Adopter'); ?></span>
                <span class="author-pet">Adopted <?php echo htmlspecialchars($review['pet_name'] ?? 'a Pet'); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="container">
      <div class="cta-card">
        <div class="cta-content">
          <h2 class="cta-title">Ready to Find Your New Best Friend?</h2>
          <p class="cta-description">
            Thousands of adorable pets are waiting for loving homes. 
            Start your adoption journey today!
          </p>
          <div class="cta-buttons">
            <a href="pets.php" class="btn btn-primary btn-lg">
              <span>🐾</span>
              Browse Available Pets
            </a>
            <a href="contact.php" class="btn btn-outline-white btn-lg">Contact Us</a>
          </div>
        </div>
        <div class="cta-decoration">
          <div class="paw-print paw-1">🐾</div>
          <div class="paw-print paw-2">🐾</div>
          <div class="paw-print paw-3">🐾</div>
        </div>
      </div>
    </div>
  </section>

<?php include 'includes/footer.php'; ?>
