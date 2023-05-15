<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Router;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use Throwable;

class Handler extends ExceptionHandler
{

    use ApiResponser;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {

        $error = $e->validator->errors()->getMessages();
        if($this->isFrontend($request)){
            return $request->ajax() ? response()->json($error, 422) : redirect()->back()->withInput($request->input())->withErrors($error);
        }
        if ($e->response) {
            return $e->response;
        }

        return $this->invalidJson($request, $e);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return $this->errorResponse($exception->errors(), $exception->status);
        /*return response()->json([
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);*/
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
            if(config('app.debug')){

                return parent::render($request, $e);

            } else {

                if (method_exists($e, 'render') && $response = $e->render($request)) {
                    return Router::toResponse($request, $response);
                }

                if ($e instanceof Responsable) {
                    return $e->toResponse($request);
                }

                $e = $this->prepareException($this->mapException($e));

                if ($e instanceof TokenMismatchException){
                    return redirect()->back()->withInput($request->input());
                }

                if ($e instanceof NotFoundHttpException){
                    return $this->errorResponse('Url Not Found', $e->getStatusCode());
                }

                if ($e instanceof AuthorizationException){
                    return $this->errorResponse($e->getMessage(), $e->getStatusCode());
                }

                if ($e instanceof MethodNotAllowedHttpException){
                    return $this->errorResponse('Method invalid', $e->getStatusCode());
                }

                if ($e instanceof HttpException){
                    return $this->errorResponse($e->getMessage(), $e->getStatusCode());
                }

                if ($e instanceof AuthenticationException){
                    $this->unauthenticated($request, $e);
                }

                if ($e instanceof ValidationException){
                    $this->convertValidationExceptionToResponse($e, $request);
                }

                if ($e instanceof QueryException){
                    $errorCode = $e->errorInfo[1];

                    if($errorCode == 1451){
                        return $this->errorResponse('Cannot remove this resource permantly. It is related with any other resource', 409);
                    }
                }

                if($e instanceof AuthenticationException) {
                    return $this->unauthenticated($request, $e);
                }

                if(config('app.debug')){
                    parent::render($request, $e);
                }
                return $this->errorResponse('Unexpected Exception. Please try it later', 500);

                if ($response = $this->renderViaCallbacks($request, $e)) {
                    return $response;
                }

                return match (true) {
                    $e instanceof HttpResponseException => $e->getResponse(),
                    $e instanceof AuthenticationException => $this->unauthenticated($request, $e),
                    $e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
                    default => $this->renderExceptionResponse($request, $e),
                };
            }

    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($this->isFrontend($request)){
            return redirect()->guest('login');
        }
        return $this->errorResponse('Unauthenticated',$exception->getCode());
    }

    public function isFrontend($request){
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
