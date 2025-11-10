<?php
include(__DIR__ . '/../helpers.php');

/**
 * Generate CSRF Token
 */
function get_csrf_token(): string
{
    // Initialize 
    if (!isset($_SESSION['csrf_token'])) {
        $random = random_bytes(32);
        $token = b64url_encode_strict($random);
        $_SESSION['csrf_token'] = $token;
    }

    return $_SESSION['csrf_token'];
}

/**
 * Print CSRF in forms
 */
function csrf_input(): string
{
    $token = htmlspecialchars(get_csrf_token(), ENT_QUOTES);
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF token
 */
function csrf_verify(int $max_age = 7200): bool
{
    // Enforce POST for state-changing actions
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return false;
    
    // Require same-origin via Origin/Referer hardening
    if (!empty($_SERVER['HTTP_ORIGIN'])) {
        $origin = $_SERVER['HTTP_ORIGIN'];
        $host   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

        if (stripos($origin, $host) !== 0) return false;
    } elseif (!empty($_SERVER['HTTP_REFERER'])) {
        $ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

        if ($ref !== $_SERVER['HTTP_HOST']) return false;
    }

    $token = $_POST['csrf_token'] ?? '';

    if (is_string($token) && hash_equals($_SESSION['csrf_token'], $token)) return true;

    return false;
}
