<?php

namespace App\Services;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\CoreController;
use App\Setting;
use Intervention\Image\Image;
use Modules\Newsletter\Http\Controllers\AdobeStockController;

class StockService extends Service {
    /**
     * @var array
     */
    private $headers;
    /**
     * @var AdobeStockController
     */
    private $controller;
    
    /**
     * @var CoreController
     */
    protected $core;
    
    
    public function __construct() {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $settingValue = Setting::where('setting_key', 'adobe_stock_api_setting')->first();
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $value = null;
        if ($settingValue && $settingValue->setting_value) {
            $value = json_decode($settingValue->setting_value);
        }
        $this->headers = [
            'x-api-key: ' . ($value ? $value->access_key : null),
            'x-product: ' . ($value ? $value->app_name : null)
        ];
        $this->controller = app(AdobeStockController::class);
    }

    /**
     * To get the news letter is enabled or not to perform the newsletter related task.
     *
     * @return bool
     */
    public function isNewsletterEnabled() {
        $tenancy = app(\Hyn\Tenancy\Environment::class);
        $setting = AccountSettings::where('account_id', $tenancy->hostname()->id)->first()->setting;
        return isset($setting['news_letter_enable']) && $setting['news_letter_enable'];
    }
    
    public function addFileNameToPath($path, $url) {
        $ext = pathinfo($url)['extension'];
        return "$path/" . md5(time()) . ".$ext";
    }
    
    
    /**
     * To get the adobe stock image and upload it to s3 and return the image url
     * it also have commented to make it possible for resize the image
     *
     * @param $request
     * @param $path
     * @param $visibility
     * @return mixed
     * @throws CustomValidationException
     */
    public function uploadImage($request, $path, $visibility) {
        $imageIsPossibleToUpload = $this->controller->checkImagePossilbeToUpload($request->imageId, $request->search);
        if ($imageIsPossibleToUpload === true) {
            // getting the image url from the stock by using curl
            $imageUrl = $this->getImageUrl($request->imageId);
            // getting the intervention image object to perform resize and crop
            $image = $this->getImageByUrl($imageUrl);
            // as we can't use the putFile with intervention image object so we need to use put and give the path
            // also the path prepared here with filename will gonna store in s3 so this will gonna return
            $path = $this->addFileNameToPath($path, $imageUrl);
            $image = $this->cropImage($request, $image);
            $image = $this->resizeImage($image, $request->width);
            // as we are using put so to be sure image uploaded we use boolean value returned by s3 put method
            if (!$this->uploadImageToS3($image, $path, $visibility)) {
                throw new CustomValidationException(__("cocktail::message.invalid_stock_image"));
            }
            return $path;
        } else {
            // this will throw if account doesn't have the credits left
            throw new CustomValidationException(__("cocktail::message.out_of_credit"));
        }
    }
    
    /**
     * To get the image stock url from the id
     *
     * @param $imageId
     * @return string
     * @throws CustomValidationException
     */
    public function getImageUrl($imageId) {
        $imageUrl = $this->controller->get_image_url_by_id($imageId);
        if ($imageUrl == null || $imageUrl == '') {
            throw new CustomValidationException(__("validation.exists", ['attribute' => 'image id']));
        }
        return $imageUrl;
    }
    
    /**
     * To get the image instance by stock image id
     *
     * @param $imageId
     * @return Image
     * @throws CustomValidationException
     */
    public function getImageByUrl($imageUrl) {
        $image = $this->controller->getImageByUrl($imageUrl);
        if (!$image) {
            throw new CustomValidationException(__("validation.exists", ['attribute' => 'image id']));
        }
        return $image;
    }
    
    /**
     * @param $request
     * @param Image $image
     * @return Image
     */
    public function cropImage($request, $image) {
        if ($request->crop == 1) { // we need to crop only if required from front
            // as this is using for both upload and crop
            $w = (int)$request->w;
            $h = (int)$request->h;
            $x = (int)$request->x;
            $y = (int)$request->y;
            $image->crop($w, $h, $x, $y);
        }
        return $image;
    }
    
    /**
     * to resize the image in provided length with keeping the aspect ration same
     *
     * @param Image $image
     * @param $width
     * @return mixed
     */
    public function resizeImage($image, $width) {
        $image->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        return $image;
    }
    
    /**
     * To return the s3 url of uploaded s3 image
     *
     * @param Image $image
     * @param $path
     * @param $visibility
     * @return string
     */
    public function uploadImageToS3($image, $path, $visibility) {
        $image->stream();
        return $this->core->fileUploadToS3($path, $image->__toString(), $visibility);
    }
}
