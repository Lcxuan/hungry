<?php


namespace app\business\model;


use think\Model;

class Region extends Model
{
    /**
     * @sheng   全部省份
     * @return Region[]
     */
    public function sheng(){
        return $this->field('cri_name,cri_short_name')->where('cri_parent_code','000000')->select();
    }

    /**
     * @shengData   指定省份的信息
     * @param string $shengName
     * @return Region
     */
    public function shengData(string $shengName){
        return $this->where([
            'cri_parent_code'   =>  '000000',
            'cri_name'          =>  $shengName
        ])->find();
    }

    /**
     * @shi 指定省份的市区
     * @param int $cri_code
     * @return Region[]
     */
    public function shi(int $cri_code){
        return $this->field('cri_name,cri_short_name')->where('cri_parent_code',$cri_code)->select();
    }

    /**
     * @shiData 指定市的信息
     * @param string $shiName
     * @return Region
     */
    public function shiData(string $shiName){
        return $this->where([
            'cri_name'          =>  $shiName
        ])->find();
    }

    /**
     * @qu  指定市的全部镇区
     * @param int $cri_code
     * @return Region[]
     */
    public function qu(int $cri_code){
        return $this->field('cri_name,cri_short_name')->where('cri_parent_code',$cri_code)->select();
    }

    /**
     * @quData  指定镇区的信息
     * @param string $quName
     * @return Region
     */
    public function quData(string $quName){
        return $this->where([
            'cri_name'          =>  $quName
        ])->find();
    }

    /**
     * @jie 指定镇区的全部街道
     * @param int $cri_code
     * @return Region[]
     */
    public function jie(int $cri_code){
        return $this->field('cri_name,cri_short_name')->where('cri_parent_code',$cri_code)->select();
    }
}