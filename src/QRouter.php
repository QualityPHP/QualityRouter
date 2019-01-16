<?php

namespace QualityPHP\QualityRouter;

use Exception;
use Closure;

class QRouter
{
    /**
     * The available methods
     *
     * @var array
     */
    protected static $methods = ['GET', 'POST', 'ANY'];

    /**
     * The routes list
     *
     * @var array
     */
    protected static $routes = [];
    
    /**
     * Router configuration
     *
     * @var array
     */
    public static $rConfig = ['AUTO' => false];

    /**
     * App Configuration Keys
     *
     * @var array
     */
    protected $configKeys = ['APP_DIR', 'ROUTES'];
    
    /**
     * App configuration
     *
     * @var array
     */
    public $config;

    /**
     * Create a new instance of QRouter
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->loadRoutes();
    }
    
    /**
     * Create a new route statically
     *
     * @param string $method
     * @param array $args
     * @return void
     * @throws \Exception
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
     * Load routes
     *
     * @return void
     */
    protected function loadRoutes()
    {
        include $this->config['ROUTES'];
    }

    /**
     * Set a configuration key
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws \Exception
     */
    public static function CONFSET(string $key, $value)
    {
        if (!array_key_exists($key, self::$rConfig)) {
            throw new Exception('Invalid router configuration key.');
        }
        
        if (gettype($value) !== gettype(self::$rConfig[$key])) {
            throw new Exception("Invalid value given to $key.");
        }

        self::$rConfig[$key] = $value;
    }
}
