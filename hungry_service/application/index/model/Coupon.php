<?php


namespace app\index\model;
use think\Db;
use think\Model;
//优惠卷
class Coupon extends Model
{
    /**
     * @findCoupon  查找优惠券
     * @return mixed
     */
    public function findCoupon(){
        return json_decode($this->field('id,name,money,endTime')->where('status',1)->all(),true);
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException列出所有优惠卷信息
     */
    public  function  selallCoupon(){
        $data = Db::table('coupon')
            ->field('id,businessId,type,photo,name,money,fullMoney,endTime')
            ->where('status','=','1')
            ->select();
        return $data;
    }
    /**购物车查询指定商家拥有的优惠卷
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selCoupon($bid){
        return  Db::table('coupon')
            ->field('id,name')
            ->where('businessId',"=", $bid)
            ->where('status',"=", '1')
            ->select();
    }
}