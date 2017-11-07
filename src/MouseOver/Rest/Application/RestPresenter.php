<?php
namespace MouseOver\Rest\Application;


use MouseOver\Rest\Http\IInputFactory;
use Nette,
    Nette\Application,
    Nette\Application\Responses,
    Nette\Http;
use MouseOver,
    MouseOver\Rest;

/**
 * Class RestPresenter
 *
 * @package MouseOver\Rest
 */
class RestPresenter extends Nette\Object implements Application\IPresenter
{

    /** @var array */
    protected $params = array();

    /** @var Nette\DI\Container */
    private $context;

    /** @var Nette\Application\Request */
    private $request;

    /** @var  \Nette\Http\IRequest */
    private $httpRequest;

    /** @var  \Nette\Http\IResponse */
    private $httpResponse;

    /** @var bool */
    private $startupCheck;

    /** @var  \MouseOver\Rest\Resource\IResourceFactory */
    private $resourceFactory;

    /** @var  \MouseOver\Rest\Resource\IResource */
    private $resource;

    /** @var  \MouseOver\Rest\Application\IResponseFactory */
    private $responseFactory;

    /** @var  \MouseOver\Rest\Http\IInputFactory */
    private $inputFactory;

    /** @var  \MouseOver\Rest\Http\IInput */
    private $input;

    /** @var array */
    protected $formats = array(
        'json' => Rest\Resource\IResource::JSON,
        'xml' => Rest\Resource\IResource::XML,
        'none' => Rest\Resource\IResource::NULL,
        'ics' => 'text/calendar'
    );

    /**
     * Return's http request
     *
     * @return Http\IRequest
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * Return's http response
     *
     * @return Http\IResponse
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * Return's resource factory
     *
     * @return Rest\Resource\IResourceFactory
     */
    public function getResourceFactory()
    {
        return $this->resourceFactory;
    }

    /**
     * Return's response factory
     *
     * @return IResponseFactory
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * Return's input factory
     *
     * @return IInputFactory
     */
    public function getInputFactory()
    {
        return $this->inputFactory;
    }


    /**
     * Return's true if is ajax request
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->httpRequest->isAjax();
    }

    /**
     * Inject primary dependencies
     *
     * @param \Nette\DI\Container $context DI context
     * @param \Nette\Http\IRequest $httpRequest Http request
     * @param \Nette\Http\IResponse $httpResponse Http response
     * @param \MouseOver\Rest\Resource\IResourceFactory $resourceFactory Resource factory
     * @param \MouseOver\Rest\Application\IResponseFactory $responseFactory Response factory
     * @param \MouseOver\Rest\Http\IInputFactory $inputFactory Input factory
     *
     * @return void
     */
    public function injectPrimary(
        Nette\DI\Container $context,
        Http\IRequest $httpRequest,
        Http\IResponse $httpResponse,
        Rest\Resource\IResourceFactory $resourceFactory,
        IResponseFactory $responseFactory,
        IInputFactory $inputFactory
    )
    {
        if ($this->context !== null) {
            throw new Nette\InvalidStateException(
                "Method " . __METHOD__ . " is intended for initialization and should not be called more than once."
            );
        }

        $this->context = $context;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
        $this->resourceFactory = $resourceFactory;
        $this->responseFactory = $responseFactory;
        $this->inputFactory = $inputFactory;
    }

    /**
     * Run presenter
     *
     * @param \Nette\Application\Request $request Application request
     *
     * @return \Nette\Application\IResponse
     */
    public function run(Application\Request $request)
    {
        $this->request = $request;

        if (!$this->httpRequest->isAjax() && ($request->isMethod('get') || $request->isMethod('head'))) {
            $refUrl = clone $this->httpRequest->getUrl();
            $url = $this->context->getByType('Nette\Application\IRouter')
                ->constructUrl($request, $refUrl->setPath($refUrl->getScriptPath()));
            if ($url !== null && !$this->httpRequest->getUrl()->isEqual($url)) {
                return new Responses\RedirectResponse($url, Http\IResponse::S301_MOVED_PERMANENTLY);
            }
        }

        try {
            $this->params = $request->getParameters();
            $this->authenticate();
            $this->checkRequirements($this->getReflection());
            $this->startup();
            if (!$this->startupCheck) {
                $class = $this->getReflection()->getMethod('startup')->getDeclaringClass()->getName();
                throw new Rest\InvalidStateException(
                    "Method $class::startup() or its descendant doesn't call parent::startup()."
                );
            }

            if ($request->getMethod() === 'OPTIONS') {
                 //- nothing to do
            } else {
                if ($this->getParameter('closure', false)) {
                    $this->processCallback($this->getParameter('closure'));
                } else {
                    $this->execute($this->getInput(), $this->getResource());
                }
            }

            $this->success();
        } catch (\Exception $exception) {
            $this->resource = $this->createErrorResource($exception);
        }

        return $this->createResponse($this->getParameter('format', null));
    }

    /**
     * Successfuly run
     *
     * @return void
     */
    protected function success()
    {
         $this->getResource()->status = 'success';
    }

