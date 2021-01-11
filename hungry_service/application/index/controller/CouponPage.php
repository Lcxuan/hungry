<?php


namespace app\index\controller;


use app\common\Base;
use app\index\model\Business;
use app\index\model\Coupon;
use app\index\model\CouponReceive;
use think\Request;

class CouponPage  extends Base
{
    /**
     * @param Request $request
     * @param Coupon $coupon
     * @param Business $business
     * @return \think\response\Json
     */
       //列分页优惠卷(成功)
    public function selCoupon(Request $request , Coupon $coupon ,Business $business){
        $pagenum = $request->post('pagenum');//获取当前页数

        $data = array();
        //查询优惠卷
        $coupontext = $coupon -> selallCoupon();
        //每页起始键                   定义分页数据条数
        $start = $pagenum  * Base::$pageSize - Base::$pageSize;
        //每页结尾键
        $end = $pagenum * Base::$pageSize;
        //如果操过了，就按数量最后
        if($end > count($coupontext)){
            $end = count($coupontext);
        }
        //循环出指定键
        for($i=$start;$i<$end;$i++){
            $businessId = $coupontext[$i]['businessId'];
            //获取商家的地址等信息
            $businesstext = $business->findBusinessadd($businessId);
            array_push($data, $coupontext[$i]);
            foreach ($data as $key => $value)
            $data[$key]['business'] = $businesstext[0];
        }

        $page =intdiv(count($coupontext),5) + 1 ;
        $data['pagenum'] = $page;
        $data['nowpage'] = $pagenum;

        return responseJson(Base::OK,'成功',$data);
}
    /**
     * @param Request $request
     * @param CouponReceive $couponReceive
     * @return \think\response\Json
     */
       //领取优惠卷 (成功)
       public function  addCoupan( Request $request,CouponReceive $couponReceive){
           //$token是你所登录用户返回的请求头(数据库信息)
           $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
           //将请求头转为数组
           $tokenarr = json_decode($token,true);
           $userId = $tokenarr['id'];//获取用户id

           $arr = $request->post();//获取所有数据
           if($couponReceive->addCoupan($userId,$arr)){
               return responseJson(Base::OK,'添加成功');
           }
           
       }
}