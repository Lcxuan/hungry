<?php


namespace app\business\controller;

use app\business\model\CommodityAppraises;
use app\business\model\Order;
use app\business\model\OrderCommodity;
use app\business\validate\CommodityValidate;
use app\common\Upload;
use app\business\model\Commodity;
use app\business\model\CommodityClass;
use app\business\validate\BusinessValidator;
use app\common\Base;
use think\Exception;
use think\Request;

/**
 * Class BusinessPage   商家信息
 * @package app\business\controller
 */
class BusinessPage extends  Base
{
    /**
     * @reply   商家回复评论
     * @param Request $request
     * @param BusinessValidator $businessValidator
     * @param CommodityAppraises $commodityAppraises
     * @return \think\response\Json
     * @throws Exception
     */
    public function reply(Request $request,BusinessValidator $businessValidator,CommodityAppraises $commodityAppraises){
        try {
            if (!$businessValidator->check($request->post())){
                return responseJson(Base::FAIL,$businessValidator->getError());
            }
            $reply = $request->post('reply');
            $commodityAppraisesId = $request->post('id');
            if (!$commodityAppraises->reply($reply,$commodityAppraisesId)){
                return responseJson(Base::FAIL,'回复评论失败，请重试');
            }
            return responseJson(Base::OK,'回复评论成功，请刷新');
        }catch (Exception $e){
            throw $e;
        }
    }
    /**
     * @allOrders   商家-订单类型（全部订单，待接单，待发货，已发货）
     * @param Order $order
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     */
    public function allOrders(Request $request,Order $order,OrderCommodity $orderCommodity){
        try {
            $deliveryStatus = $request->post('deliveryStatus'); //-1：待接单 0:待发货 1:已发货 2:全部
            if ($deliveryStatus == 2)$deliveryStatus = '';
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            if (!$ordersData = $order->allOrders($arr['id'],$deliveryStatus)) {
                return responseJson(Base::OK,'暂无订单');
            }
            if (empty($ordersData)) return responseJson(Base::OK,'暂无订单数据');
            foreach ($ordersData as $key => $value){
                switch ($ordersData[$key]['orderStatus']) {
                    case $ordersData[$key]['orderStatus'] = 1:
                        $ordersData[$key]['orderStatus'] = '配送中';
                        break;
                    case $ordersData[$key]['orderStatus'] = 2:
                        $ordersData[$key]['orderStatus'] = '确认收货';
                        break;
                    case $ordersData[$key]['orderStatus'] = -1:
                        $ordersData[$key]['orderStatus'] = '待接单';
                        break;
                    default :
                        $ordersData[$key]['orderStatus'] = '未付款';
                        break;
                }
                switch ($ordersData[$key]['deliveryStatus']){
                    case $ordersData[$key]['deliveryStatus'] = 0:
                        $ordersData[$key]['deliveryStatus'] = '待发货';
                        break;
                    case $ordersData[$key]['deliveryStatus'] = 1:
                        $ordersData[$key]['deliveryStatus'] = '已发货';
                        break;
                    default:
                        $ordersData[$key]['deliveryStatus'] = '待接单';
                        break;
                }
                $ordersData[$key]['createTime'] = date('Y-m-d',$ordersData[$key]['createTime']);
                $address = $ordersData[$key]['provide'].$ordersData[$key]['city'].$ordersData[$key]['county'].$ordersData[$key]['street'].$ordersData[$key]['detail'];
                unset($ordersData[$key]['provide'],$ordersData[$key]['city'],$ordersData[$key]['county'],$ordersData[$key]['street'],$ordersData[$key]['detail']);
                $ordersData[$key]['address'] = $address;
                if (empty($commodityName = $orderCommodity->findCommodityName($ordersData[$key]['orderNum']))) {
                    return responseJson(Base::FAIL,'数据获取错误，请重试');
                }
                $ordersData[$key]['commodityName'] = $commodityName['commodityName'];
            }
            return responseJson(Base::OK,'订单数据',$ordersData);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @evaluationManagement    商家-评价管理（已回复，待回复，全部）
     * @param CommodityAppraises $commodityAppraises
     * @param Order $order
     * @param Commodity $commodity
     * @return \think\response\Json
     */
    public function evaluationManagement(Request $request,CommodityAppraises $commodityAppraises,Order $order,Commodity $commodity){
        try {
            $evaluationStatus = $request->post('evaluationStatus'); //0：已回复 1：待回复 2：全部
            if ($evaluationStatus == 2)$evaluationStatus = '';
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $evaluation = $commodityAppraises->evaluation($arr['id'],$evaluationStatus);
            if ($evaluation->isEmpty()){
                return responseJson(Base::OK,'暂无评论');
            }
            foreach ($evaluation as $key => $value){
                if (empty($commodityName = $commodity->findCommodityName($evaluation[$key]['businessId'],$evaluation[$key]['commodityId']))) {
                    return responseJson(Base::FAIL,'数据获取失败，请重试');
                }
                $evaluation[$key]['commodityName'] = $commodityName['commodityName'];
                $evaluation[$key]['evaluation'] = ($evaluation[$key]['commodityScore']+$evaluation[$key]['serviceScore']+$evaluation[$key]['timeScore'])/3;
                unset($evaluation[$key]['commodityScore'],$evaluation[$key]['serviceScore'],$evaluation[$key]['timeScore']);
                if (empty($createTime = $order->createTime($evaluation[$key]['orderId']))) {
                    return responseJson(Base::FAIL,'数据获取失败，请重试');
                }
                $evaluation[$key]['createTime'] = date('Y-m-d',$createTime['createTime']);
                if (empty($orderStatus = $order->findOrderStatus($evaluation[$key]['orderId']))) {
                    return responseJson(Base::FAIL,'数据获取失败，请重试');
                }
                switch ($orderStatus) {
                    case $orderStatus = 1:
                        $orderStatus = '配送中';
                        break;
                    case $orderStatus = 2:
                        $orderStatus = '确认收货';
                        break;
                    case $orderStatus = -1:
                        $orderStatus = '待接单';
                        break;
                    default :
                        $orderStatus = '未付款';
                        break;
                }
                $evaluation[$key]['orderStatus'] = $orderStatus;
                if ($evaluation[$key]['evaluationStatus'] == 0){
                    $evaluation[$key]['evaluationStatus'] = '已回复';
                }else{
                    $evaluation[$key]['evaluationStatus'] = '待回复';
                }
            }
            return responseJson(Base::OK,'数据获取成功',$evaluation);
        }catch (Exception $e){
            throw $e;
        }
    }
    /**
     * @commoditySearch 商家-商品搜索
     * @param Request $request
     * @param CommodityValidate $commodityValidate
     * @param Commodity $commodity
     * @param CommodityClass $commodityClass
     * @return \think\response\Json
     * @throws Exception
     */
    public function commoditySearch(Request $request,CommodityValidate $commodityValidate,Commodity $commodity,CommodityClass $commodityClass){
        try {
            if (!$commodityValidate->check($request->post())){
                return responseJson(Base::FAIL,$commodityValidate->getError());
            }
            $commodityName = $request->post('commodityName');
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            if (!$data = $commodity->commoditySearch($arr['id'],$commodityName)) {
                return responseJson(Base::FAIL,'数据获取失败');
            }
            foreach ($data as $key => $val){
                $data[$key]['createTime'] = date('Y-m-d',$data[$key]['createTime']);
            }
            return responseJson(Base::OK,'数据获取成功',$data);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @commodityInfo 商家-商品信息管理
     * @param Commodity $commodity
     * @param CommodityClass $commodityClass
     * @return \think\response\Json
     * @throws Exception
     */
    public function commodityInfo(Commodity $commodity,CommodityClass $commodityClass){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            if (!$data = $commodity->showCommodity($arr['id'])) {
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            foreach ($data as $key => $value){
                $classification = $commodityClass->classification($data[$key]['catId']);
                $data[$key]['catName'] = $classification;
                unset($data[$key]['catId']);
                $data[$key]['createTime'] = date('Y-m-d',$data[$key]['createTime']);
            }
            return responseJson(Base::OK,'数据获取成功',$data);
        }catch (Exception $e){
            throw $e;
        }
    }
    
    //列出商品类型  成功

    /**
     * @param Request $request
     * @param CommodityClass $commodityClass
     * @param BusinessValidator $businessValidator
     * @return \think\response\Json
     * @throws Exception
     */
    public  function  selClass(Request $request ,CommodityClass $commodityClass, BusinessValidator$businessValidator){
//        $token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $businessId = $arr["id"];//获取商家id
//            $businessId  =1;
        try{
                if ($commodityClass ->classSel($businessId)){
                    $data = $commodityClass ->classSel($businessId);
                  return  responseJson(Base::OK,'列出成功',$data);
                }
        }catch (Exception $e){
            throw  $e;
        }
    }

    /**
     * @param Request $request
     * @param Commodity $commodity
     * @param CommodityClass $commodityClass
     * @param BusinessValidator $businessValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //添加商品 成功
    public  function  addCommodity(Request $request,Commodity $commodity ,CommodityClass $commodityClass ,BusinessValidator $businessValidator){
       
        
        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $businessId = $arr['id'];//获取商家id

        $name = $request ->post('name'); //商品名
        $typeId = $request ->post('typeId'); //商品类型
        $price = $request ->post('price'); //价格
        $desc = $request ->post('desc'); //介绍
        $img = $request ->file('img')->getInfo(); //图片路径
        $filename = "commodity". $businessId . ".png";
        $upload = new Upload();
//        return  json($img);
        //上传
        if(!$upload ->upload($img['tmp_name'],$filename)){
            return  responseJson(Base::FAIL,'添加图片失败');
        }
        try{
            if ($businessValidator -> check ($request ->post())){
                return responseJson( Base::FAIL,$businessValidator->getError());
            }
            if(!$commodity ->commodityData($name,$filename,$price,$desc,$businessId,$typeId)){
               return responseJson(Base::FAIL,'添加失败');
            }
           return responseJson(Base::JUMP_PAGE,'添加商品成功');
        }catch (Exception $e){
            throw $e;
        }

    }
    /**
     * @param Request $request
     * @param CommodityClass $commodityClass
     * @param BusinessValidator $businessValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //添加商品类型 成功
    public  function addComclass(Request $request , CommodityClass $commodityClass , BusinessValidator $businessValidator){
        $type = $request ->post('type'); //商品类型

        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $businessId = $arr['id'];//获取商家id
        
        try{
            if($businessValidator ->check($request ->post())){
                return responseJson(Base::FAIL,$businessValidator->getError());
            }
            $data = $commodityClass ->classData($type,$businessId);
            if (!$data){
                return responseJson(Base::FAIL,'添加错误');
            }
                return  responseJson(Base::OK,'添加类型成功',$data);
        }catch (Exception $e){
            throw  $e;
        }
    }

}