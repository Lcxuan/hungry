<?php
namespace app\business\model;
use think\Model;
//商家表
class Business extends Model
{
    /**
     * @login   商家登录
     * @param string $businessName
     * @param string $businessPassword
     * @return Business
     */
    public function login(string $businessName,string $businessPassword){
        $data = [
            'businessName'      =>  $businessName,
            'businessPassword'  =>  $businessPassword
        ];
        return $this->where($data)->find();
    }

    /**
     * @showInfo    显示商家信息
     * @param int $id
     * @return Business
     */
    public function showInfo(int $id){
        return $this->where('id',$id)->find();
    }

    /**
     * @updateBusiness  修改商家信息
     * @param int $id
     * @param array $info
     * @return Business
     */
    public function updateBusiness(int $id,array $info){
        $info['updateTime'] = time();
        return $this->where('id',$id)->update($info);
    }

    /**
     * @showHeadImg 显示商家头像
     * @param int $id
     * @return mixed
     */
    public function showHeadImg(int $id){
        return $this->field('headImg')->where('id',$id)->find();
    }

    /**
     * @updateHeadImg   修改商家头像
     * @param int $id
     * @param array $headImg
     * @return Business
     */
    public function updateHeadImg(int $id,array $headImg){
        $headImg['updateTim'] = time();
        return $this->where('id',$id)->update($headImg);
    }
}