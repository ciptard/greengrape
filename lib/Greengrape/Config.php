<?php
/**
 * Config class file
 *
 * @package Greengrape
 */

namespace Greengrape;

/**
 * Config
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Config implements \ArrayAccess
{
    /**
     * Data storage for config settings
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Default config settings
     *
     * @var array
     */
    protected $_defaults = array(
        'sitename'     => '[Greengrape]',
        'theme'        => 'fulcrum',
        'enable_cache' => true,
    );

    /**
     * Constructor
     *
     * @param string $configFile Filename
     * @return void
     */
    public function __construct($configFile = '')
    {
        $this->_data = $this->_defaults;
        $this->loadFile($configFile);
    }

    /**
     * Load file
     *
     * @param string $filename Filename to ini file
     * @return \Greengrape\Config
     */
    public function loadFile($filename)
    {
        $raw = parse_ini_file($filename, true);

        $this->_data = array_merge($this->_data, $raw);
        return $this;
    }

    /**
     * Get config setting
     *
     * @param string $key Setting name
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->offsetExists($key)) {
            return null;
        }

        return $this->_data[$key];
    }

    /**
     * Set a config value
     *
     * @param string $key Key
     * @param mixed $value Value
     * @return void
     */
    public function set($key, $value)
    {
        if (is_null($key)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$key] = $value;
        }
    }

    /**
     * Offset get
     *
     * ArrayAccess interface
     *
     * @param string $offset Array key offset
     * @param mixed $value Value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset exists
     *
     * ArrayAccess interface
     *
     * @param string $offset Array key
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * Offset unset
     *
     * ArrayAccess interface
     *
     * @param string $offset Array key
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /**
     * Offset get
     *
     * @param string $offset Array key
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}
