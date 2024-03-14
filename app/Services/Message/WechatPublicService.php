<?php

namespace App\Services\Message;

use Exception;
use Illuminate\Support\Facades\Http;

class WechatPublicService
{
	const TokenUrl = 'https://api.weixin.qq.com/cgi-bin/token';
	const MessageUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s';

	static function getAccountToken()
	{
		// $tokenName = 'Wechat.AccountToken';
		// $token = Cache::get($tokenName);
		// if ($token) {
		// 	return $token;
		// }
		$response = Http::get(self::TokenUrl, [
			'grant_type' => 'client_credential',
			'appid' => config('config.tencent.wechat.publicAppid'),
			'secret' => config('config.tencent.wechat.publicSecret'),
		]);

		if (!$response->ok()) {
			throw new Exception('-请求失败-');
		}

		$result = $response->json();

		// Cache::set($tokenName, $result['access_token'], $result['expires_in'] - 60);

		return $result['access_token'];
	}


	static function sendMessage($toUserOpenid, $data, $templateId, $url = '')
	{
		// 处理长度问题
		foreach ($data as $key => &$item) {
			if (strpos($key, 'thing') !== false && mb_strlen($item['value']) > 20) {
				$item['value'] = mb_substr($item['value'], 0, 17) . '...';
			}
			if (strpos($key, 'phrase') !== false && mb_strlen($item['value']) > 5) {
				$item['value'] = mb_substr($item['value'], 0, 5);
			}
		}
		$message = [
			// 请求参数
			'touser' => $toUserOpenid,
			// 	string 	是 	接收者（用户）的 openid
			'template_id' => $templateId,
			// 	string 	是 	所需下发的订阅模板id
			'data' => $data,
			// 	string 	是 	模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }的object
		];
		if ($url) {
			$message['url'] = $url;
		}
		info(__METHOD__, $message);
		$url = sprintf(self::MessageUrl, self::getAccountToken());
		$response = Http::withHeaders([
			"Content-Type" => "application/json; charset=UTF-8",
		])->post($url, $message);
		if (!$response->ok()) {
			throw new Exception('-请求失败-');
		}

		$result = $response->json();
		info(__METHOD__, (array) $result);
		return $result;
	}
}