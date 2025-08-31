# myExpence-tracker
dailly life expense update website
# MyExpenseTracker

![Expense Tracker Image](path/to/your/image.jpg) <!-- Replace with your actual image path or URL, e.g., the calculator and coins image -->

## Overview
MyExpenseTracker is a web-based application for tracking personal expenses, income, and budgets. It helps users manage their finances by allowing them to add, edit, and delete expenses/income, set monthly budgets, view progress reports, and generate category-wise summaries. The app is built with PHP, MySQL, Bootstrap, and Chart.js for visualizations.

### Key Features
- **User Authentication**: Secure registration and login system with password hashing (using `password_hash` and `password_verify`).
- **Expense Management**: Add, edit, delete expenses with categories (e.g., Food, Transport, Shopping). View recent expenses in a table.
- **Income Tracking**: Add, edit, delete income entries and compare with expenses.
- **Budget Setting**: Set monthly budgets, track progress with progress bars, and get alerts for overspending.
- **Reports**: Monthly expense reports by category, including pie charts for visualization.
- **Contact Form**: Submit messages to support, stored in the database.
- **Responsive Design**: Built with Bootstrap for mobile-friendly interfaces.
- **Session Management**: Ensures users are logged in for protected pages, with automatic redirects.

## Technologies Used
- **Backend**: PHP 7+ (with PDO/MySQLi for database interactions)
- **Database**: MySQL (database name: `project`; tables: `register`, `expenses`, `income`, `budgets`, `contact_messages`)
- **Frontend**: HTML5, CSS3, Bootstrap 5.3.2, Chart.js for charts
- **Other**: Font Awesome for icons, JavaScript for form validations and modals

## Installation
1. **Prerequisites**:
   - XAMPP/WAMP or any PHP server with MySQL.
   - PHP 7+ and MySQL 5+.

2. **Database Setup**:
   - Create a database named `project`.
   - Import the SQL schema (create the following tables manually or via script):
     ```sql
     CREATE TABLE register (
         id INT AUTO_INCREMENT PRIMARY KEY,
         fullname VARCHAR(100) NOT NULL,
         email VARCHAR(100) UNIQUE NOT NULL,
         username VARCHAR(50) UNIQUE NOT NULL,
         password VARCHAR(255) NOT NULL
     );

     CREATE TABLE expenses (
         expense_id INT AUTO_INCREMENT PRIMARY KEY,
         user_id INT NOT NULL,
         title VARCHAR(100) NOT NULL,
         amount DECIMAL(10,2) NOT NULL,
         category VARCHAR(50) NOT NULL,
         expense_date DATE NOT NULL,
         notes TEXT,
         FOREIGN KEY (user_id) REFERENCES register(id)
     );

     CREATE TABLE income (
         income_id INT AUTO_INCREMENT PRIMARY KEY,
         user_id INT NOT NULL,
         title VARCHAR(100) NOT NULL,
         amount DECIMAL(10,2) NOT NULL,
         income_date DATE NOT NULL,
         notes TEXT,
         FOREIGN KEY (user_id) REFERENCES register(id)
     );

     CREATE TABLE budgets (
         budget_id INT AUTO_INCREMENT PRIMARY KEY,
         user_id INT NOT NULL,
         amount DECIMAL(10,2) NOT NULL,
         month INT NOT NULL,
         year INT NOT NULL,
         FOREIGN KEY (user_id) REFERENCES register(id)
     );

     CREATE TABLE contact_messages (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(100) NOT NULL,
         email VARCHAR(100) NOT NULL,
         subject VARCHAR(150),
         message TEXT NOT NULL
     );