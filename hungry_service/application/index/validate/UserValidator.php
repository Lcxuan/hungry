<?php


namespace app\index\validate;

use think\Validate;
class UserValidator extends Validate
{
    protected $rule = [
        'username'      => 'alphaNum|length:6,10',
        'password'      => 'alphaNum|length:8,18',
        'oldpassword'   => 'alphaNum|length:8,18',
        'repassword'    => 'confirm:password|length:8,18',
        'paypassword'   => 'number|length:6',
        'repaypassword' => 'confirm:paypassword|number|length:6',
        'verify'        => 'captcha',
        'phone'         => 'mobile',
        'email'         => 'email',
        'sex'           => 'number|in:0,1',
        'birthday'      => 'date',
        'verifyCode'    => 'number'
    ];

    protected $message = [
        'username.alphaNum'     =>  '请输入正确的用户名',
        'username.length'       =>  '用户名不能超过6-10位',
        'password.length'       =>  '密码不能超过8-18位',
        'repassword.length'     =>  '确认密码不能超过8-18',
        'repassword.confirm'    =>  '请输入一样的密码',
        'paypassword.length'    =>  '支付密码只能是6位',
        'repaypassword.length'  =>  '确认支付密码只能是6位',
        'repaypassword.confirm' =>  '请输入相同的支付密码',
        'phone.mobile'          =>  '请输入正确的号码',
        'email.email'           =>  '请输入正确的邮箱',
        'verify.captcha'        =>  '请输入正确的验证码',
        'sex.length'            =>  '请选择称谓',
        'birthday.date'         =>  '请选择正确日期'
    ];
}