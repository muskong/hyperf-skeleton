<?php

namespace App\Services\Wechat;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MiniService
{
	static function codeExchangeOpenid($code)
	{
		$config = config('wechat.mini');
		// $access_token = self::getAccountToken($config);

		$params = [
			'js_code' => $code,
			// 'access_token' => $access_token,
			'grant_type' => 'authorization_code',
			'appid' => $config['app_id'],
			'secret' => $config['secret']
		];

		$response = Http::get($config['open_id_url'], $params);
		if (!$response->ok()) {
			throw new Exception('获取openid失败');
		}
		$result = $response->json();
		logger(__METHOD__.__LINE__, $result);
		if ($result['openid'] ?? false) {
			return $result['openid'];
		}
		return '';
	}


	static function getAccountToken($config)
	{
		$response = Http::get($config['access_token_url'], [
			'grant_type' => 'client_credential',
			'appid' => $config['app_id'],
			'secret' => $config['secret'],
		]);

		if (!$response->ok()) {
			throw new Exception('获取小程序access_token失败.');
		}

		$result = $response->json();
		if (!$result['access_token']) {
			throw new Exception('获取小程序access_token失败');
		}

		return $result['access_token'];
	}
}
