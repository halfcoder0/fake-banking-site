<?php
require_once('../helpers.php');

final class AppKeys
{
    private static ?string $enc = null;
    private static ?string $mac = null;

    static function enc(): string
    {
        if (self::$enc === null) {
            $raw = b64url_decode_strict($_ENV['APP_ENC_KEY'] ?: '');
            if ($raw === false || strlen($raw) !== 32) throw new RuntimeException('Bad APP_ENC_KEY');
            self::$enc = $raw;
        }
        return self::$enc;
    }

    static function mac(): string
    {
        if (self::$mac === null) {
            $raw = b64url_decode_strict($_ENV['APP_HMAC_KEY'] ?: '');
            if ($raw === false || strlen($raw) < 32) throw new RuntimeException('Bad APP_HMAC_KEY');
            self::$mac = $raw;
        }
        return self::$mac;
    }
}
