<?php

namespace App\Services\Message;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * 企业微信
 */
class WeComService
{
	static function getAccountToken($config)
	{
		$cache_key = sprintf('wecom-token:%s:', config('app.env'));
		if ($token = Cache::get($cache_key)) {
			return $token;
		}
		$response = Http::get($config['url_get_token']);

		if (!$response->ok()) {
			throw new Exception('获取企业微信access_token失败.');
		}

		$result = $response->json();
		if (!$result['access_token']) {
			throw new Exception('获取企业微信access_token失败');
		}

		Cache::set($cache_key, $result['access_token'], 7000);

		return $result['access_token'];
	}

	public static function Notice($touser, $content)
	{
		logger(__METHOD__ . __LINE__, compact('touser', 'content'));
		try {
			if (empty($touser)) {
				throw new Exception('接收人为空');
			}
			if (is_array($touser)) {
				$touser = implode('|', $touser);
			}
			$config = config('config.wecom');
			$token = self::getAccountToken($config);

			$url = sprintf('%s%s', $config['url_send_message'], $token);
			$data['touser'] = $touser;
			$data['msgtype'] = 'text';
			$data['agentid'] = $config['agentid'];
			$data['text']['content'] = $content;

			$response = Http::post($url, $data);
			if (!$response->ok()) {
				throw new Exception('-请求失败-');
			}

			$result = $response->json();

			logger(__METHOD__ . __LINE__, (array) $result);
			if ($result['errcode']) {
				throw new Exception($result['errmsg']);
			}
			return true;
		} catch (Exception $e) {

			logger(__METHOD__ . __LINE__, [
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'message' => $e->getMessage(),
			]);

			return false;
		}
	}
}