<?php

namespace App\Services\Model;

use App\Services\Algorithm\Crypt;
use Hyperf\Contract\CastsAttributes;

/**
 * 重写分页返回数组
 * Class LengthAwarePaginatorService
 * @package App\Services
 */
class CryptService implements CastsAttributes
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
		if ($value) {
			return Crypt::dehex($value);
		}
		return $value;
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
			return Crypt::enhex($value);
		}
		return $value;
	}
}
