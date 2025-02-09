<?php
class Seguridad
{
	static function encriptar_AES($string, $key)
	{
		/*
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM );
			mcrypt_generic_init($td, $key, $iv);
			$encrypted_data_bin = mcrypt_generic($td, $string);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			$encrypted_data_hex = bin2hex($iv).bin2hex($encrypted_data_bin);
			*/

		// Store the cipher method
		$ciphering = "AES-128-CTR";

		// Use OpenSSl Encryption method
		//$iv_length = openssl_cipher_iv_length($ciphering);
		$options = 0;

		// Non-NULL Initialization Vector for encryption
		$encryption_iv = '1234567891011121';

		// Store the encryption key
		$encryption_key = $key;

		// Use openssl_encrypt() function to encrypt the data
		$encrypted_data_hex = openssl_encrypt(
			$string,
			$ciphering,
			$encryption_key,
			$options,
			$encryption_iv
		);
		return $encrypted_data_hex;
	}

	static function desencriptar_AES($encrypted_data_hex, $key)
	{
		/*
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv_size_hex = mcrypt_enc_get_iv_size($td)*2;
			$iv = pack("H*", substr($encrypted_data_hex, 0, $iv_size_hex));
			$encrypted_data_bin = pack("H*", substr($encrypted_data_hex, $iv_size_hex));
			if(strlen($iv)<>16)
			{
				throw new Exception("Verifique que la cadena a Desencriptar este en formato Hexadecimal.");
			}
			else
			{
			mcrypt_generic_init($td, $key, $iv);
			}
			$decrypted = mdecrypt_generic($td, $encrypted_data_bin);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			*/

		$ciphering = "AES-128-CTR";

		$options = 0;

		$decryption_iv = '1234567891011121';

		// Store the decryption key
		$decryption_key = $key;

		// Use openssl_decrypt() function to decrypt the data
		$decryption = openssl_decrypt(
			$encrypted_data_hex,
			$ciphering,
			$decryption_key,
			$options,
			$decryption_iv
		);


		return $decryption;
	}
}
