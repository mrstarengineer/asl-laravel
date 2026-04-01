<?php


namespace App\Supports;



use App\Exceptions\BaseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;

class ApiJsonResponse extends ResponseFactory
{
    protected $code = 200;
    protected $message;
    protected $data;
    protected $details = '';
    protected $headers = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function setHeader(string $key, string $value) : self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function details(string $data = '') : self
    {
        $this->details = $data;

        return $this;
    }

    public function setHeaders(array $headers) : self
    {
        foreach($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this;
    }

    public function success(string $message = '', int $code = Response::HTTP_OK) : JsonResponse
    {

        $this->code = $code;
        $this->message = $message;
        $response = [
            'status' => 'SUCCESS',
            'code' => $this->code,
            'message' => $message,
            'details' => $this->details,
            'data' => $this->data
        ];

        return $this->json($response, $this->code, $this->headers);
    }

    public function fails(string $message = '', int $code = Response::HTTP_BAD_REQUEST) : JsonResponse
    {

        $this->code = $code;
        $this->message = $message;
        $response = [
            'status' => 'ERROR',
            'code' => $this->code,
            'message' => $message,
            'details' => $this->details,
            'data' => $this->data
        ];

        return $this->json($response, $this->code, $this->headers);
    }

    /**
     * @param $exception
     * @param mixed ...$params
     * @throws \Throwable
     */
    public function throw($exception, ... $params) : void
    {
        if ($exception instanceof \Exception) {
            throw $exception;
        }

        throw_if(class_exists($exception), $exception, ... $params);
    }

    public function exception(\Exception $exception) : JsonResponse
    {
        $code = Response::HTTP_BAD_REQUEST;
        $data = "Something went wrong";
        $details = 'System throw an exception : ' . get_class($exception);
        $message = is_object($exception) ? get_class($exception) : "EXCEPTION_THROWN";

        if (is_object($exception)) {
            $code = (int) $exception->getCode();
            if ($code < 100 || $code > 600) {
                $code = Response::HTTP_BAD_REQUEST;
            }

            $data = get_class($exception) . ' | Something went wrong';
            $message = $exception->getMessage();
            $message = blank($message) ? $data : $message;
        }


        if (app()->environment() != 'production') {
            $data = $exception->getTraceAsString();
        }

        if ($exception instanceof BaseException) {
            $details = $exception->getDetails();
        }

        if ($exception instanceof ValidationException) {
            $data = $exception->validator->errors();
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        $this->data = $data;

        return $this->details($details)->fails($message, $code);
    }
}
