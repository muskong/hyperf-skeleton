<?php

namespace App\Services\Message;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WechatAppletService
{
	const TokenUrl = 'https://api.weixin.qq.com/cgi-bin/token';
	const MessageUrl = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=%s';

	static function getAccountToken()
	{
		// $tokenName = 'Wechat.AccountToken';
		// $token = Cache::get($tokenName);
		// if ($token) {
		// 	return $token;
		// }
		$response = Http::get(self::TokenUrl, [
			'grant_type' => 'client_credential',
			'appid' => config('config.tencent.wechat.appletAppid'),
			'secret' => config('config.tencent.wechat.appletSecret'),
		]);

		if (!$response->ok()) {
			throw new Exception('-请求失败-');
		}

		$result = $response->json();

		// Cache::set($tokenName, $result['access_token'], $result['expires_in'] - 60);

		return $result['access_token'];
	}


	static function sendMessage($toUserOpenid, $data, $templateId, $page = '', $lang = 'zh_CN')
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
		$state = App::isProduction() ? 'formal' : (App::isLocal() ? 'developer' : 'trial');
		$message = [
			// 请求参数
			'template_id' => $templateId,
			// 	string 	是 	所需下发的订阅模板id
			// 'page' => '', // 	string 	否 	点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转
			'touser' => $toUserOpenid,
			// 	string 	是 	接收者（用户）的 openid
			'data' => $data,
			// 	string 	是 	模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }的object
			'miniprogram_state' => $state,
			// 	string 	是 	跳转小程序类型：developer为开发版；trial为体验版；formal为正式版；默认为正式版
			'lang' => $lang,
			// 	string 	是 	进入小程序查看”的语言类型，支持zh_CN(简体中文)、en_US(英文)、zh_HK(繁体中文)、zh_TW(繁体中文)，默认为zh_CN
		];
		if ($page) {
			$message['page'] = $page;
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