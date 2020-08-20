<?php

namespace Modules\Newsletter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Newsletter\Entities\AccountSetting;

class NewsModeration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
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
