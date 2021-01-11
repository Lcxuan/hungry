<?php


namespace app\business\model;
use think\Model;
//商品
class Commodity extends Model
{
    /**
     * @findCommodityName   查询商家名
     * @param $businessId
     * @param $commodityId
     * @return Commodity
     */
    public function findCommodityName(int $businessId,int $commodityId){
        return $this->field('commodityName')->where([
            'businessId'        =>  $businessId,
            'id'                =>  $commodityId
        ])->find();
    }
    /**
     * @commoditySearch 商品搜索
     * @param int $id
     * @param string $commodityName
     * @param int $catId
     * @return Commodity[]
     */
    public function commoditySearch(int $id,string $commodityName){
        $arr = [
            'businessId'        =>  $id,
            'commodityName'     =>  $commodityName,
        ];
        return $this->where($arr)
            ->field('commodityImg,commodityName,catId,presentPrice,commodityDesc,createTime,isSale,isRecome,deleted')
            ->select();
    }

    /**
     * @showCommodity   显示商品
     * @param int $id
     * @return Commodity[]
     */
    public function showCommodity(int $id){
        return $this->where('businessId',$id)
            ->field('commodityImg,commodityName,catId,presentPrice,commodityDesc,createTime,isSale,isRecome,deleted')
            ->select();
    }
    //添加商品

    /**
     * @param string $name
     * @param string $filename
     * @param float $price
     * @param string $desc
     * @param int $businessId
     * @param int $typeId
     * @return int|string
     */
        public function  commodityData(string $name,string $filename,float $price,string $desc,int $businessId,int $typeId){
            $CommodityData =[ //商品表
                'businessId' => $businessId,//商家编号
                'catId' => $typeId, //所属分类
                'commodityName' => $name,
                'commodityImg' =>"http://hungry.wistudy.xyz/". $filename, //商品图片
                'originalPrice' => $price,
                'presentPrice'=> $price,
                'commodityDesc'=> $desc,
                'createTime'=> time(),
                'updateTime'=> time(),
                'deleted' => 0 //是否删除	做逻辑删除，杜绝物理删除
            ];
            return $this->table('commodity') -> insert($CommodityData);
        }

}