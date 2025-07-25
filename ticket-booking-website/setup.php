<?php
/**
 * TicketHub Setup Script
 * This script helps with the initial setup of the ticket booking system
 */

// Check if setup is already completed
if (file_exists('setup_completed.txt')) {
    die('<h1>Setup Already Completed</h1><p>The setup has already been completed. Delete "setup_completed.txt" to run setup again.</p><a href="index.php">Go to Website</a>');
}

$error = '';
$success = '';
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 1) {
        // Database connection test
        $host = $_POST['host'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $database = $_POST['database'];
        
        try {
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
            $pdo->exec("USE `$database`");
            
            // Update config file
            $config_content = "<?php
// Database configuration
define('DB_HOST', '$host');
define('DB_USER', '$username');
define('DB_PASS', '$password');
define('DB_NAME', '$database');

// Create database connection
function getConnection() {
    try {
        \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME, DB_USER, DB_PASS);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return \$pdo;
    } catch(PDOException \$e) {
        die(\"Connection failed: \" . \$e->getMessage());
    }
}

// Start session
session_start();

// Helper functions
function isLoggedIn() {
    return isset(\$_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    \$pdo = getConnection();
    \$stmt = \$pdo->prepare(\"SELECT * FROM users WHERE id = ?\");
    \$stmt->execute([\$_SESSION['user_id']]);
    return \$stmt->fetch(PDO::FETCH_ASSOC);
}

function redirect(\$url) {
    header(\"Location: \$url\");
    exit();
}
?>";
            
            file_put_contents('php/config.php', $config_content);
            $success = 'Database connection successful! Configuration saved.';
            $step = 2;
            
        } catch (Exception $e) {
            $error = 'Database connection failed: ' . $e->getMessage();
        }
    } elseif ($step == 2) {
        // Create tables and insert sample data
        try {
            require_once 'php/config.php';
            $pdo = getConnection();
            
            // Read and execute SQL file
            $sql = file_get_contents('sql/database.sql');
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            
            // Mark setup as completed
            file_put_contents('setup_completed.txt', date('Y-m-d H:i:s'));
            
            $success = 'Database tables created successfully! Setup completed.';
            $step = 3;
            
        } catch (Exception $e) {
            $error = 'Database setup failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketHub Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .setup-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 2rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: white;
        }
        
        .step.active {
            background: #667eea;
        }
        
        .step.completed {
            background: #28a745;
        }
        
        .step.pending {
            background: #ccc;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        
        .text-center {
            text-align: center;
        }
        
        a {
            color: #667eea;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>🎫 TicketHub Setup</h1>
        
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? ($step == 1 ? 'active' : 'completed') : 'pending'; ?>">1</div>
            <div class="step <?php echo $step >= 2 ? ($step == 2 ? 'active' : 'completed') : 'pending'; ?>">2</div>
            <div class="step <?php echo $step >= 3 ? 'active' : 'pending'; ?>">3</div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <h2>Step 1: Database Configuration</h2>
            <div class="info-box">
                <strong>Note:</strong> Make sure MySQL is running and you have the necessary credentials.
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="host">Database Host:</label>
                    <input type="text" id="host" name="host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Database Username:</label>
                    <input type="text" id="username" name="username" value="root" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Database Password:</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="database">Database Name:</label>
                    <input type="text" id="database" name="database" value="ticket_booking" required>
                </div>
                
                <button type="submit" class="btn">Test Connection & Save</button>
            </form>
            
        <?php elseif ($step == 2): ?>
            <h2>Step 2: Database Setup</h2>
            <div class="info-box">
                This will create the necessary tables and insert sample data.
            </div>
            
            <form method="POST">
                <input type="hidden" name="step" value="2">
                <button type="submit" class="btn">Create Tables & Sample Data</button>
            </form>
            
        <?php elseif ($step == 3): ?>
            <h2>Setup Complete!</h2>
            <div class="alert alert-success">
                <strong>Congratulations!</strong> TicketHub has been set up successfully.
            </div>
            
            <div class="info-box">
                <h3>Demo Account:</h3>
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> admin123</p>
            </div>
            
            <div class="info-box">
                <h3>Sample Events:</h3>
                <ul>
                    <li>Concert Night - $75.00</li>
                    <li>Tech Conference 2024 - $150.00</li>
                    <li>Comedy Show - $45.00</li>
                    <li>Food Festival - $25.00</li>
                </ul>
            </div>
            
            <div class="text-center">
                <a href="index.php" class="btn" style="display: inline-block; text-decoration: none; margin-top: 1rem;">
                    Go to Website
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>