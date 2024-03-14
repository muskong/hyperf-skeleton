<?php
declare(strict_types=1);

namespace App\Model\Work;

use App\Model\Log;
use App\Model\Model;
use App\Model\User;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\SoftDeletes;

use Hyperf\Database\Model\Relations\MorphMany;

class Order extends Model
{
	use SoftDeletes;

	protected array $fillable = [
		'type',
		'udid',
		'user_id',
		'product_tag_id',
		'amount',
		'cost_amount',
		'notfy_url',
		'status',
		'mobile',
	];
	protected array $appends = ['countDown', 'statusText'];


	// 订单状态; created:下单, third:第三方处理中, success:成功, failed:失败
	const StatusCreated = 'created';
	const StatusPaid = 'paid';
	const StatusSuccess = 'success';
	const StatusFailed = 'failed';
	const StatusTexts = [
		self::StatusCreated => '未支付',
		self::StatusPaid => '充值中',
		self::StatusSuccess => '充值成功',
		self::StatusFailed => '充值失败',
	];

	const TypeRecharge = 'recharge';
	const TypeVirtual = 'virtual';

	public function getCountDownAttribute($value)
	{
		$timeCreate = strtotime($this->created_at);
		$end = strtotime('+2 hour', $timeCreate);
		$countDown = $end - time();
		$result = 0;
		if ($countDown > 0) {
			$result = $countDown * 1000;
		}
		return $result;
	}
	public function getStatusTextAttribute()
	{
		return self::StatusTexts[$this->status] ?? '--';
	}

	/**
	 * 根据订单ID查询
	 */
	public function scopeQueryOrder(Builder $query, string $orderUdid): void
	{
		$query->where('udid', '=', $orderUdid);
	}

	/**
	 * 根据用户ID查询
	 */
	public function scopeQueryMember(Builder $query, int $userId): void
	{
		$query->where('user_id', '=', $userId);
	}
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	// 日志
	public function logs(): MorphMany
	{
		return $this->morphMany(Log::class, 'log');
	}

	public function product()
	{
		return $this->hasOneThrough(
			Product::class,
			ProductTag::class,
			'product_id',
			'id',
			'product_tag_id',
			'id',
		);
	}
}
