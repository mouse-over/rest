<?php

namespace MouseOver\Rest\Validation;

use MouseOver\Rest\BadRequestException;


/**
 * Class ValidationFailedException
 * @package MouseOver\Rest\Validation
 */
class ValidationFailedException extends BadRequestException
{
    public function __construct($message, $errors)
    {
        $this->errors = $errors;
        parent::__construct($message, 400, null);
    }

}