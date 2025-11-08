<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
        // Refuse request unless HTTPS (In prod)
        // exit('HTTPS required');
    }
}

const SESSION_INACTIVITY_LIMIT = 900;    // 15 minutes idle timeout
const SESSION_ROTATE_INTERVAL  = 600;    // rotate every 10 minutes

configure_session();
session_start();
perform_checks();

/**
 * Configure session & cookie
 */
function configure_session()
{
    ini_set('display_errors', '0');                         // Hide errors from user
    ini_set('log_errors', '1');                             // Log errors
    ini_set('error_log', 'php://stderr');                   // Print logs to container logs
    ini_set('session.use_cookies', '1');
    ini_set('session.use_only_cookies', '1');               // no SID in URLs
    ini_set('session.use_trans_sid', '0');                  // disable URL-based session IDs
    ini_set('session.cookie_httponly', '1');
    //ini_set('session.cookie_secure', '1');                // cookie only over HTTPS
    ini_set('session.use_strict_mode', '1');                // reject uninitialized IDs (anti-fixation)
    ini_set('session.sid_length', '48');                    // longer IDs
    ini_set('session.sid_bits_per_character', '6');         // base64-like charset
    ini_set('session.gc_maxlifetime', '1800');              // 30 min server-side storage lifetime
    ini_set('session.save_path', '/var/lib/php/sessions');   // set session save path

    $cookieParams = [
        'lifetime' => 0,            // session cookie (dies with browser)
        'path'     => '/',
        'domain'   => '',
        //'secure'   => true,       // require HTTPS
        'httponly' => true,         // prevent js from reading cookie  
        'samesite' => 'Lax',
    ];

    session_set_cookie_params($cookieParams);
    session_name('APPSESSID');
}

/**
 * Initialise session metadata
 */
function init_session_meta(): void
{
    if (!isset($_SESSION['_meta'])) {
        $_SESSION['_meta'] = [
            'created'     => time(),
            'last_regen'  => time(),
            'last_seen'   => time(),
            'fingerprint' => get_fingerprint(),
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
        ];
    }
}

/** 
 * Perform checks on request
 */
function perform_checks()
{
    init_session_meta();

    // Fingerprint Check
    $current_fp = get_fingerprint();
    if ($_SESSION['_meta']['fingerprint'] !== $current_fp) kill_session();

    // IP Check
    //if ($_SERVER['REMOTE_ADDR'] != $_SESSION['_meta']['ip_address']) kill_session();

    $age = time() - ($_SESSION['_meta']['created'] ?? time());
    $last_regen = $_SESSION['_meta']['last_regen'] ?? 0;
    $last_seen = $_SESSION['_meta']['last_seen'] ?? 0;

    // Inactivity timeout
    if ($last_seen && (time() - $last_seen > SESSION_INACTIVITY_LIMIT)) kill_session();

    // Periodic ID rotation to narrow fixation/hijack windows
    if ($age > 10 && (time() - $last_regen) > SESSION_ROTATE_INTERVAL) {
        session_regenerate_id(true);
        $_SESSION['_meta']['last_regen'] = time();
    }

    $_SESSION['_meta']['last_seen'] = time();
}

/** 
 * Kill Session
 */
function kill_session()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'] ?? '/',
            'domain'   => $p['domain'] ?? '',
            'secure'   => $p['secure'] ?? true,
            'httponly' => $p['httponly'] ?? true,
            'samesite' => $p['samesite'] ?? 'Lax',
        ]);
    }
    session_destroy();

    session_start();
    init_session_meta();
    header('Location: /');
    exit;
}

/**
 * Recreate session after hard login
 */
function hard_recreate_session(): void
{
    session_regenerate_id(true);
    $_SESSION = ['_meta' => [
        'created'     => time(),
        'last_regen'  => time(),
        'last_seen'   => time(),
        'fingerprint' => get_fingerprint(),
        'ip_address'   => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
    ]];
}

/**
 * Secure logout
 */
function session_secure_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $params['path'] ?? '/',
            'domain'   => $params['domain'] ?? '',
            //'secure'   => $params['secure'] ?? true, // require HTTPS
            'httponly' => $params['httponly'] ?? true,
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }
    session_destroy();
}

/**
 * Hash User Agent as fingerprint
 */
function get_fingerprint()
{
    return hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? ''));
}
