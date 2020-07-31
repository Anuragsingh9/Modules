<?php

namespace Modules\Cocktail\Services;

use App\Services\Service;
use Illuminate\Support\Facades\Storage;

class KctService extends Service {
    
    public function uploadFile($file, $for) {
        // if there is a config exists for which we have path
        // then put it into $path variable and proceed further
        if ($path = config('cocktail.s3.' . $for)) {
            $path .= '/'. time(). '.' . $file->getClientOriginalExtension(); // put the file name se time and the extension came originally
            $s3 = Storage::disk('s3');
            $s3->put('/' . $path, file_get_contents($file), 'public');
            return $s3->url($path);
        }
    }
    
}