<?php
declare(strict_types=1);

namespace App\Model\Work;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Relations\Pivot;

class ProductTag extends Pivot
{
	/**
	 * 标识 ID 是否自增
	 *
	 * @var bool
	 */
	public bool $incrementing = true;


	protected array $fillable = [
		'id',
		'tag_id',
		'product_id',
		'price',
		'sale',
		'cost',
	];

	public function scopeQueryMerchant(Builder $builder)
	{
		$builder->whereHas('tag', function ($query) {
			$query->where('type', Tag::TypeMerchant);
		});
	}

	public function tag()
	{
		return $this->belongsTo(Tag::class);
	}
	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
