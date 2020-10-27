<?php

namespace App\Http\Controllers;

use App\Services\OSS;
use hanneskod\classtools\Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Service\UploadService;
use OSS\Core\OssException;
use OSS\OssClient;

class FileController extends Controller
{

    private $accessKeyId = '';
    private $accesskeySecret = '';
    private $endpoint = '';
    private $bucket = '';


    private $ossClient = '';
    public function __construct()
    {
        $this->accessKeyId = env("ALIYUN_ACCESSKEY_ID");
        $this->accesskeySecret = env("ALIYUN_ACCESSKEY_SECRET");
        $this->endpoint = env("ALIYUN_ENDPOINT");
        $this->bucket = 'zhihao-lthink-resource-test';
        $this->ossClient = new OssClient($this->accessKeyId, $this->accesskeySecret, $this->endpoint);
    }

    public function upload(Request $request)
    {
        $fileObj = $request->file('file');
        if (!$fileObj) {
            return view('uploadfile');
        } else {
            $remoteDir = config("filesystems.disks.oss.ad_upload_dir");
            $res = UploadService::doUpload($fileObj, $remoteDir);
            return $res;
        }
    }

    /**
     * 创建一个bucket
     */
    public function createBucket()
    {
        try {
            $options = array(
                //设置存储空间
                OssClient::OSS_STORAGE => OSSClient::OSS_STORAGE_IA
            );
            $this->ossClient->createBucket($this->bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 获取所有存储空间
     */
    public function getBucketList()
    {
        try {

            $bucketListInfo =  $this->ossClient->listBuckets();
            //获取存储空间地域
            $Regions =  $this->ossClient->getBucketLocation($this->bucket);

            //获取存储空间元信息
            $Metas =  $this->ossClient->getBucketMeta($this->bucket);

        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        if (!$bucketListInfo) {
            return ['msg' => '当前存储空间为空', 'list' => []];
        }
//        if ($bucketListInfo == true) {
//            print(__FUNCTION__ . ": OK" . "\n");
//        } else {
//            print(__FUNCTION__ . ": FAILED" . "\n");
//        }
        $bucketList = $bucketListInfo->getBucketList();
        $list = [];
        foreach ($bucketList as $k => $bucket) {
            $list[$k]['bucketName'] = $bucket->getName();
            $list[$k]['createTime'] = $bucket->getCreateDate();
        }
        return ['msg' => 'success', 'list' => $list];
    }


    /**
     * 获取存储空间详细信息
     */
    public function getBucketDetail()
    {
        try {
            // 获取存储空间的信息，包括存储空间名称（Name）、所在地域（Location）、创建日期（CreateDate）、存储类型（StorageClass）、外网访问域名（ExtranetEndpoint）以及内网访问域名（IntranetEndpoint）等。
            $info =  $this->ossClient->getBucketInfo($this->bucket);
            printf("bucket name:%s\n", $info->getName());
            printf("bucket location:%s\n", $info->getLocation());
            printf("bucket creation time:%s\n", $info->getCreateDate());
            printf("bucket storage class:%s\n", $info->getStorageClass());
            printf("bucket extranet endpoint:%s\n", $info->getExtranetEndpoint());
            printf("bucket intranet endpoint:%s\n", $info->getIntranetEndpoint());
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 设置请求者付费模式
     */

    public function setPaymentMode()
    {
        try {
            // 设置请求者付费模式。
            $this->ossClient->putBucketRequestPayment($this->bucket, "Requester");
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 获取请求者付费模式配置
     */
    public function getPaymentConfig()
    {
        try {
            // 获取请求者付费模式配置。
            $payer = $this->ossClient->getBucketRequestPayment($this->bucket);
            // 打印结果。
            print($payer);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 第三方付费访问object
     */
    public function getThirdParty()
    {
        $object = "PutObject";
        // 指定为请求者付费模式。
        $options = array(
            OssClient::OSS_HEADERS => array(
                OssClient::OSS_REQUEST_PAYER => 'requester',
            ));

        try {
            // PutObject接口指定付费者。
            $content = "hello";
            $this->ossClient->putObject($this->bucket, $object, $content, $options);

            // GetObject接口指定付费者。
            $this->ossClient->getObject($this->bucket, $object, $options);

            // DeleteObject接口指定付费者。
            $this->ossClient->deleteObject($this->bucket, $object, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 设置防盗链
     */
    public function setPreventingHotlinking()
    {
//        $refererConfig = new RefererConfig();
//        // 设置允许空Referer。
//        $refererConfig->setAllowEmptyReferer(true);
//        // 添加Referer白名单。Referer参数支持通配符星号（*）和问号（？）。
//        $refererConfig->addReferer("www.aliiyun.com");
//        $refererConfig->addReferer("www.aliiyuncs.com");
//        try{
//            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
//
//            $ossClient->putBucketReferer($this->bucket, $refererConfig);
//        } catch(OssException $e) {
//            printf(__FUNCTION__ . ": FAILED\n");
//            printf($e->getMessage() . "\n");
//            return;
//        }
//        print(__FUNCTION__ . ": OK" . "\n");
    }







}
