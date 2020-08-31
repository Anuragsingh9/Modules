<?php

namespace Modules\Newsletter\Http\Middleware;

use App\AccountSetting;
use Closure;
use Illuminate\Http\Request;
use Modules\Newsletter\Exceptions\CustomValidationException;

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
        if(isset($enable->setting['news_moderation']) && isset($enable->setting['newsletter_menu_enable'])
            && $enable->setting['news_moderation'] == 1 && $enable->setting['newsletter_menu_enable'] == 1 )
        {
            return $next($request);
        }
        return response()->json(['status' => FALSE, 'msg' => 'Unauthorized Action','error'=> __('auth')],401);
    }
}
