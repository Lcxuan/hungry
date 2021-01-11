<?php
namespace app\index\controller;

use app\common\Base;
use app\index\model\User;
use app\index\model\Money;
use app\index\validate\MoneyValidator;
use app\index\validate\UserValidator;
use think\Exception;
use think\Request;
use app\common\Upload;


class Update extends Base
{
    /**
     * @param Request $request
     * @param User $user
     * @param UserValidator $userValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //更改用户信息
    //传值username,sex,birthday
    public function updateUserInfo(Request $request,User $user,UserValidator $userValidator){
        try{
            if (!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
                $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
                $arr = json_decode($token,true);
                //显示个人信息
                $username = $user->findUserInfo($arr['id']);

                //更改个人信息
            $data = $user->updateUser($arr['id'],$request->post());

            if ($data != 1){
                return responseJson(Base::FAIL,'更改信息失败');
            }
            return responseJson(Base::OK,'更改信息成功');

        }catch (Exception $e){
            throw $e;
        }

    }

    /**
     * @param Request $request
     * @param User $user
     * @param UserValidator $userValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //更改登录密码
    //传值oldpassword,password,newpassword
    public function updatePasswordInfo(Request $request,User $user,UserValidator $userValidator){
        try{
            if (!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            //$token是你所登录用户返回的请求头(数据库信息)
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //将请求头转为数组
            $arr = json_decode($token,true);
            //查找数据库里的密码
            $password = $user->findPassword($arr['id']);
            //oldpassword为输入旧密码
            $oldpassword = md5($request->post('oldpassword'));
            //判断输入的旧密码与数据库的是否相等
            if ($password != $oldpassword){
                return responseJson(Base::FAIL,'请输入正确的旧密码');
            }
            //newpassword为输入的新密码，repassword为输入的确认密码
            $newpassword = md5($request->post('password'));
            if ($newpassword == $password){
                return responseJson(Base::FAIL,'修改的密码不能与旧密码相同');
            }
            //由于验证器已判断是否相等，所以直接传$data
            $data = $user->updatePassword($arr['id'],$newpassword);

            //判断data
            if ($data != 1){
                return responseJson(Base::FAIL,'更改密码失败');
            }
            return responseJson(Base::OK,'更改密码成功');


        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @param UserValidator $userValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //添加支付密码
    //传值paypassword,repaypassword
    public function addPayPasswordInfo(Request $request,User $user,UserValidator $userValidator){
        try{
            if(!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            //获取返回的请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //将返回的请求头转为数组
            $arr = json_decode($token,true);
            //获取请求回来(输入)的支付密码
            $paypassword = md5($request->post('paypassword'));
            //验证器已判断支付密码和确认支付密码是否相等，so直接传data
            $data = $user->AddUpdatePaypassword($arr['id'],$paypassword);

            //判断data
            if ($data != 1){
                return responseJson(Base::FAIL,'添加支付密码失败');
            }
            return responseJson(Base::OK,'添加支付密码成功');

        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @param UserValidator $userValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //修改支付密码
    //调用发送验证码的接口，然后获取验证码
    //传值phone,paypassword,repaypassword,verifyCode
    public function updatePayPasswordInfo(Request $request,User $user,UserValidator $userValidator){
        try{
            if (!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            //获取返回的请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //请求头转数组
            $arr = json_decode($token,true);
            //获取数据库里的phone
            $dphone = $user->findPhone($arr['id']);
            //获取输入的手机号
            $phone = $request->post('phone');
            //判断输入的手机号和数据库里的phone是否相同
            if ($dphone != $phone){
                return responseJson(Base::FAIL,'请输入正确的手机号');
            }
            //获取用户输入的验证码
            $verifyCode = $request->post('verifyCode');
            //获取发送短信后redis里的键
            $phoneKey = 'sms'.md5($phone);
            //获取redis键的值
            $code = app('mycache')->get($phoneKey);
            //判断
            if($code != $verifyCode){
                return responseJson(Base::FAIL,'验证码输入错误');
            }
            //获取输入的新密码
            $paypassword = md5($request->post('paypassword'));
            $oldpaypassword = $user->findPaypassword($arr['id']);
            //判断新密码和旧密码是否相同
            if ($paypassword == $oldpaypassword){
                return responseJson(Base::FAIL,'修改的密码不能与旧密码相同');
            }
            //由于paypassword(新支付密码)和repaypassword(确认新支付密码)在验证器已经判断是否相等，所以直接传data
            $data = $user->addUpdatePaypassword($arr['id'],$paypassword);

            //判断data
            if ($data != 1){
                return responseJson(Base::FAIL,'修改支付密码失败');
            }
            return responseJson(Base::OK,'修改支付密码成功');

        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @param Money $money
     * @param User $user
     * @param MoneyValidator $moneyValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //充值
    //传值money,paypassword
    public function addMoneyInfo(Request $request,Money $money,User $user,MoneyValidator $moneyValidator){
        try{
            if(!$moneyValidator->check($request->post())){
                return responseJson(Base::FAIL,$moneyValidator->getError());
            }
            //获取请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //将请求头转为数组
            $arr = json_decode($token,true);
            //查询数据库的用户余额
            $balance = $money->findUserMoney($arr['id'],$request->post());   //余额
            //判断是否有数据(如果没有数据，说明第一次充值，余额为0)
//            for ($i=0;$i<=count($balance);$i++){
                if ($balance->isEmpty()){
                    $balance = 0;
                }
//            }
            //获取输入的充值数额
            $addmoney = $request->post('money');
            //判断是支付宝支付的还是微信支付   MicroMessenger为微信   AlipayClient为支付宝
            $paytype = $request->post('paytype');
            if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
                $request->post('paytype','0',true);
            }else{
                $request->post('paytype','1',true);
            }
            //获取输入的支付密码
            $paypassword = md5($request->post('paypassword'));
            //获取数据库支付密码
            $dpaypassword = $user->findPaypassword($arr['id']);
            //判断
            if ($dpaypassword != $paypassword){
                return responseJson(Base::FAIL,'请输入正确的支付密码');
            }
            //判断完密码直接传data
            $data = $money->userAddMoney($arr['id'],$paytype,$request->post());

            //判断data
            if ($data != 1){
                return responseJson(Base::FAIL,'充值失败');
            }
            return responseJson(Base::OK,'充值成功');

        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @param UserValidator $userValidator
     * @return \think\response\Json
     * @throws Exception
     */
    //更改用户头像
    //传值file
    public function updateHeaderInfo(Request $request,User $user,UserValidator $userValidator){
        try{
            if(!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            //$file为前端input上传的图片的信息
            $file = $request->file('header')->getInfo();
            $upload = new Upload();
            //上传的文件的本地路径
            $filePath = $file['tmp_name'];
            //$key上传后的文件名
            $key = time() . '.' . $file['name'];
            //将文件本地路径和上传文件后的名字传到upload的方法里
            $upload_qiniu = $upload->upload($filePath,$key);
            //将七牛的域名与文件名拼接
            $domain_qiniu = 'http:/n/hungry.wistudy.xyz/' . $upload_qiniu['key'];
            //获取请求头
            $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
            //将请求头转为数组
            $arr = json_decode($token,true);
            //将最后的文件名传入数据库
            $data = $user->updateHeader($arr['id'],$domain_qiniu);

            return responseJson(Base::OK,'上传头像成功');

        }catch (Exception $e){
            throw $e;
        }
    }

}