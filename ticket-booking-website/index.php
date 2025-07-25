<?php
require_once 'php/config.php';

// Get all events
$pdo = getConnection();
$stmt = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketHub - Online Ticket Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">🎫 TicketHub</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="my-bookings.php">My Bookings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <li><span>Welcome, <?php echo htmlspecialchars(getCurrentUser()['full_name']); ?>!</span></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Book Your Tickets Online</h1>
                <p>Discover amazing events and book your tickets instantly</p>
                <div class="search-container" style="margin-top: 2rem;">
                    <input type="text" id="search-input" placeholder="Search events..." 
                           style="padding: 0.8rem; border: none; border-radius: 5px; width: 300px; margin-right: 1rem;">
                    <button onclick="searchEvents()" class="btn">Search</button>
                </div>
            </div>
        </section>

        <section class="container">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">Upcoming Events</h2>
            
            <?php if (empty($events)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <h3>No upcoming events available</h3>
                    <p>Please check back later for new events!</p>
                </div>
            <?php else: ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <div class="event-image">
                                <?php echo htmlspecialchars($event['title']); ?>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                                
                                <div class="event-details">
                                    <div>
                                        <div class="event-date">
                                            📅 <?php echo date('M j, Y', strtotime($event['event_date'])); ?> 
                                            at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                        </div>
                                        <div class="event-venue">
                                            📍 <?php echo htmlspecialchars($event['venue']); ?>
                                        </div>
                                        <div style="margin-top: 0.5rem;">
                                            🎫 <?php echo $event['available_tickets']; ?> tickets available
                                        </div>
                                    </div>
                                    <div class="event-price">$<?php echo number_format($event['price'], 2); ?></div>
                                </div>

                                <?php if (isLoggedIn()): ?>
                                    <form method="POST" action="book-ticket.php" class="booking-form" 
                                          data-available="<?php echo $event['available_tickets']; ?>"
                                          style="margin-top: 1rem;">
                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                            <label for="quantity_<?php echo $event['id']; ?>">Quantity:</label>
                                            <input type="number" 
                                                   id="quantity_<?php echo $event['id']; ?>"
                                                   name="quantity" 
                                                   min="1" 
                                                   max="<?php echo $event['available_tickets']; ?>" 
                                                   value="1" 
                                                   data-price="<?php echo $event['price']; ?>"
                                                   style="width: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 3px;">
                                        </div>
                                        <div class="total-price" style="margin-bottom: 1rem; font-weight: bold;">
                                            Total: $<?php echo number_format($event['price'], 2); ?>
                                        </div>
                                        <button type="submit" class="btn btn-success" style="width: 100%;">
                                            Book Now
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div style="margin-top: 1rem; text-align: center;">
                                        <a href="login.php" class="btn" style="width: 100%; display: block; text-align: center;">
                                            Login to Book
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 TicketHub. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>