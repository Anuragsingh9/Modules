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
}
