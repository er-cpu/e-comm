# HESMB - SmartTrade Africa E-Commerce Platform

A modern, full-featured e-commerce platform built for **SmartTrade Africa Ltd**, a Tanzanian-based online marketplace. Built with Laravel 12 and modern frontend tooling.

## Project Overview

HESMB is a production-ready e-commerce solution with comprehensive customer, admin, and security features. The platform supports multiple payment gateways, biometric authentication, OTP verification, real-time notifications, and complete order management.

## Features

### Customer Features
- **Product Catalog** — Browse products with category filtering and keyword search
- **Product Detail Pages** — Ratings, reviews, stock info, discounts
- **Shopping Cart** — Add/update/remove items with stock validation
- **Wishlist** — Save products for later
- **Checkout & Payment** — Multi-gateway payment processing
- **Order Management** — View order history and individual order details
- **PDF Receipts** — Downloadable PDF order receipts
- **Product Ratings & Reviews** — Star ratings (1-5) with optional text reviews
- **In-App Notifications** — Real-time bell notifications (order confirmed, etc.)
- **Activity History** — Per-user activity log
- **User Profile** — Edit profile, change password, account deletion
- **Informational Pages** — About, Contact, FAQ, Privacy Policy, Terms, Refund/Return Policies, Shipping Policy, Payment Policy

### Authentication & Security
- **Email/Password Auth** — Registration, login, password reset, email verification
- **Biometric Authentication (WebAuthn/FIDO2)** — Passwordless login via fingerprint, Face ID, or Windows Hello
- **OTP via SMS** — Phone number verification for biometric enrollment
- **Role-Based Access** — Admin and user roles with middleware protection
- **Password Confirmation** — Re-authenticate before sensitive actions

### Admin Dashboard
- **Dashboard** — Stats: total products, orders, users, revenue; recent orders
- **Product CRUD** — Create/edit/delete products with image upload
- **Category Management** — Create/edit/delete categories
- **Order Management** — View all orders, update status (pending/confirmed/shipped/completed/refunded)
- **Payment Management** — View payments, verify pending, process refunds
- **User Management** — View/edit/delete users, change roles
- **Analytics & Reports** — Revenue stats, order breakdown, monthly revenue chart (6 months), best-selling products (top 10), most active customers (top 10)
- **Activity Logs** — Full admin view of all user activity
- **Ratings Moderation** — Review all product ratings
- **Customer Support** — Manage support tickets, reply, close tickets

## Tech Stack

| Component | Technology |
|---|---|
| **Framework** | Laravel 12.x |
| **PHP** | ^8.2 |
| **Database** | SQLite (default), MySQL configurable |
| **Frontend** | Blade, Tailwind CSS 3, Bootstrap 5.3, Alpine.js |
| **Build Tool** | Vite 7.x |
| **Payment** | Stripe, PayPal, Flutterwave, Mock Gateway |
| **Currency** | Tanzanian Shilling (TZS) with configurable USD conversion |
| **PDF** | barryvdh/laravel-dompdf |
| **Biometrics** | lbuchs/webauthn (WebAuthn/FIDO2) |
| **Auth Scaffold** | Laravel Breeze |
| **Testing** | PHPUnit 11.x |

## Requirements

- PHP ^8.2
- Composer
- Node.js & npm
- SQLite or MySQL

## Installation

```bash
# Clone the repository
git clone https://github.com/bamugileki/SECURE_E-COMERCE_WEBSITE.git
cd SECURE_E-COMERCE_WEBSITE

# Install PHP dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure your database in .env, then run migrations
php artisan migrate --seed

# Install and build frontend assets
npm install
npm run build

# Start the development server
php artisan serve
```

### Development Server with Queue & Vite

```bash
composer run dev
```

## Setup

For a one-command setup on a fresh project:

```bash
composer run setup
```

## Project Structure

### Controllers
- **AdminController** — Full admin dashboard and management features
- **ProductController** — Product browsing with filtering and search
- **CartController** — Shopping cart management
- **WishlistController** — Wishlist operations
- **PaymentController** — Payment processing and order creation
- **OrderController** — Order history and details
- **ReceiptController** — PDF receipt generation
- **RatingController** — Product ratings and reviews
- **BiometricController** — WebAuthn/FIDO2 registration and authentication
- **OtpController** — OTP generation and verification
- **NotificationController** — In-app notifications
- **ActivityLogController** — User activity tracking
- **Auth Controllers** — Complete authentication workflow (Login, Register, Password Reset, Email Verification)

