<?php
namespace MouseOver\Rest\Resource;

/**
 * IResourceFactory
 *
 * @package Drahak\Restful
 * @author  Drahomír Hanák
 */
interface IResourceFactory
{

    /**
     * Create new API resource
     *
     * @param array $data Optional initial resource data
     *
     * @return IResource
     */
    public function create(array $data = array());

}
