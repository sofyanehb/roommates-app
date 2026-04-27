# Roommates App

A PHP 8+ and MySQL roommate search application based on the requirements specification.

## Included

- Registration and login with password hashing
- Session-protected dashboard
- Add listing flow
- Search with city and budget filters
- Optional messaging support
- Bootstrap-based responsive UI
- Business plan summary page
- Admin moderation dashboard
- Admin analytics dashboard with CSV export
- Verification badge requests and moderation queue
- Listing status, expiry, and media upload
- Favorites shortlist and saved-search alerts
- Compatibility profile scoring
- Notification center
- Report and block workflows
- Subscription tier management
- Light/dark theme toggle with persistent preference
- Demo seed data for a populated showcase

## Setup

1. Import `setup_all_in_one.sql` into MySQL to create schema + seed data.
2. Configure XAMPP so the project is served from `c:\xampp\htdocs\roommates-app`.
3. Update database credentials if needed in `php/functions.php` or via environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
4. Open `index.php` in the browser.

## Notes

- The project uses PHP pages instead of static HTML pages for the core app so sessions and database queries work cleanly.
- Search results support a live client-side filter in addition to the server-side query.
- The admin area lets the demo administrator remove listings and messages during moderation.
- Admin analytics export is POST + CSRF protected.
- Logout is POST + CSRF protected.
- Listing media is stored under `uploads/listings/`.
- Demo login credentials from `setup_all_in_one.sql` use `Admin123`, `Student123`, `Student456`, and `Student789` as passwords.