### Models
- **User** — Customer and admin users with biometric credentials
- **Product** — Product catalog with pricing and stock management
- **Category** — Product categorization
- **Order** — Order management with status tracking
- **OrderItem** — Line items in orders
- **Cart** — Shopping cart items
- **Wishlist** — Saved products
- **Payment** — Payment transactions with multi-gateway support
- **ProductRating** — Product ratings and reviews
- **Credential** — WebAuthn biometric credentials
- **OtpCode** — OTP codes for phone verification
- **ActivityLog** — User activity history
- **SupportMessage** — Customer support tickets
- **BiometricSetupToken** — Token-based biometric setup

### Payment Gateways
- **Stripe** — Primary payment processor
- **PayPal** — Alternative payment method
- **Flutterwave** — Regional payment option
- **Mock Gateway** — Testing and development

## Testing

```bash
composer run test
```

## Payment Configuration

Configure payment gateways in `.env`:

```env
# Payment Gateway Selection
PAYMENT_GATEWAY=stripe  # or paypal, flutterwave, mock

# Stripe Configuration
STRIPE_ENABLED=true
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...

# PayPal Configuration
PAYPAL_ENABLED=false
PAYPAL_CLIENT_ID=...
PAYPAL_SECRET=...
PAYPAL_MODE=sandbox

# Flutterwave Configuration
FLUTTERWAVE_ENABLED=false
FLUTTERWAVE_PUBLIC_KEY=...
FLUTTERWAVE_SECRET_KEY=...
FLUTTERWAVE_MODE=sandbox

# Mock Payment (for testing)
MOCK_PAYMENT_ENABLED=true

# Currency Configuration
PAYMENT_CURRENCY=TZS
TZS_TO_USD_RATE=2400
```

## Biometric Setup

Users can enable biometric authentication (fingerprint, Face ID, Windows Hello) for passwordless login:

1. Go to profile settings
2. Request biometric setup link
3. Verify phone with OTP
4. Register biometric credential via secure WebAuthn protocol
5. Use biometric for login instead of email/password

## API Integration Points

### Authentication Endpoints
- `POST /auth/login` — Standard email/password login
- `GET /auth/register` — User registration
- `POST /biometric/register-options` — Get biometric registration options
- `POST /biometric/register` — Register biometric credential
- `POST /biometric/authenticate-options` — Get biometric authentication options
- `POST /biometric/authenticate` — Authenticate with biometric
- `POST /otp/send` — Send OTP for phone verification
- `POST /otp/verify` — Verify OTP code

### Customer Endpoints
- `GET /products` — Browse products
- `GET /products/{id}` — Product details
- `POST /cart` — Add to cart
- `PUT /cart/{id}` — Update cart item
- `DELETE /cart/{id}` — Remove from cart
- `POST /wishlist` — Add to wishlist
- `DELETE /wishlist/{id}` — Remove from wishlist
- `POST /checkout` — Initiate payment
- `GET /orders` — View orders
- `GET /orders/{id}` — Order details
- `POST /ratings` — Submit product rating

### Admin Endpoints
- `GET /admin/dashboard` — Dashboard overview
- `GET /admin/products` — Product management
- `GET /admin/orders` — Order management
- `GET /admin/payments` — Payment management
- `GET /admin/users` — User management
- `GET /admin/reports` — Analytics and reports
- `GET /admin/support` — Support tickets

## Email Configuration

Configure email driver in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Database Seeding

The project includes database seeders for demo data:

```bash
php artisan migrate --seed
```

This will create:
- Sample categories and products
- Demo admin user
- Test customers
- Sample orders and payments

## Security Features

- ✅ CSRF protection on all forms
- ✅ XSS protection with Blade escaping
- ✅ Rate limiting on authentication endpoints
- ✅ Password hashing with Bcrypt
- ✅ Email verification required for account creation
- ✅ Biometric authentication with WebAuthn
- ✅ OTP verification for sensitive operations
- ✅ Role-based access control (RBAC)
- ✅ Activity logging for audit trails
- ✅ Secure payment token handling

## Performance Optimizations

- Database query optimization with eager loading
- Pagination for large datasets
- Image optimization for product uploads
- Caching for frequently accessed data
- Asset minification with Vite

## License

[License](LICENSE)

## Support

For technical support or issues, please contact the development team or open an issue in the repository.

---

**Built by:** SmartTrade Africa Ltd  
**Platform:** HESMB E-Commerce System  
**Version:** 1.0.0  
**Last Updated:** June 2026
