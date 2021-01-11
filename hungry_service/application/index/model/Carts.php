<?php


namespace app\index\model;
use think\Db;
use think\Model;
//购物车
class Carts extends  Model
{
    //查询出此用户的的所有购物车中商家名
    /**
     * @param $uId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  selCartName(int $uId){
        $businessName = Db::table('carts')
                ->field('businessName')
                ->where('userId',"=", $uId)
//                ->order('id')
                ->select();
        return $businessName;
    }
    /**
     * @showBusinessPageCarts   显示商家页购物车的数据
     * @param int $userId
     * @param string $businessName
     * @return Carts[]
     */
    public function showBusinessPageCarts(int $userId,string $businessName){
        return $this->where([
            'userId'            =>  $userId,
            'businessName'      =>  $businessName,
            'deleted'           =>  0
        ])->select();
    }

    //查询出此用户的的所有购物车中商品id

    /**
     * @param $bname
     * @param $uId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function  selCartComid(string $bname,int $uId){
        $commodityId = Db::table('carts')
            ->field('commodityId')
            ->where('userId',"=", $uId)
            ->where('businessName',"=", $bname)
            ->select();
        return $commodityId;
    }

    /**
     * @param string $bname
     * @param int $uId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */    //查询出此用户的的所有购物车中商家id
    public  function  selCartBid(string $bname,int $uId){
        $commodityId = Db::table('carts')
            ->field('businessId')
            ->where('userId',"=", $uId)
            ->where('businessName',"=", $bname)
            ->select();
        return $commodityId;
    }
    
    public function findCart(int $userId,int $commodityId,string $businessName){
        return $this->where([
            'userId'        =>  $userId,
            'commodityId'   =>  $commodityId,
            'businessName'  =>  $businessName,
            'deleted'       =>  0
        ])->find();
    }
    public function updateCart(int $userId,int $commodityId,string $businessName,string $commodityNum){
        return $this->where([
            'userId'        =>  $userId,
            'commodityId'   =>  $commodityId,
            'businessName'  =>  $businessName,
            'deleted'       =>  0
        ])->update([
            'commondityNum' =>  $commodityNum
        ]);
    }
    /**
     * @cartInsert  插入购物车
     * @param int $userId
     * @param int $commodityId
     * @param string $commodityName
     * @param string $commodityNum
     * @return int|string
     */
    public function cartInsert(int $userId,int $commodityId,string $businessName,string $commodityNum){
        return $this->insert([
            'userId'        =>  $userId,
            'commodityId'   =>  $commodityId,
            'businessName'  =>  $businessName,
            'commondityNum' =>  $commodityNum
        ]);
    }

    /**
     * @delBusinessPageCats 商家页清空购物车
     * @param $userId
     * @param $businessName
     * @return Carts
     */
    public function delBusinessPageCats(int $userId,string $businessName){
        return $this->where([
            'userId'        =>  $userId,
            'businessName'  =>  $businessName
        ])->update([
            'deleted'       =>  1
        ]);
    }

    /**
     * @singleDelete    商家页购物车单个删除
     * @param int $userId
     * @param int $commodityId
     * @param string $businessName
     * @return Carts
     */
    public function delSingleDelete(int $userId,int $commodityId,string $businessName){
        return $this->where([
            'userId'        =>  $userId,
            'commodityId'   =>  $commodityId,
            'businessName'    =>  $businessName
        ])->update([
            'deleted'       =>  1
        ]);
    }
}