<?php
//结算

namespace app\index\controller;
use app\common\Base;
use app\common\Upload;
use app\index\model\Business;
use app\index\model\CouponReceive;
use app\index\model\Money;
use app\index\model\Order;
use app\index\model\OrderCommodity;
use app\index\model\User;
use app\index\model\UserAddress;
use app\index\validate\SettlementValidator;
use Endroid\QrCode\ErrorCorrectionLevel;
use think\cache\driver\Redis;

use think\Exception;
use think\Request;
use Endroid\QrCode\QrCode;

class Settlement extends  Base
{

    //列出收获地址 成功
    /**
     * @param UserAddress $userAddress
     * @return \think\response\Json
     * @throws Exception
     */
    public  function  selAddress(UserAddress $userAddress){
        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $uId = $arr['id'];//获取用户id

        try{
            if ($userAddress->selAddress($uId)){
                return  responseJson(Base::OK,'列出成功',$userAddress->selAddress($uId));
            }

        }catch (Exception $e){
            throw  $e;
        }
    }

    //去支付(存订单去数据库) 成功
//修改优惠劵状态
    /**
     * @param Request $request
     * @param Order $order
     * @param OrderCommodity $orderCommodity
     * @param SettlementValidator $SettlementValidator
     * @return \think\response\Json
     * @throws Exception
     */
        public function  addOrder( Request $request,CouponReceive $couponReceive, Order $order ,OrderCommodity $orderCommodity,SettlementValidator $SettlementValidator ){
            //$token是你所登录用户返回的请求头(数据库信息)
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //将请求头转为数组
            $arr = json_decode($token,true);
            $uid = $arr['id'];//获取用户id

            //随机出id
            $idarr =$order->selOrderid();

            $id = 'er'.rand(0,9999999);
            for($i=0;$i<9999999;$i++){
                if(in_array($id,$idarr)){
                    $id = 'er'.rand(0,9999999);
                }else{
                    break;
                }
            }



            $bid = $request ->post('businessid');//获取商家id
            $arr = $request ->post();//获取所有不需要处理的数据

            $paytype = $request ->post('paytype');//获取支付方式
            $orderRemarks = $request ->post('orderRemarks');//获取备注

            $shop = $request->post('Shop');//获取订单信息
            $coupon = $request->post('Coupon');//获取优惠卷信息



            try{
                if (!$SettlementValidator -> check ($request ->post())){
                    return responseJson( Base::FAIL,$SettlementValidator->getError());

                }
                if(!$order ->addOrder($id,$uid,$bid,$arr,$paytype,$orderRemarks) ){
                  return  responseJson(Base::FAIL,'添加失败');
                }
                for($q=0;$q<count($coupon);$q++){
                    $couid = $coupon[$q];
                    if(!$couponReceive->payOrder($uid,$couid)){
                        return responseJson(Base::FAIL);
                    };
                }
                for($i = 1;$i<=count($shop);$i ++){
                    $a = "shop" . $i;
                    $ctext = $shop[$a];
//                  循环添加多个商品
                    $cid = $ctext['commodityId'];
                    $commodityImg = $ctext['commodityImg'];
                    $commodityName= $ctext['commodityName'];
                    $commodityNum= $ctext['commodityNum'];
                    $money= $ctext['money'];
                    if(!$orderCommodity->addOC($id, $cid,$commodityName,$commodityNum,$money,$commodityImg)){
                        return responseJson(Base::FAIL);
                    } ;

                }
                $array = array();
                array_push($array,$id);
                return responseJson(Base::JUMP_PAGE,'添加成功');
            }catch (Exception $e){
                throw $e;
            }
        }

