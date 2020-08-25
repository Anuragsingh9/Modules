<?php

namespace Modules\Newsletter\Http\Middleware;

use App\AccountSetting;
use Closure;
use Illuminate\Http\Request;

class NewsModeration
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $enable=AccountSetting::where('account_id','=',1)->first();
        if(isset($enable->setting['news_moderation'])){
            if($enable->setting['news_moderation'] == 1){
                return $next($request);
            }
        }
        return response()->json(['status' => FALSE, 'msg' => __('newsletter::message.news_moderation_disabled')],401);
    }
}
