<?php


namespace app\business\model;


use think\Model;

class OrderCommodity extends Model
{
    /**
     * @findCommodityName   查询商品名
     * @param $orderNum
     * @return OrderCommodity
     */
    public function findCommodityName(string $orderNum){
        return $this->table('orderCommodity')->field('commodityName')->where('orderId',$orderNum)->find();
    }
}