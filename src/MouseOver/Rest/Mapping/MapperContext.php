<?php
namespace MouseOver\Rest\Mapping;

use Nette\Utils\Strings;
use MouseOver\Rest\InvalidStateException;

/**
 * MapperContext
 *
 * @package MouseOver\Rest\Mapping
 * @author  Drahomír Hanák
 */
class MapperContext
{
    use \Nette\SmartObject;

    /** @var array */
    protected $services = array();

    /**
     * Add mapper
     *
     * @param string  $contentType
     * @param IMapper $mapper
     */
    public function addMapper($contentType, IMapper $mapper)
    {
        $this->services[$contentType] = $mapper;
    }

    /**
     * Get mapper
     *
     * @param string $contentType in format mimeType[; charset=utf8]
     *
     * @return IMapper
     *
     * @throws InvalidStateException
     */
    public function getMapper($contentType)
    {
        $contentType = explode(';', $contentType);
        $contentType = Strings::trim($contentType[0]);
        if (!isset($this->services[$contentType])) {
            throw new InvalidStateException('There is no mapper for Content-Type: ' . $contentType);
        }
        return $this->services[$contentType];
    }

}
