<?php

namespace Modules\Newsletter\Exceptions;

use Exception;

class CustomValidationException extends Exception
{
    /**
     * @param $request
     * @param Exception $exception
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof CustomValidationException)  {
            return $exception->render($request);
        }
        return parent::render($request, $exception);
    }
}
