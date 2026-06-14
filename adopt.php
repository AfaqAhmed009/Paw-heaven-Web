<?php
require_once 'config.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please login to apply for adoption';
    redirect('login.php');
}

$conn = getDBConnection();
$user = getCurrentUser();
$userId = $_SESSION['user_id'];

$error = '';
$success = '';

// Get pet details
$petId = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : null;
if (!$petId) {
    redirect('pets.php');
}

$petStmt = $conn->prepare("SELECT p.*, c.name as category_name, s.name as shelter_name FROM pets p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN shelters s ON p.shelter_id = s.id WHERE p.id = ?");
$petStmt->bind_param("i", $petId);
$petStmt->execute();
$pet = $petStmt->get_result()->fetch_assoc();
$petStmt->close();

if (!$pet || $pet['status'] !== 'available') {
    $_SESSION['error'] = 'This pet is no longer available for adoption';
    redirect('pets.php');
}

// Handle adoption application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = sanitize($_POST['message']);
    
    // Check if user already applied
    $checkStmt = $conn->prepare("SELECT id FROM adoption_applications WHERE user_id = ? AND pet_id = ?");
    $checkStmt->bind_param("ii", $userId, $petId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $error = 'You have already applied for this pet';
    } else {
        $stmt = $conn->prepare("INSERT INTO adoption_applications (user_id, pet_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $petId, $message);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Adoption application submitted successfully! We will contact you soon.';
            redirect('dashboard.php');
        } else {
            $error = 'Failed to submit application';
        }
        $stmt->close();
    }
    $checkStmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adopt <?php echo htmlspecialchars($pet['name']); ?> - Paw Heaven</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="logged-in">
<?php include 'includes/header.php'; ?>

  <div class="section" style="padding-top: 6rem;">
    <div class="container" style="max-width: 800px;">
      <div class="section-header">
        <h2 class="section-title">Adoption Application</h2>
        <p class="section-description">Apply to adopt <?php echo htmlspecialchars($pet['name']); ?></p>
      </div>

      <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Pet Info Card -->
        <div class="pet-card">
          <div class="pet-image-container">
            <img src="<?php echo htmlspecialchars($pet['image_url'] ?? 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&h=300&fit=crop'); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">
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
              <span class="pet-detail">Age: <?php echo htmlspecialchars($pet['age']); ?></span>
              <span class="pet-detail">Weight: <?php echo htmlspecialchars($pet['weight'] ?? 'Unknown'); ?> lbs</span>
            </div>
            <p style="margin-top: 1rem; font-size: 0.875rem; color: var(--muted-foreground);">
              <?php echo htmlspecialchars($pet['description'] ?? 'No description available'); ?>
            </p>
          </div>
        </div>

        <!-- Application Form -->
        <div>
          <form method="POST" class="form">
            <div class="form-group">
              <label class="form-label">Your Name</label>
              <input type="text" class="input" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
            </div>
            <div class="form-group">
              <label class="form-label">Your Email</label>
              <input type="email" class="input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
              <label class="form-label">Phone</label>
              <input type="tel" class="input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" disabled>
            </div>
            <div class="form-group">
              <label class="form-label">Why do you want to adopt <?php echo htmlspecialchars($pet['name']); ?>?</label>
              <textarea name="message" class="textarea" rows="4" required placeholder="Tell us about your home, lifestyle, and why you'd be a great fit..."></textarea>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary btn-full">Submit Application</button>
              <a href="pets.php" class="btn btn-outline btn-full" style="margin-top: 0.5rem;">Back to Pets</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
