<?php


namespace app\index\model;


use think\Db;
use think\Model;

class Business extends Model
{
    public function setMalSel(string $position){
        return $this->query("SELECT id,businessName from business where concat(provide,city,county,street) like '$position%'");
    }

    /**
     * @youLikeBusiness 猜你喜欢商家
     * @return Business[]
     */
    public function youLikeBusiness(){
        return $this->field('id,businessName')->select();
    }
    /**
     * @BusinessInfo    商家页信息
     * @param int $id
     * @return Business
     */
    public function BusinessInfo(int $id){
        return $this ->where('id',$id)
            ->field('businessName,provide,city,county,street,businessAddress,startTime,endTime')
            ->find();
    }
    /**
     * @randBusiness    随机商家
     * @return mixed
     */
    public function randBusiness(string $str){
        return json_decode($this->field($str)->select(),true);
    }
    /**
     * @rankingList 首页-排行榜
     * @return Business[]
     */
    public function rankingList(){
        return $this->field('id,businessName,businessImg,businessHits')
            ->limit(3)
            ->order('businessHits','desc')
            ->select();
    }

    /**
     * @findBusiness    查找商家
     * @param int $id
     * @return mixed
     */
    //查找商家名
    public function findBusiness(int $id){
        return $this->where('id',$id)->value('businessName');
    }

    /**
     * @param int $id
     * @return mixed
     */
    //查找商家头像(通过商家id)
    public function findBusinessImg(int $id){
        return $this->where('id',$id)->value('businessImg');
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function findBusinessadd(int $id){
        return  Db::table('business')
            ->field('businessName,provide,city,county,street,businessAddress,businessImg')
            ->where('id',"=", $id)
            ->select();
    }
    /**搜索结果
     * @param $input
     * @param $sort
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchBusiness(string $input,int $sort){
        //默认0，由高到低
        $order='';
        if($sort == 0){
            $order ='desc';
        }
        $data = Db::table('business')
            ->field('id,businessName,businessImg,provide,city,county,street,businessAddress,businessHits')
            ->whereLike('businessName',["%$input%"],'and')
            ->where('deleted','=','0')
            ->order('businessHits' , "$order")
            ->select();
        return $data;
    }
}