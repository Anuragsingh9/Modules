<?php

namespace Modules\Newsletter\Exceptions;

use Exception;

class CustomAuthorizationException extends Exception
{


    /**
     * @param $request
     * @param Exception $exception
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof CustomAuthorizationException)  {
            return $exception->render($request);
        }
        return parent::render($request, $exception);
    }
}
