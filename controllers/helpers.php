<?php

if (!function_exists('redirect')) {
    /**
     * Redirect to route
     */
    function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }
}

if (!function_exists('argon_hash')) {
    /**
     * Hash string with Argon algorithm
     */
    function argon_hash($string)
    {
        $options = [
            'memory_cost' => 1 << 17, // 131072 KB (128 MB)
            'time_cost'   => 4,       // Number of iterations
            'threads'     => 2        // Parallel threads
        ];
        $hash = password_hash($string, PASSWORD_ARGON2ID, $options);

        return $hash;
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable with default (null)
     */
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }
}

if (!function_exists('remove_non_alphanum')) {
    /**
     *  Remove non-alphanumeric (Can be used for sanitization)
     */
    function remove_non_alphanum($string)
    {
        return preg_replace("/[^[:alnum:][:space:]]/u", '', $string);
    }
}

if (!function_exists('http_post')) {
    /**
     * Server-Side HTTP Post request
     */
    function http_post($url, $data)
    {
        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_TIMEOUT => 5
        ]);

        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);
        return ['ok' => $body !== false, 'status' => $status, 'body' => $body, 'error' => $err];
    }
}

if (!function_exists('add_csp_header')) {
    /**
     *  Add CSP header
     * 
     *  $script_hashes = list of sha256 hash for scripts (sha256-.....)
     *  $style_hashes = list of sha256 hash for css (sha256-.....)
     */
    function add_csp_header($script_hashes, $style_hashes, $nonce)
    {
        $q = static fn($h) => str_starts_with($h, "'") ? $h : ("'" . $h . "'");

        $script_hashes = array_map($q, $script_hashes);
        $style_hashes  = array_map($q, $style_hashes);

        $directives = [
            "script-src " .
                "'nonce-$nonce' " .
                "'strict-dynamic' " .
                implode(' ', $script_hashes) . ' ',
            "style-src " .
                "'self' " .
                "'nonce-$nonce' " .
                implode(' ', $style_hashes),
            "object-src 'none'",
            "base-uri 'none'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests",
        ];

        header_remove('Content-Security-Policy'); // Remove conflicting header
        header('Content-Security-Policy: ' . implode('; ', $directives));
    }
}

if (!function_exists('generate_random')) {
    /**
     * Generate Random (For CSP & CSRF) - 32 bytes by default
     */
    function generate_random()
    {
        $raw = random_bytes(32);
        $random = b64url_encode_strict($raw);
        return $random;
    }
}

if (!function_exists('b64url_decode_strict')) {
    /**
     * Base64 strict decode
     */
    function b64url_decode_strict(string $b64): string
    {
        $b64 .= str_repeat('=', (4 - strlen($b64) % 4) % 4);
        return base64_decode(strtr($b64, '-_', '+/'), true);
    }
}

if (!function_exists('b64url_encode_strict')) {
    /**
     * Base64 strict encode
     */
    function b64url_encode_strict(string $text): string
    {
        return rtrim(strtr(base64_encode($text), '+/', '-_'), '=');
    }
}

if (!function_exists('check_for_non_alphanum')) {
    /**
     *  Remove non-alphanumeric (Can be used for sanitization)
     */
    function check_for_non_alphanum($string)
    {
        return preg_match("/[^[:alnum:][:space:]]/u", '', $string);
    }
}
