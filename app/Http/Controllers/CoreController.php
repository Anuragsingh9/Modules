<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoreController extends Controller
{
    public static function getInstance() {

        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    public function fileUploadToS3($blob)
    {
        $path = config('newsletter.s3.news_image');
        $fileName = time() . '.' . $blob->getClientOriginalExtension();
        $path=Storage::disk('s3')
            ->putFileAs($path, $blob, $fileName, 'public'); // to put the file on specific path with custom name,
        return $path;
    }

    public function getS3Parameter($mediaUrl)
    {
        $file_path=$mediaUrl;
        $type='';
        $file_name='abc';
        $url = '';
        $config['Bucket'] = env('AWS_BUCKET');
        $config['Key'] = $file_path;
        $s3 = Storage::disk('s3');
        if ($s3->exists($file_path)) {
            if ($type == 1) {
                if ($file_name != NULL) {
                    $config['ResponseContentDisposition'] = 'attachment;filename="' . $file_name . '"';
                } else {
                    $config['ResponseContentDisposition'] = 'attachment';
                }
                $command = $s3->getDriver()->getAdapter()->getClient()->getCommand('GetObject', $config);
                $requestData = $s3->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+5 minutes');
                $url = $requestData->getUri();
                return (string)$url;
            } else {
                return Storage::disk('s3')->url($file_path);
            }
        }

        return NULL;
    }
}
