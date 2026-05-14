# PCX E-Commerce System - Setup Guide

## Initial Setup Instructions

### Step 1: Run the Database Setup Script

1. Make sure your XAMPP MySQL is running
2. Navigate to: `http://localhost/pcx/public/setup.php`
3. This will:
   - Create the `pcx_db` database
   - Create all required tables
   - Insert seed data (branches, categories, products, promotions, employees, customers, inventory)

**Note:** The setup script will DROP any existing database named `pcx_db` and create a fresh one.

### Step 2: Verify System Health

After setup, check the system health at: `http://localhost/pcx/public/health.php`

This will show:
- Database connection status
- All tables and record counts
- Sample data for testing

---

## Test Credentials

### Employee Login (Admin Dashboard)
Access: `http://localhost/pcx/public/?r=auth/auth/login`

| Role | Username | Password |
|------|----------|----------|
| Administrator | `admin` | `admin123` |
| Sales Representative | `john` | `admin123` |
| Technician | `tech` | `admin123` |

### Customer Login/Register
- **Email:** `customer@test.local`
- **Password:** `customer123`
- Or register a new account

---

## Features Implemented

### 1. Employee Dashboard
- Professional layout with header, navigation bar, and footer
- Dashboard shows pending orders, low stock alerts, and service tickets
- Sales report display

### 2. Admin Management (Full CRUD)
- **User Management:** View, edit, delete customers
- **Employee Management:** Create, edit, delete employees (roles: Administrator, Sales Rep, Technician)
- **Product Management:** Create, edit, delete products
- **Category Management:** Create, edit, delete product categories

### 3. Database Entities
- **Branches:** Multiple business locations
- **Categories:** Product categorization
- **Products:** Complete product catalog with pricing and warranty
- **Promotions:** Sales promotions and campaigns
- **Employees:** Staff management with roles
- **Customers:** User accounts
- **Orders & Inventory:** Order and stock management
- **Service Tickets:** Customer support ticketing

---

## Admin Authority

The **Administrator** role has the highest authority and can:
- Manage all users (customers)
- Manage all employees and assign roles
- Create, edit, and delete products
- Manage product categories
- View all orders and inventory
- Manage service tickets and promotions

Role-based access control is enforced on all admin pages.

---

## Database Structure

### Key Tables
- **Branch** - Business locations
- **Employee** - Staff with roles (Administrator, Sales Representative, Technician)
- **Customer** - Customer accounts
- **Category** - Product categories
- **Product** - Product catalog
- **Promotion** - Sales promotions
- **Orders** - Customer orders
- **Inventory** - Stock management
- **Service_Ticket** - Support tickets

### Password Hashing
All passwords are hashed using PHP's `password_hash()` with bcrypt algorithm.

---

## Troubleshooting

### Can't login?
1. Run the setup script: `http://localhost/pcx/public/setup.php`
2. Check health status: `http://localhost/pcx/public/health.php`
3. Verify MySQL is running
4. Check database credentials in: `app/config.php`

### Missing data (products, promotions, etc.)?
Run the setup script to populate seed data:
`http://localhost/pcx/public/setup.php`

### 404 errors?
Check that the routing is correct in `public/index.php` and the module exists.

---

## File Locations

- Setup Script: `/pcx/public/setup.php`
- Health Check: `/pcx/public/health.php`
- Config: `/pcx/app/config.php`
- Admin Module: `/pcx/modules/admin/`
- Auth Module: `/pcx/modules/auth/`
- Database Scripts: `/pcx/sql/`
