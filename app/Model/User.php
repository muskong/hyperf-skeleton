<?php

declare(strict_types=1);
namespace App\Model;

use App\Constants\ErrorCode;
use App\Services\TokenService;
use Exception;
use Hyperf\Config\Annotation\Value;

use Hyperf\Database\Model\Relations\MorphMany;
use Hyperf\Database\Model\Relations\MorphOne;

class User extends Model
{

	#[Value("config.token.expiration")]
	private $tokenExpiration;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected array $fillable = [
		'openid',
		'nickname',
		'password',
		'invite_id',
		'level',
		'status',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected array $hidden = [
		'password',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected array $casts = [
		'password' => 'hashed',
	];
	/**
	 * 头像
	 */
	public function avatar(): MorphOne
	{
		return $this->morphOne(Image::class, 'model')->where('type', '=', 'avatar');
	}
	// 日志
	public function logs(): MorphMany
	{
		return $this->morphMany(Log::class, 'log');
	}

	public function passwordVerify($password)
	{
		$pass = $this->password;
		if ($password && !password_verify($password, $pass)) {
			throw new Exception('账号、密码错误', ErrorCode::PasswordVerify);
		}
	}

	public function generateToken()
	{
		$data          = [
			'id'       => $this->id,
			// 'merchant_id' => $this->merchant_id,
			// 'udid' => $this->udid,
			'avatar'   => $this->avatar ?: '',
			'nickname' => $this->nickname,

			'exp'      => strtotime($this->tokenExpiration),
			// 'phone' => $this->phone,
			// 'funds' => $this->funds,
		];
		$data['token'] = TokenService::generate([
			'id'       => $this->id,
			'nickname' => $this->nickname,
			'openid'   => $this->openid,
		], $this->tokenExpiration);

		return $data;
	}
}
