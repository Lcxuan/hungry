<?php


namespace app\index\model;


use think\Db;
use think\Model;

class Commodity extends Model
{
    /**
     * @youLikeCount    猜你喜欢商家的商品数
     * @param int $businessId
     * @return Commodity
     */
    public function youLikeCount(int $businessId){
        return $this->where('businessId',$businessId)->count();
    }

    /**
     * @youLikeCommodity    猜你喜欢指定商家的全部商品
     * @param int $businessId
     * @return mixed
     */
    public function youLikeCommodity(int $businessId){
        return json_decode($this->field('commodityImg')->where('businessId',$businessId)->select(),true);
    }

    /**
     * @businessCommodity   商家商品
     * @param int $businessId
     * @return mixed
     */
    public function businessCommodity(int $businessId){
        return json_decode($this->field('id,commodityName,commodityImg,originalPrice,presentPrice')
            ->where([
                'businessId'    =>  $businessId,
                'deleted'       =>  0
            ])
            ->select(),true);
    }

    public  function  selCom(string $key,string $what,string $value){
        $data = Db::table('commodity')
            ->field($value)
            ->where($key,"=", $what)
            ->select();
        return $data;
    }
    /**
     * @commodityAll    使用商家id查找商品
     * @param int $id
     * @param string $str
     * @return Commodity[]
     */
    public function commodityAll(int $id,string $str){
        return $this->field($str)->where('businessId',$id)->select();
    }

    /**
     * @useCommodityCIdFind 使用商家分类id查找商品
     * @param int $commodityClassId
     * @return Commodity[]
     */
    public function useCommodityCIdFind(int $commodityClassId){
        return $this->field('id,commodityName,commodityImg,presentPrice')->where('catId',$commodityClassId)->select();
    }
}

