# Simple Payroll System

A comprehensive web-based payroll management system built with PHP, MySQL, and Bootstrap 5.

## Features

### Admin Features
- **Dashboard**: Overview of employees, payslips, and statistics
- **Employee Management**: Create, edit, and delete employee accounts
- **Payslip Management**: View all payslips with filtering options
- **Reports**: Filter payslips by employee, month, and year

### Employee Features
- **Dashboard**: Generate new payslips and view recent ones
- **Payslip Generation**: Input daily wage and days worked to auto-calculate payslip
- **Payslip History**: View and print all personal payslips
- **Filtering**: Filter personal payslips by month and year

### Payslip Calculations
- **Gross Income**: Daily Wage × Days Worked
- **Deductions**:
  - Tax: 10% of gross income
  - SSS: 2% of gross income
  - PhilHealth: ₱250 (fixed)
  - Pag-IBIG: ₱200 (fixed)
- **Net Income**: Gross Income - Total Deductions

## Technology Stack

- **Backend**: PHP 7.4+ with MySQLi
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5.3.0
- **Authentication**: Session-based with password hashing
- **Security**: Prepared statements, input validation, CSRF protection

## Installation

### Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser

### Setup Instructions

1. **Clone/Download the project**
   ```
   Place the project folder in your web server directory:
   - XAMPP: C:\xampp\htdocs\simplepayslipSystem
   - WAMP: C:\wamp64\www\simplepayslipSystem
   ```

2. **Database Setup**
   - Start Apache and MySQL services
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the database structure:
     - Create a new database named `payroll_system`
     - Import the `database.sql` file or run the SQL commands manually

3. **Configuration**
   - Update database credentials in `config/database.php` if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'payroll_system');
     ```

4. **Access the Application**
   - Open your web browser
   - Navigate to: `http://localhost/simplepayslipSystem/setup.php` (recommended first visit)
   - Or go directly to: `http://localhost/simplepayslipSystem`
   - You will be redirected to the login page

## Default Accounts

### Admin Account
- **Email**: admin@payroll.com
- **Password**: admin123
- **Role**: Administrator

### Employee Account (Demo)
- **Email**: john@payroll.com
- **Password**: employee123
- **Role**: Employee

## File Structure

```
simplepayslipSystem/
├── admin/
│   ├── dashboard.php          # Admin dashboard
│   ├── employees.php          # Employee management
│   ├── payslips.php          # All payslips view
│   └── view_payslip.php      # Payslip details
├── employee/
│   ├── dashboard.php          # Employee dashboard
│   ├── payslips.php          # Employee payslips
│   └── view_payslip.php      # Payslip details
├── includes/
│   └── auth.php              # Authentication class
├── config/
│   └── database.php          # Database configuration
├── assets/
│   └── css/
│       └── style.css         # Custom styles
├── index.php                 # Main entry point
├── login.php                 # Login page
├── logout.php                # Logout handler
├── unauthorized.php          # Access denied page
├── database.sql              # Database structure
└── README.md                 # This file
```

## Usage Guide

### For Administrators

1. **Login** with admin credentials
2. **Add Employees**: Go to Employees → Add Employee
3. **Manage Employees**: Edit or delete employee accounts
4. **View Payslips**: Monitor all employee payslips
5. **Filter Reports**: Use filters to find specific payslips

### For Employees

1. **Login** with employee credentials
2. **Generate Payslip**: 
   - Select month and year
   - Enter daily wage amount
   - Enter number of days worked
   - Click "Generate Payslip"
3. **View History**: Access all your previous payslips
4. **Print Payslips**: View and print individual payslips

## Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **Prepared Statements**: All database queries use prepared statements
- **Session Management**: Secure session handling for authentication
- **Role-based Access**: Different access levels for admin and employees
- **Input Validation**: Server-side validation for all forms
- **SQL Injection Prevention**: MySQLi prepared statements

## Customization

### Adding New Deduction Types
1. Add new columns to the `payslips` table
2. Update the payslip calculation logic in `employee/dashboard.php`
3. Modify the payslip display templates

### Changing Deduction Rates
Update the calculation logic in `employee/dashboard.php`:
```php
$tax_deduction = $gross_income * 0.10; // Change tax rate here
$sss_deduction = $gross_income * 0.02; // Change SSS rate here
```

### Styling Modifications
- Edit `assets/css/style.css` for custom styles
- Bootstrap classes can be modified in individual PHP files

## Troubleshooting

### Quick Diagnosis
- **First time setup**: Visit `http://localhost/simplepayslipSystem/setup.php`
- **Connection issues**: Visit `http://localhost/simplepayslipSystem/test_connection.php`
- **Database setup**: Visit `http://localhost/simplepayslipSystem/install.php`

### Common Issues

1. **"Failed to open stream" or "No such file or directory"**
   - **Solution**: Run the installation script: `install.php`
   - **Cause**: Database or config files not properly set up

2. **Database Connection Error**
   - Check XAMPP/WAMP MySQL service is running
   - Verify database credentials in `config/database.php`
   - Ensure database `payroll_system` exists
   - Run: `http://localhost/simplepayslipSystem/test_connection.php`

3. **"Database does not exist" Error**
   - **Solution**: Run `install.php` to create database and tables
   - Or manually import `database.sql` in phpMyAdmin

4. **Login Issues**
   - Verify default accounts exist in database
   - Check password hashing is working correctly
   - Try running installation script again

5. **Permission Errors**
   - Ensure web server has read/write permissions
   - Check file paths are correct
   - Make sure you're accessing via `http://localhost/` not file://

6. **Styling Issues**
   - Verify Bootstrap CDN is accessible (internet connection)
   - Check custom CSS file is loading
   - Clear browser cache

7. **XAMPP Issues**
   - Start Apache and MySQL services in XAMPP Control Panel
   - Check if ports 80 (Apache) and 3306 (MySQL) are available
   - Try restarting XAMPP services

## Browser Compatibility

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review the code comments
3. Test with default accounts first

## Version History

- **v1.0.0** - Initial release with core functionality
  - User authentication system
  - Employee management
  - Payslip generation and calculation
  - Print functionality
  - Responsive design
