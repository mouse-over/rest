<?php
namespace MouseOver\Rest\Resource;

use Nette\Object;

/**
 * DefaultResourceFactory
 *
 * @package Drahak\Restful
 * @author  Drahomír Hanák
 */
class DefaultResourceFactory extends Object implements IResourceFactory
{

    /**
     * Create new API resource
     *
     * @param array $data Optional initial resource data
     *
     * @return IResource
     */
    public function create(array $data = array())
    {
        return new Resource($data);
    }

}
