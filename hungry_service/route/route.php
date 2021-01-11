<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::post('user/test/test', 'index/UserLogin/test');

Route::group('user',function () {
    Route::post('user/register', 'index/UserLogin/register');       //注册
    Route::get('user/verify', 'index/UserLogin/verify');            //验证码
    Route::post('user/findpass','index/UserLogin/findpass');        //找回密码
    Route::post('user/login','index/UserLogin/login');              //邮箱登录
    Route::post('user/smslogin','index/UserLogin/smsLogin');        //短信登录
    Route::post('user/sendsms','index/UserLogin/sendSms');          //短信发送
    Route::post('user/YouLike','index/Index/youLike');              //首页-猜你喜欢
    Route::post('user/rankingList','index/Index/rankingList');      //首页-排行榜
    Route::post('user/coupon','index/Index/getCoupons');            //首页-领劵中心
    Route::post('user/setMeal','index/Index/setMeal');              //首页-今日套餐
    Route::post('user/found','index/Index/found');                  //首页-发现好店

    Route::group('settlement',function (){
        Route::post('settlement/firstaddorder','Settlement/addOrder');//第一次添加账单
        Route::post('settlement/qrcode','Settlement/qrCode');//结算二维码生成
        Route::post('settlement/comtext','Settlement/comText');//结算页，查看跟多，商品详情
        Route::post('settlement/getpaytext','Settlement/getPaytext');//移动端数据
        Route::post('settlement/seladdress','Settlement/selAddress');//列出收货地址
        Route::post('settlement/addadd','Settlement/addAdd');//添加收货地址
        Route::post('settlement/pay','Settlement/pay');//付款，修改订单状态
        Route::post('settlement/paystate','Settlement/payState');//支付状态

    })->middleware('Check');
    Route::group('ShoppingCart',function (){
        Route::post('shoppingcart/selcart','ShoppingCart/selCart');//列出购物车数据
        Route::post('shoppingcart/selcartcoupon','ShoppingCart/selCartcoupon');//商品商家优惠卷
    })->middleware('Check');
    Route::group('CouponPage',function (){
        Route::post('coupon/selcoupon','CouponPage/selCoupon');//列出优惠卷
        Route::post('coupon/addcoupan','CouponPage/addCoupan');//领取优惠卷
    })->middleware('Check');
    Route::group('Search',function (){
        Route::post('search/youlike','Search/youLike');//猜你喜欢
        Route::post('search/insertfavorites','Search/insertFavorites');//添加收藏
        Route::post('search/delfavorites','Search/delFavorites');//删除收藏
        Route::post('search/searchbusiness','Search/searchBusiness');//搜索结果
    })->middleware('Check');

    Route::group('middle',function (){
        Route::post('user/businessInfo','index/MerchantsPage/businessInfo');                //商家页信息
        Route::post('user/youLike','index/MerchantsPage/youLike');                          //商家页猜你喜欢
        Route::post('user/hotPin','index/MerchantsPage/hotPin');                            //商家页热销
        Route::post('user/discount','index/MerchantsPage/discount');                        //商家页折扣
        Route::post('user/classification','index/MerchantsPage/commodityClassification');   //商家页商品分类
        Route::post('user/showCarts','index/MerchantsPage/showCarts');                      //商家页显示购物车数据
        Route::post('user/shoppingCart','index/MerchantsPage/shoppingCart');                //商家页加入商品
        Route::post('user/emptyCarts','index/MerchantsPage/emptyCarts');                    //商家页清空购物车
        Route::post('user/singleDelete','index/MerchantsPage/singleDelete');                //商家页购物车单个删除
        Route::post('user/historyBusiness','index/MerchantsPage/historyBusiness');          //商家页历史商家
        Route::post('user/favoritesShow','index/MerchantsPage/favoritesBusinessShow');      //商家页显示收藏商家
        Route::post('user/favoritesBusiness','index/MerchantsPage/favoritesBusiness');      //商家页收藏商家
        Route::post('user/comment','index/MerchantsPage/comment');                          //商家评论-按时间排序
        Route::post('user/qualityComment','index/MerchantsPage/qualityComment');            //商家评论-按质量排序
        Route::post('user/updateinfo', 'index/Update/updateUserInfo');                      //更改用户信息
        Route::post('user/updateheader','index/Update/updateHeaderInfo');                   //更改用户头像
        Route::post('user/updatepassword','index/Update/updatePasswordInfo');               //更改用户登录密码
        Route::post('user/addpaypassword','index/Update/addPaypasswordInfo');               //添加支付密码
        Route::post('user/updatepaypassword','index/Update/updatePaypasswordInfo');         //修改支付密码
        Route::post('user/addmoney','index/Update/addMoneyInfo');                           //充值

        Route::post('user/orderStatus','index/OrderStatus/orderStatusInfo');                //下单
        Route::post('user/appraisesInfo','index/Appraises/appraisesOrderInfo');             //订单评价(信息)
        Route::post('user/insertAppraises','index/Appraises/appraisesInsertInfo');          //订单评价(评价)
        Route::post('user/waitToAppraises','index/Appraises/waitToAppraises');              //待评价列表

        Route::post('user/history','index/HistoryAndFavorites/History');                    //历史店铺
    })->middleware('Check');

});

Route::group('business',function (){
    Route::post('business/login','business/BusinessLogin/login');                           //商家登录
    Route::post('business/province','business/BusinessInfo/province');                      //省
    Route::post('business/city','business/BusinessInfo/city');                              //市
    Route::post('business/area','business/BusinessInfo/area');                              //区
    Route::post('business/street','business/BusinessInfo/street');                          //街道
    Route::group('middle',function (){
        Route::post('business/evaluation','business/BusinessPage/evaluationManagement');    //商家评价管理（已回复，待回复，全部）
        Route::post('business/reply','business/BusinessPage/reply');                        //商家回复评论
        Route::post('business/allOrders','business/BusinessPage/allOrders');                //订单类型（全部订单，待接单，待发货，已发货）
        Route::post('business/Info','business/BusinessInfo/shopInfoShow');                  //店铺信息管理-显示店铺信息
        Route::post('business/updateBusiness','business/BusinessInfo/updateBusiness');      //店铺信息管理-修改商家信息
        Route::post('business/showHeadImg','business/BusinessInfo/showHeadImg');            //商家信息-显示商家头像
        Route::post('business/updateHeadImg','business/BusinessInfo/updateHeadImg');        //商家信息-修改商家头像
        Route::post('business/commodityInfo','business/BusinessPage/commodityInfo');        //商品信息管理-商品显示
        Route::post('business/commoditySearch','business/BusinessPage/commoditySearch');    //商品信息管理-商品搜索
        Route::post('commodity/add','business/BusinessPage/addCommodity');//添加商品
        Route::post('commodityClass/add','business/BusinessPage/addComclass');//添加商品类型
        Route::post('commodityClass/select','business/BusinessPage/selClass');//列出商品类型
    })->middleware('Check');

});
