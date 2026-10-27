# Hospital Patient Registration System

A minimal, beginner-friendly Hospital Patient Registration portal built with **pure PHP (no frameworks)**, **MySQL accessed via PDO with prepared statements**, **vanilla JavaScript**, and **clean, handcrafted CSS**.

---

## 📋 Features & User Flow

1. **Phone Number Lookup (`index.php`)**:
   - Single input field for the patient's 10-digit phone number.
   - Submits via `GET` to `check.php`.
   - Centered card view with zero unnecessary vertical scrolling.

2. **Smart Patient Routing (`check.php`)**:
   - **Existing Patient**: Automatically logs in, stores patient ID in session, and redirects to `view.php` with a *"Welcome back, <Name>!"* banner.
   - **New Patient**: Renders a registration form with the phone number pre-filled and locked.

3. **New Patient Registration (`register.php`)**:
   - Fields: Full Name, Phone (pre-filled, unique), Age, Gender, Address, Reason for Visit.
   - Validated both client-side and server-side.
   - Submits via `POST`, inserts into database using prepared statements, and redirects to `view.php`.

4. **Patient Details View (`view.php`)**:
   - Displays all saved medical intake details in a structured card.
   - Includes a primary **"Edit details"** button and a **"Logout / New search"** link.
   - Protected: Only accessible when a valid session exists; otherwise redirects to `index.php`.

5. **Edit Details (`edit.php`)**:
   - Pre-fills current details for editing.
   - Phone number is locked as `readonly`.
   - Submits via `POST` to update the MySQL record using prepared statements.

6. **Session & Security (`logout.php`)**:
   - Uses native PHP sessions (`session_start()`).
   - All database operations use **PDO prepared statements** to prevent SQL injection.
   - All output is escaped using `htmlspecialchars()` to prevent Cross-Site Scripting (XSS).
   - Logging out clears `$_SESSION`, destroys the session cookie, and returns to the home page.

---

## 🗂️ File Structure

| File | Type | Description |
|---|---|---|
| [`index.php`](index.php) | PHP / HTML | Home page with phone number lookup input |
| [`check.php`](check.php) | PHP | Handles `GET` phone lookup; routes to details or registration |
| [`register.php`](register.php) | PHP | Handles `POST` patient registration with validation |
| [`view.php`](view.php) | PHP / HTML | Displays patient profile, welcome banner, and edit action |
| [`edit.php`](edit.php) | PHP / HTML | Pre-filled edit form; updates record via `POST` |
| [`logout.php`](logout.php) | PHP | Destroys PHP session and redirects to home |
| [`db.php`](db.php) | PHP | PDO database connection with exception error handling |
| [`validate.js`](validate.js) | JavaScript | Vanilla client-side validation with inline error messages |
| [`style.css`](style.css) | CSS | Minimal, human-crafted styling (no Tailwind/Bootstrap) |
| [`schema.sql`](schema.sql) | SQL | Database and table creation script |

---

## 🗄️ Database Schema

The system uses a database named **`hospital`** and a table named **`patients`**:

```sql
CREATE DATABASE IF NOT EXISTS hospital;
USE hospital;

CREATE TABLE IF NOT EXISTS patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone CHAR(10) NOT NULL UNIQUE,
  age TINYINT UNSIGNED NOT NULL,
  gender VARCHAR(10) NOT NULL,
  address TEXT NOT NULL,
  reason VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## ⚙️ Prerequisites

- **PHP 7.4+ or 8.x** with the `pdo_mysql` extension enabled in `php.ini`.
- **MySQL / MariaDB Server** (e.g., via XAMPP, WAMP, or standalone MySQL).

---

## 🚀 Setup & Installation

### Step 1: Start MySQL
If you are using **XAMPP**, open the **XAMPP Control Panel** and click **Start** next to **MySQL**.

### Step 2: Import the Database Schema
Open a terminal in the project directory (`d:\desktop\web tech`) and run:

```bash
# If your MySQL root user has no password (standard XAMPP default):
mysql -u root < schema.sql

# If your MySQL root user has a password:
mysql -u root -p < schema.sql
```

*(Alternatively, open phpMyAdmin at `http://localhost/phpmyadmin`, go to the **Import** tab, and select `schema.sql`).*

### Step 3: Configure Database Credentials
Open [`db.php`](db.php) and verify your connection settings:

```php
$host = '127.0.0.1';
$db   = 'hospital';
$user = 'root';
$pass = ''; // Enter your MySQL password if you have one set
```

### Step 4: Run the Development Server
From the project folder, start the built-in PHP development server:

```bash
php -S localhost:8000
```

*(If using XAMPP's PHP executable specifically, you can run: `C:\xampp\php\php.exe -S localhost:8000`).*

### Step 5: Open in Your Browser
Navigate to:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🧪 Validation Rules

Validation is enforced both **client-side** in [`validate.js`](validate.js) and **server-side** in PHP:

- **Full Name**: Required, letters and spaces only (`/^[a-zA-Z\s]+$/`).
- **Phone Number**: Required, exactly 10 digits (`/^\d{10}$/`), unique in database.
- **Age**: Required, whole number between `0` and `120`.
- **Gender**: Required, must select one of `Male`, `Female`, or `Other`.
- **Address**: Required, non-empty.
- **Reason for Visit**: Required, non-empty.
