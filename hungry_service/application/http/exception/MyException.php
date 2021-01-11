<?php

namespace app\http\exception;

use think\exception\Handle;
use Exception;
use app\common\DingDing;

class MyException extends Handle {

    public function render(Exception $e)
    {
        trace($e->getMessage(), 'error');
        trace($e->__toString(), 'error');
        DingDing::sendBug($e->__toString());

        $data = [
            'code' => 50000,
            'msg' => '服务器异常',
            'data' => []
        ];
        return json($data, 500);
    }

}