<?php


namespace app\business\validate;


use think\Validate;

class CommodityValidate extends Validate
{
    protected  $rule = [
        'commodityName'     =>  'chsAlpha|length:0,50',
        'catName'           =>  'chsAlpha|length:0,50',
    ];
    protected  $message = [
        'commodityName.chsAlpha'    =>  '请输入正确的格式，汉字、字母',
        'commodityName.length'      =>  '请在正确的范围内输入，50字之内',
        'catName.chsAlpha'          =>  '请输入正确的格式，汉字、字母',
        'catName.length'            =>  '请在正确的范围内输入，50字之内'
    ];
}