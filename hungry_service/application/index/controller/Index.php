<?php
namespace app\index\controller;

use app\common\Base;
use app\index\model\Business;
use app\index\model\Commodity;
use app\index\model\CommodityAppraises;
use app\index\model\Coupon;
use app\index\model\Order;
use think\Exception;
use think\Request;

class Index extends Base
{
    /**
     * @randBusiness    随机查找商家
     * @param $str
     * @param $num
     * @return array|\think\response\Json
     */
    public function randBusiness($str,$num){
        $business = new Business();
        if (!$data = $business->randBusiness($str)) {
            return responseJson(Base::FAIL,'数据获取失败,请重试');
        }
        $arr = array_rand($data,$num);
        shuffle($arr);
        $businessData = [];
        foreach ($arr as $value){
            array_push($businessData,$data[$value]);
        }
        return $businessData;
    }

    /**
     * @youLike 首页-猜你喜欢显示
     * @return \think\response\Json
     * @throws Exception
     */
    public function youLike(Business $business,Commodity $commodity){
        try {
            $businessData = $business->youLikeBusiness();
            $newBusinessData = [];
            foreach ($businessData as $key => $value){
                $count = $commodity->youLikeCount($businessData[$key]['id']);
                if ($count > 3){
                    array_push($newBusinessData,$value);
                }
            }
            shuffle($newBusinessData);
            $arr = array_rand($newBusinessData,1);
            $youBusiness = $newBusinessData[$arr];

            $commodityData = $commodity->youLikeCommodity($youBusiness['id']);
            shuffle($commodityData);
            $commodityArr = array_rand($commodityData,3);
            $youCommodity = [];
            foreach ($commodityArr as $key => $value){
                array_push($youCommodity,$commodityData[$value]);
                $youCommodity[$key]['id'] = $youBusiness['id'];
                $youCommodity[$key]['businessName'] = $youBusiness['businessName'];
            }
            return responseJson(Base::OK,'数据获取成功',$youCommodity);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @rankingList 首页-排行榜显示
     * @param Business $business
     * @return \think\response\Json
     */
    public function rankingList(Business $business){
        try {
            if (empty($data = $business->rankingList())) {
                return responseJson(Base::FAIL,'获取失败，请重试');
            }
            return responseJson(Base::OK,'数据获取成功',$data);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @getCoupons  领劵中心
     * @param Coupon $coupon
     * @return \think\response\Json
     */
    public function getCoupons(Coupon $coupon){
        if (!empty($data = $coupon->findCoupon())) {
            return responseJson(Base::FAIL,'数据获取失败,请重试');
        }
        $arr = array_rand($data,3);
        shuffle($arr);
        $couponData = [];
        foreach ($arr as $value){
            array_push($couponData,$data[$value]);
        }
        return responseJson(Base::OK,'数据获取成功',$couponData);
    }

    /**
     * @setMeal 首页-今日套餐
     * @param Commodity $commodity
     * @param Business $business
     * @return \think\response\Json
     * @throws Exception
     */
    public function setMeal(Request $request,Commodity $commodity,Business $business){
        try {
            $position = $request->post('position');
            $nearBusiness = $business->setMalSel($position);
            foreach ($nearBusiness as $key => $val){
                $commodityCount = $commodity->youLikeCount($nearBusiness[$key]['id']);
                if ($commodityCount < 1){
                    unset($nearBusiness[$key]);
                }
            }
            count($nearBusiness) < 4 ? $arr = array_rand($nearBusiness,count($nearBusiness)) : $arr = array_rand($nearBusiness,4);
            shuffle($arr);
            $businessData = [];
            foreach ($arr as $value){
                array_push($businessData,$nearBusiness[$value]);
            }
            foreach ($businessData as $key => $value){
                $allCommodity = $commodity->businessCommodity($businessData[$key]['id']);
                shuffle($allCommodity);
                $commodityData = array_rand($allCommodity,1);
                $businessData[$key]['commodity'] = $allCommodity[$commodityData];
            }
            return responseJson(Base::OK,'',$businessData);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @found   首页-发现好店和附近店铺
     * @param Business $business
     * @param CommodityAppraises $commodityAppraises
     * @param Order $order
     * @return \think\response\Json
     * @throws Exception
     */
    public function foundBusiness(Business $business,CommodityAppraises $commodityAppraises,Order $order){
        try {
            $businessData = self::randBusiness('id,businessName,businessImg,businessRange',4);
            foreach ($businessData as $i => $val){
                //五星评价
                $businessData[$i]['Evaluation'] = ceil($commodityAppraises->avgEvaluation($businessData[$i]['id'])/3);
                //人均
                $businessData[$i]['perCapita'] = $order->avgBusiness($businessData[$i]['id']);
            }
            return responseJson(Base::OK,'数据获取成功',$businessData);
        }catch (Exception $e){
            throw $e;
        }
    }
}
