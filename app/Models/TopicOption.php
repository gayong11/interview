<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TopicOption
 * @package App\Models
 */
class TopicOption extends Model
{
    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['topic_id', 'option', 'content',];

    /**
     * 不维护created_at updated_at字段
     * @var bool
     */
    public $timestamps = false;
}
