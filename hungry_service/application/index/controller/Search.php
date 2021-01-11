<?php


namespace app\index\controller;


use app\common\Base;
use app\index\model\Business;
use app\index\model\CommodityAppraises;
use app\index\model\Favorites;
use app\index\model\Order;
use think\Request;

class Search extends Base
{

    //猜你喜欢(成功）

    public function  youLike(Business $business, CommodityAppraises $commodityAppraises,Order $order ){
        //喜欢随机出来
        if (!$data = $business->randBusiness('id,businessName,businessImg,county')) {
            return responseJson(Base::FAIL,'数据获取失败,请重试');
        }
        $arr = array_rand($data,5);
        shuffle($arr);
        $businessData = [];
        foreach ($arr as $value){
            array_push($businessData,$data[$value]);
        }
        foreach ($businessData as $i => $val){
            //五星评价
            $businessData[$i]['Evaluation'] = ceil($commodityAppraises->avgEvaluation($businessData[$i]['id'])/3);
            //人均
            $businessData[$i]['perCapita'] = $order->avgBusiness($businessData[$i]['id']);
        }
        return responseJson(Base::OK,'成功',$businessData);
    }

    //搜索结果(搜索成功）（分页 成功）
    public  function  searchBusiness(Request $request , Business $business , CommodityAppraises $commodityAppraises,Order $order, Favorites $favorites){
        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $userId = $arr['id'];//获取用户id

        $pagenum = $request->post('pagenum');//获取当前页数
       
        $input = $request->post('input');//获取输入框的值
        $sort = $request->post('sort');//获取排序的值
        $min = $request->post('min');//获取人均输入框的值
        $max = $request->post('max');//获取人均输入框的值

        $favoritestext = $favorites->findFavoritesid($userId);//获取收藏店铺
        $favoritesid = array();
        for($e=0;$e<count($favoritestext);$e++){
            array_push($favoritesid,$favoritestext[$e]['businessId']);
        }
        if($input ==''){
           return responseJson(Base::FAIL,'空？');
        }

        $businessData = $business->searchBusiness($input,$sort);
        //评分
        foreach ($businessData as $i => $val){
            //五星评价
            $businessData[$i]['Evaluation'] = ceil($commodityAppraises->avgEvaluation($businessData[$i]['id'])/3);
            //人均
            $businessData[$i]['perCapita'] = $order->avgBusiness($businessData[$i]['id']);
        }
        //每页起始键                   定义分页数据条数
        $start = $pagenum  * Base::$pageSize - Base::$pageSize;
        //每页结尾键
        $end = $pagenum * Base::$pageSize;

        //循环出指定键


        //判断该用户是否收藏此用户‘
        for($w=0;$w<count($businessData);$w++){
            $bid = $businessData[$w]['id'];
            $businessData[$w]['isfavorites']=0;
                if (in_array($bid,$favoritesid)){
            $businessData[$w]['isfavorites']=1;
                }

        }

        $data=array();
        $Data=array();

        //人均区间
        if ($min == '' && $max == ''){
            if($end > count($businessData)){
                $end = count($businessData);
            }
            $page =intdiv(count($businessData),5) + 1 ;
            //添加总页数，和当前页数
            $array = ['page' =>$page,'pagenum' =>$pagenum];
            array_push($Data,$array);

            for ($ii = $start;$ii<$end;$ii++){
                array_push($Data, $businessData[$ii]);
            }
            return responseJson(Base::OK,'成功',$Data);
        }

        //最高价格
        if($min == '' ){
            for($i = 0 ;$i<count($businessData);$i++){
                $perCapita = $businessData[$i]['perCapita'];
               if($perCapita <= $max){
                   array_push($data, $businessData[$i]);
               }
                //如果操过了，就按数量最后
                if($end > count($data)){
                    $end = count($data);
                }
                $page =intdiv(count($businessData),5) + 1 ;
                //添加总页数，和当前页数
                $array = ['page' =>$page,'pagenum' =>$pagenum];
                array_push($Data,$array);

                for ($ii = $start;$ii<$end;$ii++){
                    array_push($Data, $data[$ii]);
                }
            }
            return responseJson(Base::OK,'成功最高'.$max,$Data);
        }
        //最低价格
        if($max == '' ){
            for($i = 0 ;$i<count($businessData);$i++){
                $perCapita = $businessData[$i]['perCapita'];
               if($perCapita >= $min){
                   array_push($data, $businessData[$i]);
               }
                //如果操过了，就按数量最后
                if($end > count($data)){
                    $end = count($data);
                }
                $page =intdiv(count($businessData),5) + 1 ;
                //添加总页数，和当前页数
                $array = ['page' =>$page,'pagenum' =>$pagenum];
                array_push($Data,$array);

                for ($ii = $start;$ii<$end;$ii++){
                    array_push($Data, $data[$ii]);
                }
            }
            return responseJson(Base::OK,'成功最低'.$min,$Data);
        }
        //区间价格
        for($i = 0 ;$i<count($businessData);$i++){
            $perCapita = $businessData[$i]['perCapita'];
            if($perCapita >= $min && $perCapita <= $max){
                array_push($data, $businessData[$i]);
            }
            //如果操过了，就按数量最后
            if($end > count($data)){
                $end = count($data);
            }
            $page =intdiv(count($businessData),5) + 1 ;
            //添加总页数，和当前页数
            $array = ['page' =>$page,'pagenum' =>$pagenum];
            array_push($Data,$array);

            for ($ii = $start;$ii<$end;$ii++){
                array_push($Data, $data[$ii]);
            }
        }
        return responseJson(Base::OK,'成功最高'.$max.'最低'.$min,$Data);
    }
//加入收藏 （成功）
    public function  insertFavorites(Request $request,Favorites $favorites){
        $bid =  $request->post('businessid');//获取商家id
        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $userId = $arr['id'];//获取用户id
        if ($favorites->insertFavorites($userId,$bid)){
            return responseJson(Base::OK,'添加成功');
        }
    }
    //删除收藏 （成功）
    public function  delFavorites(Request $request,Favorites $favorites){
        $bid =  $request->post('businessid');//获取商家id
        //$token是你所登录用户返回的请求头(数据库信息)
        $token = app('mycache')->get($_SERVER['HTTP_TOKEN']);
        //将请求头转为数组
        $arr = json_decode($token,true);
        $userId = $arr['id'];//获取用户id
        if ($favorites->delFavorites($userId,$bid)){
            return responseJson(Base::OK,'删除成功');
        }
    }
}