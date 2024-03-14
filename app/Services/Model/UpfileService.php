<?php

namespace App\Services\Model;

use App\Services\IdService;
use Hyperf\Contract\CastsAttributes;
use Hyperf\Stringable\Str;

/**
 * 重写分页返回数组
 * Class LengthAwarePaginatorService
 * @package App\Services
 */
class UpfileService implements CastsAttributes
{
	/**
	 * 将取出的数据进行转换
	 *
	 * @param  \Hyperf\Database\Model\Model  $model
	 * @param  string  $key
	 * @param  mixed  $value
	 * @param  array  $attributes
	 * @return mixed
	 */
	public function get($model, $key, $value, $attributes)
	{
		return [
			[
				// 'uid' => IdService::GenerateIdIncrement('rc-'),
				"name" => Str::afterLast($value, '/'),
				"status" => "done",
				"response" => $value ? parse_url($value) : '',
				// "type" => "image/png",
				// "xhr" => [],
				"thumbUrl" => $value ? parse_url($value) : '',
			]
		];

		// {
		// 	"uid": "rc-upload-1700458337812-2",
		// 	"lastModified": 1694674143000,
		// 	"name": "ff99c4cb4a2d2631b5ab90ee3d33eca8.png",
		// 	"size": 46032,
		// 	"type": "image/png",
		// 	"percent": 100,
		// 	"originFileObj": {
		// 		"uid": "rc-upload-1700458337812-2"
		// 	},
		// 	"status": "done",
		// 	"response": "/storage/files/1f25c14bfc667ab3192b7ebcfadca88a.png",
		// 	"xhr": [],
		// 	"thumbUrl":
	}

	/**
	 * 转换成将要进行存储的值
	 *
	 * @param  \Hyperf\Database\Model\Model  $model
	 * @param  string  $key
	 * @param  array  $value
	 * @param  array  $attributes
	 * @return mixed
	 */
	public function set($model, $key, $value, $attributes)
	{
		if ($value) {
			return $value[0]['response'];
		}
		return $value;
	}
}
