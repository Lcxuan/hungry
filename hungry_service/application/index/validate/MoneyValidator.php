<?php


namespace app\index\validate;
use think\Validate;

class MoneyValidator extends Validate
{
    protected $rule = [
        'Money'         => 'number|between:1,20000',
        'payType'       => 'number|in:0,1'
    ];

    protected $message = [
        'Money.between'     => '充值余额最多充20000',
        'payType.number'    => '请选择支付方式'
    ];
}