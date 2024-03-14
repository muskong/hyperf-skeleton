<?php
declare(strict_types=1);

namespace App\Model\Work;

use App\Model\Model;
use Hyperf\Database\Model\Builder;


class Product extends Model
{
	const Status1 = 1;
	const Status0 = 0;



	protected array $fillable = [
		'udid',
		'title',
		'note',
		'price',
		'sale_price',
		'cost_price',
		'status',
	];

	public function scopeQueryTags(Builder $builder, $isp)
	{
		$builder->where('status', 1)
			->whereHas('tags', function ($query) use ($isp) {
				$udid = trim(bin2hex($isp['isp']));
				$query->where('udid', $udid);
			});
	}

	public function tags()
	{
		return $this->belongsToMany(Tag::class);
	}
	public function prices()
	{
		return $this->hasMany(ProductTag::class);
	}
}
