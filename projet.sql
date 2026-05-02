-- Roommates App single-file setup
-- Includes schema + demo seed data

CREATE DATABASE IF NOT EXISTS roommates_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE roommates_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20) NOT NULL,
    city VARCHAR(100) NOT NULL,
    plan_tier ENUM('free', 'pro', 'verified') NOT NULL DEFAULT 'free',
    verification_status ENUM('none', 'pending', 'verified', 'rejected') NOT NULL DEFAULT 'none',
    sleep_schedule ENUM('early', 'late', 'flexible') DEFAULT 'flexible',
    smoking_preference ENUM('no', 'yes', 'occasionally') DEFAULT 'no',
    pet_preference ENUM('no_pets', 'pets_ok', 'pet_owner') DEFAULT 'no_pets',
    study_habit ENUM('quiet', 'moderate', 'social') DEFAULT 'moderate',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    budget INT NOT NULL,
    move_in_date DATE NOT NULL,
    preferences TEXT NOT NULL,
    status ENUM('open', 'reserved', 'closed') NOT NULL DEFAULT 'open',
    expires_at DATETIME NULL,
    image_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_listings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favorite (user_id, listing_id),
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS saved_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    city VARCHAR(100) NOT NULL DEFAULT '',
    budget_max INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_saved_searches_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS verification_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    document_url VARCHAR(255) NOT NULL,
    note TEXT,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_verification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_verification_admin FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS listing_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('open', 'reviewed', 'dismissed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reports_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    CONSTRAINT fk_reports_reporter FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blocked_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blocked_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_block (user_id, blocked_user_id),
    CONSTRAINT fk_block_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_block_blocked FOREIGN KEY (blocked_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    event_type VARCHAR(100) NOT NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_event (event_type),
    INDEX idx_activity_created_at (created_at),
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

USE roommates_db;

INSERT INTO users (name, email, password, age, gender, city) VALUES
('Admin User', 'admin@roommates.local', '$2y$12$8Kf4008c4MILfTR/fCudjOo/Ro3zegz2jwocmZd1WeXruam5nb1IO', 32, 'Other', 'Casablanca'),
('Sara El Amrani', 'sara@example.com', '$2y$12$3z5x4fxp.ADQRdsInQq9meNRJqbOuo7gZtuxEDj17FVuUY0UFIFt6', 24, 'Female', 'Rabat'),
('Youssef Benali', 'youssef@example.com', '$2y$12$NRFgBzykwlVq9O.Hy2IMYuO7LQxPCuEbyu46Og/UqI5XQ5qRq15/O', 26, 'Male', 'Casablanca'),
('Mariam El Idrissi', 'mariam@example.com', '$2y$12$m7vweUg9GWZ8iM5ys1rRIOqycZysqLEdBB/CQSTaBs9d8iSyrFHRy', 23, 'Female', 'Marrakech')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
password = VALUES(password),
age = VALUES(age),
gender = VALUES(gender),
city = VALUES(city);

UPDATE users
SET
	plan_tier = CASE email
		WHEN 'admin@roommates.local' THEN 'verified'
		WHEN 'sara@example.com' THEN 'pro'
		WHEN 'youssef@example.com' THEN 'free'
		WHEN 'mariam@example.com' THEN 'verified'
		ELSE plan_tier
	END,
	verification_status = CASE email
		WHEN 'admin@roommates.local' THEN 'verified'
		WHEN 'mariam@example.com' THEN 'verified'
		WHEN 'sara@example.com' THEN 'none'
		WHEN 'youssef@example.com' THEN 'none'
		ELSE verification_status
	END,
	sleep_schedule = CASE email
		WHEN 'sara@example.com' THEN 'early'
		WHEN 'youssef@example.com' THEN 'late'
		ELSE 'flexible'
	END,
	smoking_preference = CASE email
		WHEN 'youssef@example.com' THEN 'occasionally'
		ELSE 'no'
	END,
	pet_preference = CASE email
		WHEN 'mariam@example.com' THEN 'pets_ok'
		ELSE 'no_pets'
	END,
	study_habit = CASE email
		WHEN 'sara@example.com' THEN 'quiet'
		WHEN 'youssef@example.com' THEN 'social'
		ELSE 'moderate'
	END;

INSERT INTO listings (user_id, budget, move_in_date, preferences)
SELECT u.id, 2500, '2026-09-01', 'Looking for a clean and quiet apartment near the university with respectful roommates.'
FROM users u
WHERE u.email = 'sara@example.com'
AND NOT EXISTS (
	SELECT 1 FROM listings l
	WHERE l.user_id = u.id AND l.budget = 2500 AND l.move_in_date = '2026-09-01'
)
UNION ALL
SELECT u.id, 3200, '2026-08-15', 'Prefer a furnished room, stable internet, and a balanced lifestyle.'
FROM users u
WHERE u.email = 'youssef@example.com'
AND NOT EXISTS (
	SELECT 1 FROM listings l
	WHERE l.user_id = u.id AND l.budget = 3200 AND l.move_in_date = '2026-08-15'
)
UNION ALL
SELECT u.id, 2100, '2026-07-20', 'Need a calm place with good access to transport and shared responsibilities.'
FROM users u
WHERE u.email = 'mariam@example.com'
AND NOT EXISTS (
	SELECT 1 FROM listings l
	WHERE l.user_id = u.id AND l.budget = 2100 AND l.move_in_date = '2026-07-20'
);

UPDATE listings
SET
	status = CASE
		WHEN user_id = (SELECT id FROM users WHERE email = 'sara@example.com' LIMIT 1) AND budget = 2500 THEN 'open'
		WHEN user_id = (SELECT id FROM users WHERE email = 'youssef@example.com' LIMIT 1) AND budget = 3200 THEN 'reserved'
		ELSE 'open'
	END,
	expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY);

INSERT INTO messages (sender_id, receiver_id, message)
SELECT s.id, r.id, 'Hi, your listing looks close to my budget. Are you still looking for a roommate?'
FROM users s
JOIN users r ON r.email = 'youssef@example.com'
WHERE s.email = 'sara@example.com'
AND NOT EXISTS (
	SELECT 1 FROM messages m
	WHERE m.sender_id = s.id
	  AND m.receiver_id = r.id
	  AND m.message = 'Hi, your listing looks close to my budget. Are you still looking for a roommate?'
)
UNION ALL
SELECT s.id, r.id, 'Hello, I am interested in your profile. Can we discuss the apartment details?'
FROM users s
JOIN users r ON r.email = 'mariam@example.com'
WHERE s.email = 'youssef@example.com'
AND NOT EXISTS (
	SELECT 1 FROM messages m
	WHERE m.sender_id = s.id
	  AND m.receiver_id = r.id
	  AND m.message = 'Hello, I am interested in your profile. Can we discuss the apartment details?'
)
UNION ALL
SELECT s.id, r.id, 'Thanks for the response. Let me know when you are available to chat.'
FROM users s
JOIN users r ON r.email = 'sara@example.com'
WHERE s.email = 'mariam@example.com'
AND NOT EXISTS (
	SELECT 1 FROM messages m
	WHERE m.sender_id = s.id
	  AND m.receiver_id = r.id
	  AND m.message = 'Thanks for the response. Let me know when you are available to chat.'
);

INSERT INTO favorites (user_id, listing_id)
SELECT u.id, l.id
FROM users u
JOIN listings l ON l.user_id = (SELECT id FROM users WHERE email = 'mariam@example.com' LIMIT 1) AND l.budget = 2100
WHERE u.email = 'sara@example.com'
AND NOT EXISTS (
	SELECT 1 FROM favorites f WHERE f.user_id = u.id AND f.listing_id = l.id
)
UNION ALL
SELECT u.id, l.id
FROM users u
JOIN listings l ON l.user_id = (SELECT id FROM users WHERE email = 'sara@example.com' LIMIT 1) AND l.budget = 2500
WHERE u.email = 'youssef@example.com'
AND NOT EXISTS (
	SELECT 1 FROM favorites f WHERE f.user_id = u.id AND f.listing_id = l.id
);

INSERT INTO saved_searches (user_id, city, budget_max)
SELECT u.id, 'Casablanca', 3000
FROM users u
WHERE u.email = 'sara@example.com'
UNION ALL
SELECT u.id, 'Rabat', 2800
FROM users u
WHERE u.email = 'youssef@example.com';

INSERT INTO notifications (user_id, title, body)
SELECT u.id, 'Welcome to Pro plan', 'Your account was upgraded to Pro for demo purposes.'
FROM users u
WHERE u.email = 'sara@example.com'
UNION ALL
SELECT u.id, 'Saved search alert', 'A new listing in Rabat matches your criteria.'
FROM users u
WHERE u.email = 'youssef@example.com'
UNION ALL
SELECT u.id, 'Verification complete', 'Your account now has a verified badge.'
FROM users u
WHERE u.email = 'mariam@example.com';

INSERT INTO verification_requests (user_id, document_url, note, status, reviewed_by, reviewed_at)
SELECT u.id, 'https://example.com/verification/mariam', 'Student card attached.', 'approved', a.id, NOW()
FROM users u
JOIN users a ON a.email = 'admin@roommates.local'
WHERE u.email = 'mariam@example.com';

INSERT INTO listing_reports (listing_id, reporter_id, reason, status)
SELECT l.id, u.id, 'Possible outdated availability status.', 'open'
FROM users u
JOIN listings l ON l.user_id = (SELECT id FROM users WHERE email = 'youssef@example.com' LIMIT 1) AND l.budget = 3200
WHERE u.email = 'sara@example.com';

INSERT INTO activity_logs (user_id, event_type, details)
SELECT u.id, 'login', 'Demo login'
FROM users u
WHERE u.email = 'sara@example.com'
UNION ALL
SELECT u.id, 'saved_search', 'Saved search in Casablanca'
FROM users u
WHERE u.email = 'sara@example.com'
UNION ALL
SELECT u.id, 'listing_created', 'Created demo listing'
FROM users u
WHERE u.email = 'youssef@example.com'
UNION ALL
SELECT u.id, 'verification_request', 'Requested verification'
FROM users u
WHERE u.email = 'mariam@example.com';
