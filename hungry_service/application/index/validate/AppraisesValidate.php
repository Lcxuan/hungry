<?php


namespace app\index\validate;


use think\Validate;

class AppraisesValidate extends Validate
{
    protected $rule = [
        'content'           => 'chsDash|length:10,200',
        'commodityScore'    => 'integer|between:1,5',
        'serviceScore'      => 'integer|between:1,5',
        'timeScore'         => 'integer|between:1,5'
    ];

    protected $message = [
        'content.length'    => '评论内容必须在10~200字之间',
        'commodityScore'    => '评论出错',
        'serviceScore'      => '评论出错',
        'timeScore'         => '评论出错'
    ];
}