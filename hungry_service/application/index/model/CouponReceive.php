<?php


namespace app\index\model;
use think\Db;
use think\Model;
//优惠券领取记录
class CouponReceive extends Model
{
    /**购物车查询指定ID拥有的优惠卷
     * @param $uId
     * @param $couid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selCoupon(int $userId,int $couid){
        return  Db::table('couponReceive')
            ->field('id,couponMoney,fullMoney,starTime')
            ->where('userId',"=", $userId)
            ->where('couponId',"=", $couid)
            ->where('status',"=", '0')
            ->select();
    }
    public function  addCoupan(int $userId , array  $arr){
        $CoupanData =[
            'userId' => $userId,//用户编号
            'couponId' => $arr['couponId'], //优惠卷编号
            'couponMoney' =>$arr['couponMoney'],//优惠价金额
            'fullMoney' =>$arr['fullMoney'],//金额满
            'starTime'=> $arr['starTime'],//开始时间
            'creatTime'=> time()//领取时间

        ];
        return $this->table('couponReceive') -> insert($CoupanData);
    }


    public function  payOrder(int $uid ,int  $couid){
        $data  = [
            'status' => '1',
        ];
        Db::table('order')
            ->where('userId',$uid)
            ->where('orderNum',$couid)
            ->update($data);
    }
}