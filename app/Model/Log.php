<?php

declare(strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Relations\MorphTo;

class Log extends Model
{

    protected array $fillable = [
        'model_id',
        'model_type',
        'title',
        'content',
    ];

    protected array $casts = [
        'content' => 'json',
    ];

    /**
     * 获取父级 log 模型
     */
    public function log(): MorphTo
    {
        return $this->morphTo();
    }

}
