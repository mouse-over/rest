<?php
namespace MouseOver\Rest\DI;

use MouseOver\Rest\Resource\IResource;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\Utils\Validators;


/**
 * Class Extension
 *
 * @package MouseOver\Rest
 */
class Extension extends CompilerExtension
{

    private $defaults = [
        'client' => true,
        'server' => true,
        'validations' => true
    ];

    /**
     * Load DI configuration
     */
    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);

        if ($config['client'] || $config['server']) {
            $this->loadMapping($container, $config);
            if ($config['server']) {
                $this->loadRestful($container, $config);
            }

            if ($config['validations']) {
                $this->loadValidation($container, $config);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param                  $config
     */
    private function loadMapping(ContainerBuilder $container, $config)
    {

        // Mappers
        $container->addDefinition($this->prefix('xmlMapper'))
            ->setClass('MouseOver\Rest\Mapping\XmlMapper');
        $container->addDefinition($this->prefix('jsonMapper'))
            ->setClass('MouseOver\Rest\Mapping\JsonMapper');
        $container->addDefinition($this->prefix('queryMapper'))
            ->setClass('MouseOver\Rest\Mapping\QueryMapper');
        $container->addDefinition($this->prefix('dataUrlMapper'))
            ->setClass('MouseOver\Rest\Mapping\DataUrlMapper');
        $container->addDefinition($this->prefix('nullMapper'))
            ->setClass('MouseOver\Rest\Mapping\NullMapper');

        $container->addDefinition($this->prefix('mapperContext'))
            ->setClass('MouseOver\Rest\Mapping\MapperContext')
            ->addSetup('$service->addMapper(?, ?)', array(IResource::XML, $this->prefix('@xmlMapper')))
            ->addSetup('$service->addMapper(?, ?)', array(IResource::JSON, $this->prefix('@jsonMapper')))
            ->addSetup('$service->addMapper(?, ?)', array(IResource::JSONP, $this->prefix('@jsonMapper')))
            ->addSetup('$service->addMapper(?, ?)', array(IResource::QUERY, $this->prefix('@queryMapper')))
            ->addSetup('$service->addMapper(?, ?)', array(IResource::DATA_URL, $this->prefix('@dataUrlMapper')))
            ->addSetup('$service->addMapper(?, ?)', array(IResource::FILE, $this->prefix('@nullMapper')))
            ->addSetup('$service->addMapper(?, ?)', array(IResource::NULL, $this->prefix('@nullMapper')));
    }

    /**
     * @param ContainerBuilder $container
     * @param                  $config
     */
    private function loadRestful(ContainerBuilder $container, $config)
    {
        // Input & validation
        $container->addDefinition($this->prefix('inputFactory'))
            ->setClass('MouseOver\Rest\Http\InputFactory');

        $container->addDefinition($this->prefix('resourceFactory'))
            ->setClass('MouseOver\Rest\Resource\DefaultResourceFactory');

        $container->addDefinition($this->prefix('responseFactory'))
            ->setClass('MouseOver\Rest\Application\ResponseFactory');

        // Http
        $container->addDefinition($this->prefix('httpResponseFactory'))
            ->setClass('MouseOver\Rest\Http\ResponseFactory');

        $container->getDefinition('httpResponse')
            ->setFactory($this->prefix('@httpResponseFactory') . '::createHttpResponse');
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    private function loadValidation(ContainerBuilder $container, $config)
    {
        $container->addDefinition($this->prefix('validator'))
            ->setClass('MouseOver\Rest\Validation\Validator');

        $container->addDefinition($this->prefix('validationScopeFactory'))
            ->setClass('MouseOver\Rest\Validation\ValidationScopeFactory');

        $container->addDefinition($this->prefix('validationScope'))
            ->setClass('MouseOver\Rest\Validation\ValidationScope')
            ->setFactory($this->prefix('@validationScopeFactory') . '::create');

    }

}