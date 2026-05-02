<?php
/**
 * config.php - Centralized configuration file
 * Database connection and global constants
 * Include this file in all pages and scripts
 */

declare(strict_types=1);

// Application root and basic constants
define('APP_NAME', 'Roommates App');
define('APP_ROOT', dirname(__FILE__));
define('ASSETS_URL', '/roommates-app');
define('BASE_URL', '/roommates-app');

// Session management and timezone
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
date_default_timezone_set('UTC');

// Include helper functions early so pages can call them even if DB fails
require_once APP_ROOT . '/php/functions.php';

// Database configuration
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'roommates_db';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbCharset = 'utf8mb4';

// PDO connection
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE =>
PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false, ]); } catch (PDOException $e) { http_response_code(500); exit('Database connection failed: ' . $e->getMessage()); } ?>
