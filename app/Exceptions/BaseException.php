<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class BaseException extends \Exception
{
    protected $message = 'SERVICE_FAILED_TO_PROCESS';
    protected $details = 'Service failed to process the request!';
    protected $code = Response::HTTP_BAD_REQUEST;


    /**
     * BaseException constructor.
     * @param null $message
     * @param null $details
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct($message = null, $details = null, $code = Response::HTTP_BAD_REQUEST, \Throwable $previous = null)
    {
        $this->message = $message ?? $this->message;
        $this->details = $details ?? $this->details;
        $this->code = $code ?? $this->code;

        parent::__construct($this->message, $this->code, $previous);
    }

    public function getDetails()
    {
        return $this->details ?? 'System throw an exception : ' . get_class($this);
    }
}
