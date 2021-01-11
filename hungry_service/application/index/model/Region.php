<?php


namespace app\index\model;
use think\Db;
use think\Model;
//地区
class Region extends Model
{
    protected function  selProvide(){

    }
    public function selAdd(){
        $data = Db::table('region')->field('cri_name,cri_code')->where('cir_code','=','1')->select();

    }
}