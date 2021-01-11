<?php


namespace app\business\model;


use think\Model;

class Money extends Model
{
    public function findBusinessMoney($businessId){
        return $this->field('Money')->where([
            'targetId'      =>  $businessId,
            'targetType'    =>  1
        ])->order('createTime','desc')->limit(1)->find();
    }
}