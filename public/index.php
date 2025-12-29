<?php
/**
 * Public Entry Point
 * All requests should be routed through this file
 */

// Load bootstrap
require_once __DIR__ . '/../bootstrap.php';

// Load routes
require_once BASE_PATH . '/routes/web.php';

// Dispatch request
\Core\Router::dispatch();
