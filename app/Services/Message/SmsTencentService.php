<?php

namespace App\Services\Message;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsTencentService
{
	const domain = 'https://sms.tencentcloudapi.com';

	static function Send($mobile, $code, $template = '1234')
	{
		try {
			// 实际调用需要更新参数，这里仅作为演示签名验证通过的例子
			$payloadObj = [
				"SmsSdkAppId" => config('config.tencent.sms.SmsSdkAppId'),
				"SignName" => config('config.tencent.sms.SignName'),
				"TemplateId" => $template,
				"TemplateParamSet" => [$code],
				"PhoneNumberSet" => ["+86{$mobile}"],
				"SessionContext" => "Tiger",
			];

			$headers = self::sign($payloadObj);
			$response = Http::withHeaders($headers)
				->post(self::domain, $payloadObj);
			if (!$response->ok()) {
				throw new Exception("短信发送,请求失败");
			}

			$result = $response->json('Response');
			if (isset($result['Error'])) {
				Log::channel('extend')->error('腾讯短信发送失败', $result);
				throw new Exception($result['Error']['Message']);
			}

			return $result['SendStatusSet'];
		} catch (Exception $e) {
			Log::channel('extend')->error($e->getMessage(), [
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'result' => $response->json() ?? '-',
			]);
			return false;
		}
	}

	static function sign($payloadObj)
	{
		$secretId = config('config.tencent.sms.secretId');
		$secretKey = config('config.tencent.sms.secretKey');
		$host = "sms.tencentcloudapi.com";
		$service = "sms";
		$version = "2021-01-11";
		$action = "SendSms";
		$region = "ap-shanghai";
		$timestamp = time();
		$algorithm = "TC3-HMAC-SHA256";
		$gmdate = gmdate('Y-m-d');

		// step 1: build canonical request string
		$httpRequestMethod = "POST";
		$canonicalUri = "/";
		$canonicalQueryString = "";
		$canonicalHeaders = "content-type:application/json; charset=utf-8\n" . "host:" . $host . "\n";
		$signedHeaders = "content-type;host";
		$payload = json_encode($payloadObj);
		$hashedRequestPayload = hash("SHA256", $payload);
		$canonicalRequest = $httpRequestMethod . "\n"
			. $canonicalUri . "\n"
			. $canonicalQueryString . "\n"
			. $canonicalHeaders . "\n"
			. $signedHeaders . "\n"
			. $hashedRequestPayload;


		// step 2: build string to sign
		$credentialScope = $gmdate . "/" . $service . "/tc3_request";
		$hashedCanonicalRequest = hash("SHA256", $canonicalRequest);
		$stringToSign = $algorithm . "\n"
			. $timestamp . "\n"
			. $credentialScope . "\n"
			. $hashedCanonicalRequest;

		// step 3: sign string
		$secretDate = hash_hmac("SHA256", $gmdate, "TC3" . $secretKey, true);
		$secretService = hash_hmac("SHA256", $service, $secretDate, true);
		$secretSigning = hash_hmac("SHA256", "tc3_request", $secretService, true);
		$signature = hash_hmac("SHA256", $stringToSign, $secretSigning);

		// step 4: build authorization
		$authorization = $algorithm
			. " Credential=" . $secretId . "/" . $credentialScope
			. ", SignedHeaders=content-type;host, Signature=" . $signature;

		return [
			'Authorization' => $authorization,
			'X-TC-Action' => $action,
			'X-TC-Timestamp' => $timestamp,
			'X-TC-Version' => $version,
			'X-TC-Region' => $region,
		];
	}
}