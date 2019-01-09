<?php

namespace yak0d3\QualityRouter;

use Exception;
use Closure;

class QRouter
{
    /**
     * The available methods
     *
     * @var array
     */
    protected static $methods = ['GET', 'POST'];

    /**
     * The routes list
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * The configuration keys
     *
     * @var array
     */
    protected $configKeys = ['APP_DIR', 'ROUTES'];
    
    /**
     * The configuration array
     *
     * @var array
     */
    public $config;

    /**
     * Create a new instance of QRouter
     *
     * @return void
     */
    public function __construct(string $config)
    {
        $this->config = $this->getConfig($config);
        $this->loadRoutes();
    }
    
    /**
     * Create new route statically
     *
     * @param string $method
     * @param array $args
     * @return void
     */
    public static function __callStatic(string $method, array $args)
    {
        if (!in_array($method, self::$methods)) {
            throw new Exception("Invalid method: $method");
        }

        self::registerRoute($method, $args[0], $args[1]);
    }

    /**
     * Register a new route
     *
     * @param string $method
     * @param string $uri
     * @param Closure $callback
     * @return void
     */
    protected static function registerRoute(string $method, string $uri, Closure $callback)
    {
        $route = [
                'METHOD' => $method,
                'CALLBACK' => $callback
        ];
        self::$routes[$uri] = $route;
    }

    /**
     * Get route object
     *
     * @param string $uri
     * @return object
     */
    public function getRoute(string $uri)
    {
        return (object) self::$routes[$uri];
    }

    /**
     * Check if QRouter has a route
     *
     * @param string $uri
     * @return bool
     */
    public function has(string $uri)
    {
        return in_array($uri, array_keys(self::$routes));
    }

    /**
     * Get the configuration file
     *
     * @param string $configFile
     * @return array
     * @throws \Exception
     */
    protected function getConfig(string $configFile)
    {
        $configFile = __DIR__.'/'.$configFile;

        if (!file_exists($configFile)) {
            throw new Exception("The configuration file '$configFile' was not found.");
        }

        $config = include $configFile;

        if (!is_array($config)) {
            throw new Exception('Invalid configuration.');
        }

        foreach ($this->configKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new Exception('Invalid configuration');
            }
        }

        return $config;
    }

    /**
     * Load routes
     *
     * @return void
     */
    protected function loadRoutes()
    {
        include $this->config['ROUTES'];
    }
}
