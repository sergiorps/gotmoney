<?php

/**
 * Abstract for basic operations in tables
 *
 * @author Mauricio Lauffer 
 */
abstract class Application_Model_TableAbstract
{
    /**
     * Class constructor
     *
     * @return
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    } //end FUNCTION


    /**
     * Set operations
     *
     * @return
     */
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            $logger = Zend_Registry::get('logger');
            $logger->err('Invalid property: ' . $name);
            throw new Exception('Invalid property: ' . $name);
        }
        $this->$method($value);
    } //end FUNCTION


    /**
     * Get operations
     *
     * @return
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            $logger = Zend_Registry::get('logger');
            $logger->err('Invalid property: ' . $name);
            throw new Exception('Invalid property: ' . $name);
        }
        return $this->$method();
    } //end FUNCTION


    /**
     * Check if given domain object property has been set
     *
     * @return boolean
     */
    public function __isset($property)
    {
        return isset($this->_data[$property]);
    }


    /**
     * Unset domain object property
     *
     * @return
     */
    public function __unset($property)
    {
        if (isset($this->_data[$property])) {
            unset($this->_data[$property]);
        }
    }


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }

        return $this;
    } //end FUNCTION



    /**
     * Return encoded string
     *
     * @return string
     */
    protected function encodeString($value)
    {
        return utf8_encode(htmlentities($value));
    } //end FUNCTION



    /**
     * Return decoded string
     *
     * @return string
     */
    protected function decodeString($value)
    {
        return html_entity_decode(utf8_decode($value));
    } //end FUNCTION



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this as $key => $value) {
            $array[$key] = $this->__get($key);
        }
        return $array;
    } //end FUNCTION
} //end CLASS
