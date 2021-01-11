<?php


namespace app\index\model;
use think\Db;
use think\Model;
//用户收货地址
//定义类型
class UserAddress extends Model
{
    /**查找指定用户收货地址
     * @param int $uId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function selAddress(int $uId){
        $data = Db::table('userAddress')
            ->field('provide,city,county,street,detail,name,sex,phone,isDefault')
            ->where('userId','=', $uId)
            ->select();
        return $data;
    }
}