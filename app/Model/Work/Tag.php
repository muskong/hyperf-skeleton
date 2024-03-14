<?php
declare(strict_types=1);

namespace App\Model\Work;
use App\Model\Model;

class Tag extends Model
{
	const TypeBrand='brand';
	const TypeMerchant='merchant';

	protected array $fillable = [
		'udid',
		'title',
		'type',
		'status',
	];
}
