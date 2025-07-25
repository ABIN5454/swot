-- Create database
CREATE DATABASE IF NOT EXISTS ticket_booking;
USE ticket_booking;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    total_tickets INT NOT NULL,
    available_tickets INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Insert sample events
INSERT INTO events (title, description, event_date, event_time, venue, total_tickets, available_tickets, price, image_url) VALUES
('Concert Night', 'Amazing live music concert featuring top artists', '2024-08-15', '19:00:00', 'City Arena', 500, 500, 75.00, 'concert.jpg'),
('Tech Conference 2024', 'Latest trends in technology and innovation', '2024-08-20', '09:00:00', 'Convention Center', 300, 300, 150.00, 'tech-conf.jpg'),
('Comedy Show', 'Stand-up comedy night with famous comedians', '2024-08-25', '20:00:00', 'Comedy Club', 200, 200, 45.00, 'comedy.jpg'),
('Food Festival', 'Taste delicious food from around the world', '2024-09-01', '11:00:00', 'Central Park', 1000, 1000, 25.00, 'food-fest.jpg');

-- Create admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, phone) VALUES
('admin', 'admin@ticketbooking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', '1234567890');