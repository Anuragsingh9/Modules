<?php

namespace Modules\Cocktail\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Http\Requests\UploadFileRequest;
use Exception;
use Modules\Cocktail\Services\KctService;

/*
        try {
            DB::connection('tenant')->beginTransaction();
    
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 200);
        }

*/
class KctController extends Controller {

    private $service;
    public function __construct() {
        $this->service = KctService::getInstance();
    }
    
    public function upload(Request $request) {
        try {
            return $this->service->uploadFile($request->upload_file, $request->for);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 200);
        }
    }

    public function updated(Request $request) { // update  news
        try {
            DB::beginTransaction();// to provide the tenant environment and transaction will only apply to model which extends tenant model
            $param = [
                'fname'       => $request->fname,
                'lname'      => $request->lname,
                'email' => $request->email,
            ];
            if ($request->has('avatar')) { // if update news has media then  $params will be prepared
                $params = [
                    'request_avatar' => $request->has('avatar') ? $request->avatar : null,
                ];
            }
            if (isset($params)) { // if has media then it will merge the two array
                $param = array_merge($param, $params); // if update has media then merging media $params with $param
            }
            $event = $this->service->updated($param);
            return $event;
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }


}
