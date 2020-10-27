<?php


namespace App\Service;


use Illuminate\Support\Facades\Storage;

class UploadService
{
    //上传文件
    public static function doUpload($fileObj,$remoteDir){
        $path = $fileObj->store($remoteDir,'oss');
        $imgUrl = "http://".config("filesystems.disks.oss.bucket").".".config("filesystems.disks.oss.endpoint").$path;
        //        $imgUrl = config("filesystems.disks.oss.cdnDomain").$path;
        //        $imgUrl = Storage::url($path);
        //        dump($imgUrl);die;
        if ( $path ){
            return ["status" => "SUCCESS","fileUrl" => $imgUrl];
        }
        return ["status" => "ERROR","fileUrl" => ""];
    }


    //删除文件
    public static function deleteImg($imgUrl){
        $path = str_replace("http://","","$imgUrl");
        $pos = strpos($path,"/");
        $imgUrl = substr($path,$pos+1);
        $isExist = Storage::exists($imgUrl);
        if (!$isExist){
            return true;
        }
        $res = Storage::delete($imgUrl);
        return $res ;
        if(!$res){
            return false;
        }
        return true;

    }

}
