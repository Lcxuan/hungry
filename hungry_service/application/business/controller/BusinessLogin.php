<?php


namespace app\business\controller;


use app\business\model\Business;
use app\common\Base;
use app\business\validate\BusinessValidator;
use think\Exception;
use think\Request;

class BusinessLogin extends Base
{
    public function login(Request $request,BusinessValidator $businessValidator,Business $business){
        try {
            if (!$businessValidator->check($request->post())){
                responseJson(Base::FAIL,$businessValidator->getError());
            }
            $businessName = $request->post('businessName');
            $businessPassword = md5($request->post('businessPassword'));
            if(empty($data = $business->login($businessName,$businessPassword))){
               return responseJson(Base::NOT_LOGGED_IN,'请输入正确的商家');
            }
            $token = $this->createToken($data['id'],$businessName);
            $arr = [
                'id'            =>  $data['id'],
                'type'          =>  'seller'
            ];
            app('mycache')->set($token,json_encode($arr),86400);
            return responseJson(Base::OK,'商家登录成功',[
                'token'     =>  $token
            ]);

        }catch (Exception $e){
            throw $e;
        }
    }
    public function createToken($id,$businessName){
        $str = md5($id.time().$businessName);
        return $str;
    }
}