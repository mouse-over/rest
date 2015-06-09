<?php
namespace MouseOver\Rest\Mapping;

/**
 * Resource data mapper interface
 *
 * @package MouseOver\Rest\Mapping
 * @author  Drahomír Hanák
 */
interface IMapper
{

    /**
     * Convert array or Traversable input to string output response
     *
     * @param array|\Traversable $data
     * @param bool               $prettyPrint
     *
     * @return mixed
     *
     */
    public function stringify($data, $prettyPrint = TRUE);

    /**
     * Convert client request data to array or traversable
     *
     * @param mixed $data
     *
     * @return array|\Traversable
     *
     * @throws MappingException
     */
    public function parse($data);

}