<?php
namespace app\index\model;
use think\Db;
use think\Model;

//Money表
class Money extends Model
{
    /**
     * @param int $targetId
     * @param int $payType
     * @param int $Money
     * @return mixed
     */
    public function userAddMoney(int $targetId,string $paytype,array $request){
        $data = [
            'targetId'      =>  $targetId,
            'targetType'    =>  '0',        //0:用户,1:商家
            'dataSrc'       =>  '5',        //1:交易订单 2:订单结算 3:提现申请 4.退款订单 5.充值
            'moneyType'     =>  '1',        //0:支出,1:收入
            'payType'       =>  $paytype,
            'Money'         =>  $request['money'],
            'createTime'    =>  time()
        ];
        return $this->where('targetId',$targetId)->insert($data);
    }

    /**
     * @param int $targetId
     * @param int $Money
     * @return mixed
     */
    public function findUserMoney(int $targetId,array $request){
        $data = [
            'targetType'    =>  '0',    //判断是商家还是用户，0为用户，1为商家
            'Money'         =>  $request['money']
        ];
        return $this->where('targetId',$targetId)->select($data);
    }

    //列出账单信息
    public function selMoneytext(int $uId,int $type){
        $data = Db::table('money')
            ->field('Money')
            ->where('targetId',"=", $uId)
            ->where('targetType',"=", $type)
            ->order('createTime','desc')
            ->select();
        return $data;
    }
    //移动端付款
    public function  payMoney(int $uId,float $paymoney,int $businessId,float $userbalance,float $businessbalance){
        $userData =[
            'targetId'=>$uId,//用户ID
            'targetType'=>0,//用户类型	0:用户,1:商家
            'dataSrc'=>1,//来源	1:交易订单 2:订单结算 3:提现申请 4.退款订单 5.充值
            'moneyType'=>0,//流水标志	0:支出,1:收入
            'payType'=>1,//支付类型	0:微信支付;1:支付宝
            'Money'=>$userbalance - $paymoney,//余额
            'createTime'=>time(),//创建时间
        ];
        $businessData =[
            'targetId'=>$businessId,//用户IDs
            'targetType'=>1,//用户类型	0:用户,1:商家
            'dataSrc'=>2,//来源	1:交易订单 2:订单结算 3:提现申请 4.退款订单 5.充值
            'moneyType'=>1,//流水标志	0:支出,1:收入
            'payType'=>1,//支付类型	0:微信支付;1:支付宝
            'Money'=>$businessbalance + $paymoney,//余额
            'createTime'=>time(),//创建时间
        ];


        Db::startTrans();
        try{
          Db::connect('user')->table('money')->insert($userData);
          Db::connect('business')->table('money')->insert($businessData);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            dump($e->getMessage());
            // 回滚事务
            Db::rollback();
            return false;
            //注意：我们做了回滚处理，
        }


    }
}