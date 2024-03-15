<?php

namespace App\Services;

use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Response;

class RespondService
{
	static function Error($message): ResponseInterface
	{
		return Response::json([
			'time' => date('Y-m-d H:i:s'),
			'success' => false,
			'message' => $message,
		]);
	}

	static function Success($data): ResponseInterface
	{
		return Response::json([
			'time' => date('Y-m-d H:i:s'),
			'success' => true,
			'data' => $data
		]);
	}

}
