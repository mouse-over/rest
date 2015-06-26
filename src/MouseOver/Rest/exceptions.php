<?php
namespace MouseOver\Rest;

use Nette;

/**
 * Determines usage error
 */
class LogicException extends \LogicException
{
}

/**
 * Thrown when invalid argumnet given to method, function or constructor
 */
class InvalidArgumentException extends LogicException
{
}

/**
 * When requested feature is not implemented
 */
class NotImplementedException extends LogicException
{
}

/**
 * Determines runtime error
 */
class RuntimeException extends \RuntimeException
{
}

/**
 * Thrown when invalid state happend
 */
class InvalidStateException extends RuntimeException
{
}

/**
 * Thrown when crud adapter not found
 */
class CrudAdapterNotFoundException extends InvalidArgumentException
{
}

/**
 * Thrown when crud adapter creation failed
 */
class CrudAdapterCreationException extends InvalidStateException
{
}

/**
 * BadRequestException
 *
 * @package MouseOver\Restful\Application
 * @author  Drahomír Hanák
 *
 * @property array $errors
 */
class BadRequestException extends Nette\Application\BadRequestException
{

    /** @var array Some other errors appear in request */
    public $errors = array();

    /****************** Simple factories ******************/

    /**
     * Is thrown when request is not understood
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function badRequest($message = '', \Exception $previous = NULL)
    {
        return new self($message, 400, $previous);
    }

    /**
     * Is thrown when trying to reach secured resource without authentication
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function unauthorized($message = '', \Exception $previous = NULL)
    {
        return new self($message, 401, $previous);
    }

    /**
     * Is thrown when access to this resource is forbidden
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function forbidden($message = '', \Exception $previous = NULL)
    {
        return new self($message, 403, $previous);
    }

    /**
     * Is thrown when resource's not found
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function notFound($message = '', \Exception $previous = NULL)
    {
        return new self($message, 404, $previous);
    }

    /**
     * Is thrown when request method (e.g. POST or PUT) is not allowed for this resource
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function methodNotSupported($message = '', \Exception $previous = NULL)
    {
        return new self($message, 405, $previous);
    }

    /**
     * Is thrown when this resource is not no longer available (e.g. with new API version)
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function gone($message = '', \Exception $previous = NULL)
    {
        return new self($message, 410, $previous);
    }

    /**
     * Is thrown when incorrect (or unknown) Content-Type was provided in request
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function unsupportedMediaType($message = '', \Exception $previous = NULL)
    {
        return new self($message, 415, $previous);
    }


    /**
     * Is thrown to reject request due to rate limiting
     *
     * @param string     $message
     * @param \Exception $previous
     *
     * @return BadRequestException
     */
    public static function tooManyRequests($message = '', \Exception $previous = NULL)
    {
        return new self($message, 429, $previous);
    }

}
