<?php
namespace MouseOver\Rest\Application\Responses;

use MouseOver;
use MouseOver\Rest\InvalidArgumentException;
use MouseOver\Rest\Mapping\IMapper;
use Nette\Application\IResponse;
use Nette\Http\IRequest;

/**
 * BaseResponse
 *
 * @package MouseOver\Rest\Application\Responses
 * @author  Drahomír Hanák
 *
 * @property-read string   $contentType
 */
abstract class BaseResponse implements IResponse
{

    use \Nette\SmartObject;

    /** @var array|\stdClass|\Traversable */
    protected $data;

    /** @var IMapper */
    protected $mapper;

    /** @var string */
    protected $contentType;

    /** @var boolean */
    private $prettyPrint = TRUE;

    /**
     * @param null    $contentType
     * @param IMapper $mapper
     */
    public function __construct(IMapper $mapper, $contentType = NULL)
    {
        $this->contentType = $contentType;
        $this->mapper = $mapper;
    }

    /**
     * Is pretty print enabled
     *
     * @return bool
     */
    public function isPrettyPrint()
    {
        return $this->prettyPrint;
    }

    /**
     * Set pretty print
     *
     * @param boolean $pretty
     */
    public function setPrettyPrint($pretty)
    {
        $this->prettyPrint = (bool)$pretty;
        return $this;
    }

    /**
     * Get response content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get response data
     *
     * @return array|\stdClass|\Traversable
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set mapper
     *
     * @param IMapper $mapper
     *
     * @return BaseResponse
     */
    public function setMapper(IMapper $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

}
