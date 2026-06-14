<?php
require_once 'config.php';
$page_title = 'Find Pets';

$conn = getDBConnection();

// Clear duplicates and get unique pets
$species = isset($_GET['species']) ? $_GET['species'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

$query = "
    SELECT DISTINCT p.*, c.name as category_name 
    FROM pets p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.status = 'available'
";
$params = [];
$types = "";

if ($species) {
    $query .= " AND LOWER(c.name) = ?";
    $params[] = strtolower($species);
    $types .= "s";
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.breed LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$query .= " ORDER BY p.created_at DESC LIMIT 20";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$pets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
$conn->close();

include 'includes/header.php';
?>

  <div class="section" style="padding-top: 6rem;">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Find Your Perfect Pet</h2>
        <p class="section-description">Browse all available pets waiting for their forever home</p>
      </div>

      <!-- Search and Filter -->
      <div class="form" style="margin-bottom: 2rem;">
        <form method="GET" class="form-grid">
          <div>
            <input type="text" name="search" class="input" placeholder="Search by name or breed..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
          </div>
          <div>
            <select name="species" class="select">
              <option value="">All Types</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo strtolower($cat['name']); ?>" <?php echo ($species === strtolower($cat['name'])) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <button type="submit" class="btn btn-primary btn-full">Search</button>
          </div>
        </form>
      </div>

      <!-- Pets Grid -->
      <div class="pets-grid">
        <?php foreach ($pets as $pet): ?>
          <div class="pet-card" data-animate>
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

      <?php if (empty($pets)): ?>
        <div class="form" style="text-align: center;">
          <p style="color: var(--muted-foreground);">No pets found matching your criteria.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
