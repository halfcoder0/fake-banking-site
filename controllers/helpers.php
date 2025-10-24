<?php

/**
 * Redirect to route
 */
if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }
}


/**
 * Hash string with Argon algorithm
 */
if (!function_exists('argon_hash')) {
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


/**
 * Get environment variable with default (null)
 */
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }
}

/**
 *  Remove non-alphanumeric (Can be used for sanitization)
 */
if (!function_exists('remove_non_alphanum')) {
    function remove_non_alphanum($string)
    {
        return preg_replace("/[^[:alnum:][:space:]]/u", '', $string);
    }
}


/**
 * HTTP Post request
 */
if (!function_exists('http_post')) {
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
