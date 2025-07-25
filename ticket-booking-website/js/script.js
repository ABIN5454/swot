// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeBookingForm();
    initializeQuantityControls();
    initializeFormValidation();
});

// Booking form functionality
function initializeBookingForm() {
    const bookingForms = document.querySelectorAll('.booking-form');
    
    bookingForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const quantity = parseInt(form.querySelector('input[name="quantity"]').value);
            const availableTickets = parseInt(form.dataset.available);
            
            if (quantity > availableTickets) {
                e.preventDefault();
                showAlert('Not enough tickets available!', 'error');
                return;
            }
            
            if (quantity < 1) {
                e.preventDefault();
                showAlert('Please select at least 1 ticket!', 'error');
                return;
            }
        });
    });
}

// Quantity controls
function initializeQuantityControls() {
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateTotalPrice(this);
        });
        
        input.addEventListener('input', function() {
            updateTotalPrice(this);
        });
    });
}

// Update total price based on quantity
function updateTotalPrice(quantityInput) {
    const price = parseFloat(quantityInput.dataset.price || 0);
    const quantity = parseInt(quantityInput.value || 0);
    const totalElement = quantityInput.closest('.event-card')?.querySelector('.total-price');
    
    if (totalElement) {
        const total = price * quantity;
        totalElement.textContent = `Total: $${total.toFixed(2)}`;
    }
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], select[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
}

// Validate individual field
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    if (fieldName === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Password validation
    if (fieldName === 'password' && value && value.length < 6) {
        isValid = false;
        errorMessage = 'Password must be at least 6 characters long';
    }
    
    // Phone validation
    if (fieldName === 'phone' && value) {
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(value) || value.length < 10) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }
    
    // Show/hide error
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

// Show field error
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#721c24';
    errorDiv.style.fontSize = '0.9rem';
    errorDiv.style.marginTop = '0.25rem';
    
    field.parentNode.appendChild(errorDiv);
}

// Clear field error
function clearFieldError(field) {
    field.classList.remove('error');
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Show alert messages
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-dynamic');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dynamic`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '1000';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
    
    // Add click to close
    alertDiv.addEventListener('click', () => {
        alertDiv.remove();
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Mobile menu toggle (if needed)
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('mobile-active');
}

// Search functionality
function searchEvents() {
    const searchInput = document.getElementById('search-input');
    const eventCards = document.querySelectorAll('.event-card');
    
    if (!searchInput) return;
    
    const searchTerm = searchInput.value.toLowerCase();
    
    eventCards.forEach(card => {
        const title = card.querySelector('.event-title')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.event-description')?.textContent.toLowerCase() || '';
        const venue = card.querySelector('.event-venue')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchTerm) || description.includes(searchTerm) || venue.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Booking confirmation
function confirmBooking(eventTitle, quantity, totalAmount) {
    return confirm(`Confirm booking for:\n\nEvent: ${eventTitle}\nQuantity: ${quantity} ticket(s)\nTotal: $${totalAmount.toFixed(2)}\n\nProceed with booking?`);
}

// Loading state management
function showLoading(element) {
    element.classList.add('loading');
    element.disabled = true;
    const originalText = element.textContent;
    element.textContent = 'Loading...';
    element.dataset.originalText = originalText;
}

function hideLoading(element) {
    element.classList.remove('loading');
    element.disabled = false;
    element.textContent = element.dataset.originalText || element.textContent;
}