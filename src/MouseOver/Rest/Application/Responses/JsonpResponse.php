<?php
namespace MouseOver\Rest\Application\Responses;

use MouseOver;
use MouseOver\Rest\Mapping\IMapper;
use MouseOver\Rest\InvalidArgumentException;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Strings;

/**
 * JSONP response
 *
 * @package MouseOver\Rest\Application\Responses
 * @author  Drahomír Hanák
 */
class JsonpResponse extends BaseResponse
{

    /**
     * @param array   $data
     * @param IMapper $mapper
     * @param null    $contentType
     */
    public function __construct($data, IMapper $mapper, $contentType = NULL)
    {
        parent::__construct($mapper, $contentType);
        $this->data = $data;
    }

    /**
     * Send JSONP response to output
     *
     * @param IRequest  $httpRequest
     * @param IResponse $httpResponse
     *
     * @throws \MouseOver\Rest\InvalidArgumentException
     */
    public function send(IRequest $httpRequest, IResponse $httpResponse)
    {
        $httpResponse->setContentType($this->contentType ? $this->contentType : 'application/javascript', 'UTF-8');

        $data = array();
        $data['response'] = $this->data;
        $data['status'] = $httpResponse->getCode();
        $data['headers'] = $httpResponse->getHeaders();

        $callback = $httpRequest->getQuery('jsonp') ? Strings::webalize($httpRequest->getQuery('jsonp'), NULL, FALSE) : '';
        echo $callback . '(' . $this->mapper->stringify($data, $this->isPrettyPrint()) . ');';
    }


}
