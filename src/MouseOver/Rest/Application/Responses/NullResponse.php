<?php
namespace MouseOver\Rest\Application\Responses;

use Nette\Application\IResponse;
use Nette\Http;

/**
 * NullResponse
 *
 * @package MouseOver\Rest\Application\Responses
 * @author  Drahomír Hanák
 */
class NullResponse implements IResponse
{

    use \Nette\SmartObject;

    /**
     * Do nothing
     *
     * @param Http\IRequest  $httpRequest
     * @param Http\IResponse $httpResponse
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
    {
    }


}