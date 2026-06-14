<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($fullName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $conn = getDBConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            // Insert new user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullName, $email, $phone, $passwordHash);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Paw Heaven</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

  <div class="auth-layout">
    <div class="container auth-container">
      <div class="form">
        <h2 class="section-title" style="text-align: center; margin-bottom: 0.5rem;">Create Your Account</h2>
        <p class="form-footer" style="margin-bottom: 1.5rem;">Join our community of pet lovers!</p>
        
        <form method="POST" class="auth-form" novalidate>
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="full_name" class="input" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" placeholder="John Doe">
          </div>
          <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="input" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="your@email.com">
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="input" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="(555) 123-4567">
          </div>
          <div class="form-group">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="input" required minlength="6" placeholder="At least 6 characters">
            <div class="password-strength"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="confirm_password" class="input" required minlength="6" placeholder="Confirm password">
            <div class="password-match"></div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-full">Register</button>
          </div>
          <div class="form-footer">
            Already have an account? <a href="login.php">Login here</a>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
