# Roommates App - School Project Report (Current State)

Date: 2026-04-27  
Project path: c:/xampp/htdocs/roommates-app  
Technology stack: PHP 8+, MySQL, Bootstrap 5, JavaScript, XAMPP

## 1. Project Context

Finding compatible roommates is often unstructured and unreliable when done through social media alone. This project proposes a web platform that centralizes roommate search, communication, and moderation in one system.

## 2. Main Objective

The objective was to build a practical full-stack web application that allows users to:

- create accounts securely,
- publish and search roommate listings,
- contact other users,
- manage personal preferences,
- and support administration/moderation features.

## 3. Method and Technical Choices

The project follows a classic web architecture:

- Frontend: Bootstrap-based responsive pages with custom CSS and JavaScript.
- Backend: PHP scripts handling validation, sessions, authorization, and business logic.
- Database: MySQL relational model with normalized tables.

Main reasons for these choices:

- Strong compatibility with school/local environments (XAMPP).
- Clear separation between pages, reusable helpers, and action scripts.
- Good balance between simplicity and real-world practice.

## 4. Functional Requirements Achieved

### 4.1 User features

- Registration and login with secure password handling.
- Dashboard access for authenticated users.
- Listing creation and management.
- Listing search by city and budget.
- Contact/chat between users.
- Favorites shortlist and saved searches.
- Notification center (including unread counts).
- Profile preferences and subscription plan change.
- Verification request submission.
- Report and block workflows.

### 4.2 Admin features

- Admin-only area for moderation.
- Review of verification requests (approve/reject).
- Listing/message moderation actions.
- Analytics dashboard.
- CSV export for analytics data.

## 5. Security Implementation

The current version includes important security controls:

- Password hashing: `password_hash()` and `password_verify()`.
- SQL injection prevention: PDO prepared statements.
- Access control: `require_login()` and `require_admin()` guards.
- Method hardening: mutating operations restricted to POST.
- CSRF protection: token generation and validation (`csrf_token()`, `require_csrf()`).
- Safe redirection: sanitized return paths (`sanitize_return_to()`).
- Hardened sensitive actions: logout and admin export use POST + CSRF.

## 6. UI/UX and Responsiveness

The interface is designed for desktop and mobile use:

- Responsive Bootstrap layout.
- Shared navigation and footer components.
- Light/dark mode with persistent user preference.
- Accessibility-oriented theme toggle labels/states.
- Recent responsive improvement: custom navbar expansion behavior for dense logged-in menus at large screen widths.

## 7. Database Coverage

Implemented core tables:

- `users`
- `listings`
- `messages`
- `favorites`
- `saved_searches`
- `notifications`
- `verification_requests`
- `listing_reports`
- `blocked_users`
- `activity_logs`

This model supports both normal user workflows and moderation/analytics workflows.

## 8. Testing and Validation Performed

Validation activities completed during development:

- Syntax checks on modified PHP files.
- Browser-based smoke tests for user and admin journeys.
- Re-testing after security hardening changes.
- Re-testing after responsive/navbar fixes.

Result: The current version behaves correctly for the tested critical flows.

## 9. Current Limitations

The project is strong for school demonstration but still has production-level gaps:

- No automated test suite yet.
- No CI/CD pipeline.
- No password reset/email verification flow.
- Limited rate-limiting/anti-abuse mechanisms.
- Hosting-specific behavior may still require extra checks in some providers.

## 10. Learning Outcomes

This project demonstrates practical understanding of:

- full-stack web development with PHP and MySQL,
- session/authentication management,
- secure form handling (CSRF + POST rules),
- relational database design,
- role-based authorization,
- and responsive UI iteration based on real testing.

## 11. Conclusion

The Roommates App meets its educational objective and provides a complete, secure, and usable platform for roommate matching with both user and admin roles.

Final assessment: suitable for academic evaluation and live demonstration, with clear next steps for professional production hardening.
