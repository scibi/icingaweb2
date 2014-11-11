<?php

namespace Icinga\Module\Monitoring\Backend;

use Icinga\Application\Config;
use Icinga\Data\ResourceFactory;
use Icinga\Data\ConnectionInterface;
use Icinga\Data\Queryable;
use Icinga\Data\Selectable;
use Icinga\Exception\ConfigurationError;
use Icinga\Exception\ProgrammingError;


class MonitoringBackend implements Selectable, Queryable, ConnectionInterface
{
    /**
     * @var Config
     *
    protected $config;

    /**
     * Resource
     *
     * @var mixed
     */
    protected $resource;

    /**
     * Type
     *
     * @var string
     */
    protected $type;

    /**
     * The configured name of this backend
     *
     * @var string
     */
    protected $name;

    /**
     * Already created instances
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Create a new backend
     *
     * @param   string  $name
     * @param   mixed   $config
     */
    protected function __construct($name, $config)
    {
        $this->name   = $name;
        $this->config = $config;
    }

    public static function instance($name = null)
    {
        if (! array_key_exists($name, self::$instances)) {

            list($foundName, $config) = static::loadConfig($name);
            $type = $config->get('type');
            $class = implode(
                '\\',
                array(
                __NAMESPACE__,
                ucfirst($type),
                ucfirst($type) . 'Backend'
                )
            );

            if (!class_exists($class)) {
                throw new ConfigurationError(
                    mt('monitoring', 'There is no "%s" monitoring backend'),
                    $class
                );
            }

            self::$instances[$name] = new $class($foundName, $config);
            if ($name === null) {
                self::$instances[$foundName] = self::$instances[$name];
            }
        }

        return self::$instances[$name];
    }

    /**
     * Clear all cached instances. Mostly for testing purposes.
     */
    public static function clearInstances()
    {
        self::$instances = array();
    }

    /**
     * Whether this backend is of a specific type
     *
     * @param  string $type Backend type
     *
     * @return boolean
     */
    public function is($type)
    {
        return $this->getType() === $type;
    }

    /**
     * Get the configured name of this backend
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the backend type name
     *
     * @return string
     */
    public function getType()
    {
        if ($this->type === null) {
            $parts = preg_split('~\\\~', get_class($this));
            $class = array_pop($parts);
            if (substr($class, -7) === 'Backend') {
                $this->type = lcfirst(substr($class, 0, -7));
            } else {
                throw new ProgrammingError(
                    '%s is not a valid monitoring backend class name', 
                    $class
                );
            }
        }
        return $this->type;
    }

    /**
     * Return the configuration for the first enabled or the given backend
     */
    protected static function loadConfig($name = null)
    {
        $backends = Config::module('monitoring', 'backends');

        if ($name === null) {

            $count = 0;

            foreach ($backends as $name => $config) {
                $count++;
                if ((bool) $config->get('disabled', false) === false) {
                    return array($name, $config);
                }
            }

            if ($count === 0) {
                $message = mt('monitoring', 'No backend has been configured');
            } else {
                $message = mt('monitoring', 'All backends are disabled');
            }

            throw new ConfigurationError($message);

        } else {

            $config = $backends->get($name);

            if ($config === null) {
                throw new ConfigurationError(
                    mt('monitoring', 'No configuration for backend %s'),
                    $name
                );
            }

            if ((bool) $config->get('disabled', false) === true) {
                throw new ConfigurationError(
                    mt('monitoring', 'Configuration for backend %s is disabled'),
                    $name
                );
            }

            return array($name, $config);
        }
    }

    /**
     * Create a backend
     *
     * @deprecated
     *
     * @param   string $backendName Name of the backend or null for creating the default backend which is the first INI
     *                              configuration entry not being disabled
     *
     * @return  Backend
     * @throws  ConfigurationError  When no backend has been configured or all backends are disabled or the
     *                              configuration for the requested backend does either not exist or it's disabled
     */
    public static function createBackend($name = null)
    {
        return self::instance($name);
    }

    public function getResource()
    {
        if ($this->resource === null) {
            $this->resource = ResourceFactory::create($this->config->get('resource'));
            if ($this->is('ido') && $this->resource->getDbType() !== 'oracle') {
                // TODO(el): The resource should set the table prefix
                $this->resource->setTablePrefix('icinga_');
            }
        }
        return $this->resource;
    }

    /**
     * Backend entry point
     *
     * @return self
     */
    public function select()
    {
        return $this;
    }

    /**
     * Create a data view to fetch data from
     *
     * @param   string  $name
     * @param   array   $columns
     *
     * @return  DataView
     */
    public function from($name, array $columns = null)
    {
        $class = $this->buildViewClassName($name);
        return new $class($this, $columns);
    }

    /**
     * View name to class name resolution
     *
     * @param   string $viewName
     *
     * @return  string
     * @throws  ProgrammingError When the view does not exist
     */
    protected function buildViewClassName($view)
    {
        $class = '\\Icinga\\Module\\Monitoring\\DataView\\' . ucfirst($view);
        if (!class_exists($class)) {
            throw new ProgrammingError(
                'DataView %s does not exist',
                ucfirst($view)
            );
        }
        return $class;
    }

    /**
     * Get a specific query class instance
     *
     * @param  string $name     Query name
     * @param  array  $columns Optional column list
     *
     * @return Icinga\Data\QueryInterface
     */
    public function query($name, $columns = null)
    {
        $class = $this->buildQueryClassName($name);

        if (!class_exists($class)) {
            throw new ProgrammingError(
                'Query "%s" does not exist for backend %s',
                $name,
                $this->getType()
            );
        }

        return new $class($this->getResource(), $columns);
    }

    /**
     * Whether this backend supports the given query
     *
     * @param string $name Query name to check for
     *
     * @return bool
     */
    public function hasQuery($name)
    {
        return class_exists($this->buildQueryClassName($name));
    }

    /**
     * Query name to class name resolution
     *
     * @param   string $query
     *
     * @return  string
     * @throws  ProgrammingError When the query does not exist for this backend
     */
    protected function buildQueryClassName($query)
    {
        $parts = preg_split('~\\\~', get_class($this));
        array_pop($parts);
        array_push($parts, 'Query', ucfirst($query) . 'Query');
        return implode('\\', $parts);
    }
}
