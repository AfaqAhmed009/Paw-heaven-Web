<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, full_name, email, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                redirect('dashboard.php');
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
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
    <title>Login - Paw Heaven</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

  <div class="auth-layout">
    <div class="container auth-container">
      <div class="form">
        <h2 class="section-title" style="text-align: center; margin-bottom: 0.5rem;">Login to Your Account</h2>
        <p class="form-footer" style="margin-bottom: 1.5rem;">Welcome back! Please login to continue.</p>
        
        <form method="POST" class="auth-form" novalidate>
          <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="input" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="your@email.com">
          </div>
          <div class="form-group">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="input" required placeholder="Enter your password">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-full">Login</button>
          </div>
          <div class="form-footer">
            Don't have an account? <a href="register.php">Register here</a>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
