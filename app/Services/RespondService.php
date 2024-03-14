<?php

namespace App\Services;

use App\Services\Algorithm\Md5;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class RespondService
{
	static function Error($message): JsonResponse
	{
		return Response::json([
			'time' => date('Y-m-d H:i:s'),
			'success' => false,
			'message' => $message,
		]);
	}

	static function Success($data): JsonResponse
	{
		return Response::json([
			'time' => date('Y-m-d H:i:s'),
			'success' => true,
			'data' => $data
		]);
	}

	static function TCError($message, $code): JsonResponse
	{
		return Response::json([
			'time' => date('Y-m-d H:i:s'),
			'code' => $code,
			'message' => $message,
		]);
	}

	static function TCSuccess(array $data): JsonResponse
	{
		$data = array_merge([
			'time' => date('Y-m-d H:i:s'),
			'code' => '0000',
			'message' => '请求成功',
		], $data);
		$data['sign'] = Md5::encode($data, session('merchant.md5key'));
		return Response::json($data);
	}
}
