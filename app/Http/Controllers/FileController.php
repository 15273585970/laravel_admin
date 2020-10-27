<?php

namespace App\Http\Controllers;

use App\Services\OSS;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Service\UploadService;
class FileController extends Controller
{
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

    public function read()
    {
        $dir  = "http://".config("filesystems.disks.oss.bucket").config('ad_upload_dir');
        dd( $dir );
        $list =  Storage::directories('test/upload');
        return ['list' => $list];
    }




}
