<?php
namespace app\common;

use think\Cache;
use think\facade\Config;

class MyCache extends Cache {

    public function __construct(array $config = [])
    {
        $this->init(Config('cache.' . env('CACHE')));
    }
}