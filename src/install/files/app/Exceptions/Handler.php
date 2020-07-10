<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            return response()->view('admin::errors.404', [], 404);
        }

        if (($request->is('admin/*') && $request->isXmlHttpRequest())
            || (env('APP_ENV') == 'testing')
        ) {
            if ($e instanceof ValidationException) {
                $data = [
                    'status'  => 'error',
                    'code'    => $e->getCode(),
                    'message' => $this->getErrorValidation($e),
                ];

                return response()->json($data, 422);
            }

            $data = [
                'status'  => 'error',
                'code'    => $e->getCode(),
                'message' => class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage(),
            ];

            return response()->json($data, 500);
        }

        if ($this->isHttpException($e)) {
            return $this->renderHttpException($e);
        }

        return parent::render($request, $e);
    }

    private function getErrorValidation($e)
    {
        $errors = '';
        foreach ($e->errors() as $k => $collection) {
            foreach ($collection as $messageError) {
                $errors .= "{$messageError} <br>";
            }
        }

        return $errors;
    }
}
