<?php

namespace app\http\middleware;

use app\common\Base;

class Check extends Base
{
    public function handle($request, \Closure $next)
    {
        //获取请求的token
        $server = $_SERVER['HTTP_TOKEN'];
        //获取redis的token
        $cacheToken = app('mycache')->get($server);
        //判断是否登录
        if (empty($cacheToken)){
            return responseJson(Base::NOT_LOGGED_IN,'尚未登录，跳转到登录页面');
        }
        return $next($request);
    }
}
