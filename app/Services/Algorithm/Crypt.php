<?php

namespace App\Services\Algorithm;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * 继皋给出的 3DES 例子
 * 秘钥:			AD6D2678AG2KKGYD
 * 明文:			abc1000002abcdfd
 * 加密之后密文:	B2142ED37152EE5311E80D2FA1953005E32B3F2DE9DA6251
 * 加密之后密文:	b2142ed37152ee5311e80d2fa1953005e32b3f2de9da6251
 */
class Crypt
{
	private $passphrase = 'AD6D2678AG2KKGYD';
	private $iv = '';
	private $cipherAlgo = 'des-ede';

	public function __construct($cipherAlgo, $passphrase, $iv)
	{
		$this->passphrase = $passphrase;
		$this->iv = $iv;
		$this->cipherAlgo = $cipherAlgo;
	}

	public function method()
	{
		var_export(openssl_get_cipher_methods());
	}

	static function __callStatic($name, $arguments)
	{
		$passphrase = '';
		$cipherAlgo = '';
		$iv = '';
		extract(config('config.card'));
		$crypt = new self($cipherAlgo, $passphrase, $iv);

		if (!method_exists($crypt, $name)) {
			return 'not exists';
		}
		if ($arguments[1] ?? false) {
			$crypt->cipherAlgo = $arguments[1];
		}
		if ($arguments[2] ?? false) {
			$crypt->passphrase = $arguments[2];
		}
		if ($arguments[3] ?? false) {
			$crypt->iv = $arguments[3];
		}
		return $crypt->{$name}($arguments[0]);
	}

	protected function enhex($data)
	{
		return bin2hex($this->_encrypt($data));
	}
	protected function dehex($data)
	{
		return $this->_decrypt(hex2bin($data));
	}

	protected function enbase64($data)
	{
		return base64_encode($this->_encrypt($data));
	}
	protected function debase64($data)
	{
		return $this->_decrypt(base64_decode($data));
	}


	private function _encrypt(string $data)
	{
		try {
			$encrypt = openssl_encrypt($data, $this->cipherAlgo, $this->passphrase, OPENSSL_RAW_DATA, $this->iv);
			if (false === $encrypt) {
				throw new Exception(openssl_error_string());
			}
			return $encrypt;
		} catch (Exception $e) {
			Log::error('解密错误', [
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			Log::info(__METHOD__, openssl_error_string());
			return '';
		}
	}

	private function _decrypt(string $data)
	{
		try {
			$decrypt = openssl_decrypt($data, $this->cipherAlgo, $this->passphrase, OPENSSL_RAW_DATA, $this->iv);
			if (false === $decrypt) {
				throw new Exception(openssl_error_string());
			}
			return $decrypt;
		} catch (Exception $e) {
			Log::error('解密错误', [
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			Log::info(__METHOD__, openssl_error_string());
			return '';
		}
	}


	public function pkcs7padding($data, $blocksize)
	{
		$padding = $blocksize - strlen($data) % $blocksize;
		$padding_text = str_repeat(chr($padding), $padding);
		return $data . $padding_text;
	}

	public function pkcs7unPadding($data)
	{
		$length = strlen($data);
		$unpadding = ord($data[$length - 1]);
		return substr($data, 0, $length - $unpadding);
	}
}
