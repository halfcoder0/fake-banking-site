<?php
require_once('appkeys.php');

function encrypt(string $plaintext, string $additional_data = ''): array
{
    $key = AppKeys::enc();
    $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
    $cipher_text = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($plaintext, $additional_data, $nonce, $key);

    return ['nonce' => base64_encode($nonce), 'cipher_text' => base64_encode($cipher_text)];
}

function decrypt(string $nonce_b64, string $ct_b64, string $additional_data = ''): string
{
    $key = AppKeys::enc();
    $nonce = base64_decode($nonce_b64, true);
    $cipher_text = base64_decode($ct_b64, true);
    $plaintext = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt($cipher_text, $additional_data, $nonce, $key);

    if ($plaintext === false) throw new RuntimeException('Decrypt failed');
    return $plaintext;
}
