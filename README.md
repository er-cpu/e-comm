# HESMB - SmartTrade Africa E-Commerce Platform

A modern, full-featured e-commerce platform built for **SmartTrade Africa Ltd**, a Tanzanian-based online marketplace. Built with Laravel 12 and modern frontend tooling.

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

## Testing

```bash
composer run test
```

## License

[License](LICENSE)
