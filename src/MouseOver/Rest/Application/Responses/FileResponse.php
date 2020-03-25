<?php
namespace MouseOver\Rest\Application\Responses;

use MouseOver\Rest\Mapping\IMapper;
use MouseOver\Rest\Resource\Media;
use Nette\Http;

/**
 * FileResponse
 *
 * @package MouseOver\Rest\Application\Responses
 * @author  Drahomír Hanák
 */
class FileResponse extends BaseResponse
{

    /**
     * @param Media       $data
     * @param IMapper     $mapper
     * @param string|null $contentType
     */
    public function __construct(Media $data, IMapper $mapper, $contentType = NULL)
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
        $httpResponse->setContentType($this->contentType);
        $name = $this->data->getName();
        if ($name) {
            $httpResponse->setHeader('Content-Disposition',
                ($this->data->isForceDownload() ? 'attachment' : 'inline')
                . '; filename="' . $name . '"'
                . '; filename*=utf-8\'\'' . rawurlencode($name));
        }

        echo $this->data->content;
    }


}
