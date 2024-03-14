<?php

namespace App\Services;

use App\Services\Algorithm\Aes;
use App\Services\Algorithm\Algorithm;
use Exception;

class TokenService
{
	static function generate($data, $expire='+2 day')
	{
		$data = new Algorithm($data, $expire);

		return sprintf("Bearer %s", Aes::encryptString($data));
	}

	static function decode($token)
	{
		try {
			if (!$token) {
				throw new Exception("!$token", __LINE__);
			}

			$algorithm = unserialize(Aes::decryptString($token));

			if (!$algorithm instanceof Algorithm) {
				throw new Exception("algorithm instanceof Algorithm", __LINE__);
			}

			if ($algorithm->validateIssAud()) {
				throw new Exception("validateIssAud", __LINE__);
			}

			if ($algorithm->validateNbf()) {
				throw new Exception("validateNbf", __LINE__);
			}

			if ($algorithm->validateExp()) {
				throw new Exception("validateExp", __LINE__);
			}

			return $algorithm->getData();

		} catch (Exception $e) {
			LoggerService::exception($e, __METHOD__ . __LINE__);
			return false;
		}
	}
}
