<?php

namespace MouseOver\Rest\Crud;

use MouseOver\Rest\InvalidStateException;
use Nette;
use Nette\Object;
use Nette\Utils\Arrays;


/**
 * Class CrudAdapterFactory
 *
 * @package Eset\Rest\Models
 */
class CrudAdapterFactory extends Object
{

    /** @var bool */
    public $caseSensitive = FALSE;

    /** @var array[] of module => splited mask */
    private $mapping = array(
        '*' => array('', '*Module\\', '*Crud')
    );

    /** @var string */
    private $baseDir;

    /** @var array */
    private $cache = array();

    /** @var  \Nette\DI\Container */
    private $container;

    /** @var  string */
    private $default;

    /**
     * Sets mapping as pairs [module => mask]
     * @return self
     */
    public function setMapping(array $mapping)
    {
        foreach ($mapping as $module => $mask) {
            if (!preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#', $mask, $m)) {
                throw new InvalidStateException("Invalid mapping mask '$mask'.");
            }
            $this->mapping[$module] = array($m[1], $m[2] ?: '*Module\\', $m[3]);
        }
        return $this;
    }

    /**
     * AuthenticatorFactory constructor.
     *
     * @param string              $baseDir Base dir to look for crud files
     * @param \Nette\DI\Container $context DI Container
     */
    public function __construct($baseDir, \Nette\DI\Container $context)
    {
        $this->baseDir = $baseDir;
        $this->container = $context;
    }

    /**
     * Set's default adapter name
     *
     * @param string $default Adapter name
     *
     * @return void
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Creates new presenter instance.
     *
     * @param string $name Crud name
     *
     * @return ICrudAdapter
     */
    public function createCrud($name)
    {
        $class = $this->getCrudClass($name);
        if (count($services = $this->container->findByType($class)) === 1) {
            $crud = $this->container->createService($services[0]);
        } else {
            $crud = $this->container->createInstance($class);
        }
        $this->container->callInjects($crud);

        return $crud;
    }

    /**
     * Generates and checks presenter class name.
     *
     * @param string $name Crud name
     *
     * @return string  class name
     * @throws InvalidCrudException
     */
    public function getCrudClass(& $name)
    {
        if (isset($this->cache[$name])) {
            list($class, $name) = $this->cache[$name];
            return $class;
        }

        if (!is_string($name) || !Nette\Utils\Strings::match($name, '#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*\z#')) {
            throw new InvalidCrudException("Crud name must be alphanumeric string, '$name' is invalid.");
        }

        $class = $this->validateCrudClass($name, true);

        $reflection = new \Nette\Reflection\ClassType($class);
        $class = $reflection->getName();

        if (!$reflection->implementsInterface('MouseOver\Rest\Crud\ICrudAdapter')) {
            throw new InvalidCrudException(
                "Cannot load crud '$name', class '$class' is not MouseOver\\Rest\\Crud\\ICrudAdapter implementor."
            );
        }

        if ($reflection->isAbstract()) {
            throw new InvalidCrudException("Cannot load crud '$name', class '$class' is abstract.");
        }

        // canonicalize presenter name
        $realName = $this->unformatCrudClass($class);
        if ($name !== $realName) {
            if ($this->caseSensitive) {
                throw new InvalidCrudException("Cannot load crud '$name', case mismatch. Real name is '$realName'.");
            } else {
                $this->cache[$name] = array($class, $realName);
                $name = $realName;
            }
        } else {
            $this->cache[$name] = array($class, $realName);
        }

        return $class;
    }

    /**
     * Validate Crud class
     *
     * @param string $name    Crund name (including module)
     * @param bool   $default Optionaly use default crud class if true
     *
     * @return string
     * @throws \MouseOver\Rest\Crud\InvalidCrudException
     */
    protected function validateCrudClass($name, $default = false)
    {
        $class = $this->formatCrudClass($name);

        if (!class_exists($class)) {
            // internal autoloading
            $file = $this->formatCrudFile($name);
            if (is_file($file) && is_readable($file)) {
                call_user_func(
                    function () use ($file) {
                        require $file;
                    }
                );
            }

            if ($default) {
                $parts = explode(':', $name);
                array_pop($parts);
                $parts[] = 'Default';
                return $this->validateCrudClass(implode(':', $parts), false);
            }


            if (!class_exists($class)) {
                throw new InvalidCrudException("Cannot load crud '$name', class '$class' was not found in '$file'.");
            }
        }
        return $class;
    }

    /**
     * Formats crud class name from its name.
     *
     * @param string $crud Crud name
     *
     * @return string
     *
     * @internal
     */
    public function formatCrudClass($crud)
    {
        $parts = explode(':', $crud);
        $mapping = isset($parts[1], $this->mapping[$parts[0]])
            ? $this->mapping[array_shift($parts)]
            : $this->mapping['*'];

        while ($part = array_shift($parts)) {
            $mapping[0] .= str_replace('*', $part, $mapping[$parts ? 1 : 2]);
        }
        return $mapping[0];
    }

    /**
     * Formats crud class file name.
     *
     * @param string $crud Crud name
     *
     * @return string
     */
    public function formatCrudFile($crud)
    {
        $path = '/' . str_replace(':', 'Module/', $crud);
        return $this->baseDir . substr_replace($path, '/crud', strrpos($path, '/'), 0) . 'Crud.php';
    }

    /**
     * Formats crud name from class name.
     *
     * @param string $class Class name
     *
     * @return string
     * @internal
     */
    public function unformatCrudClass($class)
    {
        foreach ($this->mapping as $module => $mapping) {
            $mapping = str_replace(array('\\', '*'), array('\\\\', '(\w+)'), $mapping);
            if (preg_match("#^\\\\?$mapping[0]((?:$mapping[1])*)$mapping[2]\\z#i", $class, $matches)) {
                return ($module === '*' ? '' : $module . ':')
                . preg_replace("#$mapping[1]#iA", '$1:', $matches[1]) . $matches[3];
            }
        }
    }
}