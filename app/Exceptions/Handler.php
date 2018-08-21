<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use src\ApiHelper\ApiResponse;
use src\Exceptions\UnAuthorizedException;
use Util\Logger;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return string
     * @throws UnAuthorizedException
     */
    public function render($request, Exception $exception)
    {
        $errArr = [
            'status' => $exception->getCode(),
            'msg' => $exception->getMessage(),
            'fileName' => $exception->getFile(),
            'line' => $exception->getLine(),
            'url' => $request->fullUrl(),
            'params' => $request->all(),
        ];
        if ($exception->getMessage() == 'Unauthenticated.'){
            throw new UnAuthorizedException();
        }
        Logger::fatal(json_encode($errArr));
        return ApiResponse::response($exception->getCode(),$exception->getMessage());
    }
}
