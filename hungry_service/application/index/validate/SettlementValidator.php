<?php


namespace app\index\validate;


use think\Validate;

class SettlementValidator extends Validate
{
    protected $rule=[
        'orderRemarks' =>'length:0,233'
    ];
    protected  $message=[
        'orderRemarks'  =>'备注不可以写那么多喔'
    ];
}