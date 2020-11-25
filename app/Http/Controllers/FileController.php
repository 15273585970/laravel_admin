<?php

namespace App\Http\Controllers;

use App\Services\OSS;
use hanneskod\classtools\Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Service\UploadService;
use OSS\Core\OssException;
use OSS\Core\OssUtil;
use OSS\Model\ServerSideEncryptionConfig;
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
        $this->bucket = 'zhihao-lthink-resource';
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


    /**
     * 追加上传字符串
     */
    public function appendSploadString()
    {
        // 设置文件名称。
        $object = "testName";
        // 获取文件内容。
        $content_array = array('Hello OSS', 'Hi OSS', 'OSS OK');
        try{
            // 第一次追加上传。第一次追加的位置是0，返回值为下一次追加的位置。后续追加的位置是追加前文件的长度。
            $position = $this->ossClient->appendObject($this->bucket, $object, $content_array[0], 0);
            $position = $this->ossClient->appendObject($this->bucket, $object, $content_array[1], $position);
            $position = $this->ossClient->appendObject($this->bucket, $object, $content_array[2], $position);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 追加上传文件
     *
     * 会把多个文件里面的content 合并到  一个问价content
     */
    public function appendUploadFile()
    {
        // 设置文件名称。
        $object = "addend_upload_file";
        // 获取本地文件1。
        $filePath = "D:\project\yx_b2b_app\/test.txt";
        // 获取本地文件2。
        $filePath1 = "D:\project\yx_b2b_app\/test\/test2.txt";
        try{
            $position = $this->ossClient->appendFile($this->bucket, $object, $filePath, 0);
            $position = $this->ossClient->appendFile($this->bucket, $object, $filePath1, $position);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 分片上传
     */
    public function ShardToUpload()
    {
        $bucket= $this->bucket;
        $object = "测试分片上传";
        $uploadFile = "D:\software\/navcate12";

        /**
         *  步骤1：初始化一个分片上传事件，获取uploadId。
         */
        try{
            $ossClient = $this->ossClient;

            //返回uploadId。uploadId是分片上传事件的唯一标识，您可以根据uploadId发起相关的操作，如取消分片上传、查询分片上传等。
            $uploadId = $ossClient->initiateMultipartUpload($bucket, $object);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": initiateMultipartUpload FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": initiateMultipartUpload OK" . "\n");
        /*
         * 步骤2：上传分片。
         */
        $partSize = 10 * 1024 * 1024;
        $uploadFileSize = filesize($uploadFile);
        $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
        $responseUploadPart = array();
        $uploadPosition = 0;
        $isCheckMd5 = true;
        foreach ($pieces as $i => $piece) {
            $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
            $upOptions = array(
                // 上传文件。
                $ossClient::OSS_FILE_UPLOAD => $uploadFile,
                // 设置分片号。
                $ossClient::OSS_PART_NUM => ($i + 1),
                // 指定分片上传起始位置。
                $ossClient::OSS_SEEK_TO => $fromPos,
                // 指定文件长度。
                $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
                // 是否开启MD5校验，true为开启。
                $ossClient::OSS_CHECK_MD5 => $isCheckMd5,
            );
            // 开启MD5校验。
            if ($isCheckMd5) {
                $contentMd5 = OssUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
                $upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
            }
            try {
                // 上传分片。
                $responseUploadPart[] = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
            } catch(OssException $e) {
                printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
                printf($e->getMessage() . "\n");
                return;
            }
            printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
        }
        // $uploadParts是由每个分片的ETag和分片号（PartNumber）组成的数组。
        $uploadParts = array();
        foreach ($responseUploadPart as $i => $eTag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $eTag,
            );
        }
        /**
         * 步骤3：完成上传。
         */
        try {
            // 执行completeMultipartUpload操作时，需要提供所有有效的$uploadParts。OSS收到提交的$uploadParts后，会逐一验证每个分片的有效性。当所有的数据分片验证通过后，OSS将把这些分片组合成一个完整的文件。
            $ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
        }  catch(OssException $e) {
            printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
    }


    /**
     * 分片本地上传
     */
    public function ShardToUploadLocal()
    {

        $object = "test.php";
        $file = "C:\Users\Administrator\Desktop\/test.php";
        $file = iconv('utf-8', 'gbk//ignore', $file);
        $options = array(
            OssClient::OSS_CHECK_MD5 => true,
            OssClient::OSS_PART_SIZE => 1,
        );
        try{
            $this->ossClient->multiuploadFile($this->bucket, $object, $file, $options);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ":  OK" . "\n");
    }


    /**
     * 分片上传目录
     */
    public function ShardToUploadDir()
    {
        $bucket= $this->bucket;
        $localDirectory = "C:\Windows\Web\Screen";
        $prefix = "samples/codes";
        try {
            $this->ossClient->uploadDir($bucket, $prefix, $localDirectory);
        }  catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ":  OK" . "\n");
    }


    /**
     * 已经分片上传列表
     */
    public function getAlreadyShardUpload()
    {
        $bucket= $this->bucket;
        $object = "已经上传分片列表";
        $uploadId = "";

        try{
            $listPartsInfo = $this->ossClient->listParts($bucket, $object, $uploadId);
            foreach ($listPartsInfo->getListPart() as $partInfo) {
                print($partInfo->getPartNumber() . "\t" . $partInfo->getSize() . "\t" . $partInfo->getETag() . "\t" . $partInfo->getLastModified() . "\n");
            }
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 下载文件到本地
     */
    public function dwLocalFile()
    {
        $object = "samples/codes/img101.png";
// 获取0~4字节（包括0和4），共5个字节的数据。如果指定的范围无效（比如开始或结束位置的指定值为负数，或指定值大于文件大小），则下载整个文件。
        $options = array(OssClient::OSS_RANGE => '0-4');
        try {
            $content = $this->ossClient->getObject($this->bucket, $object, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print ($content);
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 批量下载文件
     */
    public function batchDownloadFile()
    {
        $object = "samples/codes/img101.png.copy";
// 获取0~4字节（包括0和4），共5个字节的数据。如果指定的范围无效（比如开始或结束位置的指定值为负数，或指定值大于文件大小），则下载整个文件。
        $options = array(OssClient::OSS_RANGE => '0-4');
        try{
            $content = $this->ossClient->getObject($this->bucket, $object, $options);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print ($content);
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 判断指定的文件是否存在
     */
    public function isFile()
    {
        $bucket = "<yourBucketName>";
        $object = "samples/codes/img100.jpg";

        try {
            $exist = $this->ossClient->doesObjectExist($this->bucket, $object);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
        var_dump($exist);
    }


    /**
     * 管理文件访问权限  / 获取文件访问权限
     */
    public function managementFileAccess()
    {
        $object = "samples/codes/img100.jpg";
        // 设置文件的访问权限为公共读，默认为继承Bucket。
        $acl = "public-read";
        try {
            //  $this->ossClient->putObjectAcl($this->bucket, $object, $acl);
            $row = $this->ossClient->getObjectAcl($this->bucket, $object);
            dd($row);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 管理元文件信息
     *  : 设置文件元信息
     */
    public function managementMetafileInfo()
    {

// yourObjectName表示想要设置文件元信息的Object所在存储空间的完整名称，即包含文件后缀在内的完整路径，如填写为example/test.jpg。
        $object = "samples/codes/img100.jpg";
        $content = file_get_contents(__FILE__);
        $options = array(
            OssClient::OSS_HEADERS => array(
                'Expires' => '2012-10-01 08:00:00',
                'Content-Disposition' => 'attachment; filename="xxxxxx"',
                'x-oss-meta-self-define-title' => 'user define meta info',
            ));
        try {
            $this->ossClient->putObject($this->bucket, $object, $content, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 列举所有文件
     */
    public function getBucketFileList()
    {
        $nextMarker = '';

        while (true) {
            try {
                $options = array(
                    'delimiter' => '',
                    'marker' => $nextMarker,
                );
                $listObjectInfo = $this->ossClient->listObjects($this->bucket, $options);
            } catch (OssException $e) {
                printf(__FUNCTION__ . ": FAILED\n");
                printf($e->getMessage() . "\n");
                return;
            }
            // 得到nextMarker，从上一次listObjects读到的最后一个文件的下一个文件开始继续获取文件列表。
            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();
            $listPrefix = $listObjectInfo->getPrefixList();

            if (!empty($listObject)) {
                print("objectList:\n");
                foreach ($listObject as $objectInfo) {
                    print($objectInfo->getKey() . "\n");
                }
            }
            if (!empty($listPrefix)) {
                print("prefixList: \n");
                foreach ($listPrefix as $prefixInfo) {
                    print($prefixInfo->getPrefix() . "\n");
                }
            }
            if ($listObjectInfo->getIsTruncated() !== "true") {
                break;
            }
        }
    }

    /**
     * 删除文件
     */
    public function delFile()
    {
        $object = "samples/codes/img100.jpg";

        try{
            $this->ossClient->deleteObject($this->bucket, $object);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }

    /**
     * 拷贝文件
     */
    public function copyBucket()
    {
        //被复制的bucket
        $from_bucket = $this->bucket;
        //文件地址
        $from_object = "samples/codes/img101.png";


        //复制的地方
        $to_bucket = "zhihao-lthink-resource";
        //重新命名
        $to_object = $from_object . '.copy';
        try{

            $this->ossClient->copyObject($from_bucket, $from_object, $to_bucket, $to_object);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }





    /**
     * 创建软链接
     */
    public function createSoftLinks()
    {

        dd( strtoupper(md5(uniqid(mt_rand(), true))) );
        //http://zhihao-lthink-resource.oss-cn-beijing.aliyuncs.com/Y7hyWHgDprpRwF9mIH1SBxCviuOKH1E7pkiiZZhv.jpeg
        //"http://zhihao-lthink-resource-test.oss-cn-beijing.aliyuncs.com/qqq?symlink"
        $object = "samples/codes/img101.png";
        $symlink = "ceshi";
        try {
            $row =  $this->ossClient->putSymlink($this->bucket, $symlink, $object);
            dd( $row );
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 创建软链接
     */
    public function createSoftConnection()
    {
        $object = "samples/codes/img101.png.copy";
        $symlink = "ceshi";
        try {
            $row = $this->ossClient->putSymlink($this->bucket, $symlink, $object);
            dd($row);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 管理版本控制
     *  : 列举出bucket所有object的版本信息
     */
    public function managedVersionControl()
    {
        $bucket = $this->bucket;

        $ossClient = $this->ossClient;

        try {

            $maxKey = 100;
            $nextKeyMarker = '';
            $nextVersionIdMarker = '';

            while (true) {
                $options = array(
                    'delimiter' => '',
                    'key-marker' => $nextKeyMarker,
                    'max-keys' => $maxKey,
                    'version-id-marker' => $nextVersionIdMarker,
                );
                $result = $ossClient->listObjectVersions($bucket, $options);

                $nextKeyMarker = $result->getNextKeyMarker();
                $nextVersionIdMarker = $result->getNextVersionIdMarker();

                $objectList = $result->getObjectVersionList();
                $deleteMarkerList = $result->getDeleteMarkerList();

                // 打印Object版本信息。
                if (!empty($objectList)) {
                    print("objectList:\n");
                    foreach ($objectList as $objectInfo) {
                        print($objectInfo->getKey() . ",");
                        print($objectInfo->getVersionId() . ",");
                        print($objectInfo->getLastModified() . ",");
                        print($objectInfo->getETag() . ",");
                        print($objectInfo->getSize() . ",");
                        print($objectInfo->getIsLatest() . "\n");
                    }
                }

                // 打印删除标记版本信息。
                if (!empty($deleteMarkerList)) {
                    print("deleteMarkerList: \n");
                    foreach ($deleteMarkerList as $deleteMarkerInfo) {
                        print($deleteMarkerInfo->getKey() . ",");
                        print($deleteMarkerInfo->getVersionId() . ",");
                        print($deleteMarkerInfo->getLastModified() . ",");
                        print($deleteMarkerInfo->getIsLatest() . "\n");
                    }
                }

                if ($result->getIsTruncated() !== "true") {
                    break;
                }
            }
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 版本控制 上传简单文件
     */
    public function controlerPullFille()
    {
        $object = "D:\project\laravel_admin\resources\views\uploadfile.blade.php";
        $content = "hello world";
        try {
            // 在受版本控制的Bucket中上传Object。
            $ret = $this->ossClient->putObject($this->bucket, $object, $content);
            // 查看Object的版本信息。
            print("versionId:" . $ret[OssClient::OSS_HEADER_VERSION_ID]);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 版本控制 分片上传文件
     */
    public function controlerPullShardFile()
    {
        $bucket = $this->bucket;
        $object = "C:\Users\Administrator\Downloads\GitKrakenSetup-6.5.1.exe";
        // 填写本地文件的完整路径。
        $uploadFile = "C:\Users\Administrator\Downloads\GitKrakenSetup-6.5.1.exe";

        /**
         *  步骤1：初始化一个分片上传事件，获取uploadId。
         */
        try {
            $ossClient = $this->ossClient;

            // 返回uploadId。uploadId是分片上传事件的唯一标识。您可以根据uploadId发起相关的操作，如取消分片上传、查询分片上传等。
            $uploadId = $ossClient->initiateMultipartUpload($bucket, $object);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": initiateMultipartUpload FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": initiateMultipartUpload OK" . "\n");
        /*
         * 步骤2：上传分片。
         */
        $partSize = 10 * 1024 * 1024;
        $uploadFileSize = filesize($uploadFile);
        $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
        $responseUploadPart = array();
        $uploadPosition = 0;
        $isCheckMd5 = true;
        foreach ($pieces as $i => $piece) {
            $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
            $upOptions = array(
                // 上传文件。
                $ossClient::OSS_FILE_UPLOAD => $uploadFile,
                // 设置分片号。
                $ossClient::OSS_PART_NUM => ($i + 1),
                // 指定分片上传起始位置。
                $ossClient::OSS_SEEK_TO => $fromPos,
                // 指定文件长度。
                $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
                // 是否开启MD5校验，true表示开启，false表示未开启。
                $ossClient::OSS_CHECK_MD5 => $isCheckMd5,
            );
            // 开启MD5校验。
            if ($isCheckMd5) {
                $contentMd5 = OssUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
                $upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
            }
            try {
                // 上传分片。
                $responseUploadPart[] = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
            } catch (OssException $e) {
                printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
                printf($e->getMessage() . "\n");
                return;
            }
            printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
        }
        // $uploadParts是由每个分片的ETag和分片号（PartNumber）组成的数组。
        $uploadParts = array();
        foreach ($responseUploadPart as $i => $eTag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $eTag,
            );
        }
        /**
         * 步骤3：完成上传。
         */
        try {
            // 执行completeMultipartUpload操作时，需要提供所有有效的$uploadParts。OSS收到提交的$uploadParts后，会逐一验证每个分片的有效性。当所有的数据分片验证通过后，OSS将把这些分片组合成一个完整的文件。
            $ret = $ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
            // 查看Object版本信息。
            print("versionId:" . $ret[OssClient::OSS_HEADER_VERSION_ID]);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
    }


    /**
     * 追加上传
     */
    public function additionalUpload()
    {
        $bucket = $this->bucket;
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";
        // 表示第一次、第二次以及第三次追加上传后获取的文件内容分别为Hello OSS、Hi OSS以及OSS OK。
        $content_array = array('Hello OSS', 'Hi OSS', 'OSS OK');
        try {
            $ossClient = $this->ossClient;
            // 第一次追加上传。第一次追加的位置是0，返回值为下一次追加的位置。后续追加的位置是追加前文件的长度。
            $position = $ossClient->appendObject($bucket, $object, $content_array[0], 0);
            $position = $ossClient->appendObject($bucket, $object, $content_array[1], $position);
            $position = $ossClient->appendObject($bucket, $object, $content_array[2], $position);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 上传对象文件 添加标签
     */
    public function addObjecFileLabel()
    {
        // yourObjectName表示即将上传的Object所在存储空间的完整名称，即包含文件后缀在内的完整路径，如填写为example/test.jpg。
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";
        $content = "hello world";
        $ossClient = $this->ossClient;

        // 设置对象标签。
        $options = array(
            OssClient::OSS_HEADERS => array(
                'x-oss-tagging' => 'key1=value1&key2=value2&key3=value3',
            ));

        try {
            // 通过简单上传的方式上传Object。
            $ossClient->putObject($this->bucket, $object, $content, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 获取对象文件标签名称
     */
    public function getObjecFileLabel()
    {
        // 填写Object所在存储空间的完整名称，即包含文件后缀在内的完整路径，例如example/test.jpg。
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";
        try {
            // 获取对象标签。
            $config = $this->ossClient->getObjectTagging($this->bucket, $object);
            dd($config);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 删除对象标签
     */
    public function delObjectTab()
    {
        // 填写Object所在存储空间的完整名称，即包含文件后缀在内的完整路径，例如example/test.jpg。
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";
        try {
            // 删除对象标签。
            $this->ossClient->deleteObjectTagging($this->bucket, $object);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 单链接限速
     */
    public function singleLinkSpeedLimit()
    {
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";
        $content = "hello world";

        $ossClient = $this->ossClient;

        // 限速100KB/s，即819200 bit/s。
        $options = array(
            OssClient::OSS_HEADERS => array(
                OssClient::OSS_TRAFFIC_LIMIT => 819200,
            ));

        try {
            // 限速上传。
            $ossClient->putObject($this->bucket, $object, $content, $options);

            // 限速下载。
            $row = $ossClient->getObject($this->bucket, $object, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }


    /**
     * 签民url方式 限速 下载
     */
    public function subscribeUrlWayToDownload()
    {
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";

        $ossClient = $this->ossClient;

        // 限速100 KB/s，即819200 bit/s。
        $options = array(
            OssClient::OSS_TRAFFIC_LIMIT => 819200,
        );

        // 创建限速上传的URL，有效期为60s。
        $timeout = 60;
        $signedUrl = $ossClient->signUrl($this->bucket, $object, $timeout, "PUT", $options);
        print($signedUrl);

        // 创建限速下载的URL，有效期为120s。
        $timeout = 120;
        $signedUrl = $ossClient->signUrl($this->bucket, $object, $timeout, "GET", $options);
        print($signedUrl);
    }


    /**
     *数据加密
     */
    public function dataEncryption()
    {
        $bucket = $this->bucket;

        $ossClient = $this->ossClient;

        try {
            // 将Bucket默认的服务器端加密方式设置为OSS完全托管加密（SSE-OSS）。
            $config = new ServerSideEncryptionConfig("AES256");
            $ossClient->putBucketEncryption($bucket, $config);

            // 将Bucket默认的服务器端加密方式设置为KMS，且不指定CMK ID。
            $config = new ServerSideEncryptionConfig("KMS");
            $ossClient->putBucketEncryption($bucket, $config);

            // 将Bucket默认的服务器端加密方式设置为KMS，且指定了CMK ID。
            $config = new ServerSideEncryptionConfig("KMS", "your kms id");
            $ossClient->putBucketEncryption($bucket, $config);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        print(__FUNCTION__ . ": OK" . "\n");
    }

    /**
     * sts 临时授权访问
     */
    public function stsTemporaryAuthorization()
    {
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";
        $securityToken = $this->accesskeySecret;

        $content = "Hi, OSS.";

        try {
            // 使用STS临时授权上传文件。
            $this->ossClient->putObject($this->bucket, $object, $content);
        } catch (OssException $e) {
            print $e->getMessage();
        }

    }


    /**
     * 图片处理
     */
    public function imageProcess()
    {
        $object = "C:\Users\Administrator\Desktop\大头像.jpg";
        // 指定处理后的图片名称。
        $download_file = "处理后的图片";

        // 若目标图片不在指定Bucket内，需上传图片到目标Bucket。
        // $ossClient->uploadFile($bucket, $object, "<yourLocalFile>");

        // 将图片缩放为固定宽高100 px，并保存在本地。
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $download_file,
            OssClient::OSS_PROCESS => "image/resize,m_fixed,h_100,w_100" );
        $this->ossClient->getObject($this->bucket, $object, $options);
    }



    public function ceshiData()
    {
        echo "ceshi";
    }










}
