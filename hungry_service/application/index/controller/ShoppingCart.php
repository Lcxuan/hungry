<?php


namespace app\index\controller;


use app\common\Base;
use app\index\model\Carts;
use app\index\model\Commodity;
use app\index\model\Coupon;
use app\index\model\CouponReceive;
use think\Request;

class ShoppingCart extends  Base
{
    
    //列出购物车数据(成功)
    /**
     * @param Request $request
     * @param Carts $carts
     * @param Commodity $commodity
     * @param Coupon $coupon
     * @param CouponReceive $couponReceive
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selCart(Carts $carts,Commodity $commodity){
        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $uId = $arr['id'];//获取用户id
//        $uId = 14;
        $businessName = $carts->selCartName($uId);

        //遍历多维数组
        $arr = array();
        foreach($businessName as $key => $value){
            array_push($arr,$businessName[$key]['businessName']);
        }
        //清除重复商家名
        $arr = array_unique($arr);

        $array  =array();
        //按商家名来排序

        for($i = 0 ;$i<count($arr);$i++){
            $bname = $arr[$i];
            $commodityId = $carts->selCartComid($bname,$uId);

            //查询出商品ID后，
            $comarr = array();
            //查询商品信息

            foreach ($commodityId as $keya => $valuea){
                $comtext = $commodity-> selCom('id',$valuea['commodityId'],'id,businessId,commodityName,commodityImg,presentPrice');
                $comarr[$keya]= $comtext[0];
                $comarr[$keya]['businessname']= $bname;

          //添加最终数组

        }   $array[$i] = $comarr;

        } return responseJson(Base::OK,'查询成功',$array) ;
    }

    /**
     * @param Carts $carts
     * @param Commodity $commodity
     * @param Coupon $coupon
     * @param CouponReceive $couponReceive
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    //查询购物车优惠卷（成功）
    public function  selCartcoupon(Carts $carts,Commodity $commodity,Coupon $coupon,CouponReceive $couponReceive){
        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $userId = $arr['id'];//获取用户id

        $businessName = $carts->selCartName($userId);

        //遍历多维数组
        $arr = array();
        foreach($businessName as $key => $value){
            array_push($arr,$businessName[$key]['businessName']);
        }
        //清除重复商家名
        $arr = array_unique($arr);

        $array  =array();
        //按商家名来排序

        for($i = 0 ;$i<count($arr);$i++) {
            $bname = $arr[$i];
            $bId = $carts->selCartBid($bname, $userId)[$i]['businessId'];

            $coupontext = $coupon->selCoupon($bId);
            $couarr = array();
        // 查询优惠卷

            foreach ($coupontext as $key => $value){
                        $couid = $value['id'];
                        $couname = $value['name'];
                        $coutext = $couponReceive->selCoupon($userId,$couid);
                        if(count($coutext)!=0){
                            array_push($couarr,$coutext[0]);
                            for($q=0;$q<count($couarr);$q++){
                                $couarr[$q]['name'] = $couname;
                            }

                         }
                     }
            $array[$bname] = $couarr;
        }
        return responseJson(Base::OK,'查询成功',$array);
    }
}
