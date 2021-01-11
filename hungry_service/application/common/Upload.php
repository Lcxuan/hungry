<?php


namespace app\common;


use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Upload
{
//上传7牛图库
    public  function  upload($local, $filename){
        //$local本地文件路径，$filename 上传后的文件名
        try{
            $auth = new Auth(env('QINIU_ACCESSKEY'), env('QINIU_SECRETKEY'));
            $token = $auth->uploadToken(env('QINIU_BUCKET'));
            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($token, $filename, $local);
            if ($err !== null) {
                throw new \Exception($err);
            }
            return $ret;
        }catch (\Exception $e){
            throw $e;
        }
    }
}