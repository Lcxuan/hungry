<?php


namespace app\business\model;


use think\Model;

class CommodityAppraises extends Model
{
    /**
     * @evaluation  商家评价管理
     * @param $businessId
     * @param $evaluationStatus
     * @return CommodityAppraises[]
     */
    public function evaluation(int $businessId,string $evaluationStatus){
        if ($evaluationStatus == ""){
            return $this->table('commodityAppraises')
                ->field('id,businessId,orderId,commodityId,commodityScore,serviceScore,timeScore,content,evaluationStatus,businessReply')
                ->where('businessId',$businessId)
                ->select();
        }
        return $this->table('commodityAppraises')
            ->field('id,businessId,orderId,commodityId,commodityScore,serviceScore,timeScore,content,evaluationStatus,businessReply')
            ->where([
                'businessId'        =>  $businessId,
                'evaluationStatus'  =>  $evaluationStatus
            ])
            ->select();
    }

    /**
     * @reply   商家回复评论
     * @param $reply
     * @param $commodityAppraisesId
     * @return CommodityAppraises
     */
    public function reply(string $reply,int $commodityAppraisesId){
        return $this->table('commodityAppraises')
            ->where('id',$commodityAppraisesId)
            ->update([
                'businessReply'     =>  $reply
            ]);
    }
}