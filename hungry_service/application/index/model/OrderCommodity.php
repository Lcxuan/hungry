<?php


namespace app\index\model;
use think\Db;
use think\Model;
//订单商品
class OrderCommodity extends Model
{
    //查询数据
    /**
     * @param string $key
     * @param string $what
     * @param string $value
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function  selOC(string $key,string $what,string $value){
        $data = Db::table('orderCommodity')
            ->field($value)
            ->where($key,"=", $what)
            ->select();
        return $data;
    }
    //添加订单商品

    /**
     * @param string $id
     * @param int $cid
     * @param string $commodityName
     * @param int $commodityNum
     * @param float $money
     * @param string $commodityImg
     * @return int|string
     */
    public function  addOC(string $id ,int  $cid,string $commodityName,int $commodityNum,float $money,string $commodityImg){
        $AddOC =[ //
            'orderId' => $id,//订单编号
            'cid' => $cid,//商品id
            'commodityName' => $commodityName,//商品名
            'commodityImg' => $commodityImg,//商品图片
            'commodityNum' => $commodityNum,//商品数量
            'money' => $money
        ];
        return $this ->table('orderCommodity')-> insert($AddOC) ;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findCommodity(int $id){
        return $this->table('orderCommodity')->where('cid',$id)->select()->count();
    }

    /**
     * @param int $orderNum
     * @return mixed
     */
    //查找商品名(已下单)
    public function findComodityName(string $orderNum){
        return $this->table('orderCommodity')->field('commodityName')->where('orderId',$orderNum)->select();
    }

    //查找商品数量
    public function findComodityNum(string $orderNum){
        return $this->table('orderCommodity')->field('commodityName')->where('orderId',$orderNum)->select()->count();
    }
}