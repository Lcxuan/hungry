<?php


namespace app\index\model;
use think\Db;
use think\Model;
//订单
class Order extends Model
{
    /**
     * @countOrder  获取指定商家的条数
     * @param int $id
     * @return mixed
     */
    public function countOrder(int $id){
        return $this->where('businessId',$id)->select()->count();
    }

    /**
     * @rowBusiness 商家条数
     * @param int $id
     * @return Order
     */
    public function avgBusiness(int $id){
        return $this->where('businessId',$id)->avg('totalMoney');
    }

    /**查找订单信息
     * @param string $key
     * @param string $what
     * @param string $value
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function  selOrder(string $key,string $what,string $value){
        $data = Db::table('order')
            ->field($value)
            ->where($key,"=", $what)
            ->select();
        return $data;
    }

    /**查询订单id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function  selOrderid(){
        $data = Db::table('order')
            ->field('orderNum')
            ->select();
        return $data;
    }
    //添加订单（1未支付）

    /**
     * @param string $id
     * @param int $uid
     * @param int $bid
     * @param array $arr
     * @param int $paytype
     * @param string $orderRemarks
     * @return int|string
     */
    public  function  addOrder(string $id,int $uid,int $bid,array $arr,int $paytype,string $orderRemarks){
        $AddOrder =[ //
            'orderNum' => $id,//订单编号
            'businessId' => $bid,//商家编号
            'userId' => $uid,
            'payType' => $paytype,//支付方式 0 微信|1 支付宝
            'orderStatus' => '-2',//状态未支付
            'deliveryStatus' => '-1',//发货状态-1：待接单 0:待发货 1:已发货
            'userName'=> $arr['username'],
            'userPhone'=> $arr['userphone'],
            'provide'=> $arr['provide'],//省/直辖市
            'city'=> $arr['city'],//市/直辖市
            'county'=> $arr['county'],//区/县
            'street'=> $arr['street'],//街道
            'detail'=> $arr['detail'],//详细地址
            'totalMoney'=> $arr['moneyall'],//总价
            'realTotalMoney'=> $arr['realTotalMoney'],//真实总金额	算上折扣后
            'orderRemarks'=> $orderRemarks,//买家备注
//            'cancalReason'=> '',//取消原因
//            'deliveryTime'=> '',//发货时间
//            'receiveTime'=> '',//收货时间
            'createTime'=> time(),
            'updateTime'=> time(),
            'deleted' => 0 //是否删除	做逻辑删除，杜绝物理删除
        ];
        return $this ->table('order')-> insert($AddOrder);
    }
    //订单付款（2付款）

    /**
     * @param $orderId
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function  payOrder(string $orderId){
        $data  = [
            'orderStatus' => '-1',
            'updateTime'=> time()
        ];
        Db::table('order')->where('orderNum',$orderId)->update($data);
    }

    /**
     * 订下单状态
     * @param int $userid
     * @return mixed
     */
    //查找订单信息(用于判断是否已下单)
    public function findOrder(int $userid){
        return $this->table('order')->where('userId', $userid)->select();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    //查找订单信息(已下单页面order表信息，及orderNum,businessId)
    public function findOrderInfo(int $userId){
        return $this->table('order')->field('orderStatus,createTime,realTotalMoney,orderNum,businessId')->where('userId',$userId)->select();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    //(待评价)查找订单信息
    public function waitToAppFindOrderInfo(int $userId){
        return $this->table('order')->field('orderStatus,createTime,realTotalMoney,orderNum,businessId')->where([
            'userId'=>$userId,
            'orderStatus'=>'2'
        ])->select();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    //待评价信息区域查找的信息
    public function appFindOrderInfo(int $userId,string $orderNum){
        return $this->table('order')->field('createTime,realTotalMoney,orderNum,businessId')->where([
            'userId'=>$userId,
            'orderNum'=>$orderNum
        ])->select();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    //找出orderId
    public function appFindOrderId(int $userId,string $orderNum){
        return $this->table('order')->field('Id')->where([
            'userId'=>$userId,
            'orderNum'=>$orderNum
        ])->select();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    //找出订单号
    public function appFindOrderNum(int $userId,string $orderNum){
        return $this->table('order')->field('orderNum')->where([
            'userId'=>$userId,
            'orderNum'=>$orderNum
        ])->select();
    }

    //找出订单的商家Id
    public function appFindBusinessId(int $userId,string $orderNum){
        return $this->table('order')->field('businessId')->where([
            'userId'=>$userId,
            'orderNum'=>$orderNum
        ])->select();
    }
}
