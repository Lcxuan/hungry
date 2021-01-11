<?php


namespace app\index\controller;

use app\common\Base;
use app\index\model\Business;
use app\index\model\Carts;
use app\index\model\Commodity;
use app\index\model\CommodityAppraises;
use app\index\model\CommodityClass;
use app\index\model\Favorites;
use app\index\model\History;
use app\index\model\Order;
use app\index\model\OrderCommodity;
use app\index\model\User;
use think\Exception;
use think\Request;

/**
 * Class MerchantsPage 商家页
 * @package app\index\controller
 */
class MerchantsPage extends Base
{
    /**
     * @qualityComment  商家页评论-按质量排序
     * @param Request $request
     * @param CommodityAppraises $commodityAppraises
     * @param User $user
     * @param Business $business
     * @return \think\response\Json
     */
    public function qualityComment(Request $request,CommodityAppraises $commodityAppraises,User $user,Business $business){
        $businessId = $request->post('businessId');
        $commentData = $commodityAppraises->qualityComment($businessId);
        if ($commentData->isEmpty()){
            return responseJson(Base::OK,'暂无评论');
        }
        foreach ($commentData as $key => $val){
            $commentData[$key]['Evaluation'] = $commentData[$key]['commodityScore'] + $commentData[$key]['serviceScore'] + $commentData[$key]['timeScore'];
            unset($commentData[$key]['commodityScore'],$commentData[$key]['serviceScore'],$commentData[$key]['timeScore']);
            if (empty($userData = $user->commentUsernameHeader($commentData[$key]['userId']))) {
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            $commentData[$key]['username'] = $userData['username'];
            $commentData[$key]['header'] = $userData['header'];
            $commentData[$key]['createTime'] = self::commentTime($commentData[$key]['createTime']);
            if (!$commentData[$key]['businessName'] = $business->findBusiness($commentData[$key]['businessId'])) {
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            $commentData[$key]['replyTime'] = self::commentTime($commentData[$key]['replyTime']);
        }

        for ($i=0;$i<count($commentData);$i++){
            for ($j=$i;$j<count($commentData)-1;$j++){
                if ($commentData[$i]['Evaluation'] < $commentData[$j+1]['Evaluation']){
                    $str = $commentData[$i]['Evaluation'];
                    $commentData[$i]['Evaluation'] = $commentData[$j+1]['Evaluation'];
                    $commentData[$j+1]['Evaluation'] = $str;
                }
            }
        }
        return responseJson(Base::OK,'数据获取成功',$commentData);
    }
    /**
     * @comment 商家页评论-按时间排序
     * @param Request $request
     * @param CommodityAppraises $commodityAppraises
     * @param User $user
     * @param Business $business
     * @return \think\response\Json
     * @throws Exception
     */
    public function comment(Request $request,CommodityAppraises $commodityAppraises,User $user,Business $business){
        try {
            $businessId = $request->post('businessId');
            $commentData = $commodityAppraises->businessComment($businessId);
            if ($commentData->isEmpty()){
                return responseJson(Base::OK,'暂无评论');
            }
            foreach ($commentData as $key => $val){
                if (empty($userData = $user->commentUsernameHeader($commentData[$key]['userId']))) {
                    return responseJson(Base::FAIL,'数据获取失败，请重试');
                }
                $commentData[$key]['username'] = $userData['username'];
                $commentData[$key]['header'] = $userData['header'];
                $commentData[$key]['createTime'] = self::commentTime($commentData[$key]['createTime']);
                if (!$commentData[$key]['businessName'] = $business->findBusiness($commentData[$key]['businessId'])) {
                    return responseJson(Base::FAIL,'数据获取失败，请重试');
                }
                $commentData[$key]['replyTime'] = self::commentTime($commentData[$key]['replyTime']);
            }
            return responseJson(Base::OK,'数据获取成功',$commentData);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @commentTime 评论时间
     * @param $time
     * @return string
     */
    public function commentTime($time){
        $remaining = time() - $time;
        if ($remaining > 31104000){
            return floor($remaining / 31104000).'年前';
        }
        if ($remaining > 2592000){
            return floor($remaining / 2592000).'月前';
        }
        if ($remaining >= 86400){
            return floor($remaining / 86400).'天前';
        }
        if ($remaining >= 3600){
            return floor($remaining / 3600).'时前';
        }
        if ($remaining >= 60){
            return floor($remaining / 60).'分前';
        }
        return $remaining.'秒前';
    }

    /**
     * @businessInfo    商家页信息
     * @param Request $request
     * @param Business $business
     * @param CommodityAppraises $commodityAppraises
     * @return \think\response\Json
     * @throws Exception
     */
    public function businessInfo(Request $request,Business $business,CommodityAppraises $commodityAppraises){
        try {
            if (empty($data = $business->BusinessInfo($request->post('id')))) {
                return responseJson(Base::FAIL,'数据获取失败');
            }
            //五星评价
            $data['Evaluation'] = ceil($commodityAppraises->avgEvaluation($request->post('id'))/3);

            $address = $data['provide'].$data['city'].$data['county'].$data['street'].$data['businessAddress'];
            $data['address'] = $address;
            unset($data['provide'],$data['city'],$data['county'],$data['street'],$data['businessAddress']);
            return responseJson(Base::OK,'数据获取成功',$data);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @youLike 商家页-猜你喜欢
     * @param Business $business
     * @param Index $index
     * @param Order $order
     * @return \think\response\Json
     * @throws Exception
     */
    public function youLike(Business $business,Index $index,Order $order){
        try {
            if (!$businessData = $index->randBusiness('id,businessName,businessImg,startDeliver',6)) {
                return responseJson(Base::FAIL,'数据获取失败');
            }
            //使用商家id计算月销
            foreach ($businessData as $key => $value){
                $OnThePin = $order->countOrder($businessData[$key]['id']);
                $businessData[$key]['OnThePin'] = $OnThePin;
            }
            return responseJson(Base::OK,'数据获取成功',$businessData);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @hotPin  商家页-热销
     * @param Request $request
     * @param Commodity $commodity
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     */
    public function hotPin(Request $request,Commodity $commodity,OrderCommodity $orderCommodity){
        try {
            $data = $commodity->commodityAll($request->post('id'),'id,commodityName,commodityImg,presentPrice');
            if ($data->isEmpty()){
                return responseJson(Base::OK,'暂无商品');
            }
            //使用商品id计算月销
            foreach ($data as $key => $value){
                $OnThePin = $orderCommodity->findCommodity($data[$key]['id']);
                $data[$key]['OnThePin'] = $OnThePin;
            }
            for ($i=0;$i<count($data);$i++){
                for ($j=$i;$j<count($data)-1;$j++){
                    if ($data[$i]['OnThePin'] < $data[$j+1]['OnThePin']){
                        $str = $data[$i];
                        $data[$i] = $data[$j+1];
                        $data[$j+1] = $str;
                    }
                }
            }
            $commodityData = [];
            foreach ($data as $key => $val){
                if ($data[$key]['OnThePin'] > 30){
                    $commodityData[] = $data[$key];
                }
            }
            return responseJson(Base::OK,'数据获取成功',$commodityData);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @discount    商家页-折扣
     * @param Request $request
     * @param Commodity $commodity
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     */
    public function discount(Request $request,Commodity $commodity,OrderCommodity $orderCommodity){
        try {
            if (empty($data = $commodity->commodityAll($request->post('businessId'),'id,commodityName,commodityImg,presentPrice'))) {
                return responseJson(Base::FAIL,'数据获取失败');
            }
            //使用商品id计算月销
            foreach ($data as $key => $value){
                $OnThePin = $orderCommodity->findCommodity($data[$key]['id']);
                $data[$key]['OnThePin'] = $OnThePin;
            }
            return responseJson(Base::OK,'数据获取成功',$data);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @commodityClassification 商品分类
     * @param Request $request
     * @param CommodityClass $commodityClass
     * @param Commodity $commodity
     * @param OrderCommodity $orderCommodity
     * @return \think\response\Json
     */
    public function commodityClassification(Request $request,CommodityClass $commodityClass,Commodity $commodity,OrderCommodity $orderCommodity){
        try {
            if (!empty($commodityClassData = $commodityClass->classificationName($request->post('businessId')))) {
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            foreach ($commodityClassData as $key => $value){
                if (empty($commodityData = $commodity->useCommodityCIdFind($commodityClassData[$key]['id']))) {
                    return responseJson(Base::FAIL,'数据获取失败，请重试');
                }
                foreach ($commodityData as $k => $val){
                    $OnThePin = $orderCommodity->findCommodity($commodityData[$k]['id']);
                    $commodityData[$k]['OnThePin'] = $OnThePin;
                }
                $commodityClassData[$key]['commodity'] = $commodityData;
            }
            return responseJson(Base::OK,'数据获取成功',$commodityClassData);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @showCarts   显示商家页商品的订单
     * @param Request $request
     * @param Carts $carts
     * @return \think\response\Json
     */
    public function showCarts(Request $request,Business $business,Carts $carts){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $businessId = $request->post('businessId');
            if (!$businessName = $business->findBusiness($businessId)) {
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            $data = $carts->showBusinessPageCarts($arr['id'],$businessName);
            if ($data->isEmpty()) {
                return responseJson(Base::OK,'没有购物车数据');
            }
            return responseJson(Base::OK,'数据获取成功',$data);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @shoppingCart    商家页购物车
     * @param Request $request
     * @param Business $business
     * @param Carts $carts
     * @return \think\response\Json
     * @throws Exception
     */
    public function shoppingCart(Request $request,Business $business,Carts $carts){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $commodityId = $request->post('commodityId');
            $commodityNum = $request->post('commodityNumber');
            $businessId = $request->post('businessId');
            $businessName = $business->findBusiness($businessId);
            $userId = $arr['id'];
            if (!empty($carts->findCart($userId,$commodityId,$businessName))){
                if (!$carts->updateCart($userId,$commodityId,$businessName,$commodityNum)){
                    return responseJson(Base::FAIL,'修改失败');
                }
                return responseJson(Base::OK,'数据更新成功');
            }
            if (!$carts->cartInsert($userId,$commodityId,$businessName,$commodityNum)){
                return responseJson(Base::FAIL,'数据刷新失败，请重试');
            }
            return responseJson(Base::OK,'数据插入成功');
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @emptyCarts  商家页清空购物车
     * @param Request $request
     * @param Business $business
     * @param Carts $carts
     * @return \think\response\Json
     */
    public function emptyCarts(Request $request,Business $business,Carts $carts){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $businessId = $request->post('businessId');
            $businessName = $business->findBusiness($businessId);
            if (!$data = $carts->delBusinessPageCats($arr['id'],$businessName)) {
                return responseJson(Base::FAIL,'数据删除失败');
            }
            return responseJson(Base::OK,'数据删除成功',$data);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @singleDelete    商家页购物车单个删除
     * @param Request $request
     * @param Business $business
     * @param Carts $carts
     * @return \think\response\Json
     */
    public function singleDelete(Request $request,Business $business,Carts $carts){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $commodityId = $request->post('commodityId');
            $businessId = $request->post('businessId');
            $businessName = $business->findBusiness($businessId);
            if (!$data = $carts->delSingleDelete($arr['id'],$commodityId,$businessName)){
                return responseJson(Base::FAIL,'数据删除失败');
            }
            return responseJson(Base::OK,'数据删除成功',$data);
        }catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * @historyBusiness     商家页历史商家
     * @param Request $request
     * @param History $history
     * @return \think\response\Json
     */
    public function historyBusiness(Request $request,History $history){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $businessId = $request->post('businessId');
            if (!empty($history->findHistory($arr['id'],$businessId))){
                if (!$history->updateTime($arr['id'],$businessId)) {
                    return responseJson(Base::OK,'系统错误，请重试');
                }
                return responseJson(Base::OK,'数据修改成功');
            }
            if ($history->createHistory($arr['id'],$businessId)) {
                return responseJson(Base::OK,'系统错误，请重试');
            }
            return responseJson(Base::OK,'数据创建成功');
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @favoritesBusinessShow   商家页收藏店铺显示
     * @param Request $request
     * @param Favorites $favorites
     * @return \think\response\Json
     * @throws Exception
     */
    public function favoritesBusinessShow(Request $request,Favorites $favorites){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $businessId = $request->post('businessId');
            if (empty($favorites->showFavorites($arr['id'],$businessId))){
                return responseJson(Base::OK,'商家未收藏');
            }
            return responseJson(Base::OK,'商家已收藏');
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @favoritesBusiness   商家页收藏商家
     * @param Request $request
     * @param Favorites $favorites
     * @return \think\response\Json
     */
    public function favoritesBusiness(Request $request,Favorites $favorites){
        try {
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $businessId = $request->post('businessId');
            if (!empty($favorites->findFavorites($arr['id'],$businessId))){
                if (!$favorites->delFavorites($arr['id'],$businessId)) {
                    return responseJson(Base::OK,'系统错误，请重试');
                }
                return responseJson(Base::OK,'取消收藏');
            }
            if (!$favorites->insertFavorites($arr['id'],$businessId)) {
                return responseJson(Base::OK,'系统错误，请重试');
            }
            return responseJson(Base::OK,'收藏成功');
        }catch (Exception $e){
            throw $e;
        }
    }
}