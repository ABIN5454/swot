<?php
require_once 'php/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get user's bookings
$pdo = getConnection();
$stmt = $pdo->prepare("
    SELECT b.*, e.title, e.description, e.event_date, e.event_time, e.venue, e.price
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get booking statistics
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_bookings,
        SUM(quantity) as total_tickets,
        SUM(total_amount) as total_spent
    FROM bookings 
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - TicketHub</title>
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
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">My Bookings</h2>
            
            <!-- Booking Statistics -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 3rem;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                    <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['total_bookings'] ?: 0; ?></h3>
                    <p style="margin: 0.5rem 0 0 0;">Total Bookings</p>
                </div>
                <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                    <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['total_tickets'] ?: 0; ?></h3>
                    <p style="margin: 0.5rem 0 0 0;">Total Tickets</p>
                </div>
                <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                    <h3 style="margin: 0; font-size: 2rem;">$<?php echo number_format($stats['total_spent'] ?: 0, 2); ?></h3>
                    <p style="margin: 0.5rem 0 0 0;">Total Spent</p>
                </div>
            </div>
            
            <?php if (empty($bookings)): ?>
                <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3>No bookings found</h3>
                    <p style="margin: 1rem 0;">You haven't booked any tickets yet.</p>
                    <a href="index.php" class="btn">Browse Events</a>
                </div>
            <?php else: ?>
                <div class="table-container" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date & Time</th>
                                <th>Venue</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($booking['title']); ?></strong>
                                            <?php if ($booking['description']): ?>
                                                <br><small style="color: #666;"><?php echo htmlspecialchars(substr($booking['description'], 0, 50)) . '...'; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php echo date('M j, Y', strtotime($booking['event_date'])); ?><br>
                                            <small><?php echo date('g:i A', strtotime($booking['event_time'])); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['venue']); ?></td>
                                    <td style="text-align: center; font-weight: bold;"><?php echo $booking['quantity']; ?></td>
                                    <td style="font-weight: bold; color: #667eea;">$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    <td>
                                        <div>
                                            <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?><br>
                                            <small><?php echo date('g:i A', strtotime($booking['booking_date'])); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge" style="
                                            background: <?php 
                                                echo $booking['status'] == 'confirmed' ? '#28a745' : 
                                                    ($booking['status'] == 'pending' ? '#ffc107' : '#dc3545'); 
                                            ?>; 
                                            color: white; 
                                            padding: 0.25rem 0.75rem; 
                                            border-radius: 15px; 
                                            font-size: 0.85rem;
                                            font-weight: bold;
                                        ">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="index.php" class="btn">Book More Tickets</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 TicketHub. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <style>
        .table-container {
            overflow-x: auto;
        }
        
        @media (max-width: 768px) {
            .table {
                font-size: 0.9rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem;
            }
        }
    </style>
</body>
</html>