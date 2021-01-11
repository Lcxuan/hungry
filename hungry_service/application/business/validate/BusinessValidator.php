<?php


namespace app\business\validate;

use think\Validate;

class BusinessValidator extends  Validate
{
    protected  $rule = [
        'name'              => 'length :1,50',
        'type'              => 'length :1,20',
        'price'             => 'number|between :0,9999',
        'desc'              => 'length :1,255',
        'img'               => 'image',
        'businessName'      =>  'alphaNum|length:6,30', //商家名
        'businessPassword'  =>  'alphaNum|length:6,18', //商家密码
        'startTime'         =>  'dateFormat:H:i',       //开始营业时间
        'endTime'           =>  'dateFormat:H:i',       //结束营业时间
        'businessDesc'      =>  'length:0,50',          //简介
        'provide'           =>  'chs',                  //省、直辖市
        'city'              =>  'chs',                  //市
        'county'            =>  'chs',                  //区
        'street'            =>  'chs',                  //街道
        'businessAddress'   =>  'chsAlphaNum',          //详细地址
        'phone'             =>  'mobile',               //手机号码
        'businessImg'       =>  'image',                //店铺主照片
        'headImg'           =>  'image',                //商家头像
        'deliverMoney'      =>  'integer',              //配送费
        'startDeliver'      =>  'integer',              //起送价格
        'reply'             =>  'chsAlphaNum|length:0,100'  //商家回复
    ];
    //改错误提示
    //https://www.kancloud.cn/manual/thinkphp5_1/354104
    protected  $message = [
        'name.length'               => '商品名至少1个最多50个字符',
        'type.length'               => '商品名至少1个最多20个字符',
        'price.number'              => '价格为数字',
        'price.length'              => '请给出合适的价格',
        'desc.length'               => '请简短描述商品，最多255个字符',
        'img'                       => '图片格式错误',
        'businessName.alphaNum'     =>  '请输入正确的商家名',
        'businessName.length'       =>  '商家名不能超过6-30',
        'businessPassword.alphaNum' =>  '请输入正确的密码',
        'businessPassword.length'   =>  '商家密码不能超过6-18',
        'startTime.dateFormat'      =>  '请选择正确的营业时间',
        'endTime.dateFormat'        =>  '请选择正确的营业时间',
        'businessDesc.length'       =>  '请输入正确的简介，长度在50字之间',
        'provide.chs'               =>  '请选择正确的地址',
        'city.chs'                  =>  '请选择正确的地址',
        'county.chs'                =>  '请选择正确的地址',
        'street.chs'                =>  '请选择正确的地址',
        'businessAddress.chsAlphaNum'=> '请选择正确的地址',
        'phone'                     =>  '请输入正确的手机号',
        'businessImg.image'         =>  '图片格式有误',
        'headImg'                   =>  '图片格式有误',
        'reply.chsAlphaNum'         =>  '请输入正确的格式，汉字、字母、数字',
        'reply.length'              =>  '请在正确的范围内输入，50字之内'
    ];
}