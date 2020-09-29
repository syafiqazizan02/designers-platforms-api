<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ModelNotDefined;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        // return json expception for incorrect route
         if($exception instanceof NotFoundHttpException){
            if($request->expectsJson()){
                return response()->json(["errors" => [
                    "message" => "Page not found 404. Incorrect route!"
                ]], 404);
            }
        }

        // return json expception for if user not authorize
         if($exception instanceof AuthorizationException){
            if($request->expectsJson()){
                return response()->json(["errors" => [
                    "message" => "You are not authorized to access this resource"
                ]], 403);
            }
        }

        // return json expception for data not found in database
        if($exception instanceof ModelNotFoundException && $request->expectsJson()){
            return response()->json(["errors" => [
                "message" => "The resource was not found in the database"
            ]], 404);
        }

        // return json expception for model not defined in repository
        if($exception instanceof ModelNotDefined && $request->expectsJson()){
            return response()->json(["errors" => [
                "message" => "No models defined on repository"
            ]], 500);
        }


        return parent::render($request, $exception);
    }
}
