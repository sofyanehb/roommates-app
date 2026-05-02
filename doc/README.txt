ROOMMATES APP - Installation & Launch Instructions

PROJECT STRUCTURE
-----------------
This project is organized according to the mini-project deliverable requirements:
- index.php: Main entry point
- config.php: Database connection and global configuration
- /pages/: All PHP pages (dashboard, listings, search, etc.)
- /includes/: Reusable templates (header, footer)
- /css/: Stylesheets
- /js/: JavaScript files
- /images/: Project images
- /php/: Backend functions and actions
- /doc/: Documentation (README, captures, diagrams)
- projet.sql: Database export with test data

INSTALLATION STEPS
------------------
1. Extract the project to your web server root directory.
2. Import the database using the SQL dump shipped with the project.
3. Ensure Apache and MySQL are running in XAMPP.
4. Open http://localhost/roommates-app/ in the browser.

DEFAULT ACCOUNT FOR TESTING
---------------------------
Administrator Account:
  Login: ENSIASD
  Password: ENSIASD2026

Additional Test Accounts:
  - sara@example.com / Student123
  - youssef@example.com / Student123
  - mariam@example.com / Student123

FEATURES
--------
- User registration and authentication
- Roommate search with filtering
- Listing creation and management
- Chat/messaging system
- Favorites/shortlist
- Profile management
- Notification system
- Admin moderation and analytics

SECURITY FEATURES
-----------------
- Password hashing (bcrypt)
- Session-based authentication
- CSRF protection on forms
- PDO prepared statements
- Output escaping to mitigate XSS
- Safe redirects for login flows

TROUBLESHOOTING
---------------
- Database connection failed: verify MySQL is running and credentials in config.php are valid.
- Login not working: clear browser cookies and confirm the test accounts above.
- Files not loading: verify the project path and that Apache is serving the roomates-app folder.

ADDITIONAL NOTES
----------------
- All uploads are stored in /uploads.
- Core helpers are in /php/functions.php.
- Templates are in /includes/.
- Database actions are handled by scripts in /php/.

Database Last Updated: 2026-05-03
Project Version: 1.0