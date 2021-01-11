<?php


namespace app\business\controller;


use app\business\model\Money;
use app\business\model\Region;
use app\business\validate\BusinessValidator;
use app\common\Base;
use app\common\Upload;
use think\Exception;
use think\Request;
use app\business\model\Business;
class BusinessInfo extends Base
{
    /**
     * @shopInfoShow 显示商家详细信息
     * @param Request $request
     * @param Business $business
     * @return \think\response\Json
     */
    public function shopInfoShow(Business $business){
        $data = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
        if (empty($result = $business->showInfo($data['id']))) {
            return responseJson(Base::FAIL,'数据获取不到，请刷新页面');
        }
        $arr = [
            'businessName'      =>  $result['businessName'],        //商家名
            'phone'             =>  $result['phone'],               //手机号码
            'startTime'         =>  $result['startTime'],           //开始营业时间
            'endTime'           =>  $result['endTime'],             //结束营业时间
            'provide'           =>  $result['provide'],             //省、直辖市
            'city'              =>  $result['city'],                //市
            'county'            =>  $result['county'],              //区
            'street'            =>  $result['street'],              //街道
            'businessAddress'   =>  $result['businessAddress'],     //详细地址
            'businessDesc'      =>  $result['businessDesc'],        //简介
            'businessImg'       =>  $result['businessImg'],         //店铺主照片
            'deliverMoney'      =>  $result['deliverMoney'],        //配送费
            'startDeliver'      =>  $result['startDeliver'],        //起送价格
        ];
        return responseJson(Base::OK,'数据获取成功',$arr);
    }

    /**
     * @updateBusiness  修改商家信息
     * @param Request $request
     * @param BusinessValidator $businessValidator
     * @param Business $business
     * @return \think\response\Json
     * @throws Exception
     */
    public function updateBusiness(Request $request,BusinessValidator $businessValidator,Business $business){
        try {
            if (!$businessValidator->check($request->post())){
                return responseJson(Base::FAIL,$businessValidator->getError());
            }

            //上传七牛
            $upload = new Upload();
            $businessImg = $request->file('businessImg')->getInfo();
            $uploadBusinessImg = $upload->upload($businessImg['tmp_name'],time().$businessImg['name']);    //上传商家主照片

            $info = [
                'startTime'         =>  $request->post('startTime'),            //开始营业时间
                'endTime'           =>  $request->post('endTime'),              //结束营业时间
                'businessDesc'      =>  $request->post('businessDesc'),         //商家简介
                'businessImg'       =>  'http://hungry.wistudy.xyz/'.$uploadBusinessImg['key'],          //店铺主照片
                'provide'           =>  $request->post('provide'),              //省、直辖市
                'city'              =>  $request->post('city'),                 //市
                'county'            =>  $request->post('county'),               //区
                'street'            =>  $request->post('street'),               //街道
                'businessAddress'   =>  $request->post('businessAddress'),      //详细地址
                'deliverMoney'      =>  $request->post('deliverMoney'),         //配送费
                'startDeliver'      =>  $request->post('startDeliver'),         //起送价格
            ];

            $data = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            if (!$business->updateBusiness($data['id'],$info)) {
                return responseJson(Base::FAIL,'修改失败，请重试');
            }
            return responseJson(Base::OK,'数据修改成功',$info);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @showHeadImg 显示商家头像
     * @param Business $business
     * @return \think\response\Json
     */
    public function showHeadImg(Business $business,Money $money){
        $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
        if (empty($data = $business->showHeadImg($arr['id']))){
            return responseJson(Base::FAIL,'数据获取失败，请重试');
        }
        $businessMoney = $money->findBusinessMoney($arr['id']);
        if ($businessMoney->isEmpty()){
            return responseJson(Base::OK,'暂无余额，余额为0');
        }
        $data['businessMoney'] = $businessMoney['Money'];
        return responseJson(Base::OK,'数据获取成功',$data);
    }

    /**
     * @updateHeadImg   修改商家头像
     * @param Request $request
     * @param BusinessValidator $businessValidator
     * @param Business $business
     * @return \think\response\Json
     * @throws Exception
     */
    public function updateHeadImg(Request $request,BusinessValidator $businessValidator,Business $business){
        try {
            if (!$businessValidator->check($request->post())){
                return responseJson(Base::FAIL,$businessValidator->getError());
            }
            $arr = json_decode(app('mycache')->get($_SERVER['HTTP_TOKEN']),true);
            $headImg = $request->file('headImg')->getInfo();
            $upload = new Upload();
            $uploadHeadImg = $upload->upload($headImg['tmp_name'],time().$headImg['name']);        //上传头像
            $info = [
                'headImg'           =>  'http://hungry.wistudy.xyz/'.$uploadHeadImg['key']              //商家头像
            ];
            if (!$business->updateBusiness($arr['id'],$info)){
                return responseJson(Base::FAIL,'数据修改失败');
            }
            return responseJson(Base::FAIL,'数据修改成功',$info);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @province    省
     * @param Region $region
     * @return \think\response\Json
     * @throws Exception
     */
    public function province(Region $region){
        try {
            $sheng = $region->sheng();
            if ($sheng->isEmpty()){
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            return responseJson(Base::OK,'数据获取成功',$sheng);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @city
     * @param Request $request
     * @param Region $region
     * @return \think\response\Json
     * @throws Exception
     */
    public function city(Request $request,Region $region){
        try {
            $shengName = $request->post('sheng');
            $sheng = $region->shengData($shengName);
            if ($sheng->isEmpty()){
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            $cri_code = $sheng['cri_code'];
            $shi = $region->shi($cri_code);
            if ($shi->isEmpty()){
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            return responseJson(Base::OK,'数据获取成功',$shi);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @area    区
     * @param Request $request
     * @param Region $region
     * @return \think\response\Json
     * @throws Exception
     */
    public function area(Request $request,Region $region){
        try {
            $shiName = $request->post('shi');
            $shi = $region->shiData($shiName);
            if ($shi->isEmpty()) {
                return responseJson(Base::FAIL, '数据获取失败，请重试');
            }
            $cri_code = $shi['cri_code'];
            $qu = $region->qu($cri_code);
            if ($qu->isEmpty()){
                return responseJson(Base::FAIL,'数据获取失败，请重试');
            }
            return responseJson(Base::OK,'数据获取成功',$qu);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @street  街道
     * @param Request $request
     * @param Region $region
     * @return \think\response\Json
     * @throws Exception
     */
    public function street(Request $request,Region $region){
        try {
            $quName = $request->post('qu');
            $qu = $region->quData($quName);
            if ($qu->isEmpty()) {
                return responseJson(Base::FAIL, '数据获取失败，请重试');
            }
            $cri_code = $qu['cri_code'];
            $jie = $region->jie($cri_code);
            if ($jie->isEmpty()){
                return responseJson(Base::OK,'暂无该街道');
            }
            return responseJson(Base::OK,'数据获取成功',$jie);
        }catch (Exception $e){
            throw $e;
        }
    }
}