# Employee Time Tracking System
A web based Employee Time Tracking System developed as a Software Development Capstone Project.
The system is designed to allow employees to track their working hours and provide employers with tools to manage employee information and time records.
# Technologies Used
PHP
MySQL
HTML
CSS
Javascript
MAMP PHPUnit Cypress Github


# Milestone 1- Authentication Module
Milestone 1 focused on user authentication, account validation, role-based access, and authentication testing.

# Features Completed
User registration
Required field validation
Strong password validation
Password hashing
Duplicate email validation
10-digit phone number validation
Login using email or phone number
Invalid login handling
Employee and Employer role-based access
Session management
User logout
Session timeout
Account lockout after repeated failed login attempts

# Testing Completed 
# PHPUnit

Authentication unit tests were implemented using PHPUnit.

Tests include:

Password hashing and verification
Valid password verification
Invalid password rejection

All PHPUnit authentication tests are passing.

# Cypress

End-to-end login testing was implemented using Cypress.

Completed test cases:

TC-F1: Valid Login
TC-F2: Invalid Login

Both Cypress tests are passing.

# Manual Testing

Manual Testing was completed for:

Valid registration 
Requirerd and password validation 
Duplicate email vallidation
Valid and invalid phone number registration 
Login using email
Login using phone number
Invalid password handling
Employee role access 
Employee role restrications 
Logout and session protection
Account lockout

# Security impplemented 

Password hashing 
Prepared SQL statements
Role-based access control
Session protection
Session timeout
Account lockout after repeated failed login attempts

# Current Project Status

# Milestone 1: Complete

Authentication functionality and Milestone 1 testing have been completed successfully.

Further development will continue according to the capstone project milestones.




# Milestone 2 - Core Functionality and Testing

Milestone 2 focused on implementing and testing the core functionality of the Employee Time Tracking System, including employee time tracking, profile management, employer record management, and application testin

# Features Completed

# Employee Time Tracking
Employees can punch in and punch out.
The system records punch-in and punch-out times.
Working hours are calculated automatically.
Employees can view their time tracking history.
Duplicate punch-ins are prevented.
The system checks for existing open time records.

# Employee Profile Management
Employees can view their profile information.
Employees can update their name, email, and phone number.
Input validation is implemented for profile updates.
Empty or invalid profile updates are prevented.

# Employer Record Management
Employers can view employee time records.
Employers can approve or reject submitted time records.
Time records display employee names, dates, punch-in times, punch-out times, total hours, and approval status.
Employee filtering is available for reviewing records.


# Announcements
Employers can create announcements.
Employees can view announcements.
Announcements display the title, message, and posting information.

# Testing Completed

# PHPUnit
PHPUnit tests were implemented for database connectivity, time tracking, and login functionality.

Tests include:

Database connection testing.
Time tracking functionality testing.
Login functionality testing.

All implemented PHPUnit tests are passing


# Cypress
End-to-end testing was performed using Cypress.

Completed testing includes:

Employee login.
Employee profile functionality.
Employee time tracking functionality.

All implemented Cypress tests are passing.

# Postman
Postman was used to test implemented API functionality.

API requests and responses were tested.
Implemented API test cases passed.

# Manual Testing

# Manual testing was performed for:
Employee punch-in and punch-out.
Automatic working-hours calculation.
Employee time record history.
Profile information updates and validation.
Employer time record management.
Time record approval and rejection.
Announcement creation and viewing.


# Security and Validation
Session-based authentication.
Role-based access for employees and employers.
Prepared SQL statements for database operations.
Input validation for user information.
Prevention of duplicate punch-ins.
Validation of time tracking operations.

# Deployment Progress

The application has been deployed using Render.
PHP application deployed using Docker.
MySQL database configured on Render.
Database tables imported successfully.
Employer and Employee roles configured.
Live employee registration tested successfully.
Live employee login tested successfully.
Live punch-in and punch-out tested successfully.
Working-hours calculation and time record display verified.


# Current Project Status

# Milestone 2: Complete

Core employee and employer functionality implemented, with PHPUnit, Cypress, Postman, and manual testing completed for the implemented features.

# Deployment: In Progress

The application is live on Render. Employee functionality has been verified, and further deployment testing is planned for employer functionality and other features