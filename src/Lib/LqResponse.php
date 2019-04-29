<?php

namespace Singsys\LQ\Lib;

class LqResponse
{
    public $data = null;
    public $error_code = null;
    public $errors = null;
    public $message =  null;
    public $error =  null;
    public $exception =  null;
    public $trace =  null;
    public $current_permission =  null;

    public function out ($status) {

        return  response()->json([
            'data' => $this->data,
            'error_code' => $this->error_code,
            'errors' => $this->errors,
            'error' => $this->error,
            'message' => $this->message,
            'exception' => $this->exception,
            'trace' => $this->trace,
            'current_permission' => $this->current_permission
        ], $status);
    }
}
