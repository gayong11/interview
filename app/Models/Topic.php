<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    // 题目类型
    const TYPE_TEXT = 'text';
    const TYPE_RADIO = 'radio';

    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['title', 'answer', 'type',];

    /**
     * 题目类型配置
     * @var array
     */
    public static $typeMap = [
        self::TYPE_TEXT   => '文本',
        self::TYPE_RADIO  => '单选',
    ];

    /**
     * 关联题目选项
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(TopicOption::class);
    }

}
