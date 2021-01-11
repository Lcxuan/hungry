<?php


namespace app\index\model;


use think\Model;

class CommodityAppraises extends Model
{
    /**
     * @qualityComment
     * @param int $businessId
     * @return CommodityAppraises[]
     */
    public function qualityComment(int $businessId){
        return $this->table('commodityAppraises')
            ->field('id,userId,businessId,commodityScore,serviceScore,timeScore,content,businessReply,createTime,replyTime')
            ->where('businessId',$businessId)
            ->select();
    }
    /**
     * @businessComment 商家评论-按时间排序
     * @param $businessId
     * @return CommodityAppraises[]
     */
    public function businessComment(int $businessId){
        return $this->table('commodityAppraises')
            ->field('id,userId,businessId,content,businessReply,createTime,replyTime')
            ->where('businessId',$businessId)
            ->order('createTime','desc')
            ->select();
    }

    /**
     * @param int $businessId
     * @return CommodityAppraises
     */
    public function avgEvaluation(int $businessId){
        $commodityScore = $this->table('commodityAppraises')->where('businessId',$businessId)->avg('commodityScore');
        $serviceScore = $this->table('commodityAppraises')->where('businessId',$businessId)->avg('serviceScore');
        $timeScore = $this->table('commodityAppraises')->where('businessId',$businessId)->avg('timeScore');
        $result = $commodityScore + $serviceScore + $timeScore;
        return $result;
    }

    /**
     * @rowBusiness 商家条数
     * @param int $id
     * @return CommodityAppraises
     */
    public function rowBusiness(int $id){
        return $this->table('commodityAppraises')->where('businessId',$id)->count();
    }

    /**
     * @param int $id
     * @param int $orderId
     * @param int $businessId
     * @param string $commodityId
     * @param string $content
     * @param int $commodityScore
     * @param int $serviceScore
     * @param int $timeScore
     * @return mixed
     */
    public function insertCommodityInfo(int $id,int $orderId,int $businessId,string $commodityId,string $content,int $commodityScore,int $serviceScore,int $timeScore){
        $data = [
            'userId'            =>  $id,
            'orderId'           =>  $orderId,
            'businessId'        =>  $businessId,
            'commodityId'       =>  $commodityId,
            'content'           =>  $content,
            'commodityScore'    =>  $commodityScore,
            'serviceScore'      =>  $serviceScore,
            'timeScore'         =>  $timeScore,
            'createTime'        =>  time()
        ];
        return $this->table('commodityAppraises')->where('userId',$id)->insert($data);
    }
}