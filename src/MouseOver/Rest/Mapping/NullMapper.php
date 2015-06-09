<?php
namespace MouseOver\Rest\Mapping;

use Nette\Object;

/**
 * NullMapper
 *
 * @package MouseOver\Rest\Mapping
 * @author  Drahomír Hanák
 */
class NullMapper extends Object implements IMapper
{

    /**
     * Convert array or Traversable input to string output response
     *
     * @param array|\Traversable $data
     * @param bool               $prettyPrint
     *
     * @return mixed
     */
    public function stringify($data, $prettyPrint = TRUE)
    {
        return $data;
    }

    /**
     * Convert client request data to array or traversable
     *
     * @param mixed $data
     *
     * @return array|\Traversable
     *
     * @throws MappingException
     */
    public function parse($data)
    {
        return $data;
    }


}
