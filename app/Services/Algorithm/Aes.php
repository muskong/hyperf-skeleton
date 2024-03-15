<?php

namespace App\Services\Algorithm;

use App\Services\LoggerService;
use Exception;

class Aes
{
	private const passphrase = 'XpPkt7uezRQBkfrsX76FXyDSNs222233';
	private const iv = 'TxfivTaSnr6mLOgy';
	private const cipherAlgo = 'aes-256-cbc';

	static function method()
	{
		var_export(openssl_get_cipher_methods());
	}

	private static function _encrypt(string $data)
	{
		try {
			$encrypt = openssl_encrypt($data, Aes::cipherAlgo, Aes::passphrase, OPENSSL_RAW_DATA, Aes::iv);
			if (false === $encrypt) {
				throw new Exception(openssl_error_string());
			}
			return $encrypt;
		} catch (Exception $e) {
			LoggerService::error('解密错误', [
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			return '';
		}
	}
	private static function _decrypt(string $data)
	{
		try {
			$decrypt = openssl_decrypt($data, Aes::cipherAlgo, Aes::passphrase, OPENSSL_RAW_DATA, Aes::iv);
			if (false === $decrypt) {
				throw new Exception(openssl_error_string());
			}
			return $decrypt;
		} catch (Exception $e) {
			LoggerService::error('解密错误', [
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'message' => $e->getMessage(),
			]);
			return '';
		}
	}

	static function encrypt(string $data)
	{
		return bin2hex(self::_encrypt($data));
	}

	static function decrypt($data)
	{
		return self::_decrypt(hex2bin($data));
	}

	static function encryptString(string $data)
	{
		// dd(openssl_get_cipher_methods());
		// dd(openssl_cipher_key_length(Aes::cipherAlgo));
		return base64_encode(self::_encrypt($data));
	}

	static function decryptString($data)
	{
		return self::_decrypt(base64_decode($data));
	}
}
