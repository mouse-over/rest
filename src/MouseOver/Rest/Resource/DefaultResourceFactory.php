<?php
namespace MouseOver\Rest\Resource;


/**
 * DefaultResourceFactory
 *
 * @package Drahak\Restful
 * @author  Drahomír Hanák
 */
class DefaultResourceFactory implements IResourceFactory
{

    use \Nette\SmartObject;

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
