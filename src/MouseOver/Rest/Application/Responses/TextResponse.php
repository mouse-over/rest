<?php
namespace MouseOver\Rest\Application\Responses;

use MouseOver\Rest\Mapping\IMapper;
use MouseOver\Rest\Resource\Media;
use Nette\Http;

/**
 * TextResponse
 *
 * @package MouseOver\Rest\Application\Responses
 * @author  Drahomír Hanák
 */
class TextResponse extends BaseResponse
{

    /**
     * @param Media       $data
     * @param IMapper     $mapper
     * @param string|null $contentType
     */
    public function __construct($data, IMapper $mapper, $contentType = NULL)
    {
        parent::__construct($mapper, $contentType);
        $this->data = $data;
    }

    /**
     * Sends response to output
     *
     * @param Http\IRequest  $httpRequest
     * @param Http\IResponse $httpResponse
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
    {
        $httpResponse->setContentType($this->contentType ? $this->contentType : 'text/plain', 'UTF-8');
        echo $this->mapper->stringify($this->data, $this->isPrettyPrint());
    }


}
