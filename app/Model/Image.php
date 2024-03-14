<?php

declare(strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Relations\MorphTo;


class Image extends Model
{

	protected array $fillable = [
		'id',
		'model_id',
		'model_type',
		'image',
		'type',
	];
	/**
	 * 获取父级 model 模型（用户或帖子）。
	 */
	public function model(): MorphTo
	{
		return $this->morphTo();
	}

}
