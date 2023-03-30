<?php
/**
 * Main API endpoint for the Todo List application.
 *
 * This file handles all incoming HTTP requests (GET, POST, PUT, DELETE)
 * for managing todo items. It acts as a router, dispatches requests
 * to the appropriate methods in the Todo model, and returns JSON responses.
 *
 * It includes necessary configurations, sets CORS headers, and provides
 * robust error handling.
 */

// Set CORS headers to allow cross-origin requests from the React frontend.
// In a production environment, replace '*' with your specific frontend domain
// (e.g., 'http://localhost:3000' during development, 'https://your-frontend-domain.com' in production).
header("Access-Control-