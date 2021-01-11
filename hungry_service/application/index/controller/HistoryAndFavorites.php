<?php
namespace app\index\controller;

use app\common\Base;
use think\Exception;

class HistoryAndFavorites extends Base
{
    public function History(){
        try{
            return 123;
        }catch (Exception $e){
            throw $e;
        }
    }
}