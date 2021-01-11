<?php


namespace app\index\model;
use think\Db;
use think\Model;
//收藏
class Favorites extends Model
{
    /**
     * @findFavorites   查找收藏商家的记录
     * @param int $userId
     * @param int $businessId
     * @return Favorites
     */
    public function findFavorites(int $userId,int $businessId){
        return $this->where([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId
        ])->find();
    }

    /**
     * @showFavorites   显示是否收藏商家
     * @param int $userId
     * @param int $businessId
     * @return Favorites
     */
    public function showFavorites(int $userId,int $businessId){
        return $this->where([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId
        ])->find();
    }

    /**
     * @insertFavorites     收藏商家
     * @param int $userId
     * @param int $businessId
     * @return int|string
     */
    public function insertFavorites(int $userId,int $businessId){
        return $this->insert([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId,
            'createTime'    =>  time(),
            'updateTime'    =>  time()
        ]);
    }

    /**
     * @delFavorites    取消收藏
     * @param int $userId
     * @param int $businessId
     * @return bool
     * @throws \Exception
     */
    public function delFavorites(int $userId,int $businessId){
        return $this->where([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId
        ])->delete();
    }
    public function findFavoritesid(int $userId){
        return Db::table('favorites')
            ->field('businessId')
            ->where('userId' ,'=',   $userId)
            ->select();
    }
    public function del(int $userId,int $businessId){
        return $this->where([
            'userId'        =>  $userId,
            'businessId'    =>  $businessId
        ])->delete();
    }
}