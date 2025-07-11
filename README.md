# Vendora - Modern E-commerce Platform

Vendora is a secure, modern e-commerce platform that connects buyers and sellers, built with PHP, MySQL, and Docker.

## 🚀 Features

### Buyer Features
- Browse products with detailed views and search functionality
- Advanced product filtering and categorization
- Shopping cart with localStorage persistence
- Secure checkout process
- Order tracking and history
- User authentication and profile management

### Seller Features
- Product management (add, edit, delete)
- Inventory tracking and stock management
- Sales analytics and reporting
- Profile and store management
- Order fulfillment tracking

### Admin Features
- User management and role control
- Platform analytics and monitoring
- Content moderation tools
- System configuration management

## 🛡️ Security Features

- **SQL Injection Protection**: Prepared statements throughout
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Token-based request validation
- **Session Security**: Secure session management with regeneration
- **File Upload Security**: MIME type validation and size limits
- **Password Security**: bcrypt hashing with strong validation
- **Rate Limiting**: Login attempt protection
- **Security Headers**: Comprehensive HTTP security headers

## 🏗️ Technical Architecture

### Backend Stack
- **PHP 8.2** with Apache
- **MySQL 8.0** database
- **Docker** containerization
- **phpMyAdmin** for database management

### Frontend Stack
- **HTML5** with semantic markup
- **Tailwind CSS** for styling
- **Vanilla JavaScript** for interactivity
- **Font Awesome** for icons

### Project Structure
```
Vendora/
├── admin/              # Admin panel files
├── assets/             # Static assets
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   └── images/        # Static images
├── buyer/              # Buyer interface files
├── database/           # Database schema and data
├── docker/             # Docker configuration
├── includes/           # Core PHP includes
│   ├── init.php       # Application initialization
│   ├── config.php     # Configuration settings
│   ├── db.php         # Database connection
│   ├── session.php    # Session management
│   ├── security.php   # Security functions
│   ├── assets.php     # Asset management
│   ├── upload.php     # File upload handling
│   ├── header.php     # Page header
│   └── footer.php     # Page footer
├── logs/               # Application logs
├── seller/             # Seller interface files
├── uploads/            # User uploads
│   ├── products/      # Product images
│   └── profiles/      # Profile images
├── docker-compose.yml  # Docker services
├── Dockerfile         # PHP application container
└── README.md          # This file
```

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose
- Git

### Installation
1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Vendora
   ```

2. **Start the application**
   ```bash
   docker-compose up -d
   ```

3. **Access the application**
   - **Main App**: http://localhost:8081
   - **phpMyAdmin**: http://localhost:8080
   - **Database**: localhost:3306

4. **Default credentials**
   - **Admin**: admin@vendora.com / password
   - **Database**: vendora / root

## 🔧 Development

### Code Organization
The application uses a modular approach with centralized includes:

- **`includes/init.php`**: Single entry point for all pages
- **`includes/assets.php`**: Asset management and path abstraction
- **`includes/session.php`**: Centralized session management
- **`includes/security.php`**: Security utilities and validation

### Asset Management
```php
// Include assets
require_once 'includes/init.php';

// Use asset functions
<img src="<?= productImage($product['image']) ?>">
<?php includeJS('cart.js'); ?>
<?php includeCSS('custom.css'); ?>
```

### Session Management
```php
// Check authentication
if (isLoggedIn()) {
    // User is logged in
}

// Check roles
if (hasRole('seller')) {
    // User is a seller
}

// Require authentication
requireAuth();

// Require specific role
requireRole('admin');
```

## 📊 Database Schema

The application includes a comprehensive database schema with:
- **Users**: Authentication and role management
- **Products**: Product catalog with categories
- **Orders**: Order management and tracking
- **Reviews**: Product reviews and ratings
- **Categories**: Product categorization

## 🔒 Security Best Practices

- All database queries use prepared statements
- Input validation and sanitization on all forms
- Secure file upload handling with type validation
- Session fixation protection
- CSRF token validation
- Comprehensive error handling without information disclosure

## Setup

1. Copy `includes/config.example.php` to `includes/config.php`.
2. Fill in your own database credentials and settings in `config.php`.
3. The real `config.php`, `logs/`, and `uploads/` are excluded from version control for security.



