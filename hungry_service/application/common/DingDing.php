<?php
namespace app\common;

use app\common\HttpClient;

class DingDing {

    /**
     * 发送报错信息
     */
    public static function sendBug(string $bug)
    {
        $title = '监控报警-饿着了';
        $data = [
            'msgtype'  => 'text',
            'text' => [
                // 'content' => $title,
                'content'  => "{$title}\n\n" . $bug
            ],
            'at' => [
                'isAtAll'   => true
            ]
        ];

        $url = env('DINGDING_ROBOT');
        HttpClient::post($url, $data, ['Content-Type: application/json;charset=utf-8']);
    } 


}