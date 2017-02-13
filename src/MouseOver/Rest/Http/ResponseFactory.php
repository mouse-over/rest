<?php
namespace MouseOver\Rest\Http;

use Nette\Http\IResponse;
use Nette\Http\IRequest;
use Nette\Http\Response;
use Nette\Object;


/**
 * ResponseFactory
 *
 * @package Drahak\Restful\Http
 * @author  Drahomír Hanák
 */
class ResponseFactory extends Object
{

    /** @var array Default response code for each request method */
    protected $defaultCodes = array(
        IRequest::GET => 200,
        IRequest::POST => 201,
        IRequest::PUT => 200,
        IRequest::HEAD => 200,
        IRequest::DELETE => 200,
        'PATCH' => 200,
    );

    /** @var IRequest */
    private $request;

    /** @var IResponse */
    private $response;

    /** @var  array */
    private $headers = [];

    /** @var array  */
    private $allowedOrigin = [];

    /**
     * @param IRequest $request
     */
    public function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    public function addHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param array $allowedOrigin
     */
    public function setAllowedOrigin($allowedOrigin)
    {
        $this->allowedOrigin = $allowedOrigin;
    }


    /**
     * Set original wrapper response since nette does not support custom response codes
     *
     * @param IResponse $response
     *
     * @return ResponseFactory
     */
    public function setResponse(IResponse $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Create HTTP response
     *
     * @param int|NULL $code
     *
     * @return IResponse
     */
    public function createHttpResponse($code = NULL)
    {
        $response = $this->response ? $this->response : new Response();
        $response->setCode($this->getCode($code));
        foreach ($this->headers as $k => $v) {
            $response->addHeader($k, $v);
        }
        if ($this->allowedOrigin) {
            $origin = $this->request->getHeader('origin');
            if ($origin && in_array($origin, $this->allowedOrigin) !== false) {
                 $response->addHeader('Access-Control-Allow-Origin', $origin);
            }
        }
        return $response;
    }

    /**
     * Get default status code
     *
     * @param int|null $code
     *
     * @return null
     */
    protected function getCode($code = NULL)
    {
        if ($code === NULL) {
            $code = $code = isset($this->defaultCodes[$this->request->getMethod()]) ?
                $this->defaultCodes[$this->request->getMethod()] :
                200;
        }
        return (int)$code;
    }

}
