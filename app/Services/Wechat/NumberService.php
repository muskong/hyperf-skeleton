<?php

namespace App\Services\Wechat;

use App\Exceptions\DataException;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NumberService
{
	static function mobile($mobile)
	{
		$config = config('wechat.mobile.number');

		$source = 'market';

		// 签名
		$datetime = gmdate('D, d M Y H:i:s T');
		$signStr = sprintf("x-date: %s\nx-source: %s", $datetime, $source);
		$sign = base64_encode(hash_hmac('sha1', $signStr, $config['SecretKey'], true));
		$auth = sprintf('hmac id="%s", algorithm="hmac-sha1", headers="x-date x-source", signature="%s"', $config['SecretID'], $sign);

		$params = [
			'mobile' => $mobile,
		];
		$headers = [
			'X-Source' => $source,
			'X-Date' => $datetime,
			'Authorization' => $auth,
		];

		$response = Http::withHeaders($headers)
			->asForm()
			->post($config['Url'], $params);
		if (!$response->ok()) {
			$result = $response->json();
			throw new DataException('获取失败', (array)$response);
		}
		$result = $response->json();
		if (($result['code'] ?? 400) != 200) {
			throw new DataException($result['msg'], (array)$result);
		}
		$data = [
			'area' => $result['data']['area'], // 手机号归属地
			'isp' => $result['data']['newIsp'], //  手机号转网运营商
			'oldisp' => $result['data']['originalIsp'], // 手机号原运营商
			'transfer' => $result['data']['isTransfer'], //是否携号转网   0：否   1：是
			'virtualIsp' => $result['data']['isVirtuallyIsp'], //是否虚拟   0：否   1：是
		];
		return $data;
	}
}
