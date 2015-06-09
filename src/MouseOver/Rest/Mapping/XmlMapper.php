<?php
namespace MouseOver\Rest\Mapping;

use DOMDocument;
use Traversable;
use Nette\Object;
use SimpleXMLElement;
use MouseOver\Rest\InvalidArgumentException;

/**
 * XmlMapper
 *
 * @package MouseOver\Rest\Mapping
 * @author  Drahomír Hanák
 *
 * @property string|NULL $rootElement
 * @property string      $itemElement
 */
class XmlMapper extends Object implements IMapper
{

    /** @internal */
    const ITEM_ELEMENT = 'item';

    /** @var DOMDocument */
    private $xml;

    /** @var null|string */
    private $rootElement;

    /** @var null|string */
    private $itemElement;

    /**
     * @param string|null $rootElement
     *
     * @throws InvalidArgumentException
     */
    public function __construct($rootElement = 'root')
    {
        $this->rootElement = $rootElement;
        $this->itemElement = self::ITEM_ELEMENT;
        $this->xml = new DOMDocument('1.0', 'UTF-8');
        $this->xml->formatOutput = TRUE;
    }

    /**
     * Get XML root element
     *
     * @return null|string
     */
    public function getRootElement()
    {
        return $this->rootElement;
    }

    /**
     * Set XML root element
     *
     * @param string|null $rootElement
     *
     * @return XmlMapper
     *
     * @throws InvalidArgumentException
     */
    public function setRootElement($rootElement)
    {
        if (!is_string($rootElement) && $rootElement !== NULL) {
            throw new InvalidArgumentException('Root element must be of type string or null if disabled');
        }
        $this->rootElement = $rootElement;
        return $this;
    }

    /**
     * Get item element
     *
     * @return string
     */
    public function getItemElement()
    {
        return $this->itemElement;
    }

    /**
     * Set item element tag name for array lists
     *
     * @param string $itemElement
     *
     * @return XmlMapper
     *
     * @throws InvalidArgumentException
     */
    public function setItemElement($itemElement)
    {
        if (!is_string($itemElement)) {
            throw new InvalidArgumentException('Item element must be of type string');
        }
        $this->itemElement = $itemElement;
        return $this;
    }

    /**
     * Parse traversable or array resource data to XML
     *
     * @param array|Traversable $data
     * @param bool              $prettyPrint
     *
     * @return mixed|string
     *
     * @throws InvalidArgumentException
     */
    public function stringify($data, $prettyPrint = TRUE)
    {
        if (!is_array($data) && !($data instanceof Traversable)) {
            throw new InvalidArgumentException('Data must be of type array or Traversable');
        }

        if ($data instanceof Traversable) {
            $data = iterator_to_array($data, TRUE);
        }

        if ($this->rootElement) {
            $data = array($this->rootElement => $data);
        }

        $this->toXml($data);
        $this->xml->preserveWhiteSpace = $prettyPrint;
        $this->xml->formatOutput = $prettyPrint;
        return $this->xml->saveXML();
    }

    /**
     * @param      $data
     * @param null $domElement
     */
    private function toXml($data, $domElement = NULL)
    {
        $domElement = is_null($domElement) ? $this->xml : $domElement;

        if (is_array($data) || $data instanceof Traversable) {
            foreach ($data as $index => $mixedElement) {
                if (is_int($index)) {
                    $node = $this->xml->createElement($this->itemElement);
                    $node->setAttribute('index', $index);
                } else {
                    $node = $this->xml->createElement($index);
                }
                $domElement->appendChild($node);
                $this->toXml($mixedElement, $node);
            }
        } else {
            $domElement->appendChild($this->xml->createTextNode($data));
        }
    }

    /**
     * Parse XML to array
     *
     * @param string $data
     *
     * @return array
     */
    public function parse($data)
    {
        return $this->fromXml($data);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    private function fromXml($data)
    {
        $xml = new SimpleXMLElement($data);
        return (array)$xml;
    }

}
