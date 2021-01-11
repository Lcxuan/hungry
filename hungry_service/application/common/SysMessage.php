<?php


namespace app\common;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


class SysMessage
{
    public function useSys($phone,$code){
        AlibabaCloud::accessKeyClient(env('SMS_ACCESSKEY'),env('SMS_ACCESSKEYSECRET'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' =>  [
                        'RegionId'      =>  "cn-hangzhou",
                        'PhoneNumbers'  =>  $phone,
                        'SignName'      =>  env('SIGNNAME'),
                        'TemplateCode'  =>  env('TEMPLATECODE'),
                        'TemplateParam' =>  "{\"code\":\"".$code."\"}",
                    ],
                ])
                ->request();
        }catch (ClientException $e){
            echo $e->getErrorMessage().PHP_EOL;
        }catch (ServerException $e){
            echo $e->getErrorMessage().PHP_EOL;
        }
    }
}