    /**
     * @param Request $request
     * @param Redis $redis
     * @param Order $order
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    //结算二维码（成功）
    public function  qrCode(Request $request , Redis $redis , Order $order){
        $orderId = $request -> post('orderId');//获取订单Id

        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $uid = $arr['id'];//获取用户id

        $ordertext = $order->selOrder("orderNum","$orderId",'businessId,realTotalMoney');//获取收款商家Id.获取订单价格
        $redisset = [
            'uid'=>$uid,
            'orderId'=>$orderId,
            'businessId'=>$ordertext[0]['businessId'],
            'money'=>$ordertext[0]['realTotalMoney']
        ];
        app('mycache')->set($orderId,json_encode($redisset),360);//信息存redis,3分钟之类完成，不然删除



        $value = "http://www.hungry.com/ ".$orderId; //二维码内容 网址加订单id
        $qrCode  = new QrCode($value);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(5);
        $qrCode->setEncoding('utf-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r'=>0,'g'=>0,'b'=>0,'a'=>0,]);
        $qrCode->setBackgroundColor(['r'=>255,'g'=>255,'b'=>255,'a'=>0,]);
        $qrCode -> setWriterOptions(['exclude_xml_declaration'=>true]);

        $uploadname= $orderId . 'qrcode.png'; //定义二维码文件名。

        $file= __DIR__.'\..\..\..\public\static\ '. $uploadname;    //定义图片存放路径

        $qrCode->writeFile($file);

        $qiniu='http://hungry.wistudy.xyz/' . $uploadname;
        $upload = new Upload();
        $upload->upload($file,$uploadname);
        unlink($file);
        return responseJson(Base::JUMP_PAGE,'生成成功',$qiniu);


    }

    // 商品详情(成功）

    /**
     * @param Request $request
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     */
    public function comText(Request $request ,OrderCommodity $orderCommodity){

        $orderId = $request ->post('orderId');//获取订单信息
        $comtext =$orderCommodity ->selOC('orderId',$orderId,'commodityImg,commodityName,commodityNum,money');//查询订单商品信息
        return responseJson(Base::OK , '查询成功',$comtext);

    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws Exception
     */
   //移动端支付信息 (成功）
    public function  getPaytext(Request $request){
        $orderId = $request -> post('orderId');//获取二维码传的地址上的订单ID
        try{
            if (!app('mycache')->get($orderId)){
                return responseJson(Base::FAIL,'服务器出错，请重试');
            }
            $Data  = app('mycache')->get($orderId) ;
            $Data = \Qiniu\json_decode($Data,true);
            return  responseJson(Base::OK,'列出成功',[$Data]);
        }catch (Exception $e){
            throw  $e;
        }

    }
    /**
     * @param Request $request
     * @param User $user
     * @param Money $money
     * @param Order $order
     * @param Business $business
     * @return \think\response\Json
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    //支付 (成功）
    public  function  pay(Request $request ,User $user,Money $money,Order $order,Business $business  ){

        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $uId = $arr['id'];//获取用户id

//        $paypassword =md5(md5($request -> post('paypassword')).$user->selUsertext('id',$uId,'salt'));//获取支付密码
        $paypassword =md5($request -> post('paypassword'));
        $orderId =$request -> post('orderId') ;//获取订单ID

        echo  app('mycache')->ttl( $orderId);

       $arr =  app('mycache')->get( $orderId);
        $arr = \Qiniu\json_decode($arr,true);

        $paymoney  =$arr['money'];//订单价格
        $updataTime = time();
        $businessId  =$arr['businessId'];//商家id
        $businessName = $business->findBusiness($businessId);//获取商家名
        $userbalance = $money->selMoneytext($uId,0)[0]['Money'];//获取用户余额

        $businessbalance = $money->selMoneytext($businessId,1)[0]['Money'];//获取商家余额

        //判断现登入用户是否和支付账号相符
        if(!$arr['uid'] == $uId){
            return responseJson(Base::FAIL,'服务器出错，请重试');
        }
        //判断订单状态是不是待付款
        if($order->selOrder('orderNum',$orderId,'orderStatus')[0]['orderStatus']!= -2){
            return responseJson(Base::FAIL,'获取订单失败，请重试（订单状态不是待付款）');
        }

        //判断支付密码是否正确
        if (!$user->selUsertext('id',$uId,'payPassword') == $paypassword){
            return responseJson(Base::JUMP_PAGE,'支付失败，请重试（密码）');
        }

        //判断余额是否充足
        if($userbalance<$paymoney){
            return responseJson(Base::JUMP_PAGE,'支付失败，请重试（余额）');
        }

        //进行交易
        if(!$money->payMoney($uId,$paymoney,$businessId,$userbalance,$businessbalance  )){
            return responseJson(Base::JUMP_PAGE,'支付失败，请重试（服务器）');
        }
        $order->payOrder($orderId);
        $paytextarr = [$businessName,$paymoney,$updataTime,$orderId];
        return responseJson(Base::JUMP_PAGE,'支付成功',$paytextarr);
    }



    
    //支付状态(成功）

    /**
     * @param Request $request
     * @param Order $order
     * @return \think\response\Json
     */
     public function  payState(Request $request , Order $order){
         $orderId = $request->post('orderId');
         $arr = $order->selOrder('orderNum',$orderId,'orderStatus , realTotalMoney') ;
         if ($arr[0]['orderStatus']=='-2'){
             return responseJson(Base::JUMP_PAGE,'支付失败，请重试');
         }
         return responseJson(Base::JUMP_PAGE,'支付成功',$arr[0]['realTotalMoney']);
     }
}