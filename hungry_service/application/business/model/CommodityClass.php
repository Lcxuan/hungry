<?php


namespace app\business\model;
use think\Db;
use think\Model;
//商品分类
class CommodityClass extends Model
{
    //查询类型种类（类型名）
    public function classSel(int $businessId)
    {
      $data = $this->table('commodityClass')
            ->field('businessId,catName')
            ->where('businessId','='," $businessId")
            ->select();
        return  $data;
    }

    //添加商品类型
    public function classData(string $type,int $businessId)
    {
        $ClassData = [ //商品类型表
            'id' => '',
            'businessId' => $businessId,//商家编号
            'catName' => $type,//类型名
            'createTime' => time(),
            'updateTime' => time(),
            'isShow' =>'',//是否显示	0:否;1:是
            'deleted' => 0 //是否删除	做逻辑删除，杜绝物理删除
        ];
        return $this->table('commodityClass')->insert($ClassData);
    }
}


