<?php


namespace app\index\controller;


use app\common\Base;
use app\common\SysMessage;
use app\index\model\User;
use app\index\validate\UserValidator;
use think\captcha\Captcha;
use think\Exception;
use think\Request;

class UserLogin extends Base
{
    /**
     * @register 注册
     * @param Request $request
     * @param Userlogin $user
     * @param UserValidator $userValidator
     * @return \think\response\Json
     * @throws Exception
     */
    public function register(Request $request,User $user,UserValidator $userValidator){
        try {
            if (!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            $username = $request->post('username');
            $password = md5($request->post('password'));
            $phone = $request->post('phone');
            $email = $request->post('email');
            if(!empty($data = $user->findPhoneEmail($email,$phone))){
                return responseJson(Base::FAIL,'该用户已存在');
            }
            $salt = md5(time().$username);
            if (!$user->register($username,$password,$phone,$email,$salt)){
                return responseJson(Base::FAIL,'注册失败');
            }
            return responseJson(Base::JUMP_PAGE,'注册成功');
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @findpass 找回密码
     * @param Request $request
     * @param UserValidator $userValidator
     * @param Userlogin $user
     * @return \think\response\Json
     * @throws Exception
     */
    public function findpass(Request $request,UserValidator $userValidator,User $user){
        try {
            if (!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            $phone = $request->post('phone');
            $email = $request->post('email');
            $password = md5($request->post('password'));

            if (empty($user->findPassPhoneEmail($email,$phone))){
                return responseJson(Base::FAIL,'该用户不存在，请先注册');
            }

            if (!empty($user->findSamePass($phone,$password))){
                return responseJson(Base::FAIL,'请输入正确的密码');
            }

            if(!$user->retrievePassword($phone,$password)){
                return responseJson(Base::FAIL,'修改失败,请重试');
            }
            return responseJson(Base::JUMP_PAGE,'修改成功，跳转页面');
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @login    邮箱登录
     * @param Request $request
     * @param UserValidator $userValidator
     * @param Userlogin $user
     * @return \think\response\Json
     * @throws Exception
     */
    public function login(Request $request,UserValidator $userValidator,User $user){
        try {
            if (!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            $email = $request->post('email');
            $password = md5($request->post('password'));

            if (empty($data = $user->login($email,$password))){
                return responseJson(Base::JUMP_PAGE,'暂无此用户，请先注册！！！');
            }

            $token = $this->makeToken($data['id'],$data['phone']);
            switch ($data['sex']){
                case '0':
                    $data['sex'] = '男';
                    break;
                case '1':
                    $data['sex'] = '女';
                    break;
            }

            $arr = [
                'id'        =>  $data['id'],
                'username'  =>  $data['username'],
                'sex'       =>  $data['sex'],
                'email'     =>  $data['email'],
                'phone'     =>  $data['phone'],
                'header'    =>  $data['header'],
                'type'      =>  'Buyer'
            ];
            app('mycache')->set($token,json_encode($arr),86400);

            if (!$user->loginlastTime($email,$data['phone'])){
                return responseJson(Base::FAIL,'修改失败');
            }
            return  responseJson(Base::JUMP_PAGE,'登录成功', ['token'=>$token]);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @smsLogin    短信登录
     * @param Request $request
     * @param UserValidator $userValidator
     * @param Userlogin $user
     * @return \think\response\Json
     * @throws Exception
     */
    public function smsLogin(Request $request,UserValidator $userValidator,User $user){
        try {
            if (!$userValidator->check($request->post())){
                return responseJson(Base::FAIL,$userValidator->getError());
            }

            $phone = $request->post('phone');
            $verifyCode = $request->post('verifyCode');
            $phoneKey = 'sms'.md5($phone);
            $code = app('mycache')->get($phoneKey);
            if($code != $verifyCode){
                return responseJson(Base::FAIL,'登录失败，请重试');
            }

            if (empty($data = $user->smsLogin($phone))){
                return responseJson(Base::JUMP_PAGE,'暂无此用户，请先注册！！！');
            }

            $token = $this->makeToken($data['id'],$phone);
            switch ($data['sex']){
                case '0':
                    $data['sex'] = '男';
                    break;
                case '1':
                    $data['sex'] = '女';
                    break;
            }

            $arr = [
                'id'        =>  $data['id'],
                'username'  =>  $data['username'],
                'sex'       =>  $data['sex'],
                'email'     =>  $data['email'],
                'phone'     =>  $data['phone'],
                'header'    =>  $data['header'],
                'type'      =>  'Buyer'
            ];
            app('mycache')->set($token,json_encode($arr),86400);

            if (!$user->loginlastTime($data['email'],$phone)){
                return responseJson(Base::FAIL,'修改失败',$data);
            }
            return  responseJson(Base::JUMP_PAGE,'登录成功', ['token'=>$token]);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @sendSms 发送短信
     * @param Request $request
     * @param UserValidator $userValidator
     * @param SysMessage $sysMessage
     * @return \think\response\Json
     * @throws Exception
     */
    public function sendSms(Request $request,UserValidator $userValidator,SysMessage $sysMessage){
        try {
            if (!$userValidator->check($request->post('phone'))){
                return responseJson(Base::FAIL,$userValidator->getError());
            }
            $phone = $request->post('phone');
            $code = rand(100000,999999);
            $phoneKey = 'sms'.md5($phone);
            app('mycache')->set($phoneKey,$code,180);
            if (!is_null($data = $sysMessage->useSys($phone,$code))) {
                return responseJson(Base::FAIL,"短信发送失败",$data);
            }
            return responseJson(Base::OK,"短信发送成功");
        }catch (Exception $e){
            throw $e;
        }

    }

    /**
     * @makeToken   生成token
     * @param $userId
     * @param $phone
     * @return string
     */
    public function makeToken($userId,$phone){
        $str = md5($userId.time().$phone);
        return $str;
    }

    /**
     * @verify  验证码图片
     * @return \think\Response
     */
    public function verify(){
        $config = [
            'fontSize'  =>  14,
            'length'    =>  4,
            'useNoise'  =>  true,
            'imageH'    =>  35,
            'imageW'    =>  90,
            'expire'    =>  1800
        ];
        $captcha = new  Captcha($config);
        return $captcha->entry();
    }
}