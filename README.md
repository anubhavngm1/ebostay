# EboStay - Tour & Booking Platform

A modern, responsive travel booking website with admin panel and AI-powered tour customization.

## Features

### 🏠 Homepage
- Modern aesthetic design with package listings
- Search & filter functionality
- Responsive design for all devices

### 👤 Customer Features
- Sign up / Login system
- Dashboard with booking history
- AI-powered tour customization (via Gemini API)
- Profile management

### 🛠️ Admin Panel
- Manage tour packages
- View and manage bookings
- Track expenses
- Gemini AI integration for operations

## Tech Stack
- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **API**: Google Gemini API

## Project Structure
```
ebostay/
├── config/
│   └── database.php
├── pages/
│   ├── index.php (Homepage)
│   ├── login.php
│   ├── signup.php
│   ├── dashboard.php
│   ├── admin-panel.php
│   └── logout.php
├── includes/
│   ├── navbar.php
│   ├── footer.php
│   └── auth.php
├── css/
│   ├── style.css (Main stylesheet)
│   └── admin.css
├── js/
│   ├── script.js
│   └── auth.js
├── api/
│   ├── gemini-api.php
│   └── booking-handler.php
└── database/
    └── schema.sql
```

## Installation

1. Clone the repository
2. Set up MySQL database using `database/schema.sql`
3. Update `config/database.php` with your credentials
4. Add Gemini API key to `api/gemini-api.php`
5. Deploy to your server

## Usage

- **Customer**: Visit homepage → Browse packages → Login/Signup → Make booking → View dashboard
- **Admin**: Access admin panel → Manage all operations

---
Made with ❤️ for EboStay
