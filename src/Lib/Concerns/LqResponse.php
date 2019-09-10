<?php

namespace Singsys\LQ\Lib\Concerns;

trait LqResponse
{
    public function response($status= 200)
    {
        return app('Lq\Response')->out($status);
    }

    public function setData($data)
    {
        app('Lq\Response')->data = $data;
        return $this;
    }
    public function setErrors($errors)
    {
        app('Lq\Response')->errors = $errors;
        return $this;
    }
    public function setError($error)
    {
        app('Lq\Response')->error = $error;
        return $this;
    }
    public function setErrorCode($error_code)
    {
        app('Lq\Response')->error_code = $error_code;
        return $this;
    }
    public function setMessage($message)
    {
        app('Lq\Response')->message = $message;
        return $this;
    }
}
