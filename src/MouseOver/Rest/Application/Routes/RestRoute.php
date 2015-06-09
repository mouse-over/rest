<?php
namespace MouseOver\Rest\Application\Routes;

use MouseOver\Rest\Application\IRestRoute;
use Nette\Application\Routers\Route;
use Nette\Http;
use Nette\Application;
use Nette\Utils\Strings;

/**
 * Class RestRoute
 *
 * @package MouseOver\Rest
 */
class RestRoute extends Route implements IRestRoute
{

    /** @var array */
    protected $actionDictionary = [
        self::GET => 'get',
        self::POST => 'post',
        self::PUT => 'put',
        self::HEAD => 'head',
        self::DELETE => 'delete',
        self::PATCH => 'path'
    ];

    /** @var array */
    private $methodDictionary = array(
        Http\IRequest::GET => self::GET,
        Http\IRequest::POST => self::POST,
        Http\IRequest::PUT => self::PUT,
        Http\IRequest::HEAD => self::HEAD,
        Http\IRequest::DELETE => self::DELETE,
        'PATCH' => self::PATCH
    );

    private $action = null;

    /**
     * Constructor
     *
     * @param string       $mask     Mask
     * @param array|string $metadata Metadata
     * @param int          $flags    Flags
     */
    public function __construct($mask, $metadata = array(), $flags = IRestRoute::GET)
    {

        if (isset($metadata['actionDictionary'])) {
            $this->actionDictionary = $metadata['actionDictionary'];
            unset($metadata['actionDictionary']);
        } else {
            /*$action = isset($metadata['method']) ? $metadata['method'] : 'default';
            if (is_string($metadata)) {
                $metadataParts = explode(':', $metadata);
                $action = end($metadataParts);
            }
            $this->action = $action;
            foreach ($this->methodDictionary as $methodName => $methodFlag) {
                if (($flags & $methodFlag) == $methodFlag) {
                    $this->actionDictionary[$methodFlag] = $action;
                }
            }*/
        }

        if (isset($metadata['action'])) {
            $this->action = $metadata['action'];
        }

        parent::__construct($mask, $metadata, $flags);
    }

    /**
     * Match
     *
     * @param Http\IRequest $httpRequest Http request
     *
     * @return Application\Request|NULL
     */
    public function match(Http\IRequest $httpRequest)
    {
        $appRequest = parent::match($httpRequest);
        if (!$appRequest) {
            return null;
        }

        // Check requested method
        $methodFlag = $this->getMethod($httpRequest);
        if (!$this->isMethod($methodFlag)) {
            return null;
        }

        // If there is action dictionary, set method
        if ($this->actionDictionary) {
            $parameters = $appRequest->getParameters();
            if (isset($parameters['action'])) {
              $parameters['action'] = $this->actionDictionary[$methodFlag] . ucfirst($parameters['action']);
            } else if (is_string($this->actionDictionary[$methodFlag])) {
                $parameters['action'] = $this->actionDictionary[$methodFlag];
            } else if (is_array($this->actionDictionary[$methodFlag])) {
                if (array_search($parameters['action'], $this->actionDictionary[$methodFlag]) === false) {
                    return null;
                }
            } else {
                $parameters['action'] = $methodFlag;
                $parameters['closure'] = $this->actionDictionary[$methodFlag];
            }

            $parameters['action'] = self::formatActionName($parameters['action'], $parameters);
            $appRequest->setParameters($parameters);
        }

        return $appRequest;
    }

    /**
     * Get request method flag
     *
     * @param Http\IRequest $httpRequest Http request
     *
     * @return string|null
     */
    public function getMethod(Http\IRequest $httpRequest)
    {
        $method = $httpRequest->getMethod();
        if (!isset($this->methodDictionary[$method])) {
            return null;
        }
        return $this->methodDictionary[$method];
    }

    /**
     * Is this route mapped to given method
     *
     * @param int $method Method
     *
     * @return bool
     */
    public function isMethod($method)
    {
        $common = array(self::CRUD, self::RESTFUL);
        $isActionDefined = $this->actionDictionary && !in_array($method, $common) ?
            isset($this->actionDictionary[$method]) :
            true;
        return ($this->flags & $method) == $method && $isActionDefined;
    }

    /**
     * Format action name
     *
     * @param string $action     Action
     * @param array  $parameters Parameters
     *
     * @return string
     */
    protected static function formatActionName($action, array $parameters)
    {
        return Strings::replace(
            $action, "@\<([0-9a-zA-Z_-]+)\>@i",
            function ($m) use ($parameters) {
                $key = strtolower($m[1]);
                return isset($parameters[$key]) ? $parameters[$key] : '';
            }
        );
    }

    /**
     * Get action dictionary
     *
     * @return array|NULL
     */
    public function getActionDictionary()
    {
        return $this->actionDictionary;
    }

    /**
     * Set action dictionary
     *
     * @param array|NULL $actionDictionary Action dictionary
     *
     * @return $this
     */
    public function setActionDictionary($actionDictionary)
    {
        $this->actionDictionary = $actionDictionary;
        return $this;
    }

    /**
     * Construct url
     *
     * @param Application\Request $appRequest App request
     * @param Http\Url            $refUrl     Url
     *
     * @return NULL|string
     */
    public function constructUrl(Application\Request $appRequest, Http\Url $refUrl)
    {
        if (count($this->actionDictionary) > 0) {
            $appRequest = clone $appRequest;
            $params = $appRequest->getParameters();

            if (isset($params['action'])) {
                $methodFlag = isset($this->methodDictionary[$appRequest->getMethod()])
                    ? $this->methodDictionary[$appRequest->getMethod()]
                    : null;

                $action = $this->actionDictionary[$methodFlag];

                if (is_string($action) && Strings::startsWith($params['action'], $action)) {
                    $action = lcfirst(Strings::substring($params['action'], strlen($action)));
                }
                if ($action) {
                    $params['action'] = $action;
                } else {
                    unset($params['action']);
                }
            } else if ($this->action) {
                $params['action'] = $this->action;
            } else {
                unset($params['action']);
            }

            $appRequest->setParameters($params);
        }

        $url = parent::constructUrl($appRequest, $refUrl);
        return $url;
    }
}