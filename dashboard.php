<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$conn = getDBConnection();
$user = getCurrentUser();
$userId = $_SESSION['user_id'];

// Get user's adoption applications
$applicationsStmt = $conn->prepare("
    SELECT aa.*, p.name as pet_name, p.image_url, c.name as category_name
    FROM adoption_applications aa
    JOIN pets p ON aa.pet_id = p.id
    JOIN categories c ON p.category_id = c.id
    WHERE aa.user_id = ?
    ORDER BY aa.created_at DESC
");
$applicationsStmt->bind_param("i", $userId);
$applicationsStmt->execute();
$applications = $applicationsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$applicationsStmt->close();

// Get user's favorited pets
$favoritesStmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM favorites f
    JOIN pets p ON f.pet_id = p.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$favoritesStmt->bind_param("i", $userId);
$favoritesStmt->execute();
$favorites = $favoritesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$favoritesStmt->close();

// Handle application cancellation
if (isset($_POST['cancel_application'])) {
    $applicationId = intval($_POST['application_id']);
    $stmt = $conn->prepare("DELETE FROM adoption_applications WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $applicationId, $userId);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success'] = 'Adoption application cancelled successfully!';
    redirect('dashboard.php');
}

$conn->close();

function getStatusBadge($status) {
    $colors = [
        'pending' => 'background: hsl(35, 100%, 90%); color: hsl(35, 100%, 40%);',
        'approved' => 'background: hsl(145, 62%, 90%); color: hsl(145, 100%, 20%);',
        'rejected' => 'background: hsl(0, 85%, 95%); color: hsl(0, 72%, 40%);',
        'completed' => 'background: hsl(210, 50%, 95%); color: hsl(210, 100%, 40%);'
    ];
    $color = $colors[$status] ?? 'background: hsl(35, 20%, 93%); color: hsl(25, 30%, 15%);';
    return "<span class='status-badge' style='$color'>" . ucfirst($status) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Paw Heaven</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="logged-in">
<?php include 'includes/header.php'; ?>

  <div class="section" style="padding-top: 6rem;">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">My Dashboard</h2>
        <p class="section-description">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid" style="margin-bottom: 3rem;">
        <div class="stat-card" data-animate>
          <div class="stat-number counter"><?php echo count($favorites); ?></div>
          <div class="stat-label">Favorite Pets</div>
        </div>
        <div class="stat-card" data-animate>
          <div class="stat-number counter"><?php echo count($applications); ?></div>
          <div class="stat-label">Applications</div>
        </div>
        <div class="stat-card" data-animate>
          <div class="stat-number counter">
            <?php 
            $activeApplications = array_filter($applications, function($a) {
                return in_array($a['status'], ['pending', 'approved']);
            });
            echo count($activeApplications);
            ?>
          </div>
          <div class="stat-label">Active Applications</div>
        </div>
      </div>

      <!-- My Favorites -->
      <div style="margin-bottom: 3rem;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.5rem;">My Favorite Pets</h3>
        <?php if (empty($favorites)): ?>
          <div class="form">
            <p style="color: var(--muted-foreground); text-align: center;">You haven't favorited any pets yet.</p>
            <div style="text-align: center; margin-top: 1rem;">
              <a href="pets.php" class="btn btn-primary">Browse Pets</a>
            </div>
          </div>
        <?php else: ?>
          <div class="pets-grid">
            <?php foreach ($favorites as $pet): ?>
              <div class="pet-card" data-animate>
                <div class="pet-image-container">
                  <img src="<?php echo htmlspecialchars($pet['image_url'] ?? 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&h=300&fit=crop'); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">
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
                  <a href="adopt.php?pet_id=<?php echo $pet['id']; ?>" class="btn btn-primary btn-full">Apply to Adopt</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- My Applications -->
      <div>
        <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.5rem;">My Adoption Applications</h3>
        <?php if (empty($applications)): ?>
          <div class="form">
            <p style="color: var(--muted-foreground); text-align: center;">No applications yet.</p>
            <div style="text-align: center; margin-top: 1rem;">
              <a href="pets.php" class="btn btn-primary">Find Your First Pet</a>
            </div>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Pet</th>
                  <th>Type</th>
                  <th>Applied On</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($applications as $application): ?>
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <img src="<?php echo htmlspecialchars($application['image_url'] ?? 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=50&h=50&fit=crop'); ?>" 
                             alt="<?php echo htmlspecialchars($application['pet_name']); ?>" 
                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <div>
                          <strong><?php echo htmlspecialchars($application['pet_name']); ?></strong>
                        </div>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars($application['category_name']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($application['created_at'])); ?></td>
                    <td><?php echo getStatusBadge($application['status']); ?></td>
                    <td>
                      <?php if ($application['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline;">
                          <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                          <button type="submit" name="cancel_application" 
                                  onclick="return confirm('Are you sure you want to cancel this application?')" 
                                  class="btn" style="background: var(--error); color: white; border: none; padding: 0.375rem 0.75rem; border-radius: var(--radius); cursor: pointer; font-size: 0.75rem;">
                            Cancel
                          </button>
                        </form>
                      <?php else: ?>
                        <span style="color: var(--muted-foreground); font-size: 0.75rem;">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
