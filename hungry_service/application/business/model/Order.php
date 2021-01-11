<?php


namespace app\business\model;


use think\Model;

class Order extends Model
{
    /**
     * @findOrderStatus 查询订单状态
     * @param $orderId
     * @return Order
     */
    public function findOrderStatus(int $orderId){
        return $this->field('orderStatus')->where('id',$orderId)->find();
    }
    /**
     * @createTime  订单创建时间
     * @param $orderId
     * @return Order
     */
    public function createTime(int $orderId){
        return $this->field('createTime')->where('id',$orderId)->find();
    }

    /**
     * @allOrders 全部订单
     * @param int $businessId
     * @return mixed
     */
    public function allOrders(int $businessId,string $deliveryStatus){
        //全部订单
        if ($deliveryStatus == ''){
            return json_decode($this->field('orderNum,orderStatus,createTime,orderRemarks,userPhone,provide,city,county,street,detail,realTotalMoney,deliveryStatus')
                ->where([
                    'businessId'        =>  $businessId,
                    'deleted'           =>  '0'
                ])->all(),true);
        }
        return json_decode($this->field('orderNum,orderStatus,createTime,orderRemarks,userPhone,provide,city,county,street,detail,realTotalMoney,deliveryStatus')
        ->where([
            'businessId'        =>  $businessId,
            'deliveryStatus'    =>  $deliveryStatus,
            'deleted'           =>  '0'
        ])->all(),true);
    }
}