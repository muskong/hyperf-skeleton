<?php

namespace App\Services\Wechat;

use App\Exceptions\DataException;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChargeService
{
	const Wait    = 'wait'; // 等待充值
	const Success = 'success'; // 充值成功
	const Fail    = 'fail'; // 充值失败

	static function fastCharge($orderId, $mobile, $amount, $notifyUrl)
	{
		$config = config('wechat.mobile.recharge');

		$params = [
			'orderId'   => $orderId,
			'mobile'    => $mobile,
			'amount'    => $amount,
			'notifyUrl' => $notifyUrl
		];

		return self::formPost($config['fastCharge'], $params);
	}

	static function fastQueryOrder($orderId)
	{
		$config = config('wechat.mobile.recharge');

		$params = [
			'orderId' => $orderId,
		];

		return self::formPost($config['fastQueryOrder'], $params);
	}

	static function fastQueryAccount($time)
	{
		$config = config('wechat.mobile.recharge');

		$params = [
			'time' => $time,
		];

		return self::formPost($config['fastQueryAccount'], $params);
	}
	static function fastQueryStatus($mobile, $time)
	{
		$config = config('wechat.mobile.recharge');

		$params = [
			'mobile' => $mobile,
			'time'   => $time,
			'type'   => 0,
		];

		return self::formPost($config['fastQueryStatus'], $params);
	}

	static function notifyData(array $data)
	{
		$config = config('wechat.mobile.recharge');
		if ($data['sign'] != self::sign($data, $config['appSecret'])) {
			throw new Exception('签名验证失败');
		}
		if (strtolower($data['status'] ?? '') == self::Fail) {
			throw new DataException($data['reason'] ?? '失败原因为空');
		}
		return [
			'chargeUdid'   => $data['number'] ?? '', // string 是 系统订单号
			'udid'         => $data['orderId'] ?? '', //  string 是 平台订单号
			'mobile'       => $data['mobile'] ?? '', //  string 是 充值手机号
			'chargeAmount' => $data['amount'] ?? '', //  double 是 充值话费额度
			'chargeActual' => $data['actualAmount'] ?? ' ', //  double 是 实际扣除金额
		];
	}

	static function sign(array $data, string $secret): string
	{
		$signData = [];
		foreach ($data as $key => $value) {
			if (in_array($key, ['sign']) || $value === '') {
				continue;
			}
			$signData[$key] = $value;
		}
		ksort($signData);
		$signString = sprintf('%s&key=%s', http_build_query($signData), $secret);
		return strtoupper(md5($signString));
	}
	static function result(array $response): array
	{
		if (($response['return_code'] ?? 9999) != 0) {
			throw new DataException($response['return_msg'], (array) $response);
		}

		$data   = $response['data'];
		$status = self::Success;
		if (isset($data['status'])) {
			if (is_numeric($data['status'])) {
				$status = $data['status'] == 1 ? self::Success : self::Fail;
			} else {
				$status = strtolower($data['status']);
			}
		}

		if (isset($data['recharge'])) {
			$data = [
				'recharge' => $data['recharge'] ?? 0, // double 是 预充总金额
				'consume'  => $data['consume'] ?? 0, // double 是 消费金额
				'locking'  => $data['locking'] ?? 0, // double 是 预扣金额
				'surplus'  => $data['surplus'] ?? 0, // double 是 剩余金额
			];
		} elseif (isset($data['tip'])) {
			$data = [
				'tip' => $data['tip'] ?? '', // 渠道状态, 提示信息
			];
		} else {
			$data = [
				'chargeUdid'   => $data['number'] ?? '', // string 是 系统订单号
				'udid'         => $data['orderId'] ?? '', //  string 是 平台订单号
				'mobile'       => $data['mobile'] ?? '', //  string 是 充值手机号
				'amount'       => $data['amount'] ?? '', //  double 是 充值话费额度
				'actualAmount' => $data['actualAmount'] ?? ' ', //  double 是 实际扣除金额
			];
		}

		return [$status, $data];
	}

	static function formPost($url, $params)
	{
		$config = config('wechat.mobile.recharge');

		$params['appKey'] = $config['appKey'];
		$params['sign']   = self::sign($params, $config['appSecret']);

		$headers = [
			// 'X-Source' => $source,
			// 'X-Date' => $datetime,
			// 'Authorization' => $auth,
		];

		$response = Http::withHeaders($headers)
			->asForm()
			->post($url, $params);
		if (!$response->ok()) {
			$result = $response->json();
			throw new DataException('获取失败', (array) $response);
		}
		return self::result($response->json());
	}
}
