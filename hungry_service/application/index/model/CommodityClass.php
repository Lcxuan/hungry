<?php


namespace app\index\model;


use think\Model;

class CommodityClass extends Model
{
    /**
     * @classificationName  查找商品的分类
     * @param int $businessid
     * @return mixed
     */
    public function classificationName(int $businessid){
        return json_decode($this->table('commodityClass')
            ->field('id,catName')
            ->where([
                'businessId'    =>  $businessid,
                'isShow'        =>  1,
                'deleted'       =>  0
            ])->select(),true);
    }
}