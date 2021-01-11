<?php
namespace app\index\model;

use think\Model;
use think\Db;

//用户表
class User extends Model
{
    /**
     * @param int $id
     * @return mixed
     */
    //查出个人信息(username,sex,birthday,email,phone)
    public function findUserInfo(int $id){
        return $this->field('username,sex,birthday,email,phone')->where('id',$id)->select();
    }

    /**
     * @commentUsernameHeader   评论的用户名和头像
     * @param int $userId
     * @return User
     */
    public function commentUsernameHeader(int $userId){
        return $this->field('username,header')->where('id',$userId)->find();
    }
    /**
     * @param $id
     * @param $request
     * @return mixed
     */
    public function updateUser(int $id,array $request){
            $data = [
            'username'  =>  $request['username'],
            'sex'       =>  $request['sex'],
            'birthday'  =>  strtotime($request['birthday']),
            'updateTime'=>  time()
        ];
        return $this->where('id',$id)->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findPassword(int $id){
        return $this->where('id',$id)->value('password');
    }

    /**
     * @param $id
     * @param $request
     * @return mixed
     */
    public function updatePassword(int $id,string $password){
        $data = [
            'password'      =>  $password,
            'updateTime'    =>  time()
        ];
        return $this->where('id',$id)->update($data);
    }

    /**
     * @param $id
     * @param $paypassword
     * @return mixed
     */
    public function addUpdatePaypassword(int $id,string $paypassword){
        $data = [
            'payPassword'   =>  $paypassword,
            'updateTime'    =>  time()
        ];
        return $this->where('id',$id)->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findPhone(int $id){
        return $this->where('id',$id)->value('phone');
    }


    public function findPaypassword(int $id){
        return $this->where('id',$id)->value('payPassword');
    }

    /**
     * @param $id
     * @param $header
     * @return mixed
     */
    public function updateHeader(int $id,string $header){
        $data = [
            'header'    =>  $header,
            'updateTime'=>  time()
        ];
        return $this->where('id',$id)->update($data);
    }

    /**
     * @param string $email
     * @param int $phone
     * @return User
     */
    public function findPhoneEmail(string $email,int $phone){
        $data = [
            'email'     =>  $email,
            'phone'     =>  $phone
        ];
        return $this->whereOr($data)->find();
    }

    /**
     * @findPassPhoneEmail  查找密码的手机号码和邮箱
     * @param string $email
     * @param float $phone
     * @return User
     */
    public function findPassPhoneEmail(string $email,float $phone){
        $data = [
            'email'     =>  $email,
            'phone'     =>  $phone
        ];
        return $this->where($data)->find();
    }

    /**
     * @findSamePass    查找相同的密码
     * @param float $phone
     * @param string $password
     * @return User
     */
    public function findSamePass(float $phone,string $password){
        return $this->where([
            'phone'     => $phone,
            'password'  => $password
        ])->find();
    }

    /**
     * @register 注册
     * @param string $username
     * @param string $password
     * @param float $phone
     * @param string $email
     * @param string $salt
     * @return int|string
     */
    public function register(string $username,string $password,float $phone,string $email,string $salt){
        $data = [
            'username'      =>  $username,
            'password'      =>  $password,
            'email'         =>  $email,
            'phone'         =>  $phone,
            'salt'          =>  $salt,
            'createTime'    =>  time(),
            'updateTime'    =>  time()
        ];
        return $this->insert($data);
    }

    /**
     * @retrievePassword    修改密码
     * @param float $phone
     * @param string $password
     * @return User
     */
    public function retrievePassword(float $phone,string $password){
        return $this->where('phone',$phone)->update(['password' => $password]);
    }

    /**
     * @login    邮箱登录
     * @param string $email
     * @param string $password
     * @return User
     */
    public function login(string $email,string $password){
        $data = [
            'email'     =>  $email,
            'password'  =>  $password,
        ];
        return $this->where($data)->find();
    }

    /**
     * @loginlastTime   用户最后登录时间
     * @param string $email
     * @param string $phone
     * @return User
     */
    public function loginlastTime(string $email,string $phone){
        $data = [
            'email'     =>  $email,
            'phone'     =>  $phone
        ];
        return $this->where($data)->update(['lastTime' => time()]);
    }

    /**
     * @smsLogin    短信登录
     * @param string $phone
     * @return User
     */
    public function smsLogin(string $phone){
        return $this->where('phone',$phone)->find();
    }

    //列出用户信息
    public function selUsertext(string $key,string $what,string $value){
    $data = Db::table('user')
        ->field($value)
        ->where($key,"=", $what)
        ->select();
    return $data;
}
}