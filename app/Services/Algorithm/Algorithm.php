<?php

namespace App\Services\Algorithm;

use Illuminate\Support\Str;

class Algorithm
{
	/**
	 * jti(JWT ID)
	 * 签发jwt时给予当前token的唯一ID，通常用于一次性消费的token。
	 */
	private $jti;

	/**
	 * iss(Issuer) jwt的颁发者，其值应为大小写敏感的字符串或Uri。
	 **/
	private $iss;
	/**
		* aud(Audience)
		* jwt的适用对象，其值应为大小写敏感的字符串或Uri。一般可以为特定的App、服务或模块。
		   private $* 比如我们颁发了一个jwt给一个叫”JsonWebToken”的app使用，sub可以是这个app的包签名或者标识。
		* 服务器端的安全策略在签发时和验证时，aud必须是一致的。
		**/
	private $aud;

	/**
	 * sub(Subject)
	 * jwt 的所有者，可以是用户ID、唯一标识。
	 **/
	private $sub;

	/**
	 * iat(Issued At)
	 * jwt的签发时间。同exp一样，需为可以解析成时间的数字类型。
	 */
	private $iat;
	/**
	 * exp(Expiration Time)
	 * jwt的过期时间，必须是可以解析为时间/时间戳的数字类型。服务器端在验证当前时间大于过期时间时，应当验证不予通过。
	 */
	private $exp;
	/**
	 * nbf(Not Before)
	 * 表示jwt在这个时间后启用。同exp一样，需为可以解析成时间的数字类型。
	 * 在此之前不可用, 表示 JWT Token 在这个时间之前是无效的
	 */
	private $nbf;

	public function __construct($data, $expiration = '+2 day')
	{
		$this->jti = Str::uuid();
		$this->iss = env('APP_NAME');
		$this->aud = env('APP_ENV');
		$this->iat = date('Y-m-d H:i:s');
		$this->nbf = date('Y-m-d H:i:s');
		$this->exp = date('Y-m-d H:i:s', strtotime($expiration));
		$this->sub = $data;
	}

	public function __toString()
	{
		return serialize($this);
	}

	public function getData()
	{
		return $this->sub;
	}

	public function validateIssAud(): bool
	{
		return !($this->iss == env('APP_NAME') && $this->aud == env('APP_ENV'));
	}

	public function validateNbf(): bool
	{
		return !($this->nbf <= date('Y-m-d H:i:s'));
	}
	public function validateExp(): bool
	{
		return ($this->exp < date('Y-m-d H:i:s'));
	}
}
