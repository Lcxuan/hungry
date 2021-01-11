<?php
namespace app\index\controller;

use app\common\Base;
use app\index\model\OrderCommodity;
use think\Exception;
use think\Request;
use app\index\model\Order;
use app\index\model\Business;

class orderStatus extends Base
{
    /**
     * @param Request $request
     * @param Order $order
     * @param Business $business
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     * @throws Exception
     */
    //最终输出变量为$arrOrderInfo
    public function orderStatusInfo(Request $request,Order $order,Business $business,OrderCommodity $orderCommodity){
        try{
            //获取请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //请求头转数组
            $arr = json_decode($token,true);
            //查找数据库是否有下单
            $haveOrder = $order->findOrder($arr['id']);
            //没有订单说明未下单
            if ($haveOrder->isEmpty()){
                return responseJson(Base::JUMP_PAGE,'跳转到未下单页面');
            }
            //下单页面(查找商家名，商家头像，订单状态，商品名，商品数量，创建时间，总价格)
            $orderInfo = $order->findOrderInfo($arr['id']);
            //转为数组
            $arrOrderInfo = json_decode($orderInfo,true);
            //循环
            for ($i=0;$i<count($arrOrderInfo);$i++){
                //订单号获取(为了找到商品名、商品数量)
                $orderInfo_orderNum = $arrOrderInfo[$i]['orderNum'];
                //获取商家Id(为了找到商家名以及商家头像)
                $orderInfo_businessId = $arrOrderInfo[$i]['businessId'];

                //获取所有商品名(转为数组)
                $orderCommodityName = $orderCommodity->findComodityName($orderInfo_orderNum);
                $arrCommodityName = json_decode($orderCommodityName,true);
                $cname = '';
                foreach ($arrCommodityName as $key => $value){
                    $cname .= $arrCommodityName[$key]['commodityName'] . '+';
                }
                //将最后的+给去掉
                $allCommodityName = substr($cname,0,strlen($cname)-1);

                //获取商品数量
                $orderCommodityNum = $orderCommodity->findComodityNum($orderInfo_orderNum);

                //获取商家名
                $orderBusinessName = $business->findBusiness($orderInfo_businessId);

                //获取商家头像
                $orderBusinessImg = $business->findBusinessImg($orderInfo_businessId);

                //放入数组
                $arrOrderInfo[$i]['commodityName'] = $allCommodityName;
                $arrOrderInfo[$i]['commodityNum'] = $orderCommodityNum;
                $arrOrderInfo[$i]['businessName'] = $orderBusinessName;
                $arrOrderInfo[$i]['businessImg'] = $orderBusinessImg;

            }
            return responseJson(Base::OK,'订单信息获取成功',$arrOrderInfo);
        }catch (Exception $e){
            throw $e;
        }
    }
}