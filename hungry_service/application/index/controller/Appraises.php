<?php
namespace app\index\controller;

use app\common\Base;
use app\index\model\Business;
use app\index\model\CommodityAppraises;
use app\index\model\Order;
use app\index\model\OrderCommodity;
use app\index\validate\AppraisesValidate;
use app\index\validate\UserValidator;
use think\Exception;
use think\Request;

class Appraises extends Base
{
    /**
     * @param Request $request
     * @param Order $order
     * @param Business $business
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     * @throws Exception
     */
    //评价订单信息区域(传值orderNum)
    public function appraisesOrderInfo(Request $request,Order $order,Business $business,OrderCommodity $orderCommodity){
        try{
            //获取请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //将请求头转为数组
            $arr = json_decode($token,true);
            //查找出数据(商家头像，商品名commodityName，以及数量，订单创建时间，总价)
            //拿到前端传的订单号
            $orderNum = $request->post('orderNum');

            //找出该订单号的信息
            $orderInfo = $order->appFindOrderInfo($arr['id'],$orderNum);
            //将订单号信息转为数组
            $arrOrderInfo = json_decode($orderInfo,true);
            //循环数据
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

    //更改评价区域(传值(当前的订单)orderNum,content,commodityScore,serviceScore,timeScore)
    public function appraisesInsertInfo(Request $request,Order $order,CommodityAppraises $commodityAppraises,AppraisesValidate $appraisesValidate){
        try{
            //验证器
            if (!$appraisesValidate->check($request->post())){
                return responseJson(Base::FAIL,$appraisesValidate->getError());
            }
            //获取请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //请求头转数组
            $arr = json_decode($token,true);
            //获取前端传给你的订单号
            $orderNum = $request->post('orderNum');
            //获取输入的content
            $content = $request->post('content');
            //获取输入的commodityScore
            $commodityScore = $request->post('commodityScore');
            //获取输入的serviceScore
            $serviceScore = $request->post('serviceScore');
            //获取输入的timeScore
            $timeScore = $request->post('timeScore');

            //获取待评价insert区域所需信息
            //找出orderId
            $orderId[] = $order->appFindOrderId($arr['id'],$orderNum);
            //找出commodityNum
            $commodityNum[] = $order->appFindOrderNum($arr['id'],$orderNum);
            //找出businessId
            $businessId[] = $order->appFindBusinessId($arr['id'],$orderNum);

            //验证器已判断评论是否合理
            //传data
            $data = $commodityAppraises->insertCommodityInfo($arr['id'],$orderId,$commodityNum,$businessId,$content,$commodityScore,$serviceScore,$timeScore);

            //判断是否传入成功
            if ($data != 1){
                return responseJson(Base::FAIL,'评论发表失败');
            }
            return responseJson(Base::OK,'发表评论成功');
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @param Order $order
     * @param Business $business
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     * @throws Exception
     */
    //待评价列表
    //最终输出变量为$arrOrderInfo
    public function waitToAppraises(Order $order,Business $business,OrderCommodity $orderCommodity){
        try{
            //获取请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //请求头转数组
            $arr = json_decode($token,true);
            //下单页面(查找商家名，商家头像，订单状态，商品名，商品数量，创建时间，总价格)
            if (($orderInfo = $order->waitToAppFindOrderInfo($arr['id']))->isEmpty()) {
                return responseJson(Base::OK,'您还没有待评价的订单');
            }
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