    /**
     * Authenticate presenter
     *
     * @return void
     */
    protected function authenticate()
    {

    }

    /**
     * Checks for requirements such as authorization.
     *
     * @param \Nette\Reflection\ClassType|\Nette\Reflection\Method $element Element reflection
     *
     * @return void
     */
    protected function checkRequirements($element)
    {
    }

    /**
     * Access to reflection.
     *
     * @return Application\UI\PresenterComponentReflection
     */
    public static function getReflection()
    {
        return new Application\UI\PresenterComponentReflection(get_called_class());
    }

    /**
     * Startup presenter
     *
     * @return void
     */
    protected function startup()
    {
        $this->startupCheck = true;
    }

    /**
     * Returns component param.
     *
     * @param string $name key
     * @param mixed $default default value
     *
     * @return mixed
     */
    public function getParameter($name = null, $default = null)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } else {
            return $default;
        }
    }

    /**
     * Process callback
     *
     * @param \Closure $callback Callback
     *
     * @return mixed
     * @throws \Exception
     * @throws \Nette\Application\BadRequestException
     * @throws \ReflectionException
     */
    protected function processCallback($callback)
    {
        $params = $this->getParameters();
        $params['presenter'] = $this;
        $params['input'] = $this->getInput();
        $params['output'] = $this->getResource();
        $reflection = Nette\Utils\Callback::toReflection(Nette\Utils\Callback::check($callback));
        $params = Application\UI\PresenterComponentReflection::combineArgs($reflection, $params);

        foreach ($reflection->getParameters() as $param) {
            if ($param->getClassName()) {
                unset($params[$param->getPosition()]);
            }
        }
        $params = Nette\DI\Helpers::autowireArguments($reflection, $params, $this->context);

        $result = call_user_func_array($callback, $params);

        return $result;
    }

    /**
     * Returns component parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Return's input
     *
     * @return \MouseOver\Rest\Http\IInput
     */
    public function getInput()
    {
        if (!$this->input) {
            $this->input = $this->inputFactory->create();
        }

        return $this->input;
    }

    /**
     * Return's resource
     *
     * @return Rest\Resource\IResource
     */
    public function getResource()
    {
        if (!$this->resource) {
            $this->resource = $this->resourceFactory->create();
        }
        return $this->resource;
    }

    /**
     * Execute presenter
     *
     * @param Rest\Http\IInput $input Input
     * @param Rest\Resource\IResource $output Resource
     *
     * @return void
     */
    protected function execute($input, $output)
    {
        $method = $this->formatActionMethodName();
        $parameters = $this->getParameters();
        $parameters['input'] = $input;
        $parameters['output'] = $output;
        if ($this->tryCall($method, $parameters) === false) {
            throw Rest\BadRequestException::notFound('Action "'.$method.'" not found!');
        }
    }

    /**
     * Return's action method name
     *
     * @return string
     */
    protected function formatActionMethodName() {
        return 'action'.ucfirst($this->getParameter('action', 'default'));
    }

    /**
     * Create error response from exception
     *
     * @param \Exception $exception Exception to create resource from
     *
     * @return \MouseOver\Rest\Resource\IResource
     */
    protected function createErrorResource(\Exception $exception)
    {
        $code = $exception->getCode() ? $exception->getCode() : 500;
        if ($code < 100 || $code > 599) {
            $code = 400;
        }

        $resource = $this->resourceFactory->create(
            [
                'code' => $code,
                'status' => 'error',
                'message' => $exception->getMessage()
            ]
        );

        if (isset($exception->errors) && $exception->errors) {
            $resource->errors = $exception->errors;
        }

        return $resource;
    }

    /**
     * Create response
     *
     * @param string|null $format Optional format
     *
     * @return \Nette\Application\IResponse
     */
    protected function createResponse($format = null)
    {
        $contentType = $this->responseFactory->getFormat($format);
        return $this->responseFactory->create($this->getResource(), $contentType);
    }

    /**
     * Redirects to another URL.
     *
     * @param string $url Url
     * @param int $code HTTP code
     *
     * @return Nette\Application\Responses\RedirectResponse
     */
    public function redirectUrl($url, $code = Http\IResponse::S302_FOUND)
    {
        return new Responses\RedirectResponse($url, $code);
    }

    /**
     * Throws HTTP error.
     *
     * @param string $message Message
     * @param int $code HTTP error code
     *
     * @return void
     * @throws Nette\Application\BadRequestException
     */
    public function error($message = null, $code = Http\IResponse::S404_NOT_FOUND)
    {
        throw new Application\BadRequestException($message, $code);
    }

    /**
     * Return's application request
     *
     * @return \Nette\Application\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Calls public method if exists.
     *
     * @param string $method Method name
     * @param array $params Parameters
     *
     * @return bool  does method exist?
     */
    protected function tryCall($method, array $params)
    {
        $rc = $this->getReflection();
        if ($rc->hasMethod($method)) {
            $rm = $rc->getMethod($method);
            if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
                $this->checkRequirements($rm);
                $rm->invokeArgs($this, $rc->combineArgs($rm, $params));
                return true;
            }
        }
        return false;
    }

}