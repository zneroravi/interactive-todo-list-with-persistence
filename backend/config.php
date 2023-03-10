<?php

/**
 * backend/config.php
 *
 * This file handles the core configuration for the PHP backend of the Todo List application.
 * It sets up environment variables, error reporting, database connection parameters,
 * and Cross-Origin Resource Sharing (CORS) settings.
 *
 * Best practices are followed:
 * - Environment variables are used for sensitive data (database credentials, allowed origins).
 * - Error reporting is configured based on the application environment (development vs. production).
 * - A singleton pattern is used for the PDO database connection to ensure efficient resource management.
 * - CORS headers are defined to allow the React frontend to communicate with the backend.
 */

// Set default timezone to prevent date/time errors in PHP functions.
date_default_timezone_set('UTC');

// --- Environment Variable Loading ---
// In a production environment, these variables would typically be set by your web server
// (e.g., Apache, Nginx), container orchestration (e.g., Docker, Kubernetes), or CI/CD pipeline.
// For local development, you might use a `.env` file and a library like `vlucas/phpdotenv`
// to load them into `$_ENV` or make them available via `getenv()`.
// For simplicity in this project, we assume `getenv()` will retrieve them.
// Provide sensible defaults for local development if environment variables are not set.

// Database Credentials
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'todo_app');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: ''); // Default to no password for local XAMPP/MAMP setups

// CORS Configuration
// Define the allowed origin(s) for API requests.
// In production, this should be the exact URL of your React frontend (e.g., 'https://your-todo-app.com').
// For development, 'http://localhost:3000' is common if React Dev Server runs on port 3000.
// Using '*' is generally discouraged in production due to security risks.
define('ALLOWED_ORIGIN', getenv('ALLOWED_ORIGIN') ?: 'http://localhost:3000');

// Application Environment
// 'development' for debugging, 'production' for live systems.
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// --- Error Reporting Configuration ---
// Adjust PHP error reporting based on the application environment.
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);         // Display errors directly in the browser
    ini_set('display_startup_errors', 1); // Display startup errors
    error_reporting(E_ALL);               // Report all PHP errors
} else {
    ini_set('display_errors', 0);         // Do not display errors in production
    ini_set('display_startup_errors', 0); // Do not display startup errors
    error_reporting(0);                   // Disable all error reporting to the client
    ini_set('log_errors', 1);             // Log errors to a file
    // Ensure the 'logs' directory exists and is writable by the web server user.
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
}

// --- Database Connection Class ---
/**
 * Class Database
 * Manages the database connection using PDO (PHP Data Objects).
 * Implements a singleton pattern to ensure only one database connection is established
 * throughout the application's lifecycle for efficiency.
 */
class Database {
    // Static property to hold the single PDO connection instance.
    private static ?PDO $conn = null;

    /**
     * Establishes and returns a PDO database connection.
     * If a connection already exists, it returns the existing one.
     *
     * @return PDO The PDO database connection object.
     * @throws PDOException If the connection fails, providing different messages for dev/prod.
     */
    public static function connect(): PDO {
        if (self::$conn === null) {
            // Data Source Name (DSN) for MySQL.
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            // PDO connection options for robust and secure connections.
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays by default
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation for better security and performance
            ];

            try {
                self::$conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Log the detailed error message for debugging.
                error_log("Database connection error: " . $e->getMessage());

                // Re-throw a more user-friendly exception based on the environment.
                if (APP_ENV === 'development') {
                    // In development, expose the full error for easier debugging.
                    throw new PDOException("Database connection failed: " . $e->getMessage(), (int)$e->getCode());
                } else {
                    // In production, hide sensitive details and provide a generic error.
                    throw new PDOException("Database connection failed. Please try again later.", 500);
                }
            }
        }
        return self::$conn;
    }

    /**
     * Closes the database connection by setting the static connection property to null.
     * While PHP automatically closes connections at script termination, this method
     * can be useful for explicit resource management in specific scenarios (e.g., testing).
     */
    public static function disconnect(): void {
        self::$conn = null;
    }
}

// --- CORS Headers Setup Function ---
/**
 * Sets the necessary CORS (Cross-Origin Resource Sharing) headers.
 * This function should be called at the very beginning of your `backend/index.php`
 * to ensure headers are sent before any output.
 */
function setup_cors(): void {
    // Allow requests from the defined origin.
    header("Access-Control-Allow-Origin: " . ALLOWED_ORIGIN);
    // Specify allowed HTTP methods.
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    // Specify allowed headers that can be sent with the request.
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    // Allow credentials (e.g., cookies, HTTP authentication) to be sent with the request.
    // Only set to true if your frontend needs to send credentials and ALLOWED_ORIGIN is not '*'.
    header("Access-Control-Allow-Credentials: true");

    // Handle preflight requests (OPTIONS method).
    // Browsers send an OPTIONS request before the actual request if it's a "complex" request.
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204); // No Content
        exit(); // Terminate script execution after sending preflight headers.
    }
}

// Note: The `setup_cors()` function is defined here, but it must be explicitly called
// in `backend/index.php` at the very beginning of the script execution to apply the headers.
```