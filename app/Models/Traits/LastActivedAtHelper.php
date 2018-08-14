<?php

namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {
        $date = Carbon::now()->toDateString();
        $now = Carbon::now()->toDateTimeString();

        $hash = $this->hash_prefix . $date;
        $field = $this->field_prefix . $this->id;

        Redis::hSet($hash, $field, $now);
    }

    public function syncUserActivedAt()
    {
        $yesterday_date = Carbon::now()->toDateString();

        $hash = $this->hash_prefix . $yesterday_date;

        $dates = Redis::hGetAll($hash);

        foreach($dates as $user_id=>$date){

            $user_id = str_replace($this->field_prefix, '', $user_id);

            if($user = $this->find($user_id)) {
                $user->last_actived_at = $date;
                $user->save();
            }

        }

        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        // 获取今日对应的哈希表名称
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        // 字段名称，如：user_1
        $field = $this->getHashField();

        // 三元运算符，优先选择 Redis 的数据，否则使用数据库中
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在的话，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
            // 否则使用用户注册时间
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date)
    {
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix . $date;
    }

    public function getHashField()
    {
        // 字段名称，如：user_1
        return $this->field_prefix . $this->id;
    }

}