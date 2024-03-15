<?php

namespace App\Services;
use Hyperf\Cache\Cache;
use Hyperf\Stringable\Str;


class IdService
{
	static function GetId(string $id, string $createdAt)
	{
		$id = Str::replaceMatches('/[A-Za-z]/', '', $id);
		$year = date('ymd', strtotime($createdAt));

		return bcsub($id, $year, 0);
	}

	// 生成唯一ID
	static function GenerateId(string $machine, int $id)
	{
		$year = date('ymd');

		$id = bcadd($year, $id, 0);
		$id = str_pad($id, 12, '0', STR_PAD_LEFT);

		return sprintf("%s%s", $machine, $id);
	}
	static function GenerateIdIncrement(string $machine)
	{
		$year = date('ymd');

		$uniqueKey = sprintf("GenerateIdIncrement:%s", $year);
		self::init($uniqueKey, 86400);
		// $id = bcadd(Cache::increment($uniqueKey), $year, 0);

		// $id = str_pad($id, 12, '0', STR_PAD_LEFT);

		// return sprintf("%s%s", $machine, $id);
	}

	static function init($uniqueKey, $ttl)
	{
		// if (!Cache::($uniqueKey)) {
		// 	Cache::add($uniqueKey, 0, $ttl);
		// }
	}

	/**
	 * 身份证号码检查接口函数
	 * @param $idCard
	 * @return bool
	 */
	static function idCardCheck($idCard)
	{
		switch (strlen($idCard)) {
			case 15:
				$idCard = self::idCard15to18($idCard);
			case 18:
				return self::idCardCheckSum18($idCard);
		}
		return false;
	}

	static private function idCardVerifyNumber($idCardBase)
	{
		if (strlen($idCardBase) != 17) {
			return false;
		}
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); //debug 加权因子
		$verifyNumberList = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); //debug 校验码对应值
		$checksum = 0;
		for ($i = 0; $i < strlen($idCardBase); $i++) {
			$checksum += substr($idCardBase, $i, 1) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verifyNumber = $verifyNumberList[$mod];
		return $verifyNumber;
	}

	/*
	 * 函数功能：将15位身份证升级到18位
	 * 函数名称：idCard15to18
	 * 参数表 ：string $idCard 十五位身份证号码
	 * 返回值 ：string
	 * 更新时间：Fri Mar 28 09:49:13 CST 2008
	 */
	static private function idCard15to18($idCard)
	{
		if (strlen($idCard) != 15) {
			return false;
		}

		// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
		if (array_search(substr($idCard, 12, 3), array('996', '997', '998', '999')) !== false) {
			$idCard = substr($idCard, 0, 6) . '18' . substr($idCard, 6, 9);
		} else {
			$idCard = substr($idCard, 0, 6) . '19' . substr($idCard, 6, 9);
		}

		$idCard = $idCard . self::idCardVerifyNumber($idCard);
		return $idCard;
	}

	/*
	 * 函数功能：18位身份证校验码有效性检查
	 * 函数名称：idCardCheckSum18
	 * 参数表 ：string $idCard 十八位身份证号码
	 * 返回值 ：bool
	 * 更新时间：Fri Mar 28 09:48:36 CST 2008
	 */
	static private function idCardCheckSum18($idCard)
	{
		if (strlen($idCard) != 18) {
			return false;
		}

		$idCardBirthday = substr($idCard, 6, 8);
		if ($idCardBirthday != date('Ymd', strtotime($idCardBirthday))) {
			return false;
		}

		$idCardBase = substr($idCard, 0, 17);
		if (self::idCardVerifyNumber($idCardBase) != strtoupper(substr($idCard, 17, 1))) {
			return false;
		}

		return true;
	}
}
