<?php

namespace App\Services\Algorithm;

use Exception;
use Illuminate\Support\Facades\Log;

class Md5
{
	/*去掉字符空值*/
	private static function filter($para)
	{
		$filters = [];
		foreach ($para as $key => $val) {
			if ($key == "sign" || $key == "Sign" || $val === "" || $key == "s" || !is_string($val))
				continue;
			else
				$filters[$key] = $para[$key];
		}
		return $filters;
	}

	public static function encode($params, $key)
	{
		$endata = self::filter($params);
		ksort($endata);
		reset($endata);
		$endata['key'] = $key;
		logger(__METHOD__ . __LINE__, $endata);
		$enstr = http_build_query($endata);
		$mysgin = md5($enstr);
		return $mysgin;
	}

	public static function verify($data, $key)
	{
		$sign = $data['sign'];
		$mysgin = self::encode($data, $key);
		logger(__METHOD__ . __LINE__, [$sign, $mysgin]);
		if ($mysgin == $sign || strtoupper($mysgin) == $sign) {
			return true;
		} else {
			return false;
		}
	}
}
