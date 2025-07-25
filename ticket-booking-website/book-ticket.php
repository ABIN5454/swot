<?php
require_once 'php/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = (int)$_POST['event_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    
    if ($quantity < 1) {
        $error = 'Please select at least 1 ticket';
    } else {
        $pdo = getConnection();
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Get event details and lock the row
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? FOR UPDATE");
            $stmt->execute([$event_id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                throw new Exception('Event not found');
            }
            
            if ($event['available_tickets'] < $quantity) {
                throw new Exception('Not enough tickets available');
            }
            
            $total_amount = $event['price'] * $quantity;
            
            // Insert booking
            $stmt = $pdo->prepare("INSERT INTO bookings (user_id, event_id, quantity, total_amount, status) VALUES (?, ?, ?, ?, 'confirmed')");
            $stmt->execute([$user_id, $event_id, $quantity, $total_amount]);
            
            // Update available tickets
            $new_available = $event['available_tickets'] - $quantity;
            $stmt = $pdo->prepare("UPDATE events SET available_tickets = ? WHERE id = ?");
            $stmt->execute([$new_available, $event_id]);
            
            // Commit transaction
            $pdo->commit();
            
            $success = "Booking successful! You have booked {$quantity} ticket(s) for {$event['title']}.";
            
        } catch (Exception $e) {
            // Rollback transaction
            $pdo->rollback();
            $error = $e->getMessage();
        }
    }
}

// Get user's current bookings for display
$pdo = getConnection();
$stmt = $pdo->prepare("
    SELECT b.*, e.title, e.event_date, e.event_time, e.venue 
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC 
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status - TicketHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">🎫 TicketHub</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="my-bookings.php">My Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars(getCurrentUser()['full_name']); ?>!</span></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">Booking Status</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <strong>Booking Failed:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="index.php" class="btn">Back to Events</a>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
                    </div>
                    
                    <div style="text-align: center; margin: 2rem 0;">
                        <a href="index.php" class="btn" style="margin-right: 1rem;">Book More Tickets</a>
                        <a href="my-bookings.php" class="btn btn-secondary">View All Bookings</a>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($recent_bookings)): ?>
                    <div style="margin-top: 3rem;">
                        <h3 style="margin-bottom: 1rem; color: #333;">Your Recent Bookings</h3>
                        <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <?php foreach ($recent_bookings as $booking): ?>
                                <div style="padding: 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking['title']); ?></strong><br>
                                        <small style="color: #666;">
                                            <?php echo date('M j, Y g:i A', strtotime($booking['event_date'] . ' ' . $booking['event_time'])); ?> 
                                            at <?php echo htmlspecialchars($booking['venue']); ?>
                                        </small><br>
                                        <small style="color: #666;">
                                            Booked: <?php echo date('M j, Y g:i A', strtotime($booking['booking_date'])); ?>
                                        </small>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: bold; color: #667eea;">
                                            <?php echo $booking['quantity']; ?> ticket(s)
                                        </div>
                                        <div style="color: #666;">
                                            $<?php echo number_format($booking['total_amount'], 2); ?>
                                        </div>
                                        <div style="margin-top: 0.25rem;">
                                            <span class="badge" style="background: <?php echo $booking['status'] == 'confirmed' ? '#28a745' : '#ffc107'; ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
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