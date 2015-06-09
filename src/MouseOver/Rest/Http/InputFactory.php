<?php
namespace MouseOver\Rest\Http;

use MouseOver\Rest\Mapping\IMapper;
use MouseOver\Rest\Mapping\MapperContext;
use MouseOver\Rest\Mapping\MappingException;
use MouseOver\Rest\BadRequestException;
use MouseOver\Rest\InvalidStateException;
use Nette\Http\IRequest;


/**
 * Class InputFactory
 *
 * @package MouseOver\Restful\Http
 * @author  Drahomír Hanák
 * @author  Václav Prokeš
 */
class InputFactory implements IInputFactory
{

    /** @var \Nette\Http\IRequest */
    protected $httpRequest;

    /** @var IMapper */
    private $mapper;

    /** @var MapperContext */
    private $mapperContext;

    /**
     * Constructor
     *
     * @param IRequest      $httpRequest   Http request
     * @param MapperContext $mapperContext Mapper
     */
    public function __construct(IRequest $httpRequest, MapperContext $mapperContext)
    {
        $this->httpRequest = $httpRequest;
        $this->mapperContext = $mapperContext;
    }

    /**
     * Create input
     *
     * @return Input
     */
    public function create()
    {
        return new Input($this->parseData());
    }

    /**
     * Parse data for input
     *
     * @return array
     *
     * @throws \MouseOver\Rest\BadRequestException
     */
    protected function parseData()
    {
        $postQuery = (array)$this->httpRequest->getPost();
        $urlQuery = (array)$this->httpRequest->getQuery();
        $requestBody = $this->parseRequestBody();

        return array_merge($urlQuery, $requestBody, $postQuery);
    }

    /**
     * Parse request body if any
     *
     * @return array|\Traversable
     * @throws \MouseOver\Rest\BadRequestException
     */
    protected function parseRequestBody()
    {
        $requestBody = array();
        $input = \Nette\Framework::VERSION_ID >= 20200 ? // Nette 2.2.0 and/or newer
            $this->httpRequest->getRawBody() :
            file_get_contents('php://input');

        if ($input) {
            try {
                $this->mapper = $this->mapperContext->getMapper($this->httpRequest->getHeader('Content-Type'));
                $requestBody = $this->mapper->parse($input);
            } catch (InvalidStateException $e) {
                throw BadRequestException::unsupportedMediaType(
                    'No mapper defined for Content-Type ' . $this->httpRequest->getHeader('Content-Type'),
                    $e
                );
            } catch (MappingException $e) {
                throw new BadRequestException($e->getMessage(), 400, $e);
            }
        }
        return $requestBody;
    }
}