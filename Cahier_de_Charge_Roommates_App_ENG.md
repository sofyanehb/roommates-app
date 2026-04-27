<div align="center">

# SOFTWARE REQUIREMENTS SPECIFICATION

## Roommates Web Application

_As-Built Software Requirements Specification (Updated)_

</div>

---

## Table of Contents

1. [Introduction and Context](#1-introduction-and-context)
2. [Technical Architecture](#2-technical-architecture)
3. [Database Model](#3-database-model)
4. [Functional Requirements](#4-functional-requirements)
5. [Security and Non-Functional Requirements](#5-security-and-non-functional-requirements)
6. [System Functional Flow](#6-system-functional-flow)
7. [Development Planning](#7-development-planning)
8. [Future Improvements](#8-future-improvements)
9. [Conclusion](#9-conclusion)

---

## 1. Introduction and Context

### 1.1 Project Context

Students often struggle to find compatible roommates through fragmented social channels. Typical problems include:

- Unstructured posts and messages
- Weak filtering capabilities
- Low trust and limited moderation
- Slow contact workflows
- No centralized product experience

This project delivers a structured web platform for discovery, matching, and communication between potential roommates.

### 1.2 Project Objective

The implemented application allows users to:

1. Register and authenticate securely
2. Publish and manage roommate listings
3. Search listings by city and budget
4. Contact listing owners and chat in-app
5. Save searches and favorites
6. Manage profile preferences and plan tier
7. Submit verification requests
8. Receive notifications
9. Log out securely

### 1.3 Project Scope

The current product includes:

- User registration, login, and protected sessions
- Listing creation, listing details page, and dashboard visibility
- Search with filters and city shortcuts
- Favorites, shortlist, and saved searches
- Reporting listings and blocking users
- Messaging and contact workflows
- Profile compatibility preferences and plan management
- Verification request workflow and admin moderation
- Admin analytics dashboard and CSV export
- Header theme toggle with persistent light/dark preference
- Responsive Bootstrap UI (desktop and mobile)

---

## 2. Technical Architecture

### 2.1 Tech Stack

| Layer         | Technology          | Role                                               |
| ------------- | ------------------- | -------------------------------------------------- |
| Frontend      | HTML5 + Bootstrap 5 | UI structure and responsive layout                 |
| Frontend      | JavaScript          | Progressive UX interactions                        |
| Backend       | PHP 8+              | Routing, business logic, sessions, security checks |
| Database      | MySQL               | Persistent relational data                         |
| Local Runtime | XAMPP               | Apache + MySQL local environment                   |

### 2.2 System Architecture

The system follows a client-server architecture:

```
Browser (UI) <-> PHP Application Layer <-> MySQL Database
```

- Browser submits form data and query filters
- PHP validates input, enforces authorization/security, and executes SQL via PDO
- MySQL stores users, listings, messages, moderation, and analytics events
- PHP returns rendered pages, redirects, flash messages, or CSV export streams

### 2.3 Current File Structure (Simplified)

```
roommates-app/
|-- index.php
|-- register.php
|-- login.php
|-- dashboard.php
|-- add_listing.php
|-- listing.php
|-- search.php
|-- shortlist.php
|-- notifications.php
|-- chat.php
|-- contact.php
|-- profile.php
|-- admin.php
|-- admin_analytics.php
|-- business_plan.php
|-- partials/
|   |-- header.php
|   `-- footer.php
|-- assets/
|   |-- css/styles.css
|   `-- js/app.js
|-- uploads/
`-- php/
    |-- functions.php
    |-- register_user.php
    |-- login_user.php
    |-- logout.php
    |-- add_listing_action.php
    |-- search_action.php
    |-- toggle_favorite.php
    |-- save_search.php
    |-- report_listing.php
    |-- block_user.php
    |-- chat_send.php
    |-- contact_action.php
    |-- update_profile.php
    |-- change_plan.php
    |-- verification_request_action.php
    |-- mark_notification_read.php
    |-- mark_all_notifications_read.php
    |-- admin_action.php
    `-- admin_export.php
```

### 2.4 Key Shared Components

- `php/functions.php` centralizes:
  - session/auth helpers
  - role guards (user/admin)
  - method guards (POST-only)
  - CSRF token generation and validation
  - flash messages and safe redirects
  - compatibility and analytics helper utilities

---

## 3. Database Model

### 3.1 Core Tables

The running product uses and depends on:

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

### 3.2 Main Relationships

- One `users` record owns many `listings`
- `messages` links sender and receiver to `users`
- `favorites` links users to listings
- `saved_searches` stores reusable filter sets per user
- `listing_reports` links reporter + listing for moderation
- `verification_requests` tracks user verification lifecycle
- `notifications` are per-user state records
- `blocked_users` stores directional block relationships
- `activity_logs` stores auditable user/system events

---

## 4. Functional Requirements

### 4.1 Authentication and Session Management

#### Registration

- User provides name, email, password, age, gender, and city
- Password is hashed with `password_hash()`
- Email uniqueness is validated before insert
- On success, account is created and user is logged in
- Redirect target after registration: dashboard

#### Login

- Email/password are validated against stored hash via `password_verify()`
- Session is initialized with authenticated user data
- Redirect target after login: dashboard (or safe `return_to` target)

#### Logout

- Logout is a POST-only action
- Valid CSRF token is required
- Session is destroyed and user is redirected to home page

### 4.2 Listing Management

- Authenticated users can create listings
- Listing fields include budget, move-in date, preferences, status, expiry, optional image
- Dashboard shows user-owned listings with details
- Public search/listing pages show discoverable listings
- Single listing page presents full details and related listings from same city

### 4.3 Search and Discovery

- Search filters: city and max budget
- City quick links available
- Results include user/listing context cards
- Authenticated users can:
  - favorite/unfavorite listings
  - save current search
  - report listing
  - block listing owner
  - open contact/chat path

### 4.4 Messaging and Contact

- Contact page enables direct outreach
- Chat page supports user-to-user messages
- Message activity appears in dashboard/admin views

### 4.5 Profile, Plans, and Verification

- Users manage compatibility preferences:
  - sleep schedule
  - smoking preference
  - pet preference
  - study habit
- Users can switch plan tier
- Users can submit verification requests with document URL and optional note
- Verification status is visible on profile

### 4.6 Notifications and Shortlist

- Notification center supports mark-read and mark-all-read actions
- Shortlist page includes favorites and saved searches with management actions

### 4.7 Administration and Moderation

Admin-only area provides:

- activity overview metrics
- user/listing/message moderation actions
- report review/dismiss actions
- verification approve/reject actions
- analytics dashboard and CSV export

---

## 5. Security and Non-Functional Requirements

### 5.1 Security Controls

| Control                  | Current Implementation                    |
| ------------------------ | ----------------------------------------- |
| Password hashing         | `password_hash()`                         |
| Password verification    | `password_verify()`                       |
| SQL injection prevention | PDO prepared statements                   |
| Session/page protection  | login and admin route guards              |
| Method hardening         | POST-only for mutating actions            |
| CSRF protection          | token generation + server-side validation |
| Redirect safety          | sanitized return-to destinations          |
| Moderation controls      | report/block/admin workflows              |

### 5.2 Performance and Usability

- Responsive UI for desktop and mobile breakpoints
- Fast local feedback under XAMPP runtime
- Flash messages for success/error states
- Clear navigation across user and admin paths
- Theme preference persistence using local storage (light/dark)

### 5.3 Constraints

- Developed as a local web application on XAMPP
- Designed for practical demonstration and extensibility
- No external cloud deployment required for the current scope

---

## 6. System Functional Flow

### 6.1 User Journey (Current)

1. User lands on home page
2. User registers or logs in
3. User accesses dashboard
4. User creates listings and explores search
5. User interacts with favorites, saved searches, messaging, and profile
6. User submits verification request (optional)
7. User logs out via POST + CSRF

### 6.2 Admin Journey

1. Admin logs in
2. Admin opens moderation dashboard
3. Admin processes reports and verification queue
4. Admin removes problematic listings/messages when needed
5. Admin reviews analytics and exports CSV

### 6.3 Example Search Query Pattern

```sql
SELECT l.*, u.name, u.city
FROM listings l
JOIN users u ON l.user_id = u.id
WHERE u.city LIKE ?
  AND l.budget <= ?;
```

---

## 7. Development Planning

### 7.1 Delivered Milestones

1. Core authentication and session guards
2. Listing lifecycle and search interface
3. Messaging/contact implementation
4. Profile, plan, and verification workflow
5. Notifications, favorites, shortlist, saved searches
6. Admin moderation and analytics export
7. Security hardening pass (POST + CSRF + route auditing)
8. UI responsiveness and pointer-overlap fixes
9. Global light/dark mode with icon toggle and contrast fixes

### 7.2 Current Quality Status

- Syntax validation completed for modified PHP files
- End-to-end browser smoke tests executed for user and admin flows
- CSRF and POST-only enforcement applied to mutating routes

---

## 8. Future Improvements

1. Email verification and password reset flows
2. Rich media validation and image optimization pipeline
3. Pagination and advanced ranking in search
4. Real-time chat transport and typing indicators
5. Stronger rate limiting and abuse prevention
6. Automated test suite (PHP unit/integration + browser regression)
7. Deployment pipeline and production observability

---

## 9. Conclusion

The project has evolved beyond the initial minimal MVP and now delivers a secure, structured, and operationally credible roommate platform with both user-facing and admin-facing workflows.

This document reflects the current implemented behavior, including hardened security policies, moderation capabilities, profile/verification flows, and analytics export.
