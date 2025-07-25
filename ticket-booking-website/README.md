# TicketHub - Online Ticket Booking System

A simple and modern online ticket booking website built with PHP, MySQL, JavaScript, and CSS.

## Features

- **User Registration & Authentication**: Secure user registration and login system
- **Event Browsing**: View available events with details like date, time, venue, and pricing
- **Ticket Booking**: Book tickets for events with quantity selection
- **Booking Management**: View booking history and status
- **Search Functionality**: Search events by title, description, or venue
- **Responsive Design**: Mobile-friendly interface
- **Real-time Updates**: Automatic ticket availability updates
- **Transaction Safety**: Database transactions ensure booking integrity

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with gradient designs and animations
- **Security**: Password hashing, SQL injection prevention, XSS protection

## Installation & Setup

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Database Setup

1. Create a MySQL database:
   ```sql
   CREATE DATABASE ticket_booking;
   ```

2. Import the database schema:
   ```bash
   mysql -u your_username -p ticket_booking < sql/database.sql
   ```

3. Update database configuration in `php/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'ticket_booking');
   ```

### Web Server Setup

#### Using XAMPP/WAMP/MAMP

1. Copy the project folder to your web server directory:
   - XAMPP: `htdocs/ticket-booking-website/`
   - WAMP: `www/ticket-booking-website/`
   - MAMP: `htdocs/ticket-booking-website/`

2. Start Apache and MySQL services

3. Access the website: `http://localhost/ticket-booking-website/`

#### Using Built-in PHP Server

1. Navigate to the project directory:
   ```bash
   cd ticket-booking-website
   ```

2. Start the PHP development server:
   ```bash
   php -S localhost:8000
   ```

3. Access the website: `http://localhost:8000`

## File Structure

```
ticket-booking-website/
├── css/
│   └── style.css              # Main stylesheet
├── js/
│   └── script.js              # JavaScript functionality
├── php/
│   └── config.php             # Database configuration
├── sql/
│   └── database.sql           # Database schema
├── images/                    # Image assets (optional)
├── index.php                  # Homepage
├── login.php                  # User login
├── register.php               # User registration
├── book-ticket.php            # Ticket booking
├── my-bookings.php            # User bookings
├── logout.php                 # Logout functionality
└── README.md                  # This file
```

## Usage

### For Users

1. **Registration**: Create a new account with username, email, and password
2. **Login**: Sign in with your credentials
3. **Browse Events**: View available events on the homepage
4. **Search Events**: Use the search bar to find specific events
5. **Book Tickets**: Select quantity and book tickets for events
6. **View Bookings**: Check your booking history and status

### Demo Account

- **Username**: admin
- **Password**: admin123

### Sample Events

The system comes with pre-loaded sample events:
- Concert Night - $75.00
- Tech Conference 2024 - $150.00
- Comedy Show - $45.00
- Food Festival - $25.00

## Features Explained

### Security Features

- **Password Hashing**: All passwords are hashed using PHP's password_hash()
- **SQL Injection Prevention**: Prepared statements used throughout
- **XSS Protection**: All user inputs are sanitized with htmlspecialchars()
- **Session Management**: Secure session handling for user authentication

### Database Features

- **Transaction Safety**: Booking process uses database transactions
- **Row Locking**: Prevents overselling of tickets with SELECT FOR UPDATE
- **Foreign Key Constraints**: Maintains data integrity
- **Automatic Timestamps**: Tracks creation and booking dates

### Frontend Features

- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Real-time Validation**: Client-side form validation with JavaScript
- **Dynamic Price Calculation**: Automatically calculates total price
- **Search Functionality**: Live search through events
- **Loading States**: Visual feedback during form submissions

## Customization

### Adding New Events

You can add new events directly in the database:

```sql
INSERT INTO events (title, description, event_date, event_time, venue, total_tickets, available_tickets, price) 
VALUES ('Your Event', 'Event description', '2024-12-25', '18:00:00', 'Event Venue', 100, 100, 50.00);
```

### Styling

- Modify `css/style.css` to change colors, fonts, and layout
- The design uses CSS Grid and Flexbox for responsive layouts
- Gradient backgrounds can be customized in the CSS variables

### Functionality

- Add new features by creating additional PHP files
- Extend the database schema as needed
- Modify JavaScript for additional client-side functionality

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `php/config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Session Issues**
   - Check PHP session configuration
   - Ensure proper file permissions
   - Clear browser cookies

3. **CSS/JS Not Loading**
   - Check file paths in HTML
   - Verify web server configuration
   - Clear browser cache

### Error Logging

Enable PHP error logging for debugging:

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Future Enhancements

Potential improvements for the system:

- **Payment Integration**: Add payment gateway integration
- **Email Notifications**: Send booking confirmations via email
- **Admin Panel**: Create admin interface for event management
- **Ticket Categories**: Add different ticket types (VIP, General, etc.)
- **Event Images**: Upload and display event images
- **QR Code Tickets**: Generate QR codes for tickets
- **Social Media Integration**: Share events on social platforms
- **Reviews & Ratings**: Allow users to rate events
- **Calendar Integration**: Export events to calendar applications

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support or questions, please create an issue in the project repository or contact the development team.

---

**TicketHub** - Making event booking simple and secure! 🎫