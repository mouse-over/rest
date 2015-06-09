<?php
namespace MouseOver\Rest\Application;

use MouseOver\Rest\Resource\IResource;
use Nette\Application\IResponse;


/**
 * IResponseFactory
 *
 * @package Drahak\Restful
 * @author  Drahomír Hanák
 */
interface IResponseFactory
{

    /**
     * Create new api response
     *
     * @param IResource   $resource    Resource instance
     * @param string|null $contentType Optional content type
     *
     * @return IResponse
     */
    public function create(IResource $resource, $contentType = null);

}
