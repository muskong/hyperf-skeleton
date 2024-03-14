<?php

namespace App\Services\Message;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsJisuService
{
	const URL = 'https://api.jisuapi.com/sms/send';

	static function Send($mobile, $code, $template = '')
	{
		try {
			$content = '';
			switch (strtolower($template)) {
				case "reg":
					$content = '';
					break;
                default:
                    throw new Exception('Unexpected value');
            }
			$content = strtr($content, [
				'@' => $code,
			]);

			$appkey = config('config.jisu.sms.key');
			$response = Http::get(self::URL, compact('mobile', 'content', 'appkey'));
			if (!$response->ok()) {
				throw new Exception("短信发送,请求失败");
			}

			if ($response->json('status') != 0) {
				throw new Exception($response->json('msg'));
			}

			return $response->json('count');
		} catch (Exception $e) {
			Log::channel('extend')
				->error($e->getMessage(), [
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'result' => $response->json() ?? '-',
				]);
			return false;
		}
	}
}
