<?php

namespace app\behavior;

class CORS
{

    public function run()
    {
        header("Access-Control-Allow-Origin: " . request()->header('Origin'));
        header("Vary: Origin");
        if (request()->isOptions()) {

            header("Access-Control-Allow-Credentials: true");
            // 允许的header
            header("Access-Control-Allow-Headers: " . request()->header('Access-Control-Request-Headers'));
            // 允许的请求方法
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            response()->send();
            exit;
        }
    }

}
