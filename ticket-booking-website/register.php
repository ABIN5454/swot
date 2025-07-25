<?php
require_once 'php/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Please fill in all required fields';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $pdo = getConnection();
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $error = 'Username or email already exists';
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                $success = 'Registration successful! You can now login.';
                // Clear form data
                $username = $email = $full_name = $phone = '';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TicketHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">🎫 TicketHub</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">Create Your Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="register.php">
                    <div class="form-group">
                        <label for="full_name">Full Name: *</label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($full_name ?? ''); ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username: *</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email: *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password: *</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               required>
                        <small style="color: #666;">Minimum 6 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password: *</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-control" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn" style="width: 100%;">Register</button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <p>Already have an account? <a href="login.php" style="color: #667eea;">Login here</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 TicketHub. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
        // Additional validation for password confirmation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword && confirmPassword.length > 0) {
                this.style.borderColor = '#f5c6cb';
                showFieldError(this, 'Passwords do not match');
            } else {
                this.style.borderColor = '#ddd';
                clearFieldError(this);
            }
        });
    </script>
</body>
</html>