<?php

namespace App\Helpers;

/**
 * Métodos para encriptar y desencriptar datos usando AES con OpenSSL.
 */
class Seguridad
{
    /**
     * Encripta un texto con AES-128-CTR.
     */
    public static function encriptar_AES(string $string, string $key): string
    {
        $ciphering = "AES-128-CTR";
        $encryption_iv = '1234567891011121';
        $options = 0;

        return openssl_encrypt($string, $ciphering, $key, $options, $encryption_iv);
    }

    /**
     * Desencripta un texto con AES-128-CTR.
     */
    public static function desencriptar_AES(string $encrypted_data_hex, string $key): string
    {
        $ciphering = "AES-128-CTR";
        $decryption_iv = '1234567891011121';
        $options = 0;

        return openssl_decrypt($encrypted_data_hex, $ciphering, $key, $options, $decryption_iv);
    }
}
