<?php
require_once 'php/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('index.php');
        } else {
            $error = 'Invalid username/email or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TicketHub</title>
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
                <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">Login to Your Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Username or Email:</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn" style="width: 100%;">Login</button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <p>Don't have an account? <a href="register.php" style="color: #667eea;">Register here</a></p>
                </div>
                
                <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <h4 style="margin-bottom: 1rem;">Demo Account:</h4>
                    <p><strong>Username:</strong> admin</p>
                    <p><strong>Password:</strong> admin123</p>
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
</body>
</html>