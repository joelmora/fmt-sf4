<?php

namespace isoft\fmtsf4\Exceptions;

class ValidationException extends \Exception
{
    protected $messages;

    public function __construct($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
