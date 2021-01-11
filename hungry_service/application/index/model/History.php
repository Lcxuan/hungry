<?php


namespace app\index\model;
use think\Model;
//历史店铺
class History extends Model
{
    /**
     * @findHistory 查找历史商家记录
     * @param int $userId
     * @param int $businessId
     * @return History
     */
    public function findHistory(int $userId,int $businessId){
        return $this->where([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId
        ])->find();
    }

    /**
     * @updateTime  更新历史商家时间
     * @param int $userId
     * @param int $businessId
     * @return History
     */
    public function updateTime(int $userId,int $businessId){
        return $this->where([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId
        ])->update([
            'updateTime'    =>  time()
        ]);
    }

    /**
     * @createHistory   创建历史商家记录
     * @param int $userId
     * @param int $businessId
     * @return int|string
     */
    public function createHistory(int $userId,int $businessId){
        return $this->insert([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId,
            'createTime'    =>  time(),
            'updateTime'    =>  time()
        ]);
    }
}